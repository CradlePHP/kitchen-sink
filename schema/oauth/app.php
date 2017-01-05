<?php //-->
return [
    'singular' => 'App',
    'plural' => 'Apps',
    'primary' => 'app_id',
    'active' => 'app_active',
    'created' => 'app_created',
    'updated' => 'app_updated',
    'relations' => [
        'profile' => [
            'primary' => 'profile_id',
            'many' => false
        ]
    ],
    'fields' => [
        'app_name' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'required' => true,
                'index' => true
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Name',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'My Custom App',
                ]
            ],
            'validation' => [
                [
                    'method' => 'required',
                    'message' => 'Name is required'
                ]
            ],
            'list' => [
                'label' => 'Name'
            ],
            'detail' => [
                'label' => 'Name'
            ],
            'test' => [
                'pass' => 'Foobar Title',
                'fail' => ''
            ]
        ],
        'app_domain' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'default' => '*'
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Domain',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => '*.acme.com',
                ]
            ],
            'validation' => [
                [
                    'method' => 'regexp',
                    'message' => 'Invalid host name',
                    'parameters' => '#^[a-zA-Z0-9\-_\*\.]+\.[a-zA-Z]{2,10}$#i'
                ]
            ],
            'test' => [
                'pass' => '*.acme.com',
                'fail' => 'not a good host'
            ]
        ],
        'app_website' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Website',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'http://acme.com',
                ]
            ],
            'validation' => [
                [
                    'method' => 'regexp',
                    'message' => 'Invalid URL',
                    'parameters' => '/^(http|https):\/\/([A-Z0-9][A-Z0'.
                    '-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/i'
                ]
            ],
            'test' => [
                'pass' => 'http://acme.com',
                'fail' => 'acme.com'
            ]
        ],
        'app_token' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'encoding' => 'token',
                'index' => true
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'list' => [
                'label' => 'Token'
            ],
            'detail' => [
                'label' => 'Token'
            ]
        ],
        'app_secret' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'encoding' => 'token',
                'index' => true
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'list' => [
                'label' => 'Secret'
            ],
            'detail' => [
                'label' => 'Secret'
            ]
        ],
        'app_permissions' => [
            'sql' => [
                'type' => 'json'
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Permissions',
                'type' => 'checkboxes',
                'options' => [
                    'public_profile' => 'Access to Viewing public profiles',
                    'personal_profile' => 'Updating your own profile',
                    'user_profile' => 'Updating other profiles',
                ]
            ]
        ],
        'app_type' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'index' => true
            ],
            'elastic' => [
                'type' => 'string'
            ]
        ],
        'app_flag' => [
            'sql' => [
                'type' => 'int',
                'length' => 1,
                'default' => 0,
                'attribute' => 'unsigned'
            ],
            'elastic' => [
                'type' => 'integer'
            ]
        ]
    ],
    'fixtures' => [
        [
            'app_id' => 1,
            'profile_id' => 1,
            'app_name' => 'Cradle App 1',
            'app_domain' => '*.cradlephp.github.io',
            'app_website' => 'http://cradlephp.github.io',
            'app_token' => '87d02468a934cb717cc15fe48a244f43',
            'app_secret' => '21e21453cad34a94b76fb840c1eeba8a',
            'app_permissions' => json_encode([
                'public_profile',
                'personal_profile',
                'user_profile',
                'admin_profile'
            ]),
            'app_type' => 'admin',
            'app_created' => date('Y-m-d h:i:s'),
            'app_updated' => date('Y-m-d h:i:s')
        ]
    ]
];
