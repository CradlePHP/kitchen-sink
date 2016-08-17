<?php

use Cradle\Framework\App;
use Cradle\Framework\Flow;

return App::i()

    //add routes here
    ->get('/queue', 'Utility Queue')
    ->get('/mail', 'Utility Mail')

    //add flows here
    ->on('Queue Task',
        function($request, $response) {
            echo 'Pseudo Task';
        }
    )

    ->flow('Utility Queue',
        Flow::queue()->send('Queue Task'),
        Flow::session()->success('Task was queued. run worker.php'),
        Flow::www()->template->body('sink'),
        Flow::www()->template->page
    )

    ->flow('Utility Mail',
        function($request, $response) {
            $response
                ->setResults('mail', 'subject', 'Welcome')
                ->setResults('mail', 'body', 'text', 'Welcome to my site!')
                ->setResults('mail', 'to', array(
                    'foobar@mailinator.com',
                    'barfoo@mailinator.com',
                ));
        },
        Flow::mail()->send,
        Flow::session()->success('Mail sent to foobar@mailinator.com'),
        Flow::www()->template->body('sink'),
        Flow::www()->template->page
    );
