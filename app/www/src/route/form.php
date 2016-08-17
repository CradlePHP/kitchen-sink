<?php

use Cradle\Framework\App;
use Cradle\Framework\Flow;

return App::i()

    //add routes here
    ->get('/csrf', 'CSRF Form')
    ->get('/captcha', 'Captcha Form')
    ->post('/csrf', 'CSRF Process')
    ->post('/captcha', 'Captcha Process')

    //add flows here
    ->flow(
        'CSRF Form',
        Flow::csrf()->load,
        Flow::csrf()->render,
        function($request, $response) {
            $content = $response->getContent();
            $content .= 'Show Error ? <input type="checkbox" name="csrf" '
                . 'value="Bad Auth Code" /> <br />';
            $response->setContent($content);
        },
        Flow::www()->template->body('form'),
        Flow::www()->template->page
    )

    ->flow(
        'Captcha Form',
        //check settings
        function($request, $response) {
            $config = $this('global')->service('captcha-main');
            if(isset($config['token']) && $config['token'] !== '<GOOGLE CAPTCHA TOKEN>') {
                $this->subflow('captcha-continue', $request, $response);
                return;
            }

            $this->subflow('captcha-stop', $request, $response);
        },

        array(
            'captcha-continue',
            Flow::captcha()->load,
            Flow::captcha()->render,
            Flow::www()->template->body('form'),
            Flow::www()->template->page
        ),
        array(
            'captcha-stop',
            Flow::session()->error('Looks like you didnt setup services.php'),
            Flow::www()->template->body('sink'),
            Flow::www()->template->page
        )
    )

    ->flow(
        'CSRF Process',
        Flow::csrf()->check,
        array(
            Flow::csrf()->yes,
            Flow::session()->success('That was valid')
        ),
        array(
            Flow::csrf()->no,
            Flow::session()->flash()
        ),
        'CSRF Form'
    )

    ->flow(
        'Captcha Process',
        Flow::captcha()->check,
        array(
            Flow::captcha()->yes,
            Flow::session()->success('That was valid')
        ),
        array(
            Flow::captcha()->no,
            Flow::session()->flash()
        ),
        'Captcha Form'
    );
