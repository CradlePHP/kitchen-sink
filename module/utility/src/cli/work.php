<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\CommandLine\Index as CommandLine;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;

/**
 * CLI starts worker
 *
 * @param Request $request
 * @param Response $response
 */
return function ($request, $response) {
    static $channel = null;

    //get the channel
    if (is_null($channel)) {
        //add a logger
        $this->addLogger(function ($message) {
            echo '[cradle] ' . $message . PHP_EOL;
        });

        $channel = $this
            ->package('global')
            ->service('rabbitmq-main')
            ->channel();
    }

    //get the queue name
    $settings = $this->package('global')->config('settings');
    $name = 'queue';
    if (isset($settings['queue']) && trim($settings['queue'])) {
        $name = $settings['queue'];
    }

    // notify its up
    $this->log('Waiting for tasks.');

    // define the job
    $job = function ($message) use ($name, $request, $response) {
        // notify once a task is received
        $this->log('A task is received.');

        // get the data
        $data = json_decode($message->body, true);

        // extract the job to perform
        if (!isset($data['__TASK__'])) {
            // once an exception is encountered, notify that task is not done
            $this->log('Task is not done.');

            // set or flag that the task is not done and the worker is free
            $message
                ->delivery_info['channel']
                ->basic_nack($message->delivery_info['delivery_tag']);

            // set or flag that the task is not done and the worker is free and requeue task
            //$message->delivery_info['channel']->basic_nack($message->delivery_info['delivery_tag'], false, true);
        }

        $task = $data['__TASK__'];
        unset($data['__TASK__']);

        try {
            //start
            $this->log($task . ' is running');

            $request->setStage($data);

            $this->triggerEvent($task, $request, $response);

            //if there was an error
            if ($response->get('json', 'error')) {
                $error = $response->getDot('json.message');

                $this->log('Task is not done.');
                $this->log($error);
                $this->log(json_encode($data));

                //an exception didn't trigger
                //it just refused to do it
                //so why try it again ?
            } else {
                $this->log($task . ' was performed');
                $this->log(json_encode($data));

                // once done, notify again, that it is done
                $this->log('Task is done.');
            }

            // set or flag that the worker is free
            $message
                ->delivery_info['channel']
                ->basic_ack($message->delivery_info['delivery_tag']);
        } catch (Throwable $e) {
            // once an exception is encountered, notify that task is not done
            $this->log('Task is not done.');
            $this->log($e->getMessage());

            // set or flag that the task is not done and the worker is free
            $message
                ->delivery_info['channel']
                ->basic_nack($message->delivery_info['delivery_tag']);
        }
    };

    // worker consuming tasks from queue
    $channel->basic_qos(null, 1, null);

    // now we need to catch the channel exception
    // when task does not exists in our queue
    try {
        // comsume messages on queue
        $channel->basic_consume(
            $name,
            '',
            false,
            false,
            false,
            false,
            $job->bindTo($this)
        );
    } catch (AMQPProtocolChannelException $e) {
        // notify that task does not exists
        $this->log('Task does not exists, creating task. Please re-run the worker.');

        // create the init queue
        $this->package('global')->queue('init');
    }

    while (count($channel->callbacks)) {
        $channel->wait();
    }
};
