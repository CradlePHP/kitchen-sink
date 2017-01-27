<?php //-->
return [
    'singular' => 'Post',
    'plural' => 'Posts',
    'primary' => 'post_id',
    'active' => 'post_active',
    'created' => 'post_created',
    'updated' => 'post_updated',
    'relations' => [
        'profile' => [
            'primary' => 'profile_id',
            'many' => false
        ],
        'oauth/app' => [
            'primary' => 'app_id',
            'many' => false
        ],
        'comment' => [
            'primary' => 'comment_id',
            'many' => true
        ]
    ],
    'fields' => [
        'post_image' => [
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
                'parameters' => [100]
            ],
            'detail' => [
                'label' => 'Image',
                'format' => 'image',
                'parameters' => [100]
            ],
            'test' => [
                'pass' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png',
                'fail' => 'not a good image',
            ]
        ],
        'post_title' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 254,
                'required' => true,
                'index' => true,
                'searchable' => true
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Title',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'Enter a Title',
                ]
            ],
            'validation' => [
                [
                    'method' => 'required',
                    'message' => 'Title is required'
                ],
                [
                    'method' => 'char_gt',
                    'message' => 'Title should be longer than 10 characters',
                    'parameters' => 10
                ],
                [
                    'method' => 'char_lt',
                    'message' => 'Title should be less than 255 characters',
                    'parameters' => 255
                ]
            ],
            'list' => [
                'label' => 'Title',
                'format' => 'link',
                'parameters' => [
                    'href' => '/post/{{post_slug}}',
                    'target' => '_blank'
                ]
            ],
            'detail' => [
                'label' => 'Title'
            ],
            'test' => [
                'pass' => 'Foobar Title',
                'fail' => 'Foobar'
            ]
        ],
        'post_slug' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'required' => true,
                'unique' => true
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Slug',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'Enter a unique SEO slug',
                ]
            ],
            'validation' => [
                [
                    'method' => 'required',
                    'message' => 'Slug is required'
                ],
                [
                    'method' => 'regexp',
                    'message' => 'Slug must only have letters, numbers, dashes',
                    'parameters' => '#^[a-zA-Z0-9\-_]+$#'
                ],
                [
                    'method' => 'unique',
                    'message' => 'Slug must be unique'
                ]
            ],
            'test' => [
                'pass' => 'a-Good-slug_1',
                'fail' => 'not a good slug'
            ]
        ],
        'post_detail' => [
            'sql' => [
                'type' => 'text',
                'required' => true,
                'searchable' => true
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
                    'method' => 'required',
                    'message' => 'Detail is required'
                ],
                [
                    'method' => 'word_gt',
                    'message' => 'Detail should have more than 10 words',
                    'parameters' => 10
                ]
            ],
            'detail' => [
                'label' => 'Detail',
                'noescape' => true
            ],
            'test' => [
                'pass' => 'One Two Three Four Five Six Seven Eight Nine Ten Eleven',
                'fail' => 'One Two Three Four'
            ]
        ],
        'post_tags' => [
            'sql' => [
                'type' => 'json'
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Tags',
                'type' => 'tag-field'
            ],
            'detail' => [
                'label' => 'Tags',
                'format' => 'link',
                'parameters' => [
                    'href' => '/post/search?product_tag=:product_tag'
                ]
            ]
        ],
        'post_status' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'default' => 'PENDING',
                'sortable' => true,
                'filterable' => true
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    'PENDING' => 'Pending',
                    'REVIEW' => 'Review',
                    'PUBLISHED' => 'Published',
                ]
            ],
            'validation' => [
                [
                    'method' => 'one',
                    'message' => 'Should be either pending, review or published',
                    'parameters' => [
                        'PENDING',
                        'REVIEW',
                        'PUBLISHED'
                    ]
                ]
            ],
            'list' => [
                'label' => 'Status',
                'searchable' => true,
                'sortable' => true,
                'filterable' => true,
                'format' => 'capital'
            ]
        ],
        'post_published' => [
            'sql' => [
                'type' => 'datetime',
                'sortable' => true
            ],
            'elastic' => [
                'type' => 'date',
                'format' => 'yyyy-MM-dd HH:mm:ss'
            ],
            'form' => [
                'label' => 'Published Date',
                'type' => 'date',
                'default' => 'NOW()'
            ],
            'list' => [
                'label' => 'Published',
                'searchable' => true,
                'sortable' => true,
                'format' => 'date',
                'parameters' => 'M d'
            ],
            'detail' => [
                'label' => 'Published On',
                'format' => 'date',
                'parameters' => 'F d, y g:iA'
            ]
        ],
        'post_flag' => [
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
            'post_id' => 1,
            'app_id' => 1,
            'profile_id' => 1,
            'post_title' => 'This is title one',
            'post_slug' => 'this-is-title-one',
            'post_detail' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi mattis, lectus vitae faucibus elementum, erat lorem maximus nulla, at condimentum nulla nibh a magna. Donec rutrum magna non mauris sodales pharetra. Duis ullamcorper augue at dolor lacinia sodales. Quisque consectetur magna in justo pulvinar placerat. Etiam eget arcu ut eros auctor porta sed a est. Curabitur sed neque eu sapien interdum vehicula. Aliquam vel finibus eros. Praesent auctor neque luctus, ultricies risus ut, vulputate lacus. Donec commodo elit non mauris congue feugiat. Nam eu purus porta, pulvinar justo vel, tempus augue. Duis rutrum augue justo, at sodales magna euismod nec. Vestibulum vel pretium velit. Cras mollis ligula nec odio tincidunt auctor. Cras faucibus consectetur ullamcorper.',
            'post_image' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png',
            'post_tags' => '["one", "two"]',
            'post_status' => 'PENDING',
            'post_published' => null,
            'post_created' => date('Y-m-d h:i:s'),
            'post_updated' => date('Y-m-d h:i:s')
        ],
        [
            'post_id' => 2,
            'app_id' => 1,
            'profile_id' => 1,
            'post_title' => 'This is title two',
            'post_slug' => 'this-is-title-two',
            'post_detail' => 'Nulla vitae urna leo. Vivamus nec ante quis purus bibendum posuere. Duis ullamcorper elementum erat quis aliquet. Morbi id euismod nunc, eget pharetra nibh. In semper malesuada mi, id tempus mi vulputate id. Fusce pharetra lacinia nibh eget vehicula. Donec sed felis vitae velit vulputate sollicitudin id eget leo. Fusce molestie, neque eget mollis auctor, tellus augue mollis eros, vitae eleifend leo dolor ut lectus.',
            'post_image' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png',
            'post_tags' => '["two", "three"]',
            'post_status' => 'REVIEW',
            'post_published' => null,
            'post_created' => date('Y-m-d h:i:s'),
            'post_updated' => date('Y-m-d h:i:s')
        ],
        [
            'post_id' => 3,
            'app_id' => 1,
            'profile_id' => 1,
            'post_title' => 'This is title three',
            'post_slug' => 'this-is-title-three',
            'post_detail' => 'Integer gravida venenatis lobortis. Vestibulum vulputate turpis id est tincidunt, ut interdum risus porttitor. Etiam aliquet at felis ac vehicula. Aenean in felis in eros convallis rhoncus id quis libero. Vivamus rhoncus hendrerit porta. Ut egestas fermentum urna, in imperdiet odio hendrerit vitae. In suscipit enim eget pellentesque imperdiet. Etiam facilisis tellus in mauris sagittis volutpat. Cras in orci maximus, ullamcorper mauris porta, mattis neque. Etiam ligula felis, mollis id magna ultricies, convallis gravida felis.',
            'post_image' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png',
            'post_tags' => '["three", "four"]',
            'post_status' => 'PUBLISHED',
            'post_published' => date('Y-m-d h:i:s'),
            'post_created' => date('Y-m-d h:i:s'),
            'post_updated' => date('Y-m-d h:i:s')
        ],
        [
            'post_id' => 4,
            'app_id' => 1,
            'profile_id' => 1,
            'post_title' => 'This is title four',
            'post_slug' => 'this-is-title-four',
            'post_detail' => 'Quisque dignissim fringilla tincidunt. Nulla facilisi. Morbi tincidunt tortor tincidunt dui bibendum, ornare suscipit ante condimentum. Aliquam erat volutpat. Etiam a nisi rhoncus, feugiat nulla sit amet, pellentesque nulla. Donec sit amet massa eu metus ultricies consequat. Vestibulum aliquam ullamcorper fermentum.',
            'post_image' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png',
            'post_tags' => '["four", "five"]',
            'post_status' => 'PUBLISHED',
            'post_published' => date('Y-m-d h:i:s'),
            'post_created' => date('Y-m-d h:i:s'),
            'post_updated' => date('Y-m-d h:i:s')
        ],
        [
            'post_id' => 5,
            'app_id' => 1,
            'profile_id' => 1,
            'post_title' => 'This is title five',
            'post_slug' => 'this-is-title-five',
            'post_detail' => 'Proin sed orci tincidunt, scelerisque dolor sit amet, viverra tellus. Sed dapibus, tortor a gravida vehicula, enim felis ullamcorper est, eget tincidunt est leo vel nibh. Curabitur et erat tristique, vehicula leo non, iaculis felis. Sed libero sapien, aliquet vitae tellus ut, rhoncus sodales lacus. Nam fringilla lacus id urna maximus, in placerat leo gravida. Nunc lorem ex, malesuada et est eu, placerat congue massa. Donec ultrices lacus quis metus sollicitudin semper. Nullam lorem neque, bibendum a elit nec, molestie ultrices mi. In imperdiet bibendum maximus.',
            'post_image' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png',
            'post_tags' => '["five", "six"]',
            'post_status' => 'PUBLISHED',
            'post_published' => date('Y-m-d h:i:s'),
            'post_created' => date('Y-m-d h:i:s'),
            'post_updated' => date('Y-m-d h:i:s')
        ]
    ]
];
