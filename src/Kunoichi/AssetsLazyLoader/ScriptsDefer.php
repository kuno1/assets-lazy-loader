<?php

namespace Kunoichi\AssetsLazyLoader;


use Kunoichi\AssetsLazyLoader\Pattern\HandleDetector;

/**
 * Defer JavaScripts
 *
 * @package assets-lazy-loader
 */
class ScriptsDefer extends HandleDetector {

	/**
	 * @var string[] Critical scripts not to be deferred.
	 */
	protected $critical_scripts = [ 'wp-i18n' ];

	/**
	 * Register filter hooks.
	 */
	protected function init() {
		if ( $this->in_admin || $this->in_login ) {
			// Avoid concatenation.
			if ( ! defined( 'CONCATENATE_SCRIPTS' ) ) {
				define( 'CONCATENATE_SCRIPTS', false );
			}
		}
		// Filter script loader.
		add_filter( 'script_loader_tag', [ $this, 'script_loader_tag' ], 9999, 2 );
	}

	/**
	 * Add defer to script tag.
	 *
	 * @param string $tag    Script tag.
	 * @param string $handle Handle of this script tag.
	 * @return string
	 */
	public function script_loader_tag( $tag, $handle ) {
		if ( ! $this->is_valid_handle( $handle ) ) {
			return $tag;
		}
		// Already having "defer" or "async", skip.
		foreach ( [ 'defer', 'async' ] as $key ) {
			if ( false !== strpos( $tag, ' ' . $key ) ) {
				return $tag;
			}
		}
		// If critical, skip.
		if ( in_array( $handle, $this->critical_scripts, true ) ) {
			return $tag;
		}
		// Having after script, skip.
		if ( $this->has_after( $handle ) ) {
			return $tag;
		}
		// Add defer.
		return str_replace( ' src=', ' defer src=', $tag );
	}

	/**
	 * If after script, skip.
	 *
	 * @param string $handle
	 * @return bool
	 */
	protected function has_after( $handle ) {
		global $wp_scripts;
		if ( ! isset( $wp_scripts->registered[ $handle ] ) ) {
			return false;
		}
		$script = $wp_scripts->registered[ $handle ];
		if ( ! empty( $script->extra['after'] ) ) {
			return true;
		}
		return false;
	}
}
