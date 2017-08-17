#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/bootstrap.php';


$application = new Application();

// Migrations Commands
$configure = new \Doctrine\DBAL\Migrations\Configuration\Configuration($c['db.booster']);
$configure->setMigrationsNamespace('BoosterMigration');
$configure->setMigrationsDirectory(DOCROOT.'/database-migration/versions');
$configure->registerMigrationsFromDirectory($configure->getMigrationsDirectory());
$configure->setMigrationsTableName('migration_versions');
$symfony_output = new \Symfony\Component\Console\Output\ConsoleOutput();
$symfony_output->setDecorated(true);
$configure->setOutputWriter(new \Doctrine\DBAL\Migrations\OutputWriter(function ($msg) use ($symfony_output) {
    $symfony_output->writeln($msg);
}));
$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($c['db.booster']),
    'dialog' => new \Symfony\Component\Console\Helper\DialogHelper(),
    'configuration' => new \Doctrine\DBAL\Migrations\Tools\Console\Helper\ConfigurationHelper($c['db.booster'], $configure),
));
$application->setHelperSet($helperSet);

$application->addCommands(array(
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand()
));

$application->run();
