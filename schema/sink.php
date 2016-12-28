<?php //-->
return [
    'singular' => 'Sink',        //for pages and messages
    'plural' => 'Dishes',    //for pages and messages
    'primary' => 'sink_id',
    'active' => 'sink_active',
    'created' => 'sink_created',
    'updated' => 'sink_updated',
    'routes' => [
        'admin' => [
            'create' => '/admin/sink/create',
            'detail' => '/admin/sink/detail/:sink_id',
            'remove' => '/admin/sink/remove/:sink_id',
            'restore' => '/admin/sink/restore/:sink_id',
            'search' => '/admin/sink/search',
            'update' => '/admin/sink/update/:sink_id'
        ],
        'api/rest' => [
            'create' => '/rest/sink/create',
            'detail' => '/rest/sink/detail/:sink_id',
            'remove' => '/rest/sink/remove/:sink_id',
            'restore' => '/rest/sink/restore/:sink_id',
            'search' => '/rest/sink/search',
            'update' => '/rest/sink/update/:sink_id'
        ]
    ],
    'permissions' => 'profile',    //session or source must have a linked profile_id
    'relations' => [
        'profile' => [
            'primary' => 'profile_id',
            'many' => false
        ],
        'app' => [
            'primary' => 'app_id',
            'many' => true
        ]
    ],
    'fields' => [
        'sink_text' => [
            'label' => 'Text Example',
            'type' => 'string',
            'field' => 'text',
            'holder' => 'Sample Text',
            'valid' => 'required',
            'searchable' => true,
            'default' => 'sample 123'
        ],
        'sink_password' => [
            'label' => 'Password Example',
            'type' => 'string',
            'field' => 'password',
            'holder' => 'Sample Password',
            'valid' => 'required',
            'encoding' => 'md5'
        ],
        'sink_token' => [
            'label' => 'Token',
            'type' => 'string',
            'field' => false,
            'encoding' => 'uuid'
        ],
        'sink_date' => [
            'label' => 'Date Example',
            'type' => 'datetime',
            'field' => 'date',
            'default' => '+30 days'
        ],
        'sink_slug' => [
            'label' => 'Alpha Numeric Example',
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'unique' => true,
            'field' => 'text',
            'valid' => 'alphanum-_'
        ],
        'sink_email' => [
            'label' => 'Email Example',
            'type' => 'string',
            'field' => 'email'
        ],
        'sink_color' => [
            'label' => 'Color Example',
            'type' => 'string',
            'field' => 'color',
            'valid' => 'hex'
        ],
        'sink_file' => [
            'label' => 'File Example',
            'type' => 'string',
            'field' => ['file', 'accept' => 'image/*'],
            'valid' => 'required'
        ],
        'sink_image' => [
            'label' => 'Image Example',
            'type' => 'json',
            'field' => ['image', 'data-do' => 'image-field'],
            'cdn' => true
        ],
        'sink_tags' => [
            'label' => 'Tags Example',
            'type' => 'json',
            'field' => 'tags'
        ],
        'sink_cc' => [
            'label' => 'Credit Card Example',
            'type' => 'string',
            'field' => 'text',
            'valid' => ['empty', 'cc']
        ],
        'sink_html' => [
            'label' => 'HTML Example',
            'type' => 'text',
            'field' => 'textarea',
            'valid' => 'html'
        ],
        'sink_url' => [
            'label' => 'URL Example',
            'type' => 'string',
            'field' => 'text',
            'valid' => 'url'
        ],
        'sink_regex' => [
            'label' => 'RegExp Example',
            'type' => 'string',
            'field' => 'text',
            'valid' => [['regex', '/[0-9]\-chris/']]
        ],
        'sink_select' => [
            'label' => 'Select Example',
            'type' => 'string',
            'field' => 'select',
            'valid' => 'required',
            'options' => [
                [
                    'value' => 'choice1',
                    'label' => 'Choice 1'
                ],
                [
                    'value' => 'choice2',
                    'label' => 'Choice 2'
                ],
                [
                    'value' => 'choice3',
                    'label' => 'Choice 3'
                ],
                [
                    'value' => 'choice4',
                    'label' => 'Choice 4'
                ],
                [
                    'value' => 'choice5',
                    'label' => 'Choice 5'
                ]
            ]
        ],
        'sink_checkboxes' => [
            'label' => 'Checkboxes Example',
            'type' => 'string',
            'field' => 'checkbox',
            'valid' => 'required',
            'options' => [
                [
                    'value' => 'choice1',
                    'label' => 'Choice 1'
                ],
                [
                    'value' => 'choice2',
                    'label' => 'Choice 2'
                ],
                [
                    'value' => 'choice3',
                    'label' => 'Choice 3'
                ],
                [
                    'value' => 'choice4',
                    'label' => 'Choice 4'
                ],
                [
                    'value' => 'choice5',
                    'label' => 'Choice 5'
                ]
            ]
        ],
        'sink_radios' => [
            'label' => 'Radios Example',
            'type' => 'string',
            'field' => 'radio',
            'valid' => 'required',
            'options' => [
                [
                    'value' => 'choice1',
                    'label' => 'Choice 1'
                ],
                [
                    'value' => 'choice2',
                    'label' => 'Choice 2'
                ],
                [
                    'value' => 'choice3',
                    'label' => 'Choice 3'
                ],
                [
                    'value' => 'choice4',
                    'label' => 'Choice 4'
                ],
                [
                    'value' => 'choice5',
                    'label' => 'Choice 5'
                ]
            ],
            'default' => 'choice3'
        ],
        'sink_bool' => [
            'label' => 'Boolean Example',
            'type' => 'bool',
            'field' => 'checkbox',
            'default' => 1
        ],
        'sink_small' => [
            'label' => 'Small Example',
            'type' => 'small',
            'field' => 'text',
            'default' => 0
        ],
        'sink_float' => [
            'label' => 'Float Example',
            'type' => 'float',
            'field' => [
                'number',
                'min' => 0,
                'step' => 0.01
            ],
            'holder' => '9999.99',
            'valid' => 'required',
            'searchable' => true,
            'default' => '0.00'
        ],
        'sink_int' => [
            'label' => 'Int Example',
            'type' => 'int',
            'field' => 'number',
            'default' => '1'
        ],

    ],
    'fixture'  => [                            //default rows to be inserted
        [
            'sink_text' => 'Text 1',
            'sink_password' => 'admin',
            'sink_date' => '2015-04-10',
            'sink_alphanum' => 'asd-asd-1',
            'sink_email' => 'cblanquera@mailinator.com',
            'sink_color' => '345344',
            'sink_file' => 'somthign',
            'sink_cc' => '4111-1111-1111-1111',
            'sink_html' => '<p>Awesome 1</p>',
            'sink_url' => 'http://someexample1.com',
            'sink_regex' => '1-chris',
            'sink_select' => 'choice2',
            'sink_checkboxes' => 'choice3,choice4',
            'sink_radios' => 'choice1',
            'sink_bool' => 1,
            'sink_small' => 4,
            'sink_float' => 1.1,
            'sink_int' => 1
        ],
        [
            'sink_text' => 'Text 2',
            'sink_password' => 'admin',
            'sink_date' => '2015-04-11',
            'sink_alphanum' => 'asd-asd-2',
            'sink_email' => 'cblanquera2@mailinator.com',
            'sink_color' => '345342',
            'sink_file' => 'somthign',
            'sink_cc' => '4111-1111-1121-1111',
            'sink_html' => '<p>Awesome 2</p>',
            'sink_url' => 'http://someexample2.com',
            'sink_regex' => '2-chris',
            'sink_select' => 'choice2',
            'sink_checkboxes' => 'choice3,choice4',
            'sink_radios' => 'choice1',
            'sink_bool' => 1,
            'sink_small' => 2,
            'sink_float' => 2.2,
            'sink_int' => 2
        ],
        [
            'sink_text' => 'Text 3',
            'sink_password' => 'admin',
            'sink_date' => '2015-04-13',
            'sink_alphanum' => 'asd-asd-3',
            'sink_email' => 'cblanquera3@mailinator.com',
            'sink_color' => '345343',
            'sink_file' => 'somthign',
            'sink_cc' => '4111-1111-1131-1111',
            'sink_html' => '<p>Awesome 3</p>',
            'sink_url' => 'http://someexample3.com',
            'sink_regex' => '3-chris',
            'sink_select' => 'choice2',
            'sink_checkboxes' => 'choice3,choice4',
            'sink_radios' => 'choice1',
            'sink_bool' => 1,
            'sink_small' => 3,
            'sink_float' => 3.3,
            'sink_int' => 3
        ]
    ]
];
