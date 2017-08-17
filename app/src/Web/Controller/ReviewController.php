<?php

namespace Booster\Web\Controller;

use Booster\Core\Paginator;
use Booster\Web\Helper\RequestRateChecker;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Review Controller
 *
 */
class ReviewController extends BaseController
{
    /** @var RequestRateChecker $requestRateChecker; */
    protected $requestRateChecker;
    
    public function __construct(\Booster\Core\Container $container)
    {
        parent::__construct($container);
        $this->requestRateChecker = $container['request_rate_checker'];
    }
    
    public function createReview(Request $request)
    {
        $fundraiser = $this->em->getRepository('Booster:FundraiserEntity')->getFundraiserById($request->get('f_id'));
        if (empty($fundraiser)) {
            $this->addFlash('danger', 'Invalid fundraiser Id!');
            return $this->redirect('/');
        }
        $params = [
            'fundraiser' => $fundraiser[0]
        ];
        return $this->render('web/reviews/create_review.html.twig', $params);
    }

    public function postReview(Request $request)
    {
        $fundraiserId = $request->request->get('f_id');
        $email = trim($request->request->get('email'));
        $rating = $request->request->get('rating');
        $name = trim($request->request->get('name'));
        $review = trim($request->request->get('review'));
        $ip = $request->getClientIp();
     
        // We don't have to check $fundraiserId as it has constraints in database.
        
        if (!in_array($rating, [1, 2, 3, 4, 5])) {
            $this->addFlash('danger', 'Valid rating value!');
            return $this->redirect('/review-create?f_id='.$fundraiserId);
        }
        
        if (empty($name)) {
            $this->addFlash('danger', 'Please enter your name!');
            return $this->redirect('/review-create?f_id='.$fundraiserId);
        }
                
        if (!\Swift_Validate::email($email)) {
            $this->addFlash('danger', 'Valid email format!');
            return $this->redirect('/review-create?f_id='.$fundraiserId);
        }
        
        if (empty($review)) {
            $this->addFlash('danger', 'Please enter the review!');
            return $this->redirect('/review-create?f_id='.$fundraiserId);
        }

        // Here we check the ip in redis to reduce potential impact to database
        if ($this->requestRateChecker->ipRateCheck($ip) === true) {
            $this->addFlash('danger', 'Request rate limit!');
            return $this->redirect('/review-create?f_id='.$fundraiserId);
        }
        
        try {
            $this->em->getRepository('Booster:ReviewEntity')->addNew($fundraiserId, $name, $email, $rating, $review, $ip);
        } catch (UniqueConstraintViolationException $e) {
            $this->addFlash('danger', 'This email address already has a review for this fundraiser!');
            return $this->redirect('/review-create?f_id='.$fundraiserId);
        }
        
        $this->addFlash('success', 'Review counted. Thank you!');
        
        return $this->redirect('/');
    }
    
    public function reviewList(Request $request)
    {
        $pageSize = 10;
        $currentPage = max(1, $request->query->get('page', 1));
        $fundraiserId = $request->query->get('f_id');
        
        /** @var Paginator $paginator */
        $paginator = new Paginator($this->em->getRepository('Booster:ReviewEntity')->getQuery($fundraiserId), $currentPage, $pageSize);
        return $this->render('web/reviews/list.html.twig', ['paginator' => $paginator, 'fundraiserId' => $fundraiserId]);
    }
}
