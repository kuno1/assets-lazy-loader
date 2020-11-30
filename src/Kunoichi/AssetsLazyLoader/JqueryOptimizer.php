<?php

namespace Kunoichi\AssetsLazyLoader;

use Kunoichi\AssetsLazyLoader\Pattern\Singleton;

/**
 * Optimize jQuery
 *
 * @package assets-lazy-loader
 * @property-read bool   $footer  If move jQuery to footer.
 * @property-read string $src     If set, change jQuery path to this.
 * @property-read string $version If set, change jQuery version.
 */
class JqueryOptimizer extends Singleton {

	/**
	 * Constructor.
	 */
	protected function init() {
		// Change jQuery path.
		add_action( 'init', [ $this, 'enhance_jQuery' ], 1 );
		$this->setting = wp_parse_args( $this->setting );
	}

	/**
	 * Add extra args.
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	protected function parse_args( $args ) {
		return array_merge( [
			'footer'  => true,
			'src'     => '',
			'version' => '',
		], $args );
	}

	/**
	 * Change jQuery to
	 */
	public function enhance_jquery() {
		// Do nothing on admin screen.
		// It's prohibit to remove jQuery.
		if ( is_admin() || $this->is_login() ) {
			return;
		}
		// Save current version.
		global $wp_scripts;
		$jquery     = $wp_scripts->registered['jquery-core'];
		// Set jQuery version and src if specified.
		// If not set, use default.
		$jquery_ver = $this->version ?: $jquery->ver;
		$jquery_src = $this->src ?: $jquery->src;
		// Flag to move_footer.
		$move_jquery_to_footer = (bool) $this->footer;
		// Remove existing.
		wp_deregister_script( 'jquery' );
		wp_deregister_script( 'jquery-core' );
		// Register them again.
		wp_register_script( 'jquery', false, [ 'jquery-core' ], $jquery_ver, $move_jquery_to_footer );
		wp_register_script( 'jquery-core', $jquery_src, [], $jquery_ver, $move_jquery_to_footer );
	}
}
