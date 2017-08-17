<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add(
    'portal',
    new Route(
        '/',
        array('_controller' => 'Booster\Web\Controller\FundraiserController::index',)
    )
);

$collection->add(
    'Fundraiser Add',
    new Route(
        '/fundraiser-create',
        array('_controller' => 'Booster\Web\Controller\FundraiserController::createFundraiser',)
    )
);

$collection->add(
    'Fundraiser Post',
    new Route(
        '/fundraiser-post',
        array('_controller' => 'Booster\Web\Controller\FundraiserController::postFundraiser',)
    )
);

$collection->add(
    'Review Create',
    new Route(
        '/review-create',
        array('_controller' => 'Booster\Web\Controller\ReviewController::createReview',)
    )
);

$collection->add(
    'Review Post',
    new Route(
        '/review-post',
        array('_controller' => 'Booster\Web\Controller\ReviewController::postReview',)
    )
);

$collection->add(
    'Review List',
    new Route(
        '/review-list',
        array('_controller' => 'Booster\Web\Controller\ReviewController::reviewList',)
    )
);

return $collection;
