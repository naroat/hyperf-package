<?php

declare(strict_types=1);

namespace Taoran\HyperfPackage\Core;

class Response
{
    //返回成功
    public function success($data, $message = 'OK')
    {
        $message = $message ?? Code::getMessage(Code::SUCCESS);
        return $this->result(Code::SUCCESS, $message, $data);
    }

    //返回错误
    public function error($message = '', $code = 422, $data = [])
    {
        if (empty($message)) {
            return $this->result($code, Code::getMessage($code), $data);
        }
        return $this->result($code, $message, $data);
    }

    public function result($code, $message, $data)
    {
        return ['code' => $code, 'message' => $message, 'data' => $data];
    }

    public function jsonEncode($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
