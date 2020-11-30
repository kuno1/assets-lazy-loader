<?php

namespace Kunoichi\AssetsLazyLoader;


use Kunoichi\AssetsLazyLoader\Pattern\Singleton;

/**
 * Style loader.
 *
 * @package assets-lazy-loader
 * @property-read string[] $exclude
 */
class StyleLoader extends Singleton {

	/**
	 * @var bool If set preload at least 1, enable.
	 */
	protected $preloaded = false;

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
	 * Add extra arguments.
	 *
	 * @param array $args
	 * @return array
	 */
	protected function parse_args( $args ) {
		return array_merge( [
			'exclude' => [],
		], parent::parse_args( $args ) );
	}

	/**
	 * Constructor.
	 */
	protected function init() {
		add_filter( 'style_loader_tag', [ $this, 'style_loader_tag' ], 9999, 4 );
		add_action( 'wp_head', [ $this, 'preload_helper' ], 100 );
		if ( $this->in_login ) {
			add_action( 'login_head', [ $this, 'preload_helper' ], 100 );
		}
		if ( $this->in_admin ) {
			add_action( 'admin_head', [ $this, 'preload_helper' ], 100 );
		}
	}

	/**
	 * Render Helper JS if enabled.
	 */
	public function preload_helper() {
		if ( ! $this->preloaded ) {
			return;
		}
		$js = $this->dir . '/dist/fg-loadcss/cssrelpreload.min.js';
		if ( ! file_exists( $js ) ) {
			return;
		}
		printf( "<script>\n%s\n</script>", file_get_contents( $js ) );
	}

	/**
	 * @param string $tag
	 * @param string $handle
	 * @param string $href
	 * @param string $media
	 *
	 * @return string
	 */
	public function style_loader_tag( $tag, $handle, $href, $media ) {
		if ( ( ! $this->in_admin ) && is_admin() ) {
			// If not in admin, skip.
			return $tag;
		}
		if ( ( ! $this->in_login ) && $this->is_login() ) {
			// If not in login, skip.
			return $tag;
		}
		if ( in_array( $handle, (array) $this->exclude, true ) ) {
			return $tag;
		}
		// If already preload or print, skip.
		if ( ( 'print' === $media ) || false !== strpos( $tag, 'preload' ) ) {
			return $tag;
		}
		// Change stylesheet.
		$this->preloaded = true;
		$html = <<<'HTML'
<link rel="preload" id="%1$s" href="%2$s" as="style" onload="this.onload=null;this.rel='stylesheet'" media="%4$s" />
<noscript>
	%3$s
</noscript>
HTML;
		return sprintf(
			$html,
			esc_attr( $handle . '-css' ),
			esc_url( $href ),
			$tag,
			esc_attr( $media )
		);
	}


}

