<?php

use Sabre\DAV;

// The autoloader
require __DIR__ . '/../vendor/autoload.php';

$logger = new \Monolog\Logger('SabreDav');
$logger->pushHandler(new \Monolog\Handler\RotatingFileHandler(__DIR__.'/../data/logs/sabredav.log', 3, \Monolog\Logger::DEBUG, true, 0600));

// Now we're creating a whole bunch of objects
$rootDirectory = new DAV\FS\Directory(__DIR__ . '/../data/public');

// The server object is responsible for making sense out of the WebDAV protocol
$server = new DAV\Server($rootDirectory);

$server->setLogger($logger);

// If your server is not on your webroot, make sure the following line has the
// correct information
$server->setBaseUri('/');

// The lock manager is reponsible for making sure users don't overwrite
// each others changes.
$lockBackend = new DAV\Locks\Backend\File('/tmp/davlocks');
$lockPlugin = new DAV\Locks\Plugin($lockBackend);
$server->addPlugin($lockPlugin);

$authBackend = new DAV\Auth\Backend\File(__DIR__ . '/../data/htdigest');
$authBackend->setRealm('SabreDAV');
$authPlugin = new DAV\Auth\Plugin($authBackend);
$server->addPlugin($authPlugin);

// This ensures that we get a pretty index in the browser, but it is
// optional.
$server->addPlugin(new DAV\Browser\Plugin());

// All we need to do now, is to fire up the server
$server->exec();

