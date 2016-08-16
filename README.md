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
$ vendor/bin/cradle install cblanquera/cradle-schema
```

It will ask you which schema to install. type `app`.
Repeat this step for `profile`, `auth` and `file`

Then go `$ cd <project folder name>/public` and run the following.

```
$ bower install
$ php -S localhost:8000
```
