<?php //-->

use Cradle\Framework\Schema\Model;

return Model::i('app', 'app_id')
    //global settings
    ->setActive('app_active')
    ->allowBulk(true)
    ->addRelation('profile', false)
    ->addAction('Edit', '/app/update/')
    ->addAction('Remove', '/app/remove/')
    //field imports
    ->importField('app_id', array(
        'field' => false,
        'column' => 'ID'
    ))
    ->importField('app_name', array(
        //database
        'type' => 'varchar(255)',
        'required' => true,
        //field
        'input' => 'text',
        'holder' => 'Openovate Labs App',
        'default' => null,
        'options' => array(),
        //labels
        'field' => 'App Name',
        'column' => 'Name',
        'title' => 'App Name'
    ))
    ->importField('app_domain', array(
        //database
        'type' => 'varchar(255)',
        'required' => true,
        //field
        'input' => 'text',
        'holder' => '*.openovate.com',
        'default' => null,
        'options' => array(),
        //label
        'field' => 'App Domain',
        'column' => false,
        'title' => 'App Domain'
    ))
    ->importField('app_website', array(
        //database
        'type' => 'varchar(255)',
        'required' => false,
        //field
        'input' => 'text',
        'holder' => 'http://openovate.com/',
        'default' => null,
        'options' => array(),
        //label
        'field' => 'App Website',
        'column' => 'Website',
        'title' => 'App Website',
    ))
    ->importField('app_permissions', array(
        //database
        'type' => 'varchar(255)',
        'required' => false,
        //field
        'input' => 'checkbox',
        'holder' => null,
        'default' => null,
        'options' => array(
            array(
                'value' => 'public_profile',
                'label' => 'Public profiles'
            ),
            array(
                'value' => 'public_sso',
                'label' => 'Single Sign On'
            ),
            array(
                'value' => 'personal_profile',
                'label' => 'Personal profile'
            ),
            array(
                'value' => 'user_profile',
                'label' => 'User Profiles'
            ),
            array(
                'value' => 'global_profile',
                'label' => 'Global Profiles'
            )
        ),
        //label
        'column' => null,
        'field' => 'Permissions',
        'title' => 'Permissions',
    ))
    ->importField('app_token', array(
        //database
        'type' => 'varchar(255)',
        'required' => true,
        //field
        'input' => false,
        'holder' => null,
        'default' => null,
        'options' => array(),
        //label
        'field' => 'Token',
        'column' => 'Token',
        'title' => 'Token',
    ))
    ->importField('app_secret', array(
        //database
        'type' => 'varchar(255)',
        'required' => true,
        //field
        'input' => false,
        'holder' => null,
        'default' => null,
        'options' => array(),
        //label
        'field' => 'Secret',
        'column' => 'Secret',
        'title' => 'Secret'
    ))
    ->importField('app_created', array(
        //database
        'type' => 'datetime',
        'required' => true,
        //field
        'input' => false,
        'holder' => null,
        'default' => null,
        'options' => array(),
        //label
        'field' => 'Created',
        'column' => 'Created',
        'title' => 'Created'
    ))
    ->importField('app_updated', array(
        //database
        'type' => 'datetime',
        'required' => true,
        //field
        'input' => false,
        'holder' => null,
        'default' => null,
        'options' => array(),
        //label
        'field' => 'Updated',
        'column' => 'Updated',
        'title' => 'Updated'
    ))

    //validations
    ->addValidator('app_name', function($value) {
        if(!$value) {
            return 'Cannot be empty!';
        }

        return true;
    })

    //formatters
    ->setFormatter('search', 'app_website', function($value) {
        if(!$value) {
            return $value;
        }

        return '<a href="'.$value.'">'.$value.'</a>';
    })
    ->setFormatter('search', 'app_permissions', function($value) {
        return substr($value, 0, 50);
    })
    ->setFormatter('create', 'app_permissions', function($value) {
        if(is_array($value)) {
            return implode(',', $value);
        }

        return $value;
    })
    ->setFormatter('update', 'app_permissions', function($value) {
        if(is_array($value)) {
            return implode(',', $value);
        }

        return $value;
    })
    ->setFormatter('create', 'app_token', function($value) {
        return md5(uniqid());
    })
    ->setFormatter('create', 'app_secret', function($value) {
        return md5(uniqid());
    })
    ->setFormatter('search', 'app_secret', function($value) {
        return '****';
    })
    ->setFormatter('search', 'app_created', function($value) {
        return date('M-d', strtotime($value));
    })
    ->setFormatter('detail', 'app_created', function($value) {
        return date('M-d', strtotime($value));
    })
    ->setFormatter('create', 'app_created', function($value) {
        return date('Y-m-d H:i:s');
    })
    ->setFormatter('search', 'app_updated', function($value) {
        return date('M-d', strtotime($value));
    })
    ->setFormatter('detail', 'app_updated', function($value) {
        return date('M-d', strtotime($value));
    })
    ->setFormatter('create', 'app_updated', function($value) {
        return date('Y-m-d H:i:s');
    })
    ->setFormatter('update', 'app_updated', function($value) {
        return date('Y-m-d H:i:s');
    });
