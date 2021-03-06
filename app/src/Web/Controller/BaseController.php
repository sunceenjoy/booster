<?php

namespace Booster\Web\Controller;

use Booster\Core\Container;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

abstract class BaseController
{
    /** @var Response $response */
    protected $response = null;

    /** @var Connection $db */
    protected $db = null;

    /** @var EntityManager $em */
    protected $em = null;

    /** @var Logger $logger */
    protected $logger = null;
    
    /** @var Session $session */
    protected $session;
    
    public function __construct(Container $container)
    {
        $this->c = $this->container = $container;
        $this->db = $this->container['db.booster'];
        $this->em = $this->container['doctrine.entity_manager'];
        $this->logger = $this->container['log.main'];
        $this->session  = $this->container['session'];
    }


    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Renders a view via twig.
     *
     * @param string   $view       The view name
     * @param array    $parameters An array of parameters to pass to the view
     * @param Response $response   A response instance
     *
     * @return Response A Response instance
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($this->container['twig']->render($view, $parameters));

        return $response;
    }
    
    /**
     * By design, flash messages are meant to be used exactly once
     * @param string $type
     * @param string $message
     */
    public function flashMessage($type, $message)
    {
        $this->session->getFlashBag()->add($type, $message);
    }
    
    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string $url    The URL to redirect to
     * @param int    $status The status code to use for the Response
     *
     * @return RedirectResponse
     */
    protected function redirect($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }
}
