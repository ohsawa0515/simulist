<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

ini_set('session.save_handler', 'memcached');
ini_set('session.save_path', 'PERSISTENT=pool ' . getenv('MEMCACHIER_SERVERS'));
ini_set('Memcached.sess_binary', 1);
ini_set('Memcached.sess_sasl_username', getenv('MEMCACHIER_USERNAME'));
ini_set('Memcached.sess_sasl_password', getenv('MEMCACHIER_PASSWORD'));

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
//Debug::enable();

require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel('heroku', false);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
