<?php
/**
 * Extended_Template_Part class.
 *
 * @package ExtendedTemplateParts
 */

declare( strict_types=1 );

/**
 * Class Extended_Template_Part
 */
class Extended_Template_Part {

	/**
	 * Slug string.
	 *
	 * @var string
	 */
	public $slug = '';
	/**
	 * Name.
	 *
	 * @var string
	 */
	public $name = '';
	/**
	 * Arguments.
	 *
	 * @var array
	 */
	public $args = [];
	/**
	 * Vars.
	 *
	 * @var array
	 */
	public $vars = [];
	/**
	 * Template.
	 *
	 * @var null
	 */
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
	public function __construct( string $slug, string $name = '', array $vars = [], array $args = [] ) {

		$args = wp_parse_args( $args, [
			'cache' => false,
			'dir'   => 'template-parts',
		] );

		$this->slug = $slug;
		$this->name = $name;
		$this->args = $args;

		$this->set_vars( $vars );
	}

	/**
	 * Gets the output of the template part.
	 *
	 * @return string The template part output.
	 */
	public function get_output() : string {
		$output = ( false !== $this->args['cache'] ) ? $this->get_cache() : '';

		if ( $output ) {
			return $output;
		}

		ob_start();
		if ( $this->has_template() ) {
			$this->load_template( $this->locate_template() );
		}
		$output = ob_get_clean();

		if ( false !== $this->args['cache'] ) {
			$this->set_cache( $output );
		}

		return $output;

	}

	/**
	 * Is the requested template part available?
	 *
	 * @return bool Whether the template part is available.
	 */
	public function has_template() : bool {
		return ! ! $this->locate_template();
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
	protected function locate_template() : string {

		if ( isset( $this->template ) ) {
			return $this->template;
		}

		$templates = [];

		if ( ! empty( $this->name ) ) {
			$templates[] = "{$this->args['dir']}/{$this->slug}-{$this->name}.php";
		}

		$templates[] = "{$this->args['dir']}/{$this->slug}.php";

		$this->template = locate_template( $templates );

		if ( 0 !== validate_file( $this->template ) ) {
			$this->template = '';
		}
		return $this->template;

	}

	/**
	 * Load the template part.
	 *
	 * @param string $template_file The template part file path.
	 */
	protected function load_template( string $template_file ) {
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
	 * @return bool Whether the transient data was successfully stored.
	 */
	protected function set_cache( string $output ) : bool {
		return set_transient( $this->cache_key(), $output, $this->args['cache'] );
	}

	/**
	 * Get the template part cache key.
	 *
	 * @return string The cache key.
	 */
	protected function cache_key() : string {
		return 'part_' . md5( $this->locate_template() . '/' . wp_json_encode( $this->args ) );
	}

}
