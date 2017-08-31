<?php
/** Core dependency injection */

$c['env'] = function ($c) {
    return new \Booster\Core\Environment(getenv('BOOSTER_ENV'));
};

$c['config'] = function ($c) {
    $configArray = parse_ini_file($c['config_dir'].'/'.($c['env']->isProd() ? 'prod' : 'dev').'/app.ini', true);
    return new \Booster\Core\Config($configArray['default']);
};

$c['log.main'] = function ($c) {
    $logger = new \Monolog\Logger('main');
    $level  = $c['config']['debug'] ? \Monolog\Logger::DEBUG : \Monolog\Logger::INFO;
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($c['log_dir'].'/main.log', $level));

    $level  = $c['config']['debug'] ? \Monolog\Logger::DEBUG : \Monolog\Logger::ERROR; // Will mail if error leve is above this setting.
    $logger->pushHandler(new \Monolog\Handler\SwiftMailerHandler($c['mailer'], new \Swift_Message('Module error need to repair'), $level));
    return $logger;
};


$c['router.request_context'] = function () {
    // Request context gets replaced later
    return new \Symfony\Component\Routing\RequestContext(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
};

$c['dispatcher'] = function ($c) {
     return new \Symfony\Component\EventDispatcher\EventDispatcher();
};

$c['request'] = function ($c) {
    return \Symfony\Component\HttpFoundation\Request::createFromGlobals();
};

$c['util.uri'] = function ($c) {
    return new \Booster\Core\Util\Uri($c);
};

$c['twig'] = function ($c) {
    $loader = new \Twig_Loader_Filesystem($c['res_dir'].'/templates');
    // Use cache when we are not in cli and debug is off
    $useCache = (PHP_SAPI !== 'cli' && !$c['config']['debug']);
    $twig = new \Twig_Environment(
        $loader,
        array(
            'cache' => ($useCache) ? $c['res_dir'].'/cache/twig' : false,
            'debug' => $c['config']['debug'],
        )
    );

    if ($c['config']['debug']) {
        // Add Twig's debug extension if debug is on
        $twig->addExtension(new \Twig_Extension_Debug());
    }

    $twig->addGlobal('session', $c['session']);
    $twig->addGlobal('request', $c['request']);
    $twig->addGlobal('uri', $c['util.uri']);
    return $twig;
};

$c['db.booster'] = function ($c) {
    $config = new \Doctrine\DBAL\Configuration();
    $connectionParams = array(
        'host'         => $c['config']['db.booster.host'],
        'dbname'       => $c['config']['db.booster.db'],
        'user'         => $c['config']['db.booster.user'],
        'password'     => $c['config']['db.booster.pass'],
        'port'         => $c['config']['db.booster.port'],
        'driver'       => 'pdo_mysql',
        'charset'      => 'utf8',
    );

    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
    return $conn;
};

$c['doctrine.entity_manager'] = function ($c) {
    $isDevMode = true;
    $config = \Doctrine\ORM\Tools\Setup::createConfiguration($isDevMode); // This can also set cache or other things
    $config->addEntityNamespace('Booster', 'Booster\\Core\\Repository\\Entity');
    $driver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver(new \Doctrine\Common\Annotations\AnnotationReader(), $c['res_dir'].'/logs');
    \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
    $config->setMetadataDriverImpl($driver);

    $entityManager = \Doctrine\ORM\EntityManager::create($c['db.booster'], $config);
    return $entityManager;
};

$c['mailer'] = function ($c) {
    // Using the native php mail function
    $transport = \Swift_MailTransport::newInstance();

    if ($c['config']['debug']) {
        // Debug mode will write all sent emails to cache dir
        $transport = \Swift_SpoolTransport::newInstance(
            new \Swift_FileSpool($c['res_dir'].'/cache/mailspool')
        );
    }

    return \Swift_Mailer::newInstance($transport);
};

$c['session'] = function () {
    $params = [
        'gc_maxlifetime' => 3600 * 24 * 10,
        'cookie_lifetime' => 3600 * 24 * 10
    ];
    return new Symfony\Component\HttpFoundation\Session\Session(new Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage($params));
};

$c['redis'] = function ($c) {
    if (isset($c['config']['redis.host'])) {
        return new \Predis\Client([
            'scheme' => 'tcp',
            'host'   => $c['config']['redis.host'],
            'port'   => $c['config']['redis.port'],
        ]);
    } else {
        return null;
    }
};
