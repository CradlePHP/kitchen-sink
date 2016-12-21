<?php //-->

return array(
    'sql-main' => new PDO('mysql:host=127.0.0.1;dbname=testing_db', 'root', ''),
    'index-main' => Elasticsearch\ClientBuilder::create()->build(),
    'cache-main' => new Predis\Client([
        "scheme" => "tcp",
        "host" => "127.0.0.1",
        "port" => 6379
    ]),
    /*
    'queue-main' => new PhpAmqpLib\Connection\AMQPLazyConnection(
        '127.0.0.1',
        5672,
        'guest',
        'guest'
    ),
    'cdn-main' => array(
        'region' => 'ap-southeast-1',
        'token' => '<AWS TOKEN>',
        'secret' => '<AWS SECRET>',
        'bucket' => '<S3 BUCKET>',
        'host' => 'https://s3-ap-southeast-1.amazonaws.com'
    ),
    'mail-main' => array(
        'host' => 'smtp.gmail.com',
        'port' => '587',
        'type' => 'tls',
        'name' => 'Project Name',
        'user' => '<EMAIL ADDRESS>',
        'pass' => '<EMAIL PASSWORD>'
    )*/
);
