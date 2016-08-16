<?php

use Cradle\Framework\App;
use Cradle\Framework\Flow;

return App::i()

    //add routes here
    ->get('/search', 'File Search Page')
    ->get('/file', 'Multi $_FILES Page')
    ->get('/base64', 'Multi Base64 Page')
    ->get('/link', 'Multi Link Page')
    ->post('/file', 'Multi $_FILES Process')
    ->post('/base64', 'Multi Base64 Process')
    ->post('/link', 'Multi Link Process')

    //add flows here
    //renders a table display
    ->flow('File Search Page',
        Flow::schema('file')->search->load,
        Flow::schema('file')->search->format,
        Flow::schema('file')->search->render,
        Flow::www()->template->body('display'),
        Flow::www()->template->page
    )
    ->flow(
        'Multi $_FILES Page',
        function($request, $response) {
            $response->setContent('<input type="file" name="file[]" multiple />');
        },
    	Flow::www()->template->body('form'),
    	Flow::www()->template->page
    )
    ->flow(
        'Multi Link Page',
        function($request, $response) {
            $response->setContent('<input type="text" name="file[]" multiple />'
            . '<input type="text" name="file[]" multiple />');
        },
    	Flow::www()->template->body('form'),
    	Flow::www()->template->page
    )
    ->flow(
        'Multi Base64 Page',
        function($request, $response) {
            $response->setContent('<input class="base64" type="file" multiple />
            <script>
            function addFile(file)
            {
                var reader = new FileReader();
                reader.addEventListener("load", function () {
                    var img = jQuery("<img>").attr("src", reader.result);
                    var input = jQuery("<input>")
                        .attr("name", "file[]")
                        .attr("type", "hidden")
                        .attr("value", reader.result);

                    $("input.base64").after(img).after(input);
                }, false);
                reader.readAsDataURL(file);
            }
            $("input.base64").change(function() {
                var files = this.files;
                for (var i = 0; i < this.files.length; i++) {
                    addFile(this.files[i]);
                }
            });
            </script>
            ');
        },
    	Flow::www()->template->body('form'),
    	Flow::www()->template->page
    )
    ->flow(
        'Multi $_FILES Process',
        Flow::file()->fromFileInput,
        Flow::file()->prepare,
        Flow::file()->task,
        Flow::session()->success('Files Created'),
        Flow::session()->redirectTo('/files/search')
    )
    ->flow(
        'Multi Link Process',
        Flow::file()->fromLinkInput,
        Flow::file()->prepare,
        Flow::file()->task,
        Flow::session()->success('Files Created'),
        Flow::session()->redirectTo('/files/search')
    )
    ->flow(
        'Multi Base64 Process',
        Flow::file()->fromBase64Input,
        Flow::file()->prepare,
        Flow::file()->task,
        Flow::session()->success('Files Created'),
        Flow::session()->redirectTo('/files/search')
    );
