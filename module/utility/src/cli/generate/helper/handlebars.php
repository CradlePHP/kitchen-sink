<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Handlebars\HandlebarsHandler;

$handlebarsBuilder = function() {
    static $handlebars = null;

    if(is_null($handlebars)) {
        $handlebars = HandlebarsHandler::i();

        $handlebars->registerHelper('capital', function($value) {
            return ucfirst($value);
        });

        $handlebars->registerHelper('camel', function($value, $first) {
            $value = str_replace(['-', '_'], ' ', $value);
            $value = ucwords($value);

            if(!is_scalar($first) || !$first) {
                $value = lcfirst($value);
            }

            return str_replace(' ', '', $value);
        });

        $handlebars->registerHelper('implode', function(array $list, $separator, $options) {
            foreach($list as $i => $variable) {
                if(is_string($variable)) {
                    $list[$i] = "'".$variable."'";
                    continue;
                }

                if(is_array($variable)) {
                    $list[$i] = "'".implode(',', $variable)."'";
                }
            }

            return implode($separator, $list);
        });

        $handlebars->registerHelper('when', function($value1, $operator, $value2, $options) {
            $valid = false;

            switch (true) {
                case $operator == '=='   && $value1 == $value2:
                case $operator == '==='  && $value1 === $value2:
                case $operator == '!='   && $value1 != $value2:
                case $operator == '!=='  && $value1 !== $value2:
                case $operator == '<'    && $value1 < $value2:
                case $operator == '<='   && $value1 <= $value2:
                case $operator == '>'    && $value1 > $value2:
                case $operator == '>='   && $value1 >= $value2:
                case $operator == '&&'   && ($value1 && $value2):
                case $operator == '||'   && ($value1 || $value2):
                    $valid = true;
                    break;
            }

            if($valid) {
                return $options['fn']();
            }

            return $options['inverse']();
        });
    }

    return $handlebars;
};

return $handlebarsBuilder();
