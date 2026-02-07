<?php

declare(strict_types=1);

use Naroat\Ivory\Random\Random;

if (! function_exists('orm_sql')) {
    /**
     * orm to sql.
     *
     * @param mixed $model
     */
    function orm_sql($model): string
    {
        $bindings = $model->getBindings();
        $sql = str_replace('?', '%s', $model->toSql());
        foreach ($bindings as $key => $val) {
            if (is_string($val)) {
                $bindings[$key] = "'" . $val . "'";
            }
        }
        return sprintf($sql, ...$bindings);
    }
}

if (! function_exists('set_save_data')) {
    /**
     * 设置保存数据（主要过滤实体，防止xss）.
     */
    function set_save_data(array $data): array
    {
        foreach ($data as $key => $v) {
            if (is_string($v)) {
                // 转换html内容
                $data[$key] = htmlspecialchars($v, ENT_QUOTES);
            } else {
                $data[$key] = $v;
            }
        }
        return $data;
    }
}

if (! function_exists('get_client_ip')) {
    /**
     * 获取客户端ip.
     *
     * @param mixed $request
     * @return mixed|string
     */
    function get_client_ip($request)
    {
        return $request->getHeaderLine('X-Forwarded-For')
            ?: $request->getHeaderLine('X-Real-IP')
                ?: ($request->getServerParams()['remote_addr'] ?? '')
                    ?: '127.0.0.1';
    }
}

if (! function_exists('get_scheme')) {
    /**
     * 获取协议架构.
     *
     * @param mixed $request
     */
    function get_scheme($request): string
    {
        if (isset($request->getHeader('X-scheme')[0])) {
            return $request->getHeader('X-scheme')[0] . '://';
        }
        return 'http://';
    }
}

if (! function_exists('create_password')) {
    /**
     * 创建一个密码
     *
     * @param string $password 密码
     * @param string $salt 扰乱码
     */
    function create_password(string $password, string $salt): string
    {
        return md5(sha1($password . $salt));
    }
}

if (! function_exists('eq_password')) {
    /**
     * 判断密码是否相等.
     *
     * @param string $encrypted_password 已加密密码
     * @param string $password 要比对的密码
     * @param string $salt 扰乱码
     * @return bool
     */
    function eq_password(string $encrypted_password, string $password, string $salt): bool
    {
        if ($encrypted_password != md5(sha1($password . $salt))) {
            return false;
        }
        return true;
    }
}
