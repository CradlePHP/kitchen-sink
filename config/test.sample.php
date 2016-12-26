<?php //-->

return array(
    'sql-build' => new PDO('mysql:host=127.0.0.1', 'root', ''),
    'sql-main' => new PDO('mysql:host=127.0.0.1;dbname=testing_db', 'root', ''),
    'elastic-main' => Elasticsearch\ClientBuilder::create()->build(),
    'redis-main' => new Predis\Client([
        "scheme" => "tcp",
        "host" => "127.0.0.1",
        "port" => 6379
    ])
);
