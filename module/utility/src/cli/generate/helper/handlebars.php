<?php //-->

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

        $file = file_get_contents(__DIR__ . '/../template/blocks/form.html');
        $handlebars->registerPartial('block_form', $file);

        $file = file_get_contents(__DIR__ . '/../template/blocks/table.html');
        $handlebars->registerPartial('block_table', $file);

        $file = file_get_contents(__DIR__ . '/../template/fields/checkbox.html');
        $handlebars->registerPartial('field_checkbox', $file);

        $file = file_get_contents(__DIR__ . '/../template/fields/images.html');
        $handlebars->registerPartial('field_images', $file);

        $file = file_get_contents(__DIR__ . '/../template/fields/radio.html');
        $handlebars->registerPartial('field_radio', $file);

        $file = file_get_contents(__DIR__ . '/../template/fields/select.html');
        $handlebars->registerPartial('field_select', $file);

        $file = file_get_contents(__DIR__ . '/../template/fields/tags.html');
        $handlebars->registerPartial('field_tags', $file);

        $file = file_get_contents(__DIR__ . '/../template/fields/text.html');
        $handlebars->registerPartial('field_text', $file);

        $file = file_get_contents(__DIR__ . '/../template/fields/textarea.html');
        $handlebars->registerPartial('field_textarea', $file);

        $file = file_get_contents(__DIR__ . '/../template/formats/input.html');
        $handlebars->registerPartial('format_input', $file);

        $file = file_get_contents(__DIR__ . '/../template/formats/output.html');
        $handlebars->registerPartial('format_output', $file);

        $file = file_get_contents(__DIR__ . '/../template/formats/template.html');
        $handlebars->registerPartial('format_template', $file);

        $file = file_get_contents(__DIR__ . '/../template/validations.html');
        $handlebars->registerPartial('validations', $file);
    }

    return $handlebars;
};

return $handlebarsBuilder();
