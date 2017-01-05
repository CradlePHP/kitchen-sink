<?php //-->
return [
    'singular' => 'Profile',
    'plural' => 'Profiles',
    'primary' => 'profile_id',
    'active' => 'profile_active',
    'created' => 'profile_created',
    'updated' => 'profile_updated',
    'relations' => [],
    'fields' => [
        'profile_image' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Image',
                'type' => 'image-field',
                'attributes' => [
                    'data-do' => 'image-field',
                ]
            ],
            'validation' => [
                [
                    'method' => 'regexp',
                    'message' => 'Should be a valid url',
                    'parameters' => '/(^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]'
                    .'*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?)|(^data:image\/[a-z]+;base64,)/i'
                ]
            ],
            'list' => [
                'label' => 'Image',
                'format' => 'image',
                'parameters' => [200, 200]
            ],
            'detail' => [
                'label' => 'Image',
                'format' => 'image',
                'parameters' => [200, 200]
            ],
            'test' => [
                'pass' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png',
                'fail' => 'not a good image',
            ]
        ],
        'profile_name' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'required' => true,
                'index' => true
            ],
            'elastic' => [
                'type' => 'string',
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword'
                    ]
                ]
            ],
            'form' => [
                'label' => 'Name',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'John Doe',
                ]
            ],
            'validation' => [
                [
                    'method' => 'required',
                    'message' => 'Name is required'
                ]
            ],
            'list' => [
                'label' => 'Name',
                'searchable' => true,
                'sortable' => true
            ],
            'detail' => [
                'label' => 'Name'
            ],
            'test' => [
                'pass' => 'John Doe',
                'fail' => ''
            ]
        ],
        'profile_email' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'index' => true
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Email',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'john@doe.com',
                ]
            ],
            'validation' => [
                [
                    'method' => 'regexp',
                    'message' => 'Must be a valid email',
                    'parameters' => '/^(?:(?:(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|\x5c(?=[@,"\[\]'.
                    '\x5c\x00-\x20\x7f-\xff]))(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|(?<=\x5c)[@,"\[\]'.
                    '\x5c\x00-\x20\x7f-\xff]|\x5c(?=[@,"\[\]\x5c\x00-\x20\x7f-\xff])|\.(?=[^\.])){1,62'.
                    '}(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|(?<=\x5c)[@,"\[\]\x5c\x00-\x20\x7f-\xff])|'.
                    '[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]{1,2})|"(?:[^"]|(?<=\x5c)"){1,62}")@(?:(?!.{64})'.
                    '(?:[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.?|[a-zA-Z0-9]\.?)+\.(?:xn--[a-zA-Z0-9]'.
                    '+|[a-zA-Z]{2,6})|\[(?:[0-1]?\d?\d|2[0-4]\d|25[0-5])(?:\.(?:[0-1]?\d?\d|2[0-4]\d|25'.
                    '[0-5])){3}\])$/'
                ]
            ],
            'list' => [
                'label' => 'Email',
                'searchable' => true
            ],
            'detail' => [
                'label' => 'Email'
            ],
            'test' => [
                'pass' => 'john@doe.com',
                'fail' => 'a bad email'
            ]
        ],
        'profile_phone' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'index' => true
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Phone',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'John Doe',
                ]
            ],
            'list' => [
                'label' => 'Phone',
                'searchable' => true
            ],
            'detail' => [
                'label' => 'Phone'
            ]
        ],
        'profile_slug' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'index' => true
            ],
            'elastic' => [
                'type' => 'string',
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword'
                    ]
                ]
            ],
            'validation' => [
                [
                    'method' => 'regexp',
                    'message' => 'Slug must only have letters, numbers, dashes',
                    'parameters' => '#^[a-zA-Z0-9\-_]+$#'
                ]
            ],
            'test' => [
                'pass' => 'a-Good-slug_1',
                'fail' => 'not a good slug'
            ]
        ],
        'profile_detail' => [
            'sql' => [
                'type' => 'text'
            ],
            'elastic' => [
                'type' => 'text',
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword'
                    ]
                ]
            ],
            'form' => [
                'label' => 'Detail',
                'type' => 'textarea',
                'attributes' => [
                    'placeholder' => 'Write about something',
                ]
            ],
            'validation' => [
                [
                    'method' => 'word_gt',
                    'message' => 'Detail should have more than 10 words',
                    'parameters' => 10
                ]
            ],
            'list' => [
                'label' => 'Detail',
                'format' => false,
                'searchable' => true
            ],
            'detail' => [
                'label' => 'Detail'
            ],
            'test' => [
                'pass' => 'One Two Three Four Five Six Seven Eight Nine Ten Eleven',
                'fail' => 'One Two Three Four'
            ]
        ],
        'profile_job' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Job Title',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'Janitor',
                ]
            ]
        ],
        'profile_gender' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'default' => 'unknown'
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Gender',
                'type' => 'radios',
                'options' => [
                    'male' => 'Male',
                    'female' => 'Female'
                ]
            ],
            'validation' => [
                [
                    'method' => 'one',
                    'message' => 'Should be either male or female',
                    'parameters' => [
                        'male',
                        'female'
                    ]
                ]
            ]
        ],
        'profile_birth' => [
            'sql' => [
                'type' => 'date'
            ],
            'elastic' => [
                'type' => 'date',
                'format' => 'yyyy-MM-dd'
            ],
            'form' => [
                'label' => 'Birth Date',
                'type' => 'date'
            ]
        ],
        'profile_website' => [
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
                    'placeholder' => 'http://www.acme.com/',
                ]
            ],
            'validation' => [
                [
                    'method' => 'regexp',
                    'message' => 'Must be a valid URL',
                    'parameters' => '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0'.
                    '-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/i'
                ]
            ],
            'test' => [
                'pass' => 'http://acme.com',
                'fail' => 'a bad website'
            ]
        ],
        'profile_facebook' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Facebook',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'http://www.acme.com/',
                ]
            ],
            'validation' => [
                [
                    'method' => 'regexp',
                    'message' => 'Must be a valid URL',
                    'parameters' => '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0'.
                    '-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/i'
                ]
            ],
            'test' => [
                'pass' => 'http://acme.com',
                'fail' => 'a bad website'
            ]
        ],
        'profile_linkedin' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'LinkedIn',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'http://www.acme.com/',
                ]
            ],
            'validation' => [
                [
                    'method' => 'regexp',
                    'message' => 'Must be a valid URL',
                    'parameters' => '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0'.
                    '-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/i'
                ]
            ],
            'test' => [
                'pass' => 'http://acme.com',
                'fail' => 'a bad website'
            ]
        ],
        'profile_twitter' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Twitter',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'http://www.acme.com/',
                ]
            ],
            'validation' => [
                [
                    'method' => 'regexp',
                    'message' => 'Must be a valid URL',
                    'parameters' => '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0'.
                    '-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/i'
                ]
            ],
            'test' => [
                'pass' => 'http://acme.com',
                'fail' => 'a bad website'
            ]
        ],
        'profile_google' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Google',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'http://www.acme.com/',
                ]
            ],
            'validation' => [
                [
                    'method' => 'regexp',
                    'message' => 'Must be a valid URL',
                    'parameters' => '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0'.
                    '-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/i'
                ]
            ],
            'test' => [
                'pass' => 'http://acme.com',
                'fail' => 'a bad website'
            ]
        ],
        'profile_type' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'index' => true
            ],
            'elastic' => [
                'type' => 'string'
            ]
        ],
        'profile_flag' => [
            'sql' => [
                'type' => 'int',
                'length' => 1,
                'default' => '0',
                'index' => true,
                'attribute' => 'unsigned'
            ],
            'elastic' => [
                'type' => 'integer'
            ]
        ]
    ],
    'fixtures' => [
        [
            'profile_name' => 'John Doe',
            'profile_email' => 'john@acme.com',
            'profile_phone' => '555-2424',
            'profile_image' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png'
        ],
        [
            'profile_name' => 'Jane Doe',
            'profile_email' => 'jane@acme.com',
            'profile_phone' => '555-2525',
            'profile_detail' => 'Nulla vitae urna leo. Vivamus nec ante quis purus bibendum posuere. Duis ullamcorper elementum erat quis aliquet. Morbi id euismod nunc, eget pharetra nibh. In semper malesuada mi, id tempus mi vulputate id. Fusce pharetra lacinia nibh eget vehicula. Donec sed felis vitae velit vulputate sollicitudin id eget leo. Fusce molestie, neque eget mollis auctor, tellus augue mollis eros, vitae eleifend leo dolor ut lectus.',
            'profile_image' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png'
        ],
        [
            'profile_name' => 'Jack Doe',
            'profile_email' => 'jack@acme.com',
            'profile_phone' => '555-2626',
            'profile_detail' => 'Integer gravida venenatis lobortis. Vestibulum vulputate turpis id est tincidunt, ut interdum risus porttitor. Etiam aliquet at felis ac vehicula. Aenean in felis in eros convallis rhoncus id quis libero. Vivamus rhoncus hendrerit porta. Ut egestas fermentum urna, in imperdiet odio hendrerit vitae. In suscipit enim eget pellentesque imperdiet. Etiam facilisis tellus in mauris sagittis volutpat. Cras in orci maximus, ullamcorper mauris porta, mattis neque. Etiam ligula felis, mollis id magna ultricies, convallis gravida felis.',
            'profile_image' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png'
        ]
    ]
];
