<?php
/**
 * Extended template parts for WordPress.
 *
 * @package   ExtendedTemplateParts
 * @version   1.0.1
 * @author    John Blackbourn <https://johnblackbourn.com>
 * @link      https://github.com/johnbillion/extended-template-parts
 * @copyright 2012-2016 John Blackbourn
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
	echo $template->get_output(); // WPCS: XSS ok.
}

class Extended_Template_Part {

	public $slug = '';
	public $name = '';
	public $args = [];
	public $vars = [];
	protected $template = null;

	/**
	 * Class constructor.
	 *
	 * @see get_extended_template_part()
	 *
	 * @param string $slug The slug name for the generic template.
	 * @param string $name The name of the specialised template.
	 * @param array  $vars Variables for use within the template part.
	 * @param array  $args Arguments for the template part.
	 */
	public function __construct( $slug, $name = '', array $vars = [], array $args = [] ) {

		$args = wp_parse_args( $args, array(
			'cache' => false,
			'dir'   => 'template-parts',
		) );

		$this->slug = $slug;
		$this->name = $name;
		$this->args = $args;

		$this->set_vars( $vars );

	}

	/**
	 * Get the output of the template part.
	 *
	 * @return string The template part output.
	 */
	public function get_output() {

		if ( false === $this->args['cache'] || ! $output = $this->get_cache() ) {

			ob_start();
			if ( $this->has_template() ) {
				$this->load_template( $this->locate_template() );
			}
			$output = ob_get_clean();

			if ( false !== $this->args['cache'] ) {
				$this->set_cache( $output );
			}

		}

		return $output;

	}

	/**
	 * Is the requested template part available?
	 *
	 * @return boolean Whether the template part is available.
	 */
	public function has_template() {
		return !! $this->locate_template();
	}

	/**
	 * Set the variables available to this template part.
	 *
	 * @param array $vars Template variables.
	 */
	public function set_vars( array $vars ) {
		$this->vars = array_merge( $this->vars, $vars );
	}

	/**
	 * Locate the template part file according to the slug and name.
	 *
	 * @return string The template part file name. Empty string if none is found.
	 */
	protected function locate_template() {

		if ( isset( $this->template ) ) {
			return $this->template;
		}

		$templates = [];

		if ( ! empty( $this->name ) ) {
			$templates[] = "{$this->args['dir']}/{$this->slug}-{$this->name}.php";
		}

		$templates[] = "{$this->args['dir']}/{$this->slug}.php";

		$this->template = locate_template( $templates );

		if ( 0 !== validate_file( $template ) ) {
			$this->template = '';
		}
		return $this->template;

	}

	/**
	 * Load the template part.
	 *
	 * @param  string $template_file The template part file path.
	 */
	protected function load_template( $template_file ) {
		global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;
		if ( 0 !== validate_file( $template_file ) ) {
			return;
		}
		require $template_file;
	}

	/**
	 * Get the cached version of the template part output.
	 *
	 * @return string|false The cached output, or boolean false if there is no cached version.
	 */
	protected function get_cache() {
		return get_transient( $this->cache_key() );
	}

	/**
	 * Cache the template part output.
	 *
	 * @param string $output The template part output.
	 */
	protected function set_cache( $output ) {
		return set_transient( $this->cache_key(), $output, $this->args['cache'] );
	}

	/**
	 * Get the template part cache key.
	 *
	 * @return string The cache key.
	 */
	protected function cache_key() {
		return 'part_' . md5( $this->locate_template() . '/' . wp_json_encode( $this->args ) );
	}

}
