<?php

use Sabre\DAV;

date_default_timezone_set('UTC');

$pdo = new PDO('psql:dbname=sabredav;host=db', 'postgres', 'secret');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

require __DIR__.'/../vendor/autoload.php';

$authBackend = new DAV\Auth\Backend\PDO($pdo);
$locksBackend = new DAV\Locks\Backend\PDO($pdo);

$logger = new \Monolog\Logger('SabreDav');
$logger->pushHandler(new \Monolog\Handler\RotatingFileHandler(__DIR__.'/../data/logs/sabredav.log', 3, \Monolog\Logger::DEBUG, true, 0600));

$nodes = [
    new DAV\FS\Directory(__DIR__.'/../data/public'),
];

$server = new DAV\Server($nodes);
$server->setBaseUri('/');

$server->setLogger($logger);

$server->addPlugin(new DAV\Auth\Plugin($authBackend));
$server->addPlugin(new DAV\Browser\Plugin());
$server->addPlugin(new DAV\Locks\Plugin($locksBackend));

$server->start();

