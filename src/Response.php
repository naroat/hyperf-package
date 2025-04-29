<?php

namespace Naroat\HyperfPackage;

use Hyperf\HttpMessage\Stream\SwooleStream;
use Naroat\HyperfPackage\Log\RequestId;
use Psr\Http\Message\ResponseInterface;
use Swow\Psr7\Message\ResponsePlusInterface;

class Response extends \Hyperf\HttpServer\Response
{
    public function getResponse(): ResponsePlusInterface
    {
        return parent::getResponse();
    }

    public function success(string $message = 'ok!', array|object $data = [], int $code = 200): ResponseInterface
    {
        return $this->format($code, $message, $data, true);
    }

    public function error(string $message = 'Server error!', array|object $data = [], int $code = 500): ResponseInterface
    {
        return $this->format($code, $message, $data, false);
    }

    public function redirect(string $toUrl, int $status = 302, string $schema = 'http'): ResponseInterface
    {
        return $this->getResponse()->redirect($toUrl, $status, $schema);
    }

    public function download(string $filePath, string $name = ''): ResponseInterface
    {
        return $this->getResponse()->download($filePath, $name);
    }

    public function image(string $image, string $type = 'image/png'): ResponseInterface
    {
        return $this->getResponse()
            ->withAddedHeader('content-type', $type)
            ->withBody(new SwooleStream($image));
    }

    public function format(int $code, string $message, array|object $data, bool $status): ResponseInterface
    {
        $format = [
            'requestId' => RequestId::getRequestId(),
            'path' => container()->get(Request::class)->getUri()->getPath(),
            'code' => $code,
            'status' => $status,    //bool
            'message' => $message,
            'data' => $data
        ];
        $format = json_encode($format);
        return $this->getResponse()
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream($format));
    }
}