<?php

if (! function_exists('orm_sql')) {
    /**
     * orm to sql
     *
     * @param $model
     * @return string
     */
    function orm_sql($model)
    {
        $bindings = $model->getBindings();
        $sql = str_replace('?', '%s', $model->toSql());
        foreach ($bindings as $key => $val) {
            if (is_string($val)) {
                $bindings[$key] = "'" . $val . "'";
            }
        }
        $tosql = sprintf($sql, ...$bindings);
        return $tosql;
    }
}

if (!function_exists('set_save_data')) {
    /**
     * 设置保存数据（主要过滤实体，防止xss）
     *
     * @param array $data
     * @return array
     */
    function set_save_data(array $data)
    {
        foreach ($data as $key => $v) {
            if (is_string($v)) {
                //转换html内容
                $data[$key] = htmlspecialchars($v, ENT_QUOTES);
            } else {
                $data[$key] = $v;
            }
        }
        return $data;
    }
}

if (!function_exists('get_client_ip')) {
    /**
     * 获取客户端ip
     *
     * @param $request
     * @return mixed|string
     */
    function get_client_ip($request) {
        return $request->getHeaderLine('X-Forwarded-For')
            ?: $request->getHeaderLine('X-Real-IP')
                ?: ($request->getServerParams()['remote_addr'] ?? '')
                    ?: '127.0.0.1';
    }
}

