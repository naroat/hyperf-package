<?php


namespace Naroat\HyperfPackage\Traits;


trait CallbackTrait
{
    /**
     * callbakc
     *
     * @param $call_user_func
     * @param ...$data
     * @return false|void
     */
    public function callback($call_user_func, ...$data)
    {
        if (!is_callable($call_user_func)) {
            return false;
        }
        $args = func_get_args();
        unset($args[0]);
        call_user_func_array($call_user_func, array_values($args));
    }
}