<?php

namespace App\Controller\Api;

class DefaultController extends AbstractApiController
{
    public function indexAction()
    {
        $this->json(['success' => true]);
    }
}