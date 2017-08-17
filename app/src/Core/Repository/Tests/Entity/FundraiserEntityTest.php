<?php

namespace Booster\Core\Repository\Entity\Tests;

use Booster\Core\Repository\Entity\FundraiserEntity;

class FundraiserEntityTest extends \PHPUnit_Framework_TestCase
{
    public function testGetterAndSetter()
    {
        $entity = new FundraiserEntity();
        $name = 'name';
        $entity->setName($name);
        $this->assertEquals($name, $entity->getName());
        
        $rating = 1;
        $entity->setRating($rating);
        $this->assertEquals($rating, $entity->getRating());
    }
}
