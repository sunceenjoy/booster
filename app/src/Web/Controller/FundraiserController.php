<?php

namespace Booster\Web\Controller;

use Booster\Core\Container;
use Booster\Core\Paginator;
use Booster\Web\Helper\RequestRateChecker;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fundraiser Controller
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
    
    /**
     * Fundraiser list page
     * @Route("/")
     * @param Request $request
     * @return Response
     */
    public function fundraiserList(Request $request)
    {
        $pageSize = 10;
        $currentPage = max(1, $request->query->get('page', 1));
        
        /** @var Paginator $paginator */
        $paginator = new Paginator($this->em->getRepository('Booster:FundraiserEntity')->getQuery(), $currentPage, $pageSize);
        return $this->render('web/fundraisers/list.html.twig', ['paginator' => $paginator]);
    }

    /**
     * Create fundraiser page
     * @Route("/fundraiser-create")
     * @param Request $request
     * @return Response
     */
    public function createFundraiser()
    {
        return $this->render('web/fundraisers/create_fundraiser.html.twig');
    }
    
    /**
     * Process fundraiser post page
     * @Route("/fundraiser-post")
     * @param Request $request
     * @return Response
     */
    public function postFundraiser(Request $request)
    {
        $name = trim($request->request->get('name'));
        $ip = $request->getClientIp();
        
        if (empty($name)) {
            $this->flashMessage('danger', 'Please enter a name!');
            return $this->redirect('/fundraiser-create');
        }
        
        // Here we check the ip in redis to reduce potential impact to database
        if ($this->requestRateChecker->ipRateCheck($ip) === true) {
            $this->flashMessage('danger', 'Request rate limit!');
            return $this->redirect('/fundraiser-create');
        }
        
        try {
            $this->em->getRepository('Booster:FundraiserEntity')->addNew($name);
        } catch (UniqueConstraintViolationException $e) {
            $this->flashMessage('danger', 'This fundraiser already exists!');
            return $this->redirect('/fundraiser-create');
        }
        $this->flashMessage('success', 'New Fundraiser Added.!');
        return $this->redirect('/');
    }
}
