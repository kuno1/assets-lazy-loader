<?php

namespace Kunoichi\AssetsLazyLoader;


use Kunoichi\AssetsLazyLoader\Pattern\Singleton;

/**
 * Lazy load all images.
 *
 * @package Kunoichi\AssetsLazyLoader
 */
class ImageLazyLoader extends Singleton {

	/**
	 * Constructor
	 */
	protected function init() {
		add_action( 'wp_body_open', [ $this, 'body_open' ], 1 );
		add_action( 'wp_footer', [ $this, 'body_close' ], 9999 );
	}

	/**
	 * Start buffering
	 */
	public function body_open() {
		ob_start();
	}

	/**
	 * Replace whole body contents.
	 */
	public function body_close() {
		$content = preg_replace_callback( '#<img([^>]+)>#u', function( $matches ) {
			list( $match, $attr ) = $matches;
			// If already set loading attributes, skip.
			if ( false !== strpos( $attr, 'loading=' ) ) {
				return $match;
			}
			// If this image shouldn't be lazy, skip.
			if ( ! $this->test_image( $match ) ) {
				return $match;
			}
			$attr = ' loading="lazy"' . $attr;
			return sprintf( '<img%s>', $attr );
		}, ob_get_contents() );
		ob_end_clean();
		echo $content;
	}

	/**
	 * Detect if image should be loading=lazy
	 *
	 * @param string $tag HTML tag.
	 * @return bool
	 */
	protected function test_image( $tag ) {
		return (bool) apply_filters( 'assets_lazy_loader_image', true, $tag );
	}
}
