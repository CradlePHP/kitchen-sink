# kitchen-sink
A framework template to get a feel of things that are possible

## Install

Issue the Composer create-project command in your terminal:

```
$ composer create-project -s dev cradlephp/kitchen-sink <project folder name>
```

Open `<project folder name>/config/services.php` and update the PDO connection
information with a new sandbox database (MySQL).

Then go cd `<project folder name>` and run the following.

```
$ vendor/bin/cradle package cblanquera/cradle-schema install
```

It will ask you which schema to install. type `app`.
Repeat this step for `profile`, `auth` and `file`

Then go `$ cd <project folder name>/public` and run the following.

```
$ bower install
$ php -S localhost:8000
```

Open your browser to `http://localhost:8000`

## Documentation

 - See [https://cradlephp.github.io/](https://cradlephp.github.io/) for the official documentation.

## Packages

 - Also see [https://github.com/cblanquera/cradle-schema](https://github.com/cblanquera/cradle-schema)
 - Also see [https://github.com/cblanquera/cradle-auth](https://github.com/cblanquera/cradle-auth)
 - Also see [https://github.com/cblanquera/cradle-file](https://github.com/cblanquera/cradle-file)
 - Also see [https://github.com/cblanquera/cradle-csrf](https://github.com/cblanquera/cradle-csrf)
 - Also see [https://github.com/cblanquera/cradle-captcha](https://github.com/cblanquera/cradle-captcha)
 - Also see [https://github.com/cblanquera/cradle-mail](https://github.com/cblanquera/cradle-mail)
 - Also see [https://github.com/cblanquera/cradle-queue](https://github.com/cblanquera/cradle-queue)
 - Also see [https://github.com/cblanquera/cradle-handlebars](https://github.com/cblanquera/cradle-handlebars)
