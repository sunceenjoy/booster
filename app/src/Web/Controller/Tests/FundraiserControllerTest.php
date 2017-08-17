<?php

namespace Booster\Web\Controller\Tests;

use Booster\Core\Repository\FundraiserRepository;
use Booster\Web\Helper\RequestRateChecker;
use Booster\Web\Controller\FundraiserController;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;

class FundraiserControllerTest extends \PHPUnit_Framework_TestCase
{
    protected $container;
    
    /**
     * the setUpBeforeClass is called before the first test of the test case class is run and after the last test of the test case class is run, respectively.
     * @global array $c
     */
    public static function setUpBeforeClass()
    {
        global $c;
        require  __DIR__.'/../../../../bootstrap.php';
        require  __DIR__.'/../../../../resources/web/services.php';
        // Mock session
        $c['session'] = function () {
            return new Session(new MockArraySessionStorage());
        };
    }
    
    protected function setUp()
    {
        global $c;
        $this->container = clone $c;
        
        // Mock database
        $this->container['doctrine.entity_manager'] = function () {
            // Now, mock the repository so it returns the mock of the employee
            $mockRepository = $this->createMock(FundraiserRepository::class);
            $mockRepository->expects($this->any())
                ->method('addNew')
                ->willReturn($this->returnValue(true));

            $mockEntityManager = $this->createMock(EntityManager::class);
            $mockEntityManager->expects($this->any())
                ->method('getRepository')
                ->willReturn($mockRepository);
            return $mockEntityManager;
        };
        
        // Mock redis
        $this->container['request_rate_checker'] = function () {
            $mockRequestRateChecker = $this->createMock(RequestRateChecker::class);
            $mockRequestRateChecker->expects($this->any())
                ->method('ipRateCheck')
                ->willReturn($this->returnValue(true));
            return $mockRequestRateChecker;
        };
    }
    
    protected function tearDown()
    {
        parent::tearDown();
        $this->container = null;
    }
    
    public function testPostFundraiser()
    {
        $request = Request::createFromGlobals();
        $request->request->set('name', 'abc');
                                
        $controller = new FundraiserController($this->container);
        $response = $controller->postFundraiser($request);
        $this->assertEquals('/', $response->getTargetUrl());
    }
    
    public function testPostFundraiserInvalidName()
    {
        $request = Request::createFromGlobals();
        $request->request->set('name', '');
                                
        $controller = new FundraiserController($this->container);
        $response = $controller->postFundraiser($request);
        $this->assertEquals('/fundraiser-create', $response->getTargetUrl());
    }
}
