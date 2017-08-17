<?php

namespace Booster\Web\Controller;

use Booster\Core\Container;
use Booster\Core\Paginator;
use Booster\Web\Helper\RequestRateChecker;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Fundraiser Controller
 *
 */
class FundraiserController extends BaseController
{
    /** @var RequestRateChecker $requestRateChecker; */
    protected $requestRateChecker;
    
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->requestRateChecker = $container['request_rate_checker'];
    }
    
    public function index(Request $request)
    {
        $pageSize = 10;
        $currentPage = $request->query->get('page', 1);
        
        /** @var Paginator $paginator */
        $paginator = new Paginator($this->em->getRepository('Booster:FundraiserEntity')->getQuery(), $currentPage, $pageSize);
        return $this->render('web/fundraisers/list.html.twig', ['paginator' => $paginator]);
    }

    public function createFundraiser()
    {
        return $this->render('web/fundraisers/create_fundraiser.html.twig');
    }
    
    public function postFundraiser(Request $request)
    {
        $name = trim($request->request->get('name'));
        $ip = $request->getClientIp();
        
        if (empty($name)) {
            $this->addFlash('danger', 'Please enter a name!');
            return $this->redirect('/fundraiser-create');
        }
        
        // Here we check the ip in redis to reduce potential impact to database
        if ($this->requestRateChecker->ipRateCheck($ip) === true) {
            $this->addFlash('danger', 'Request rate limit!');
            return $this->redirect('/fundraiser-create');
        }
        
        try {
            $this->em->getRepository('Booster:FundraiserEntity')->addNew($name);
        } catch (UniqueConstraintViolationException $e) {
            $this->addFlash('danger', 'This fundraiser already exists!');
            return $this->redirect('/fundraiser-create');
        }
        $this->addFlash('success', 'New Fundraiser Added.!');
        return $this->redirect('/');
    }
}
