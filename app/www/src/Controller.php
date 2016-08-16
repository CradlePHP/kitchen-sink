<?php //-->
/**
 * This file is part of the Cradle PHP Library.
 * (c) 2016-2018 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\App\Www;

use Cradle\App\Www\Action\Template;

use Cradle\Framework\App;
use Cradle\Framework\FlowTrait;

/**
 * Factory for model related flows
 *
 * @vendor   Cradle
 * @package  Framework
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Controller
{
    use FlowTrait {
        FlowTrait::__callFlow as __call;
        FlowTrait::__getFlow as __get;
    }

    /**
     * Sets the app and model
     */
    public function __construct(App $app)
    {
        $this->actions['template'] = $this->resolve(Template::class, $app);
    }

}
