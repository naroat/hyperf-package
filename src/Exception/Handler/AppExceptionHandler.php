<?php

namespace Naroat\HyperfPackage\Exception\Handler;

use Hyperf\Di\Annotation\Inject;
use Naroat\HyperfPackage\Constants\ErrorCode;
use Hyperf\Codec\Json;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Logger\Logger;
use Hyperf\Logger\LoggerFactory;
use Naroat\HyperfPackage\Log\RequestId;
use Naroat\HyperfPackage\Request;
use Naroat\HyperfPackage\Response;
use Psr\Http\Message\ResponseInterface;
use Hyperf\Context\ApplicationContext;
/**
 * Class AppExceptionHandler.
 */
class AppExceptionHandler extends ExceptionHandler
{
    protected Logger $logger;

    protected StdoutLoggerInterface $console;

    #[Inject]
    protected Response $baseResponse;

    public function __construct()
    {
        $this->console = console();
        $this->logger = ApplicationContext::getContainer()->get(LoggerFactory::class)->get();
    }

    public function handle(\Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $this->console->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        $this->console->error($throwable->getTraceAsString());
        $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        return $this->baseResponse->format(ErrorCode::SERVER_ERROR, $throwable->getMessage(), [], false);
    }

    public function isValid(\Throwable $throwable): bool
    {
        return true;
    }
}
