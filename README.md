[![Build Status](https://travis-ci.org/johnbillion/extended-template-parts.svg?branch=master)](https://travis-ci.org/johnbillion/extended-template-parts)
[![Stable Release](https://img.shields.io/packagist/v/johnbillion/extended-template-parts.svg)](https://packagist.org/packages/johnbillion/extended-template-parts)
[![License](https://img.shields.io/badge/license-GPL_v2%2B-blue.svg)](https://github.com/johnbillion/extended-template-parts/blob/master/LICENSE)
![PHP 7](https://img.shields.io/badge/php-7-blue.svg)

# Extended Template Parts

Extended Template Parts is a library which provides extended functionality to WordPress template parts, including template variables and fragment caching.

## Features ##

 * Pass variables into your template parts and access them via the `$this->vars` array. No polluting of globals!
 * Easy optional caching of template parts using transients.

## Minimum Requirements ##

**PHP:** 7.0  
**WordPress:** 4.4  

## Installation ##

Extended Template Parts is a developer library, not a plugin, which means you need to include it somewhere in your own project.
You can use Composer:

```bash
composer require johnbillion/extended-template-parts
```

Or you can download the library and include it manually:

```php
require_once 'extended-template-parts/extended-template-parts.php';
```

## Basic Usage ##

The `get_extended_template_part()` function behaves exactly like [WordPress' `get_template_part()` function](https://developer.wordpress.org/reference/functions/get_template_part/), except it loads the template part from the `template-parts` subdirectory of the theme for better file organisation. The usual parent/child theme hierarchy is respected.

```php
get_extended_template_part( 'foo', 'bar' );
```

Use the `$vars` parameter to pass in an associative array of variables to the template part:

```php
get_extended_template_part( 'foo', 'bar', [
	'my_variable' => 'Hello, world!',
] );
```

In your `template-parts/foo-bar.php` template part file, you can access the variables that you passed in by using `$this->vars`:

```php
echo esc_html( $this->vars['my_variable'] );
```

## Advanced Usage ##

The `get_extended_template_part()` function also accepts a second optional parameter that controls the directory name and caching.

The following code will load `foo-bar.php` from the `my-directory` subdirectory and automatically cache its output in a transient for one hour:

```php
get_extended_template_part( 'foo', 'bar', [
	'my_variable' => 'Hello, world!',
], [
	'dir'   => 'my-directory',
	'cache' => 1 * HOUR_IN_SECONDS,
] );
```

## License: GPLv2 or later ##

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
