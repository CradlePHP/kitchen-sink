<?php //-->
return [
    'singular' => 'Session',
    'plural' => 'Sessions',
    'primary' => 'session_id',
    'active' => 'session_active',
    'created' => 'session_created',
    'updated' => 'session_updated',
    'relations' => [
        'oauth/app' => [
            'primary' => 'app_id',
            'many' => false
        ],
        'oauth/auth' => [
            'primary' => 'auth_id',
            'many' => false
        ]
    ],
    'fields' => [
        'session_token' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'encoding' => 'token',
                'index' => true
            ],
            'elastic' => [
                'type' => 'string'
            ]
        ],
        'session_secret' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'encoding' => 'token',
                'index' => true
            ],
            'elastic' => [
                'type' => 'string'
            ]
        ],
        'session_permissions' => [
            'sql' => [
                'type' => 'json'
            ],
            'elastic' => [
                'type' => 'string'
            ]
        ],
        'session_status' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'default' => 'PENDING',
                'index' => true
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'list' => [
                'label' => 'Status',
                'searchable' => true,
                'sortable' => true,
                'filterable' => true,
                'format' => 'upper'
            ]
        ],
        'session_type' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255
            ],
            'elastic' => [
                'type' => 'string'
            ]
        ],
        'session_flag' => [
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
            'session_id' => 1,
            'auth_id' => 1,
            'app_id' => 1,
            'session_token' => '8323fd20795498fb77deb36a85fd3490',
            'session_secret' => '21e21453cad34a94b76fb840c1eeba8a',
            'session_permissions' => json_encode([
                'public_profile',
                'user_profile'
            ]),
            'session_created' => date('Y-m-d h:i:s'),
            'session_updated' => date('Y-m-d h:i:s')
        ]
    ]
];
