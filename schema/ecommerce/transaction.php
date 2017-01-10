<?php //-->
return [
    'singular' => 'Transaction',
    'plural' => 'Transactions',
    'primary' => 'transaction_id',
    'active' => 'transaction_active',
    'created' => 'transaction_created',
    'updated' => 'transaction_updated',
    'relations' => [
        'profile' => [
            'primary' => 'profile_id',
            'many' => false
        ]
    ],
    'fields' => [
        'transaction_products' => [
            'sql' => [
                'type' => 'json',
                'required' => true
            ],
            'elastic' => [
                'type' => 'nested'
            ],
            'validation' => [
                [
                    'method' => 'required',
                    'message' => 'Transaction Products is required'
                ]
            ],
            'test' => [
                'pass' => '[]',
                'fail' => ''
            ]
        ],
        'transaction_profile' => [
            'sql' => [
                'type' => 'json',
                'required' => true
            ],
            'elastic' => [
                'type' => 'object'
            ],
            'validation' => [
                [
                    'method' => 'required',
                    'message' => 'Transaction Profile is required'
                ]
            ],
            'test' => [
                'pass' => '[]',
                'fail' => ''
            ]
        ],
        'transaction_address' => [
            'sql' => [
                'type' => 'json',
                'required' => true
            ],
            'elastic' => [
                'type' => 'object'
            ],
            'validation' => [
                [
                    'method' => 'required',
                    'message' => 'Transaction Address is required'
                ]
            ],
            'test' => [
                'pass' => '[]',
                'fail' => ''
            ]
        ],
        'transaction_total' => [
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
                'label' => 'Total',
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
                'pass' => 200.55,
                'fail' => 'not valid total'
            ]
        ],
        'transaction_method' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'required' => true
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'validation' => [
                [
                    'method' => 'required',
                    'message' => 'Transaction Method is required'
                ]
            ],
            'test' => [
                'pass' => 'paypal',
                'fail' => ''
            ]
        ],
        'transaction_reference' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255
            ],
            'elastic' => [
                'type' => 'string'
            ]
        ],
        'transaction_status' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'default' => 'PENDING',
                'required' => true
            ],
            'elastic' => [
                'type' => 'string'
            ]
        ],
        'transaction_type' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255
            ],
            'elastic' => [
                'type' => 'string'
            ]
        ],
        'transaction_flag' => [
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
            'transaction_id' => 1,
            'profile_id' => 1,
            'transaction_products' => json_encode([
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
            ]),
            'transaction_profile' => json_encode([
                'profile_name' => 'Jack Doe',
                'profile_email' => 'jack@acme.com',
                'profile_phone' => '555-2626',
                'profile_detail' => 'Integer gravida venenatis lobortis. Vestibulum vulputate turpis id est tincidunt, ut interdum risus porttitor. Etiam aliquet at felis ac vehicula. Aenean in felis in eros convallis rhoncus id quis libero. Vivamus rhoncus hendrerit porta. Ut egestas fermentum urna, in imperdiet odio hendrerit vitae. In suscipit enim eget pellentesque imperdiet. Etiam facilisis tellus in mauris sagittis volutpat. Cras in orci maximus, ullamcorper mauris porta, mattis neque. Etiam ligula felis, mollis id magna ultricies, convallis gravida felis.',
                'profile_image' => 'https://www.google.com.ph/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png'
            ]),
            'transaction_address' => json_encode([
                'address_id' => 5,
                'address_label' => 'Home',
                'address_contact' => 'Jack Doe',
                'address_phone' => '555-2828',
                'address_street' => '567 Sesame Street',
                'address_neighborhood' => 'ABC Development',
                'address_city' => 'New York City',
                'address_state' => 'New York',
                'address_region' => 'North East',
                'address_country' => 'US',
                'address_postal' => '12345',
                'address_latitude' => 41.027199,
                'address_longitude' => -73.767048,
                'address_created' => date('Y-m-d h:i:s'),
                'address_updated' => date('Y-m-d h:i:s')
            ]),
            'transaction_total' => 1234.56,
            'transaction_method' => 'paypal',
            'transaction_reference' => '1234567890',
            'transaction_status' => 'PENDING',
            'transaction_created' => date('Y-m-d h:i:s'),
            'transaction_updated' => date('Y-m-d h:i:s')
        ]
    ]
];
