<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\CommandLine\Index as CommandLine;

/**
 * CLI project starting point
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
return function ($request, $response) {
    $event = 'help';

    if($request->hasStage(0)) {
        $event = $request->getStage(0);
        $request->removeStage(0);
    }


    if($request->hasStage()) {
        $data = [];
        $stage = $request->getStage();
        foreach($stage as $key => $value) {
            if(!is_numeric($key)) {
                $data[$key] = $value;
            } else {
                $data[$key - 1] = $value;
            }

            $request->removeStage($key);
        }

        $request->setStage($data);
    }

    $this->trigger('project-' . $event, $request, $response);
};
