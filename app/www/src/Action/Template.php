<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\App\Www\Action;

use Cradle\Framework\App;
use Cradle\Http\Request;
use Cradle\Http\Response;

/**
 * Factory for model related flows
 *
 * @vendor   Cradle
 * @package  Framework
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Template
{
    protected $app;

    /**
     * Sets the app and model
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Renders the body
     *
     * @param *Request $request
     * @param *Request $response
     * @param *string  $template
     *
     * @return Template
     */
    public function body(Request $request, Response $response, $template) {
        $handlebars = $this->app->package('global')->handlebars();

        $file = __DIR__.'/../template/'.strtolower($template).'.html';

        if(!file_exists($file)) {
            return $this;
        }

        $template = $handlebars->compile(file_get_contents($file));

        $data = array(
            'results' => $response->getResults(),
            'content' => $response->getContent()
        );

        $response->setContent($template($data));

        return $this;
    }

    /**
     * Renders the page
     *
     * @param *Request $request
     * @param *Request $response
     * @param *string  $template
     *
     * @return Template
     */
    public function page(Request $request, Response $response) {
        $title = $response->getPage('title');
        if(trim($title)) {
            $title = $this->app->package('global')->translate($title);
            $response->setTitle($title);
        }

        $handlebars = $this->app->package('global')->handlebars();

        $file = __DIR__.'/../template/_page.html';

        if(!file_exists($file)) {
            return $this;
        }

        $template = $handlebars->compile(file_get_contents($file));

        $data = array(
            'page' => $response->getPage(),
            'results' => $response->getResults(),
            'content' => $response->getContent()
        );

        $response->setContent($template($data));

        //deal with flash messages
        if(isset($_SESSION['flash'])) {
            unset($_SESSION['flash']);
        }

        return $this;
    }
}
