# Installation

This development branch targets CakePHP 6 and PHP 8.5. Install it in a CakePHP 6 application:

```sh
composer config minimum-stability dev
composer config prefer-stable true
composer require cakephp/cakephp:^6.0@dev dereuromark/cakephp-calendar:dev-cake6
bin/cake plugin load Calendar
```

Composer checks the supported PHP and CakePHP versions. The [package manifest](https://github.com/dereuromark/cakephp-calendar/blob/cake6/composer.json) defines those requirements.

The plugin does not create tables or routes. The [getting started guide](./getting-started) uses your application's existing `Events` table with `id`, `title`, and `date` columns. Map other field names in the behavior configuration and template.
