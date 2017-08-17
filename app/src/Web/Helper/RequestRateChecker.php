<?php
namespace Booster\Web\Helper;

use Monolog\Logger;
use Predis\Client;
use Predis\PredisException;

class RequestRateChecker
{
    /** @var Client $redis */
    private $redis;

    /** @var Logger $logger */
    private $logger;

    /** @var int $requestInterval
     * A same ip only can request once in 10 seconds
     */
    private $requestInterval = 10;

    /**
     *  @var int $requestRateLimit
     *  In a short time($requestInterval * $requestRateLimit)
     *  a same ip can request $requestRateLimit times
     */
    private $requestRateLimit = 3;
    
    /** @var string $keyPrefix */
    private $keyPrefix;
    
    public function __construct(Logger $logger, Client $redis, $keyPrefix = '')
    {
        $this->redis = $redis;
        $this->keyPrefix = $keyPrefix;
        $this->logger = $logger;
    }
    
    /**
     * Check whether the ip reaches the request limit
     * @param BOOL true if the ip is allowed, false otherwise.
     */
    public function ipRateCheck($ipAddress)
    {
        $requestRateLimit = $this->requestRateLimit;
        $expireSeconds = $this->requestInterval * $requestRateLimit;
        $key = $this->keyPrefix.$ipAddress;
        
        try {
            $numRequests = $this->redis->incr($key);
            if ($numRequests === 1) {
                // Only set expire time for the first time.
                $this->redis->expire($key, $expireSeconds);
            }
            $this->logger->info('test', ['numRequest'=>$numRequests, 'requestRateLimit'=>$requestRateLimit]);
            if ($numRequests > $requestRateLimit) {
                if ($numRequests % 10 == 0) { 
                    // Write the Log every 10 requests.
                    $this->logger->info('User has reached the request rate limit!', [
                        'client_ip' => $ipAddress,
                        'rate_limit' => $requestRateLimit,
                        'actual_request_times' => $numRequests,
                    ]);
                }
                return true;
            }
            return false;
        } catch (PredisException $e) {
            $this->logger->err('Redis Communication Issue', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
            ]);
        }
        return false;
    }
    
    public function getRequestRateLimit()
    {
        return $this->requestRateLimit;
    }
}
