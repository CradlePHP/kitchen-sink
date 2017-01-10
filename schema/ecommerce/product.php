<?php //-->
return [
    'singular' => 'Product',
    'plural' => 'Products',
    'primary' => 'product_id',
    'active' => 'product_active',
    'created' => 'product_created',
    'updated' => 'product_updated',
    'relations' => [
        'profile' => [
            'primary' => 'profile_id',
            'many' => false
        ],
        'app' => [
            'primary' => 'app_id',
            'many' => false
        ],
        'comment' => [
            'primary' => 'comment_id',
            'many' => true
        ]
    ],
    'fields' => [
        'product_images' => [
            'sql' => [
                'type' => 'json',
                'required' => true
            ],
            'elastic' => [
                'type' => 'nested'
            ],
            'form' => [
                'label' => 'Image',
                'type' => 'images-field',
                'attributes' => [
                    'data-do' => 'image-field',
                ]
            ],
            'validation' => [
                [
                    'method' => 'required',
                    'message' => 'Must have images'
                ]
            ],
            'list' => [
                'label' => 'Image',
                'format' => 'images',
                'parameters' => [100]
            ],
            'detail' => [
                'label' => 'Image',
                'format' => 'images',
                'parameters' => [100]
            ],
            'test' => [
                'pass' => "[]",
                'fail' => 'not valid images'
            ]

        ],
        'product_title' => [
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
                    'href' => '/product/{{product_slug}}',
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
        'product_slug' => [
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
        'product_detail' => [
            'sql' => [
                'type' => 'text',
                'required' => true
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
        'product_price' => [
            'sql' => [
                'type' => 'float',
                'length' => '10,2',
                'required' => true,
                'attribute' => 'unsigned'
            ],
            'elastic' => [
                'type' => 'float'
            ],
            'form' => [
                'label' => 'Price',
                'type' => 'number',
                'attributes' => [
                    'step' => '0.01',
                    'placeholder' => '9999.99'
                ]
            ],
            'validation' => [
                [
                    'method' => 'required',
                    'message' => 'Price is required'
                ],
                [
                    'method' => 'number',
                    'message' => 'Must be a number',
                    'parameters' => 10
                ],
                [
                    'method' => 'gt',
                    'message' => 'Must be a greater than 0',
                    'parameters' => 0
                ]
            ],
            'list' => [
                'label' => 'Price',
                'format' => 'price'
            ],
            'detail' => [
                'label' => 'Price',
                'format' => 'price'
            ],
            'test' => [
                'pass' => 100.00,
                'fail' => 'not valid price'
            ]
        ],
        'product_original' => [
            'sql' => [
                'type' => 'float',
                'length' => '10,2',
                'attribute' => 'unsigned',
                'default' => '0.00'
            ],
            'elastic' => [
                'type' => 'float'
            ],
            'form' => [
                'label' => 'Original Price',
                'type' => 'number',
                'default' => '0.00',
                'attributes' => [
                    'step' => '0.01',
                    'placeholder' => '9999.99'
                ]
            ],
            'validation' => [
                [
                    'method' => 'number',
                    'message' => 'Must be a number',
                    'parameters' => 10
                ],
                [
                    'method' => 'gt',
                    'message' => 'Must be a greater than 0',
                    'parameters' => 0
                ]
            ],
            'list' => [
                'label' => 'Original',
                'format' => 'price'
            ],
            'detail' => [
                'label' => 'Original',
                'format' => 'price'
            ]
        ],
        'product_tags' => [
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
                    'href' => '/product/search?product_tag=:product_tag'
                ]
            ]
        ],
        'product_brand' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'index' => true,
                'searchable' => true,
                'sortable' => true,
                'filterable' => true
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Brand',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'Apple',
                ]
            ],
            'detail' => [
                'label' => 'Brand'
            ]
        ],
        'product_type' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255
            ],
            'elastic' => [
                'type' => 'string'
            ]
        ],
        'product_flag' => [
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
            'product_id' => 1,
            'app_id' => 1,
            'profile_id' => 1,
            'product_title' => 'This is title one',
            'product_slug' => 'this-is-title-one',
            'product_price' => 123.45,
            'product_original' => 100,
            'product_brand' => 'Apple',
            'product_detail' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi mattis, lectus vitae faucibus elementum, erat lorem maximus nulla, at condimentum nulla nibh a magna. Donec rutrum magna non mauris sodales pharetra. Duis ullamcorper augue at dolor lacinia sodales. Quisque consectetur magna in justo pulvinar placerat. Etiam eget arcu ut eros auctor porta sed a est. Curabitur sed neque eu sapien interdum vehicula. Aliquam vel finibus eros. Praesent auctor neque luctus, ultricies risus ut, vulputate lacus. Donec commodo elit non mauris congue feugiat. Nam eu purus porta, pulvinar justo vel, tempus augue. Duis rutrum augue justo, at sodales magna euismod nec. Vestibulum vel pretium velit. Cras mollis ligula nec odio tincidunt auctor. Cras faucibus consectetur ullamcorper.',
            'product_images' => json_encode([
                [
                    'original' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png',
                    'small' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png'
                ]
            ]),
            'product_tags' => '["one","two"]',
            'product_status' => 'PENDING',
            'product_published' => null,
            'product_created' => date('Y-m-d h:i:s'),
            'product_updated' => date('Y-m-d h:i:s')
        ],
        [
            'product_id' => 2,
            'app_id' => 1,
            'profile_id' => 1,
            'product_title' => 'This is title two',
            'product_slug' => 'this-is-title-two',
            'product_price' => 123.45,
            'product_original' => 100,
            'product_brand' => 'Apple',
            'product_detail' => 'Nulla vitae urna leo. Vivamus nec ante quis purus bibendum posuere. Duis ullamcorper elementum erat quis aliquet. Morbi id euismod nunc, eget pharetra nibh. In semper malesuada mi, id tempus mi vulputate id. Fusce pharetra lacinia nibh eget vehicula. Donec sed felis vitae velit vulputate sollicitudin id eget leo. Fusce molestie, neque eget mollis auctor, tellus augue mollis eros, vitae eleifend leo dolor ut lectus.',
            'product_images' => json_encode([
                [
                    'original' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png',
                    'small' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png'
                ]
            ]),
            'product_tags' => '["two","three"]',
            'product_status' => 'REVIEW',
            'product_published' => null,
            'product_created' => date('Y-m-d h:i:s'),
            'product_updated' => date('Y-m-d h:i:s')
        ],
        [
            'product_id' => 3,
            'app_id' => 1,
            'profile_id' => 1,
            'product_title' => 'This is title three',
            'product_slug' => 'this-is-title-three',
            'product_price' => 123.45,
            'product_original' => 100,
            'product_brand' => 'Samsung',
            'product_detail' => 'Integer gravida venenatis lobortis. Vestibulum vulputate turpis id est tincidunt, ut interdum risus porttitor. Etiam aliquet at felis ac vehicula. Aenean in felis in eros convallis rhoncus id quis libero. Vivamus rhoncus hendrerit porta. Ut egestas fermentum urna, in imperdiet odio hendrerit vitae. In suscipit enim eget pellentesque imperdiet. Etiam facilisis tellus in mauris sagittis volutpat. Cras in orci maximus, ullamcorper mauris porta, mattis neque. Etiam ligula felis, mollis id magna ultricies, convallis gravida felis.',
            'product_images' => json_encode([
                [
                    'original' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png',
                    'small' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png'
                ]
            ]),
            'product_tags' => '["three","four"]',
            'product_status' => 'PUBLISHED',
            'product_published' => date('Y-m-d h:i:s'),
            'product_created' => date('Y-m-d h:i:s'),
            'product_updated' => date('Y-m-d h:i:s')
        ],
        [
            'product_id' => 4,
            'app_id' => 1,
            'profile_id' => 1,
            'product_title' => 'This is title four',
            'product_slug' => 'this-is-title-four',
            'product_price' => 234.56,
            'product_original' => 567.89,
            'product_brand' => 'Apple',
            'product_detail' => 'Quisque dignissim fringilla tincidunt. Nulla facilisi. Morbi tincidunt tortor tincidunt dui bibendum, ornare suscipit ante condimentum. Aliquam erat volutpat. Etiam a nisi rhoncus, feugiat nulla sit amet, pellentesque nulla. Donec sit amet massa eu metus ultricies consequat. Vestibulum aliquam ullamcorper fermentum.',
            'product_images' => json_encode([
                [
                    'original' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png',
                    'small' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png'
                ]
            ]),
            'product_tags' => '["four","five"]',
            'product_status' => 'PUBLISHED',
            'product_published' => date('Y-m-d h:i:s'),
            'product_created' => date('Y-m-d h:i:s'),
            'product_updated' => date('Y-m-d h:i:s')
        ],
        [
            'product_id' => 5,
            'app_id' => 1,
            'profile_id' => 1,
            'product_title' => 'This is title five',
            'product_slug' => 'this-is-title-five',
            'product_price' => 123.45,
            'product_original' => 456.78,
            'product_brand' => 'Apple',
            'product_detail' => 'Proin sed orci tincidunt, scelerisque dolor sit amet, viverra tellus. Sed dapibus, tortor a gravida vehicula, enim felis ullamcorper est, eget tincidunt est leo vel nibh. Curabitur et erat tristique, vehicula leo non, iaculis felis. Sed libero sapien, aliquet vitae tellus ut, rhoncus sodales lacus. Nam fringilla lacus id urna maximus, in placerat leo gravida. Nunc lorem ex, malesuada et est eu, placerat congue massa. Donec ultrices lacus quis metus sollicitudin semper. Nullam lorem neque, bibendum a elit nec, molestie ultrices mi. In imperdiet bibendum maximus.',
            'product_images' => json_encode([
                [
                    'original' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png',
                    'small' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png'
                ]
            ]),
            'product_tags' => '["five", "six"]',
            'product_status' => 'PUBLISHED',
            'product_published' => date('Y-m-d h:i:s'),
            'product_created' => date('Y-m-d h:i:s'),
            'product_updated' => date('Y-m-d h:i:s')
        ]
    ]
];
