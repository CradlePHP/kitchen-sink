<?php //-->
return function($request, $response) {
    //get handlebars
    $handlebars = $this->package('global')->handlebars();

    //add cache folder
    //$handlebars->setCache(__DIR__.'/../compiled');

    $handlebars->registerHelper('char_length', function($value, $length) {
        return strlen($value, $length);
    });

    $handlebars->registerHelper('word_length', function($value, $length) {
        if (str_word_count($value, 0) > $length) {
            $words = str_word_count($value, 2);
            $position = array_keys($value);
            $value = substr($text, 0, $position[$length]);
        }

        return $value;
    });

    $handlebars->registerHelper('toupper', function($value) {
        return strtoupper($value);
    });

    $handlebars->registerHelper('tolower', function($value) {
        return strtolower($value);
    });
};
