<?php

namespace Kunoichi\AssetsLazyLoader;


use Kunoichi\AssetsLazyLoader\Pattern\Singleton;

/**
 * Defer JavaScripts
 *
 * @package assets-lazy-loader
 * @property-read string[] $exclude
 */
class ScriptsDefer extends Singleton {

	/**
	 * Override parser
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	protected function parse_args( $args ) {
		return array_merge( [
			'exclude' => [],
		], parent::parse_args( $args ) );
	}

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
		if ( ! $this->in_admin && is_admin() ) {
			// This is admin and not in admin.
			return;
		}
		if ( ! $this->in_login && $this->is_login() ) {
			// This is login screen and not in login.
			return;
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
		// If this is excluded tag, skip.
		if ( in_array( $handle, (array) $this->exclude, true ) ) {
			return $tag;
		}
		// Already having "defer" or "async", skip.
		foreach ( [ 'defer', 'async' ] as $key ) {
			if ( false !== strpos( $tag, ' ' . $key ) ) {
				return $tag;
			}
		}
		// Add defer.
		return str_replace( ' src=', ' defer src=', $tag );
	}
}
