<?php

namespace App\Tests\Stub;

use Phalcon\Http\Request;

class FakeRequest extends Request
{
    private array $jsonBody = [];
    private ?string $rawBody = null;

    public function setRawBody(string $body): void
    {
        $this->rawBody = $body;
    }

    public function getRawBody(): string
    {
        return $this->rawBody ?? '';
    }

    public function getJsonRawBody(bool $associative = false): \stdClass|array|bool
    {
        if (empty($this->jsonBody)) {
            // Prevent null from being returned
            return $associative ? [] : (object)[];
        }

        return $associative ? $this->jsonBody : (object) $this->jsonBody;
    }

    public function setJsonRawBody(array $json): void
    {
        $this->jsonBody = $json;
    }

    public function reset(): void
    {
        $this->jsonRawBody = null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
