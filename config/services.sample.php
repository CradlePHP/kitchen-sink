<?php //-->

return array (
    'sql-build' => new PDO('mysql:host=<DATABASE HOST>', '<DATABASE USER>', '<DATABASE PASS>'),
    'sql-main' => new PDO('mysql:host=<DATABASE HOST>;dbname=<DATABASE NAME>', '<DATABASE USER>', '<DATABASE PASS>'),
    'elastic-main' => Elasticsearch\ClientBuilder::create()->build(),
    'redis-main' => new Predis\Client([
        "scheme" => "tcp",
        "host" => "127.0.0.1",
        "port" => 6379
    ]),
    's3-main' => array(
        'region' => '<AWS REGION>',
        'token' => '<AWS TOKEN>',
        'secret' => '<AWS SECRET>',
        'bucket' => '<S3 BUCKET>',
        'host' => 'https://<<AWS REGION>.amazonaws.com'
    ),
    'mail-main' => array(
        'host' => 'smtp.gmail.com',
        'port' => '587',
        'type' => 'tls',
        'name' => 'Project Name',
        'user' => '<EMAIL ADDRESS>',
        'pass' => '<EMAIL PASSWORD>'
    ),
    /*
    'rabbitmq-main' => new PhpAmqpLib\Connection\AMQPLazyConnection(
        '127.0.0.1',
        5672,
        'guest',
        'guest'
    ),
    */
);
