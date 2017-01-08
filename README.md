# The Kitchen Sink
A framework template to get a feel of things that are possible

## Install Framework

Issue the Composer create-project command in your terminal:

```
$ composer create-project -s dev cradlephp/kitchen-sink <project folder name>
```

Then go cd `<project folder name>` and run the following and and follow the wizard to install.

```
$ bin/cradle faucet install
$ bower install
$ bin/cradle faucet server -h 127.0.0.1 -p 8888
```

Then go `$ cd <project folder name>/public` and run the following.

Open your browser to `http://127.0.0.1:8888`

## Code Generators

Code Generators are used to help layout a project faster and used generically
written code infrastructure and logic to allow you to focus on custom business rules.

For these instructions we will install a generic post module where its definition
is found in `<project folder name>/schema/post.php`.

### Generate Module

First run the following command.

```
$ bin/cradle faucet generate-module --schema post
```

This will convert the data file found in `<project folder name>/schema/post.php`
to a code set called a `module` that can be generically used through out your project.
It's important to also run `composer update` right after.

```
$ composer update
```

### Generate SQL

The next command will generate SQL files within the module folder and
then install to your database using the versioning updater built in.

```
$ bin/cradle faucet generate-sql --schema post
```

Optionally if we want to populate the SQL we can use the following command.

```
$ bin/cradle faucet populate-sql --module post
```

### Generate Controllers

If you want to auto generate an admin for the `post` we can do so with the following command.

```
$ bin/cradle faucet generate-admin --schema post
```

Like wise with a REST controller, it can be done with the following.

```
$ bin/cradle faucet generate-rest --schema post
```

## Documentation

 - See [https://cradlephp.github.io/](https://cradlephp.github.io/) for the official documentation.

## Packages

 - Also see [https://github.com/cblanquera/cradle-csrf](https://github.com/cblanquera/cradle-csrf)
 - Also see [https://github.com/cblanquera/cradle-captcha](https://github.com/cblanquera/cradle-captcha)
 - Also see [https://github.com/cblanquera/cradle-queue](https://github.com/cblanquera/cradle-queue)
 - Also see [https://github.com/cblanquera/cradle-handlebars](https://github.com/cblanquera/cradle-handlebars)
