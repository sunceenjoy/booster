<?php

namespace Booster\Web\Helper\Tests;

use Booster\Web\Helper\RequestRateChecker;
use Monolog\Logger;

class RequestRateCheckerTest extends \PHPUnit_Framework_TestCase
{

    public function testIpRateCheck()
    {
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();
        $redis  = $this->getMockBuilder(MockRedis::class)->getMock();
        $requestRateChecker = new RequestRateChecker($logger, $redis);
        $requestLimitRate = $requestRateChecker->getRequestRateLimit();
        $currentRequestTimes = 5;
        $redis->expects($this->any())->method('incr')->will($this->returnValue($currentRequestTimes));
        $this->assertEquals($requestLimitRate >= $currentRequestTimes, !$requestRateChecker->ipRateCheck('192.168.1.11'));
    }
}
