<?php //-->

use Cradle\Framework\Schema\Model;

return Model::i('profile', 'profile_id')
    //global settings
    ->setActive('profile_active')
    ->allowBulk(true)
    ->addAction('Edit', '/profile/update/')
    ->addAction('Remove', '/profile/remove/')
    //field imports
    ->importField('profile_id', array(
        'field' => false,
        'column' => 'ID'
    ))
    ->importField('profile_name', array(
        //database
        'type' => 'varchar(255)',
        'required' => true,
        //field
        'input' => 'text',
        'holder' => 'John Doe',
        'default' => null,
        'options' => array(),
        //labels
        'field' => 'Full Name',
        'column' => 'Name',
        'title' => 'Name'
    ))
    ->importField('profile_email', array(
        //database
        'type' => 'varchar(255)',
        'required' => false,
        //field
        'input' => 'email',
        'holder' => 'john@doe.com',
        'default' => null,
        'options' => array(),
        //label
        'field' => 'Email Address',
        'column' => 'Email',
        'title' => 'Email Address'
    ))
    ->importField('profile_image', array(
        //database
        'type' => 'varchar(255)',
        'required' => false,
        //field
        'input' => 'image',
        'holder' => null,
        'default' => null,
        'options' => array(),
        //label
        'field' => 'Photo',
        'column' => 'Photo',
        'title' => 'Photo',
    ))
    ->importField('profile_number', array(
        //database
        'type' => 'varchar(255)',
        'required' => false,
        //field
        'input' => 'text',
        'holder' => '555-2424',
        'default' => null,
        'options' => array(),
        //label
        'column' => '#',
        'field' => 'Phone Number',
        'title' => 'Phone Number',
    ))
    ->importField('profile_created', array(
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
    ->importField('profile_updated', array(
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
    ->addValidator('profile_name', function($value) {
        if(!$value) {
            return 'Cannot be empty!';
        }

        return true;
    })

    //custom field
    ->setField('profile_image', function($value) {
        if($value) {
            return '<img src="' . $value . '" /><br />'
            . '<input type="file" class="form-control" '
            . 'accept="image/*" name="profile_image" />';
        }

        return '<input type="file" class="form-control" '
        . 'accept="image/*" name="profile_image" />';
    })

    //formatters
    ->setFormatter('search', 'profile_email', function($value) {
        if(!$value) {
            return $value;
        }

        return '<a href="mailto:'.$value.'">'.$value.'</a>';
    })
    ->setFormatter('detail', 'profile_email', function($value) {
        if(!$value) {
            return $value;
        }

        return '<a href="mailto:'.$value.'">'.$value.'</a>';
    })
    ->setFormatter('search', 'profile_image', function($value) {
        if(!$value) {
            return $value;
        }

        return '<img height="40" src="'.$value.'" />';
    })
    ->setFormatter('detail', 'profile_image', function($value) {
        if(!$value) {
            return $value;
        }

        return '<img src="'.$value.'" />';
    })
    ->setFormatter('search', 'profile_created', function($value) {
        return date('M-d', strtotime($value));
    })
    ->setFormatter('detail', 'profile_created', function($value) {
        return date('M-d', strtotime($value));
    })
    ->setFormatter('create', 'profile_created', function($value) {
        return date('Y-m-d H:i:s');
    })
    ->setFormatter('search', 'profile_updated', function($value) {
        return date('M-d', strtotime($value));
    })
    ->setFormatter('detail', 'profile_updated', function($value) {
        return date('M-d', strtotime($value));
    })
    ->setFormatter('create', 'profile_updated', function($value) {
        return date('Y-m-d H:i:s');
    })
    ->setFormatter('update', 'profile_updated', function($value) {
        return date('Y-m-d H:i:s');
    });
