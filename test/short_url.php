<?php
declare(strict_types=1);
ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');

error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));
! defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', SWOOLE_HOOK_ALL);

Swoole\Runtime::enableCoroutine(true);

require BASE_PATH . '/vendor/autoload.php';

$shortUrl = new \Taoran\HyperfPackage\ShortUrl\ShortUrl();
$url = 'https://www.baidu.com/s?ie=utf-8&f=8&rsv_bp=1&rsv_idx=1&tn=baidu&wd=php&fenlei=256&rsv_pq=d50968f50000bb0d&rsv_t=0dc39rU%2FodmIN8biFjUgim%2F10%2BfDJJprm%2FHWCnn%2BqkbNUPyVYzFR5nrIWbU&rqlang=cn&rsv_dl=tb';
$final_url = $shortUrl->gen($url);
var_dump($final_url);