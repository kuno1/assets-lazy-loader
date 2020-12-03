<?php

namespace Kunoichi\AssetsLazyLoader\Pattern;

/**
 * Handle detector
 *
 * @package assets-lazy-loader
 * @property-read string[] $exclude
 * @property-read string[] $include
 */
abstract class HandleDetector extends Singleton {

	/**
	 * Add extra arguments.
	 *
	 * @param array $args
	 * @return array
	 */
	protected function parse_args( $args ) {
		return array_merge( [
			'exclude' => [],
			'include' => [],
		], parent::parse_args( $args ) );
	}

	/**
	 * Check if handle is avilable.
	 *
	 * @param string $handle
	 * @return bool
	 */
	protected function is_valid_handle( $handle ) {
		if ( ( ! $this->in_admin ) && is_admin() ) {
			// If not in admin, skip.
			return false;
		}
		if ( ( ! $this->in_login ) && $this->is_login() ) {
			// If not in login, skip.
			return false;
		}
		if ( $this->include ) {
			// Allow list exists, check if it's included.
			return in_array( $handle, (array) $this->include, true );
		} elseif ( $this->exclude ) {
			// Deny list exists, check if it's included.
			return ! in_array( $handle, (array) $this->exclude, true );
		} else {
			return true;
		}
	}

}
