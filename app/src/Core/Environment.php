<?php

namespace Booster\Core;

class Environment
{
    /** @var String $env */
    private $env;

    private $env_dev = array(
        'booster_dev'
    );

    private $env_prod = array(
        'booster_prod'
    );

    public function __construct($env)
    {
        if (!in_array($env, $this->env_dev) && !in_array($env, $this->env_prod)) {
            $env = 'booster_dev';
        }
        $this->env = $env;
    }

    public function getEnv()
    {
        return $this->env;
    }

    public function isDev()
    {
        return in_array($this->env, $this->env_dev);
    }

    public function isProd()
    {
        return in_array($this->env, $this->env_prod);
    }
}
