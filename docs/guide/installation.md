# Installation

Install the package in an existing CakePHP application:

```sh
composer require dereuromark/cakephp-calendar
bin/cake plugin load Calendar
```

Composer checks the supported PHP and CakePHP versions. The [package manifest](https://github.com/dereuromark/cakephp-calendar/blob/master/composer.json) defines those requirements.

The plugin does not create tables or routes. The [getting started guide](./getting-started) uses your application's existing `Events` table with `id`, `title`, and `date` columns. Map other field names in the behavior configuration and template.
