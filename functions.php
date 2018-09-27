<?php
/**
 * Template functions.
 *
 * @package ExtendedTemplateParts
 */

declare( strict_types=1 );

/**
 * Outputs a template part.
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
 * Require class
 */
function get_extended_template_part( string $slug, string $name = '', array $vars = [], array $args = [] ) {
	$template = new Extended_Template_Part( $slug, $name, $vars, $args );
	$dir      = $template->args['dir'];
	$dir_slug = "{$dir}/{$slug}";

	/* This action is documented in WordPress core: wp-includes/general-template.php */
	do_action( "get_template_part_{$dir_slug}", "{$dir_slug}", $name );

	echo $template->get_output(); // WPCS: XSS ok.
}
