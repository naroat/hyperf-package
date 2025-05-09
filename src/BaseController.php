<?php

namespace Naroat\HyperfPackage;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

abstract class BaseController
{
    public function __construct(
        readonly protected ContainerInterface $container,
        readonly protected Request $request,
        readonly protected Response $response,
        readonly protected Verify $verify
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

    public function success(array|object $data = [], $code = 200): ResponseInterface
    {
        return $this->response->success('ok', $data, $code);
    }

    public function error(string $message, array|object $data = [], $code = 500): ResponseInterface
    {
        return $this->response->error($message, $data, $code);
    }
}