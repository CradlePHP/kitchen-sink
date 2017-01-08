# kitchen-sink
A framework template to get a feel of things that are possible

## Install

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

## Documentation

 - See [https://cradlephp.github.io/](https://cradlephp.github.io/) for the official documentation.

## Packages

 - Also see [https://github.com/cblanquera/cradle-csrf](https://github.com/cblanquera/cradle-csrf)
 - Also see [https://github.com/cblanquera/cradle-captcha](https://github.com/cblanquera/cradle-captcha)
 - Also see [https://github.com/cblanquera/cradle-queue](https://github.com/cblanquera/cradle-queue)
 - Also see [https://github.com/cblanquera/cradle-handlebars](https://github.com/cblanquera/cradle-handlebars)
