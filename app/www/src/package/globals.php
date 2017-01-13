<?php //-->
/**
 * This file is part of a Custom Project.
 * (c) 2017-2019 Acme Inc.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

/**
 * 404 and 500 page
 *
 * @param Request $request
 * @param Response $response
 * @param Throwable $error
 */
$cradle->error(function ($request, $response, $error) {
    return;
    //if this error has already been handled
    if ($response->hasContent()) {
        return;
    }

    //if it was a call for an actual file
    $path = $request->getPath('string');
    if (preg_match('/\.[a-zA-Z0-9]{1,4}$/', $path)) {
        return;
    }

    if ($response->getCode() === 404) {
        $body = cradle()->package('/app/www')->template('404');
        $class = 'page-404 page-error';
        $title = cradle('global')->translate('Oops...');

        //Set Content
        $response
            ->setPage('title', $title)
            ->setPage('class', $class)
            ->setContent($body);

        $this->trigger('render-web-page', $request, $response);

        return true;
    }

    $config = cradle('global')->config('settings');
    if ($config['environment'] === 'production' && $response->getCode() === 500) {
        $body = cradle()->package('/app/www')->template('500');
        $class = 'page-500 page-error';
        $title = cradle('global')->translate('Error');

        //Set Content
        $response
            ->setPage('title', $title)
            ->setPage('class', $class)
            ->setContent($body);

        $this->trigger('render-web-page', $request, $response);

        if (!isset($config['error_email'])
            || $config['error_email'] === '<EMAIL ADDRESS>'
        ) {
            return true;
        }

        $service = cradle('global')->service('mail-main');

        if (!$service) {
            return true;
        }

        //prepare data
        $from = [];
        $from[$service['user']] = $service['name'];

        $to = [];
        $to[$config['error_email']] = null;

        $exception = get_class($error);
        $message = $error->getMessage();
        $line = $error->getLine();
        $file = $error->getFile();
        $trace = $error->getTraceAsString();

        $body = sprintf(
            "%s thrown: %s\n%s(%s)\n\n%s",
            $exception,
            $message,
            $file,
            $line,
            $trace
        );

        //send mail
        $message = new Swift_Message('Salaaap - Error');
        $message->setFrom($from);
        $message->setTo($to);
        $message->addPart($body, 'text/plain');

        $transport = Swift_SmtpTransport::newInstance();
        $transport->setHost($service['host']);
        $transport->setPort($service['port']);
        $transport->setEncryption($service['type']);
        $transport->setUsername($service['user']);
        $transport->setPassword($service['pass']);

        $swift = Swift_Mailer::newInstance($transport);
        $swift->send($message, $failures);

        return true;
    }
});
