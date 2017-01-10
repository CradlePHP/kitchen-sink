<?php //-->
return [
    'singular' => 'Address',
    'plural' => 'Addresses',
    'primary' => 'address_id',
    'active' => 'address_active',
    'created' => 'address_created',
    'updated' => 'address_updated',
    'relations' => [
        'profile' => [
            'primary' => 'profile_id',
            'many' => false
        ]
    ],
    'fields' => [
        'address_label' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'index' => true,
                'searchable' => true,
                'sortable' => true
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Label',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'Home',
                ]
            ],
            'list' => [
                'label' => 'Label'
            ],
            'detail' => [
                'label' => 'Label'
            ]
        ],
        'address_contact' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'index' => true,
                'searchable' => true,
                'sortable' => true
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Contact Name',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'John Doe',
                ]
            ],
            'list' => [
                'label' => 'Contact'
            ],
            'detail' => [
                'label' => 'Contact'
            ]
        ],
        'address_phone' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'index' => true,
                'searchable' => true
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Phone',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => '555-2424',
                ]
            ],
            'list' => [
                'label' => 'Phone'
            ],
            'detail' => [
                'label' => 'Phone'
            ]
        ],
        'address_street' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'required' => true,
                'index' => true,
                'searchable' => true
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Street',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => '123 Sesame Street',
                ]
            ],
            'validation' => [
                [
                    'method' => 'required',
                    'message' => 'Address Street is required'
                ],
            ],
            'detail' => [
                'label' => 'Street'
            ],
            'test' => [
                'pass' => '123 Sesame Street',
                'fail' => 333
            ]
        ],
        'address_neighborhood' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'index' => true,
                'searchable' => true
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Neighborhood',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'Pulte Homes',
                ]
            ],
            'detail' => [
                'label' => 'Street'
            ]
        ],
        'address_city' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'required' => true,
                'index' => true,
                'searchable' => true,
                'sortable' => true,
                'filterable' => true
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'City',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'New York City',
                ]
            ],
            'validation' => [
                [
                    'method' => 'required',
                    'message' => 'Address City is required'
                ],
            ],
            'list' => [
                'label' => 'City'
            ],
            'detail' => [
                'label' => 'City'
            ],
            'test' => [
                'pass' => 'New Year City',
                'fail' => ''
            ]
        ],
        'address_state' => [
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
                'label' => 'State',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'New York',
                ]
            ],
            'detail' => [
                'label' => 'State'
            ]
        ],
        'address_region' => [
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
                'label' => 'Region',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => 'North East',
                ]
            ],
            'detail' => [
                'label' => 'Region'
            ]
        ],
        'address_country' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 2,
                'index' => true,
                'required' => true,
                'searchable' => true,
                'sortable' => true,
                'filterable' => true,
                'default' => 'US'
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Country',
                'type' => 'select',
                'attributes' => [],
                'options' => [
                    'AF' => 'Afghanistan',
                    'AX' => 'Aland Islands',
                    'AL' => 'Albania',
                    'DZ' => 'Algeria',
                    'AS' => 'American Samoa',
                    'AD' => 'Andorra',
                    'AO' => 'Angola',
                    'AI' => 'Anguilla',
                    'AQ' => 'Antarctica',
                    'AG' => 'Antigua And Barbuda',
                    'AR' => 'Argentina',
                    'AM' => 'Armenia',
                    'AW' => 'Aruba',
                    'AU' => 'Australia',
                    'AT' => 'Austria',
                    'AZ' => 'Azerbaijan',
                    'BS' => 'Bahamas',
                    'BH' => 'Bahrain',
                    'BD' => 'Bangladesh',
                    'BB' => 'Barbados',
                    'BY' => 'Belarus',
                    'BE' => 'Belgium',
                    'BZ' => 'Belize',
                    'BJ' => 'Benin',
                    'BM' => 'Bermuda',
                    'BT' => 'Bhutan',
                    'BO' => 'Bolivia',
                    'BA' => 'Bosnia And Herzegovina',
                    'BW' => 'Botswana',
                    'BV' => 'Bouvet Island',
                    'BR' => 'Brazil',
                    'IO' => 'British Indian Ocean Territory',
                    'BN' => 'Brunei Darussalam',
                    'BG' => 'Bulgaria',
                    'BF' => 'Burkina Faso',
                    'BI' => 'Burundi',
                    'KH' => 'Cambodia',
                    'CM' => 'Cameroon',
                    'CA' => 'Canada',
                    'CV' => 'Cape Verde',
                    'KY' => 'Cayman Islands',
                    'CF' => 'Central African Republic',
                    'TD' => 'Chad',
                    'CL' => 'Chile',
                    'CN' => 'China',
                    'CX' => 'Christmas Island',
                    'CC' => 'Cocos (Keeling) Islands',
                    'CO' => 'Colombia',
                    'KM' => 'Comoros',
                    'CG' => 'Congo',
                    'CD' => 'Congo, Democratic Republic',
                    'CK' => 'Cook Islands',
                    'CR' => 'Costa Rica',
                    'CI' => 'Cote D\'Ivoire',
                    'HR' => 'Croatia',
                    'CU' => 'Cuba',
                    'CY' => 'Cyprus',
                    'CZ' => 'Czech Republic',
                    'DK' => 'Denmark',
                    'DJ' => 'Djibouti',
                    'DM' => 'Dominica',
                    'DO' => 'Dominican Republic',
                    'EC' => 'Ecuador',
                    'EG' => 'Egypt',
                    'SV' => 'El Salvador',
                    'GQ' => 'Equatorial Guinea',
                    'ER' => 'Eritrea',
                    'EE' => 'Estonia',
                    'ET' => 'Ethiopia',
                    'FK' => 'Falkland Islands (Malvinas)',
                    'FO' => 'Faroe Islands',
                    'FJ' => 'Fiji',
                    'FI' => 'Finland',
                    'FR' => 'France',
                    'GF' => 'French Guiana',
                    'PF' => 'French Polynesia',
                    'TF' => 'French Southern Territories',
                    'GA' => 'Gabon',
                    'GM' => 'Gambia',
                    'GE' => 'Georgia',
                    'DE' => 'Germany',
                    'GH' => 'Ghana',
                    'GI' => 'Gibraltar',
                    'GR' => 'Greece',
                    'GL' => 'Greenland',
                    'GD' => 'Grenada',
                    'GP' => 'Guadeloupe',
                    'GU' => 'Guam',
                    'GT' => 'Guatemala',
                    'GG' => 'Guernsey',
                    'GN' => 'Guinea',
                    'GW' => 'Guinea-Bissau',
                    'GY' => 'Guyana',
                    'HT' => 'Haiti',
                    'HM' => 'Heard Island & Mcdonald Islands',
                    'VA' => 'Holy See (Vatican City State)',
                    'HN' => 'Honduras',
                    'HK' => 'Hong Kong',
                    'HU' => 'Hungary',
                    'IS' => 'Iceland',
                    'IN' => 'India',
                    'ID' => 'Indonesia',
                    'IR' => 'Iran, Islamic Republic Of',
                    'IQ' => 'Iraq',
                    'IE' => 'Ireland',
                    'IM' => 'Isle Of Man',
                    'IL' => 'Israel',
                    'IT' => 'Italy',
                    'JM' => 'Jamaica',
                    'JP' => 'Japan',
                    'JE' => 'Jersey',
                    'JO' => 'Jordan',
                    'KZ' => 'Kazakhstan',
                    'KE' => 'Kenya',
                    'KI' => 'Kiribati',
                    'KR' => 'Korea',
                    'KW' => 'Kuwait',
                    'KG' => 'Kyrgyzstan',
                    'LA' => 'Lao People\'s Democratic Republic',
                    'LV' => 'Latvia',
                    'LB' => 'Lebanon',
                    'LS' => 'Lesotho',
                    'LR' => 'Liberia',
                    'LY' => 'Libyan Arab Jamahiriya',
                    'LI' => 'Liechtenstein',
                    'LT' => 'Lithuania',
                    'LU' => 'Luxembourg',
                    'MO' => 'Macao',
                    'MK' => 'Macedonia',
                    'MG' => 'Madagascar',
                    'MW' => 'Malawi',
                    'MY' => 'Malaysia',
                    'MV' => 'Maldives',
                    'ML' => 'Mali',
                    'MT' => 'Malta',
                    'MH' => 'Marshall Islands',
                    'MQ' => 'Martinique',
                    'MR' => 'Mauritania',
                    'MU' => 'Mauritius',
                    'YT' => 'Mayotte',
                    'MX' => 'Mexico',
                    'FM' => 'Micronesia, Federated States Of',
                    'MD' => 'Moldova',
                    'MC' => 'Monaco',
                    'MN' => 'Mongolia',
                    'ME' => 'Montenegro',
                    'MS' => 'Montserrat',
                    'MA' => 'Morocco',
                    'MZ' => 'Mozambique',
                    'MM' => 'Myanmar',
                    'NA' => 'Namibia',
                    'NR' => 'Nauru',
                    'NP' => 'Nepal',
                    'NL' => 'Netherlands',
                    'AN' => 'Netherlands Antilles',
                    'NC' => 'New Caledonia',
                    'NZ' => 'New Zealand',
                    'NI' => 'Nicaragua',
                    'NE' => 'Niger',
                    'NG' => 'Nigeria',
                    'NU' => 'Niue',
                    'NF' => 'Norfolk Island',
                    'MP' => 'Northern Mariana Islands',
                    'NO' => 'Norway',
                    'OM' => 'Oman',
                    'PK' => 'Pakistan',
                    'PW' => 'Palau',
                    'PS' => 'Palestinian Territory, Occupied',
                    'PA' => 'Panama',
                    'PG' => 'Papua New Guinea',
                    'PY' => 'Paraguay',
                    'PE' => 'Peru',
                    'PH' => 'Philippines',
                    'PN' => 'Pitcairn',
                    'PL' => 'Poland',
                    'PT' => 'Portugal',
                    'PR' => 'Puerto Rico',
                    'QA' => 'Qatar',
                    'RE' => 'Reunion',
                    'RO' => 'Romania',
                    'RU' => 'Russian Federation',
                    'RW' => 'Rwanda',
                    'BL' => 'Saint Barthelemy',
                    'SH' => 'Saint Helena',
                    'KN' => 'Saint Kitts And Nevis',
                    'LC' => 'Saint Lucia',
                    'MF' => 'Saint Martin',
                    'PM' => 'Saint Pierre And Miquelon',
                    'VC' => 'Saint Vincent And Grenadines',
                    'WS' => 'Samoa',
                    'SM' => 'San Marino',
                    'ST' => 'Sao Tome And Principe',
                    'SA' => 'Saudi Arabia',
                    'SN' => 'Senegal',
                    'RS' => 'Serbia',
                    'SC' => 'Seychelles',
                    'SL' => 'Sierra Leone',
                    'SG' => 'Singapore',
                    'SK' => 'Slovakia',
                    'SI' => 'Slovenia',
                    'SB' => 'Solomon Islands',
                    'SO' => 'Somalia',
                    'ZA' => 'South Africa',
                    'GS' => 'South Georgia And Sandwich Isl.',
                    'ES' => 'Spain',
                    'LK' => 'Sri Lanka',
                    'SD' => 'Sudan',
                    'SR' => 'Suriname',
                    'SJ' => 'Svalbard And Jan Mayen',
                    'SZ' => 'Swaziland',
                    'SE' => 'Sweden',
                    'CH' => 'Switzerland',
                    'SY' => 'Syrian Arab Republic',
                    'TW' => 'Taiwan',
                    'TJ' => 'Tajikistan',
                    'TZ' => 'Tanzania',
                    'TH' => 'Thailand',
                    'TL' => 'Timor-Leste',
                    'TG' => 'Togo',
                    'TK' => 'Tokelau',
                    'TO' => 'Tonga',
                    'TT' => 'Trinidad And Tobago',
                    'TN' => 'Tunisia',
                    'TR' => 'Turkey',
                    'TM' => 'Turkmenistan',
                    'TC' => 'Turks And Caicos Islands',
                    'TV' => 'Tuvalu',
                    'UG' => 'Uganda',
                    'UA' => 'Ukraine',
                    'AE' => 'United Arab Emirates',
                    'GB' => 'United Kingdom',
                    'US' => 'United States',
                    'UM' => 'United States Outlying Islands',
                    'UY' => 'Uruguay',
                    'UZ' => 'Uzbekistan',
                    'VU' => 'Vanuatu',
                    'VE' => 'Venezuela',
                    'VN' => 'Viet Nam',
                    'VG' => 'Virgin Islands, British',
                    'VI' => 'Virgin Islands, U.S.',
                    'WF' => 'Wallis And Futuna',
                    'EH' => 'Western Sahara',
                    'YE' => 'Yemen',
                    'ZM' => 'Zambia',
                    'ZW' => 'Zimbabwe',
                ]
            ],
            'validation' => [
                [
                    'method' => 'char_eq',
                    'message' => 'Invalid Country Code',
                    'parameters' => 2
                ]
            ],
            'list' => [
                'label' => 'Country'
            ],
            'detail' => [
                'label' => 'Country'
            ],
            'test' => [
                'pass' => 'US',
                'fail' => 'not valid country'
            ]
        ],
        'address_postal' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255,
                'index' => true,
                'required' => true,
                'searchable' => true,
                'sortable' => true,
                'filterable' => true
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Postal Code',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => '12345',
                ]
            ],
            'validation' => [
                [
                    'method' => 'required',
                    'message' => 'Address Postal is required'
                ],
            ],
            'detail' => [
                'label' => 'Postal'
            ],
            'test' => [
                'pass' => '12345',
                'fail' => ''
            ]
        ],
        'address_latitude' => [
            'sql' => [
                'type' => 'float',
                'length' => '6,3',
                'default' => 0
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Postal Code',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => '12345',
                ]
            ]
        ],
        'address_longitude' => [
            'sql' => [
                'type' => 'float',
                'length' => '6,3',
                'default' => 0
            ],
            'elastic' => [
                'type' => 'string'
            ],
            'form' => [
                'label' => 'Postal Code',
                'type' => 'text',
                'attributes' => [
                    'placeholder' => '12345',
                ]
            ]
        ],
        'address_type' => [
            'sql' => [
                'type' => 'varchar',
                'length' => 255
            ],
            'elastic' => [
                'type' => 'string'
            ]
        ],
        'address_flag' => [
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
            'address_id' => 1,
            'profile_id' => 1,
            'address_label' => 'Home',
            'address_contact' => 'John Doe',
            'address_phone' => '555-2424',
            'address_street' => '123 Sesame Street',
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
        ],
        [
            'address_id' => 2,
            'profile_id' => 1,
            'address_label' => 'Work',
            'address_contact' => 'John Doe',
            'address_phone' => '555-2525',
            'address_street' => '234 Sesame Street',
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
        ],
        [
            'address_id' => 3,
            'profile_id' => 2,
            'address_label' => 'Home',
            'address_contact' => 'Jane Doe',
            'address_phone' => '555-2626',
            'address_street' => '345 Sesame Street',
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
        ],
        [
            'address_id' => 4,
            'profile_id' => 2,
            'address_label' => 'Work',
            'address_contact' => 'Jane Doe',
            'address_phone' => '555-2727',
            'address_street' => '456 Sesame Street',
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
        ],
        [
            'address_id' => 5,
            'profile_id' => 3,
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
        ]
    ]
];
