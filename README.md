[![Stable Release](https://img.shields.io/packagist/v/johnbillion/extended-template-parts.svg)](https://packagist.org/packages/johnbillion/extended-template-parts)
[![License](https://img.shields.io/badge/license-GPL_v2%2B-blue.svg)](https://github.com/johnbillion/extended-template-parts/blob/master/LICENSE)

# Extended Template Parts

Extended CPTs is a library which provides extended functionality to WordPress template parts, including template variables and caching.

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

## Usage ##

The `get_extended_template_part()` function behaves exactly like [WordPress' `get_template_part()` function](https://developer.wordpress.org/reference/functions/get_template_part/), except it loads the template part from the `template-parts` subdirectory of the theme for better file organisation. The usual parent/child theme hierarchy is of course respected. In addition, it accepts two optional parameters for passing variables into the template part, and for passing arguments to the function.

```php
get_extended_template_part( 'foo', 'bar' );
```

The above code behaves exactly the same as `get_template_part()`, except it loads the template part from the `template-parts` subdirectory. To pass in variables to the template part, to change the subdirectory that's used, and to automatically cache the output for an hour in a transient, try the following code:

```php
get_extended_template_part( 'foo', 'bar', [
	'my_variable' => 'Hello, world!',
], [
	'dir'   => 'my_directory',
	'cache' => 3600,
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
