<?php //-->
return function($field) {
    $normal = [
        'type' => 'string',
        'field' => ['text'],
        'valid' => [],
        'label' => '',
        'holder' => '',
        'searchable' => false,
        'sortable' => false,
        'unique' => false,
        'required' => false
    ];

    $keys = [
        'encoding',
        'type',
        'label',
        'holder',
        'searchable',
        'sortable',
        'unique',
        'sample'
    ];

    foreach($keys as $key) {
        if(isset($field[$key])) {
            $normal[$key] = $field[$key];
        }
    }

    if(isset($field['field'])) {
        if($field['field'] === false || is_array($field['field'])) {
            $normal['field'] = $field['field'];
        } else if(is_string($field['field'])) {
            $normal['field'] = [$field['field']];
        }
    }

    if(isset($field['valid'])) {
        if(is_array($field['valid'])) {
            $normal['valid'] = $field['valid'];
        } else if(is_string($field['valid'])) {
            $normal['valid'][] = [$field['valid']];
        }

        foreach($normal['valid'] as $i => $validation) {
            if(!is_array($validation)) {
                $validation = [$validation];
            }

            $normal['valid'][$i] = $validation;

            if($validation[0] === 'required') {
                $normal['required'] = true;
            }
        }
    }

    if(isset($field['default'])) {
        $normal['default'] = $field['default'];

        if(is_null($normal['default'])) {
            $normal['default'] = 'null';
        } else if($normal['type'] === 'int' && !is_numeric($normal['default'])) {
            $normal['default'] = '0';
            $normal['valid'][] = ['int'];
        } else if($normal['type'] === 'float' && !is_numeric($normal['default'])) {
            $normal['default'] = '0.00';
            $normal['valid'][] = ['float'];
        } else if($normal['type'] === 'boolean' && !is_numeric($normal['default'])) {
            $normal['default'] = '0';
            $normal['valid'][] = ['bool'];
        } else if($normal['type'] === 'datetime'
            && ($normal['default'] === 'now'
            || $normal['default'] === 'now()')
        )
        {
            $normal['default'] = 'CURRENT_TIMESTAMP';
            $normal['valid'][] = ['datetime'];
        } else if($normal['type'] === 'date') {
            $normal['valid'][] = ['date'];
        } else if($normal['type'] === 'time') {
            $normal['valid'][] = ['time'];
        } else if(is_string($normal['default'])) {
            $normal['default'] = "'".$normal['default']."'";
        }

        $normal['default'] = (string) $normal['default'];
    }

    if(isset($field['options']) && is_array($field['options'])) {
        $normal['options'] = [];

        foreach($field['options'] as $option) {
            if(is_string($option)) {
                $normal['options'][] = [
                    'value' => $option,
                    'label' => ucwords($option)
                ];

                continue;
            }

            $normal['options'][] = $option;
        }

        if($field['field'] !== 'checkbox') {
            $valid = [];
            foreach($normal['options'] as $option) {
                $valid[] = $option['value'];
            }

            if($normal['type'] !== 'file') {
                $normal['valid'][] = ['one', $valid];
            }
        }
    }

    $validKeys = [];

    foreach($normal['valid'] as $check) {
        $validKeys[] = $check[0];
    }

    //some types should imply validation
    if(in_array($normal['type'], [
            'bool',
            'date',
            'float',
            'int',
            'email',
            'url',
            'small'
        ])
        && !in_array($normal['type'], $validKeys)
    )
    {
        $normal['valid'][] = [$normal['type']];
    }

    //datetime as well
    if($normal['type'] === 'datetime' && !in_array('date', $validKeys)) {
        $normal['valid'][] = ['date'];
    }

    if(!isset($normal['sample']) && isset($normal['default'])) {
        $normal['sample'] = $normal['default'];
    }

    if(!isset($normal['sample'])) {
        $sample = 'foobar';
        foreach($normal['valid'] as $valid) {
            switch($valid[0]) {
                case 'required':
                case 'empty':
                    break;
                case 'one':
                    $sample = $valid[1][0];
                    break;
                case 'email':
                    $sample = 'test@test.com';
                    break;
                case 'hex':
                    $sample = '12321';
                    break;
                case 'cc':
                    $sample = '4111111111111111';
                    break;
                case 'html':
                    $sample = '<p>Awesome</p>';
                    break;
                case 'url':
                    $sample = 'http://example.com';
                    break;
                case 'slug':
                    $sample = 'asd-123';
                    break;
                case 'json':
                    $sample = '{"error":false}';
                    break;
                case 'date':
                    $sample = '2015-09-02';
                    break;
                case 'time':
                    $sample = '12:01:00';
                    break;
                case 'alphanum':
                    $sample = 'foo123';
                    break;
                case 'alphanum-':
                    $sample = 'foo-123';
                    break;
                case 'alphanum_':
                    $sample = 'foo_123';
                    break;
                case 'alphanum-_':
                    $sample = 'foo-_123';
                    break;
                case 'bool':
                    $sample = '1';
                    break;
                case 'small':
                    $sample = '3';
                    break;
                case 'int':
                    $sample = '3';
                    break;
                case 'float':
                    $sample = '3.3';
                    break;
                case 'price':
                    $sample = '3.30';
                    break;
            }
        }

        $normal['sample'] = "'".$sample."'";
    }

    return $normal;
};
