# CakePHP Calendar plugin

[![CI](https://github.com/dereuromark/cakephp-calendar/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/dereuromark/cakephp-calendar/actions/workflows/ci.yml?query=branch%3Amaster)
[![Coverage Status](https://codecov.io/gh/dereuromark/cakephp-calendar/branch/master/graph/badge.svg)](https://codecov.io/gh/dereuromark/cakephp-calendar)
[![Latest Stable Version](https://poser.pugx.org/dereuromark/cakephp-calendar/v/stable.svg)](https://packagist.org/packages/dereuromark/cakephp-calendar)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/dereuromark/cakephp-calendar/license.svg)](LICENSE)
[![Total Downloads](https://poser.pugx.org/dereuromark/cakephp-calendar/d/total.svg)](https://packagist.org/packages/dereuromark/cakephp-calendar)
[![Coding Standards](https://img.shields.io/badge/cs-PSR--2--R-yellow.svg)](https://github.com/php-fig-rectified/fig-rectified-standards)

A plugin to render simple calendars.

This branch is for **CakePHP 5.1+**. For details see [version map](https://github.com/dereuromark/cakephp-calendar/wiki#cakephp-version-map).

## Features
- Simple and robust
- No JS needed, more responsive than solutions like fullcalendar
- Persistent `year/month` URL pieces (copy-paste and link/redirect friendly)
- IcalView class for `.ics` calendar file output.

## Demo
See the demo [Calendar example](https://sandbox.dereuromark.de/sandbox/calendar) at the sandbox.

## Setup
```
composer require dereuromark/cakephp-calendar
```

Then make sure the plugin is loaded in bootstrap:
```
bin/cake plugin load Calendar
```

## Usage
See [Documentation](/docs).
