<?php

namespace Booster\Core\Util;

use Booster\Core\Container;
use Symfony\Component\HttpFoundation\Request;

class Uri
{
    /** @var Container $c */
    private $c;
    
    /** @var Request $request */
    private $request;

    public function __construct(Container $c)
    {
        $this->c = $c;
        $this->request = $c['request'];
    }

    public function getFullUrl()
    {
        return $this->request->getUri();
    }

    public function getHost()
    {
        return $this->request->getSchemeAndHttpHost();
    }

    public function getControllerName()
    {
        $key = $this->request->attributes->get('_route') == 'magic' ? 'controller' : '_route';
        return $this->request->attributes->get($key);
    }

    public function getActionName()
    {
        return $this->request->attributes->get('action');
    }
    
    /**
     * Create a link based on current url, then replace/add the given params.
     * @param array $params
     * @return string
     */
    public function replaceUrl($params = array())
    {
        $url = $this->request->getSchemeAndHttpHost().$this->request->getBaseUrl().$this->request->getPathInfo();
        $array = $this->request->query->all();
        unset($array[$this->getControllerName().'/'.$this->getActionName()]);
        $params = array_merge($array, $params);
        if (!empty($params)) {
            $url = $url.'?'.http_build_query($params);
        }
        return $url;
    }
}
