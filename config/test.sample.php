<?php //-->

return array (
    'sql-main' => new PDO('mysql:host=127.0.0.1;dbname=testing_db', 'root', ''),
    'index-main' => Elasticsearch\ClientBuilder::create()->build(),
    'queue-main' => new PhpAmqpLib\Connection\AMQPLazyConnection(
        '127.0.0.1',
        5672,
        'guest',
        'guest'
    ),
    'cache-main' => new Predis\Client([
        "scheme" => "tcp",
        "host" => "127.0.0.1",
        "port" => 6379
    ]),
    'cdn-main' => array(
        'region' => 'ap-southeast-1',
        'token' => '<TOKEN>',
        'secret' => '<SECRET>',
        'bucket' => 'dealcha-dev',
        'host' => 'https://s3-ap-southeast-1.amazonaws.com'
    ),
    'mail-main' => array(
        'host' => 'smtp.gmail.com',
        'port' => '587',
        'type' => 'tls',
        'name' => 'Sample Project',
        'user' => '<EMAIL ADDRESS>',
        'pass' => '<EMAIL PASSWORD>'
    ),
    'captcha-main' => array(
        'token' => '<GOOGLE CAPTCHA TOKEN>',
        'secret' => '<GOOGLE CAPTCHA SECRET>'
    )
);
