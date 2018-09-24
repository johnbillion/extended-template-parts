<?php
/**
 * Extended template parts for WordPress.
 *
 * @package   ExtendedTemplateParts
 * @version   1.1.1
 * @author    John Blackbourn <https://johnblackbourn.com>
 * @link      https://github.com/johnbillion/extended-template-parts
 * @copyright 2012-2018 John Blackbourn
 * @license   GPL v2 or later
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

/**
 * Require class
 */
require_once __DIR__ . '/class-extended-template-part.php';

/**
 * Output a template part.
 *
 * This function is functionally identical to `get_template_part()`. In addition, it allows you to pass in
 * variables for use in the template part using the `$vars` argument, which will then be available in the
 * `$this->vars` property from within the template part.
 *
 * @param string $slug The slug name for the generic template.
 * @param string $name The name of the specialised template.
 * @param array  $vars Variables for use within the template part.
 * @param array  $args {
 *     Arguments for the template part.
 *
 *     @type int|false $cache The number of seconds this template part should be cached for, or boolean false
 *                            for no caching. Default false.
 *     @type string    $dir   The theme subdirectory to look in for template parts. Default 'template-parts'.
 * }
 */
function get_extended_template_part( $slug, $name = '', array $vars = [], array $args = [] ) {
	$template = new Extended_Template_Part( $slug, $name, $vars, $args );
	$dir = $template->args['dir'];
	$dir_slug = "{$dir}/{$slug}";
	/* This action is documented in WordPress core: wp-includes/general-template.php */
	do_action( "get_template_part_{$dir_slug}", "{$dir_slug}", $name );
	echo $template->get_output(); // WPCS: XSS ok.
}
