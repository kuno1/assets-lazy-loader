<?php

namespace Kunoichi\AssetsLazyLoader;


use Kunoichi\AssetsLazyLoader\Pattern\HandleDetector;

/**
 * Style loader.
 *
 * @package assets-lazy-loader
 */
class StyleLoader extends HandleDetector {

	/**
	 * Get admin critical files.
	 *
	 * @param string[] $extra Add admin critical css to $extra styles.
	 * @return string[]
	 */
	public static function admin_critical( $extra = [] ) {
		return array_merge( [ 'login', 'common', 'admin-bar', 'admin-menu', 'dashboard' ], $extra );
	}

	/**
	 * Constructor.
	 */
	protected function init() {
		add_filter( 'style_loader_tag', [ $this, 'style_loader_tag' ], 9999, 4 );
	}

	/**
	 * Change style loader tag.
	 *
	 * @param string $tag    HTML tag.
	 * @param string $handle Handle name.
	 * @param string $href   URL of CSS.
	 * @param string $media  Media attribute.
	 *
	 * @return string
	 */
	public function style_loader_tag( $tag, $handle, $href, $media ) {
		if ( ! $this->is_valid_handle( $handle ) ) {
			return $tag;
		}
		// If already preload or print, skip.
		if ( ( 'print' === $media ) || false !== strpos( $tag, 'preload' ) ) {
			return $tag;
		}
		// Change stylesheet.
		$html ='<link id="%1$s" rel="stylesheet" href="%2$s" onload="this.onload=null;this.media=\'%3$s\'" media="print" />';
		return sprintf(
			$html,
			esc_attr( $handle . '-css' ),
			esc_url( $href ),
			esc_attr( $media )
		);
	}
}
