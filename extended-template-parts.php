<?php
/*
Copyright © 2012-2015 John Blackbourn

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

class Amsterdam_Section_Template {

	public $vars = array();

	public function __construct( $slug, $name = null, $args = null ) {

		if ( is_numeric( $args ) ) {
			$args = array(
				'cache_timeout' => $args
			);
		} else {
			$args = (array) $args;
		}

		if ( isset( $args['cache_timeout'] ) )
			$args['cache'] = true;

		$args = wp_parse_args( $args, array(
			'cache'         => false,
			'cache_timeout' => 0,
			'dir'           => 'sections',
		) );

		$this->slug = $slug;
		$this->name = $name;
		$this->args = $args;

		if ( isset( $args['vars'] ) and is_array( $args['vars'] ) )
			$this->set_vars( $args['vars'] );

	}

	public function output_template() {

		if ( $this->args['cache'] )
			echo $this->get_template();
		else
			$this->load_template( $this->locate_template() );

		return $this->locate_template();

	}

	public function get_template() {

		if ( !$args['cache'] or !$template = $this->get_cache() ) {

			ob_start();
			$this->load_template( $this->locate_template() );
			$template = ob_get_clean();

			if ( $args['cache'] )
				$this->set_cache( $template );

		}

		return $template;

	}

	public function has_template() {
		return !! $this->locate_template();
	}

	public function set_vars( array $vars ) {
		$this->vars = array_merge( $this->vars, $vars );
	}

	protected function locate_template() {

		if ( isset( $this->template ) )
			return $this->template;

		$templates = $context = array();

		if ( !empty( $this->name ) )
			$context[] = "{$this->slug}-{$this->name}.php";

		$context[] = "{$this->slug}.php";

		foreach ( $context as $file ) {
			foreach ( template_bases() as $base )
				$templates[] = ltrim( "{$base}/{$this->args['dir']}/{$file}", '/' );
		}

		$located = false;

		foreach ( $templates as $template_name ) {

			if ( file_exists( get_stylesheet_directory() . '/' . $template_name ) ) {
				$located = get_stylesheet_directory() . '/' . $template_name;
				break;
			} else if ( file_exists( get_template_directory() . '/' . $template_name ) ) {
				$located = get_template_directory() . '/' . $template_name;
				break;
			}
		}

		return $this->template = $located;

	}

	protected function load_template( $template_file ) {

		global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;

		if ( is_array( $wp_query->query_vars ) )
			extract( $wp_query->query_vars, EXTR_SKIP );

		require $template_file;

	}

	function get_cache() {
		return get_transient( $this->cache_key() );
	}

	function set_cache( $section ) {
		return set_transient( $this->cache_key(), $section, $this->args['cache_timeout'] );
	}

	protected function cache_key() {
		return 'section_' . md5( $this->locate_template() . '/' . serialize( $this->args ) );
	}

}
