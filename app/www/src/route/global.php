<?php

use Cradle\Framework\App;
use Cradle\Framework\Flow;

return App::i()

    //add routes here
    ->get('/', 'Hello World')
    ->get('/sink', 'Kitchen Sink')

    //add flows here
    ->flow(
    	'Hello World',
    	Flow::www()->template->body('home'),
    	Flow::www()->template->page
    )
    ->flow(
    	'Kitchen Sink',
    	Flow::www()->template->body('sink'),
    	Flow::www()->template->page
    );
