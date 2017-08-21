<?php

namespace Booster\Web\Controller\Tests;

use Booster\Core\Repository\ReviewRepository;
use Booster\Web\Helper\RequestRateChecker;
use Booster\Web\Controller\ReviewController;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class ReviewControllerTest extends \PHPUnit_Framework_TestCase
{
    protected $container;
    
    public function setUp()
    {
        global $c;
        $this->container = clone $c;
        
        // Mock database
        $this->container['doctrine.entity_manager'] = function () {
            // Now, mock the repository so it returns the mock repository
            $mockRepository = $this->createMock(ReviewRepository::class);
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
    
    public function testPostReview()
    {
        $request = Request::createFromGlobals();
        $request->request->set('name', 'Jack');
        $request->request->set('email', 'jack@gmail.com');
        $request->request->set('rating', 4);
        $request->request->set('f_id', 1);
        $request->request->set('review', 'My review');
                
        
        $controller = new ReviewController($this->container);
        $response = $controller->postReview($request);
        $this->assertEquals('/', $response->getTargetUrl());
    }
    
    public function testPostReviewNoName()
    {
        $f_id = 1;
        $request = Request::createFromGlobals();
        //$request->request->set('name', 'Jack');
        $request->request->set('email', 'jack@gmail.com');
        $request->request->set('rating', 4);
        $request->request->set('f_id', $f_id);
        $request->request->set('review', 'My review');
                
        
        $controller = new ReviewController($this->container);
        $response = $controller->postReview($request);
        $this->assertEquals('/review-create?f_id='.$f_id, $response->getTargetUrl());
    }
    
    public function testPostReviewInvalidEmail()
    {
        $f_id = 1;
        $request = Request::createFromGlobals();
        $request->request->set('name', 'Jack');
        $request->request->set('email', 'jackgmail.com');
        $request->request->set('rating', 4);
        $request->request->set('f_id', $f_id);
        $request->request->set('review', 'My review');
                
        
        $controller = new ReviewController($this->container);
        $response = $controller->postReview($request);
        $this->assertEquals('/review-create?f_id='.$f_id, $response->getTargetUrl());
    }
    
    public function testPostReviewNoReview()
    {
        $f_id = 1;
        $request = Request::createFromGlobals();
        $request->request->set('name', 'Jack');
        $request->request->set('email', 'jack@gmail.com');
        $request->request->set('rating', 4);
        $request->request->set('f_id', $f_id);
        //$request->request->set('review', 'My review');
                
        
        $controller = new ReviewController($this->container);
        $response = $controller->postReview($request);
        $this->assertEquals('/review-create?f_id='.$f_id, $response->getTargetUrl());
    }
    
    public function testPostReviewInvalidInvalidRating()
    {
        $f_id = 1;
        $request = Request::createFromGlobals();
        $request->request->set('name', 'Jack');
        $request->request->set('email', 'jack@gmail.com');
        $request->request->set('rating', 14);
        $request->request->set('f_id', $f_id);
        $request->request->set('review', 'My review');
                
        
        $controller = new ReviewController($this->container);
        $response = $controller->postReview($request);
        $this->assertEquals('/review-create?f_id='.$f_id, $response->getTargetUrl());
    }
    
    public function testPostReviewInvalidDuplicatedEmail()
    {
        $f_id = 1;
        $request = Request::createFromGlobals();
        $request->request->set('name', 'Jack');
        $request->request->set('email', 'jack@gmail.com');
        $request->request->set('rating', 2);
        $request->request->set('f_id', $f_id);
        $request->request->set('review', 'My review');
                
        $this->container['doctrine.entity_manager'] = function () {
            // Now, mock the repository so it returns the mock repository
            $mockRepository = $this->createMock(ReviewRepository::class);
            $mockRepository->expects($this->any())
                ->method('addNew')
                ->will($this->throwException(new UniqueConstraintViolationException('Constraint', new PDOException(new \PDOException()))));

            $mockEntityManager = $this->createMock(EntityManager::class);
            $mockEntityManager->expects($this->any())
                ->method('getRepository')
                ->willReturn($mockRepository);
            return $mockEntityManager;
        };
        
        $controller = new ReviewController($this->container);
        $response = $controller->postReview($request);
        $this->assertEquals('/review-create?f_id='.$f_id, $response->getTargetUrl());
    }
}
