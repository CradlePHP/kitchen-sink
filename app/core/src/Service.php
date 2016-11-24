<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2016-2018 Acme Products Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\App\Core;

use Cradle\Framework\App;
use Cradle\Sql\SqlFactory;

/**
 * Methods given the app that will return 3rd party services
 *
 * @vendor   Custom
 * @package  Component
 * @author   John Doe <john@acme.com>
 * @standard PSR-2
 */
class Service
{
    /**
     * @var App $app
     */
    protected $app = null;

    /**
     * Registers the app for use
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Returns main cache object
     *
     * @return object
     */
    public function cache()
    {
        return $this->custom('cache-main');
    }

    /**
     * Returns main database object
     *
     * @return object
     */
    public function database()
    {
        return SqlFactory::load($this->custom('sql-main'));
    }

    /**
     * Returns main index object
     *
     * @return object
     */
    public function index()
    {
        return $this->custom('index-main');
    }

    /**
     * Returns a component model
     *
     * @return object
     */
    public function model($name)
    {
        return $this->app->package('/app/core')->model($name);
    }

    /**
     * Returns a custom service
     *
     * @return object
     */
    public function custom($key)
    {
        return $this->app->package('global')->service($key);
    }
}
