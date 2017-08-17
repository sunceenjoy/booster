<?php

namespace Booster\Cure\Util\Tests;

use Booster\Core\Util\Uri;

class ReviewControllerTest extends \PHPUnit_Framework_TestCase
{
    protected $container;
    
    public function setUp()
    {
        global $c;
        $this->container = clone $c;
    }
    
    protected function tearDown()
    {
        parent::tearDown();
        $this->container = null;
    }
    
    public function testReplaceUrl()
    {
        $this->container['request']->query->set('page', 1);
        $util = new Uri($this->container);
        $this->assertEquals('http://:/?page=10', $util->replaceUrl(['page' => 10]));
    }
}
