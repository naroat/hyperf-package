<?php

namespace Naroat\HyperfPackage;

use Hyperf\HttpMessage\Stream\SwooleStream;
use Naroat\HyperfPackage\Log\RequestId;
use Psr\Http\Message\ResponseInterface;

class Response extends \Hyperf\HttpServer\Response
{
    public function success(string $message = 'ok!', array $data = [], int $code = 200): ResponseInterface
    {
        return $this->format($code, $message, $data, true);
    }

    public function error(string $message = 'Server error!', array $data = [], int $code = 500): ResponseInterface
    {
        return $this->format($code, $message, $data, false);
    }

    protected function format(int $code, string $message, array $data, bool $status): ResponseInterface
    {
        $format = [
            'requestId' => RequestId::getRequestId(),
            'code' => $code,
            'status' => $status,    //bool
            'message' => $message,
            'data' => $data
        ];
        $format = json_encode($format);
        return parent::getResponse()
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream($format));
    }
}