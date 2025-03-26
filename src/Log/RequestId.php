<?php

namespace Naroat\HyperfPackage\Log;

use Hyperf\Context\ApplicationContext;
use Hyperf\Context\Context;
use Hyperf\Coroutine\Coroutine;
use Hyperf\Snowflake\IdGeneratorInterface;

class RequestId
{
    public const REQUEST_ID = 'log.request.id';

    private static string $type = 'uuid';

    public static function getRequestId()
    {
        if (Coroutine::inCoroutine()) {
            $requestId = Context::get(self::REQUEST_ID);
            if (is_null($requestId)) {
                $requestId = Context::get(self::REQUEST_ID, null, Coroutine::parentId());
                if (!is_null($requestId)) {
                    Context::get(self::REQUEST_ID, $requestId);
                }
            }
            if (is_null($requestId)) {
                $requestId = self::getUniqueId();
            }
        } else {
            $requestId = self::getUniqueId();
        }
        return $requestId;
    }

    protected static function getUniqueId()
    {
        $generator = ApplicationContext::getContainer()->get(IdGeneratorInterface::class);
        return strval(Context::set(self::REQUEST_ID, $generator->generate()));
    }
}