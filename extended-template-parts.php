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

class Extended_Template_Part {

	public $slug = '';
	public $name = '';
	public $args = array();
	public $vars = array();
	protected $template = null;

	public function __construct( $slug, $name = '', array $args = array() ) {

		$args = wp_parse_args( $args, array(
			'cache' => false,
			'dir'   => 'template-parts',
		) );

		$this->slug = $slug;
		$this->name = $name;
		$this->args = $args;

		if ( isset( $args['vars'] ) && is_array( $args['vars'] ) ) {
			$this->set_vars( $args['vars'] );
		}

	}

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

	public function has_template() {
		return !! $this->locate_template();
	}

	public function set_vars( array $vars ) {
		$this->vars = array_merge( $this->vars, $vars );
	}

	protected function locate_template() {

		if ( isset( $this->template ) ) {
			return $this->template;
		}

		$templates = array();

		if ( ! empty( $this->name ) ) {
			$templates[] = "{$this->args['dir']}/{$this->slug}-{$this->name}.php";
		}

		$templates[] = "{$this->args['dir']}/{$this->slug}.php";

		return $this->template = locate_template( $templates );

	}

	protected function load_template( $template_file ) {
		require $template_file;
	}

	protected function get_cache() {
		return get_transient( $this->cache_key() );
	}

	protected function set_cache( $output ) {
		return set_transient( $this->cache_key(), $output, $this->args['cache'] );
	}

	protected function cache_key() {
		return 'part_' . md5( $this->locate_template() . '/' . serialize( $this->args ) );
	}

}
