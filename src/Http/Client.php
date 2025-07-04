<?php

/*
 * (c) Rafael Ernesto Espinosa Santiesteban <rernesto.espinosa@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Http;

use RuntimeException;

class Client
{
    protected const USER_AGENTS = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
        'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/537.36 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/537.36',
        'Mozilla/5.0 (Android 13; Mobile; rv:119.0) Gecko/119.0 Firefox/119.0',
    ];

    public function __construct(protected array $defaultHeaders = [], protected bool $verifySsl = true)
    {
        $this->defaultHeaders = array_merge($this->defaultHeaders, $defaultHeaders);
        //        $this->defaultHeaders['User-Agent'] = self::USER_AGENTS[array_rand(self::USER_AGENTS)];
    }

    public function get(string $url, array $headers = []): string
    {
        return $this->request('GET', $url, [], $headers);
    }

    public function post(string $url, array $data = [], array $headers = []): string
    {
        return $this->request('POST', $url, $data, $headers);
    }

    public function put(string $url, array $data = [], array $headers = []): string
    {
        return $this->request('PUT', $url, $data, $headers);
    }

    public function patch(string $url, array $data = [], array $headers = []): string
    {
        return $this->request('PATCH', $url, $data, $headers);
    }

    public function delete(string $url, array $data = [], array $headers = []): string
    {
        return $this->request('DELETE', $url, $data, $headers);
    }

    public function request(string $method, string $url, array $data = [], array $headers = []): string
    {
        $ch = curl_init();

        $headers = array_merge($this->defaultHeaders, $headers);
        $formattedHeaders = $this->formatHeaders($headers);

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_HTTPHEADER     => $formattedHeaders,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => $this->verifySsl,
            CURLOPT_SSL_VERIFYHOST => $this->verifySsl ? 2 : 0,
        ]);

        if (\in_array(strtoupper($method), ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], true) && !empty($data)) {
            if ('application/x-www-form-urlencoded' === $headers['Content-Type']) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        $code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error || $code >= 400) {
            throw new RuntimeException("HTTP {$method} to {$url} failed: {$error} (HTTP {$code})");
        }

        return $response;
    }

    protected function formatHeaders(array $headers): array
    {
        $formatted = [];
        foreach ($headers as $key => $value) {
            $formatted[] = "{$key}: {$value}";
        }
        return $formatted;
    }
}
