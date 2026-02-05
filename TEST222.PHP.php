<?php


use Utils\Redis\RedisHandler;

if ( !@include_once 'lib/Bootstrap.php' ) {
    die( "Location: configMissing" );
}

Bootstrap::start();

$redisHandler = new RedisHandler;

$redis = $redisHandler->getConnection();

$redis->get('test');

var_dump(spl_object_hash($redis));

$redis2 = $redisHandler->getConnection();

var_dump(spl_object_hash($redis2));