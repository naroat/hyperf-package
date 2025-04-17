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