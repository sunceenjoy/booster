<?php

namespace Booster\Core\Repository\Entity\Tests;

use Booster\Core\Repository\Entity\ReviewEntity;

class ReviewEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testGetterAndSetter()
    {
        $entity = new ReviewEntity();
        $name = 'name';
        $entity->setName($name);
        $this->assertEquals($name, $entity->getName());
        
        $fundraiserId = 1;
        $entity->setFundraiserId($fundraiserId);
        $this->assertEquals($fundraiserId, $entity->getFundraiserId());
        
        $email = 'email@email';
        $entity->setEmail($email);
        $this->assertEquals($email, $entity->getEmail());
        
        $rating = 1;
        $entity->setRating($rating);
        $this->assertEquals($rating, $entity->getRating());
        
        $review = 'review';
        $entity->setReview($review);
        $this->assertEquals($review, $entity->getReview());
        
        $ip = '192.168.1.1';
        $entity->setIp($ip);
        $this->assertEquals($ip, $entity->getIp());
        
        $createdAt = new \DateTime();
        $entity->setCreatedAt($createdAt);
        $this->assertEquals($createdAt, $entity->getCreatedAt());
    }
}
