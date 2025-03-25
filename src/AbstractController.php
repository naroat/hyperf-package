<?php

namespace Naroat\HyperfPackage;

use Psr\Http\Message\ResponseInterface;

abstract class AbstractController
{
    public function __construct(
        readonly protected Request $request,
        readonly protected Response $response
    ) {
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function success(array $data, $code = 200): ResponseInterface
    {
        return $this->response->success(null, $data, $code);
    }

    public function error(string $message, $code = 500, $data = []): ResponseInterface
    {
        return $this->response->error($message, $data, $code);
    }
}