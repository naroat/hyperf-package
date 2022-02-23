<?php

declare(strict_types=1);

namespace Taoran\HyperfPackage\Core;

use Taoran\HyperfPackage\Core\AbstractController;

class Code extends AbstractController
{
    /**
     * @Message("Request Error！")
     */
    const ERROR = 0;

    /**
     * @Message("Success")
     */
    const SUCCESS = 200;

    /**
     * @Message("请求参数错误")
     */
    const BAD_REQUEST = 400;

    /**
     * @Message("请先登录")
     */
    const UNAUTHENTICATED = 401;

    /**
     * @Message("验证错误")
     */
    const VALIDATE_ERROR = 402;

    /**
     * @Message("无权访问该资源")
     */
    const DISALLOW = 403;

    /**
     * @Message("请求资源不存在")
     */
    const RECORD_NOT_FOUND = 404;

    /**
     * @Message("Mthod Not Allowed")
     */
    const METHOD_NOT_ALLOWED = 405;

    /**
     * @Message("业务逻辑异常")
     */
    const SAVE_DATA_ERROR = 1001;

    /**
     * @Message("QueryException")
     */
    const QUERYEXCEPTION = 1002;

    /**
     * @Message("用户不存在")
     */
    const USER_NOT_FOUND = 1003;

    /**
     * @Message("用户名或者密码错误")
     */
    const INCORRECT_PASSWORD = 1004;

    /**
     * @Message("crsf token 验证不通过 ")
     */
    const TOKENMISMATCH = 1010;

    /**
     * @Message("请求参数错误")
     */
    const PARAMS_ERROR = 1005;

    /**
     * @Message("用户已禁用")
     */
    const USER_DISABLE = 1007;




    public static $messages = [
        self::ERROR => '未知错误',
        self::SUCCESS => 'ok',
        self::BAD_REQUEST => '请求参数错误',
        self::UNAUTHENTICATED => '请先登录！!',
        self::VALIDATE_ERROR => '验证错误！',
        self::DISALLOW => '无权限访问！',
        self::RECORD_NOT_FOUND => '请求资源不存在！',
        self::METHOD_NOT_ALLOWED => '请求不允许！',
        self::SAVE_DATA_ERROR => '业务逻辑异常！',
        self::QUERYEXCEPTION => '执行异常！',
        self::USER_NOT_FOUND => '用户不存在！',
        self::INCORRECT_PASSWORD => '用户密码不正确！',
        self::TOKENMISMATCH => 'token 验证不通过',
        self::PARAMS_ERROR => '请求参数错误！',
        self::USER_DISABLE => '用户已禁用！',

    ];

    public static function getMessage($code)
    {
        return self::$messages[$code] ?? 'NOT CODE!';
    }
}
