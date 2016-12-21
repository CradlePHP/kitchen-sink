<?php //-->
return [
    //see: https://gist.github.com/cblanquera/3ff60b4c9afc92be1ac0a9d57afceb17#file-instructions-md
    'key' => '/tmp/travis_rsa',
    //see: https://github.com/visionmedia/deploy for config
    'servers' => [
        'app' => [
            'deploy' => false,
            'user' => 'root',
            'host' =>  '[server IP]',
            'repo' => 'git@github.com:[author]/vendor.git',
            'path' => '/path/to/public/on/live/server',
            'ref' => 'origin/[branch]'
        ],
        'mysql' => [
            'deploy' => false,
            'user' => 'root',
            'host' =>  '[server IP]'
        ],
    ]
];
