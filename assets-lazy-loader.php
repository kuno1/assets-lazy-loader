<?php
/**
 * Plugin Name: Assets Lazy Loader
 * Plugin URI:  https://github.com/kuno1/assets-lazy-loader
 * Description: Enhance assets loading for WordPress theme.
 * Version:     0.0.0
 * Author:      Kunoichi INC.
 * Author URI:  https://kunoichiwp.com
 * License:     GPLv3 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-3.0.html
 * Text Domain: assets-lazy-loader
 * Domain Path: /languages
 */

namespace Kunoichi\AssetsLazyLoader;

// This file actually do nothing.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}

// Load autoloader.
require __DIR__ . '/vendor/autoload.php';

// Move jQuery to footer and change version 3.
JqueryOptimizer::enable( [
	'footer'  => true,
	'src'     => 'https://code.jquery.com/jquery-3.5.1.slim.js', // Slim version from https://code.jquery.com/
	'version' => '3.5.1',
] );

// Enqueue jQuery
add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_script( 'jquery' );
} );

// Defer JS
ScriptsDefer::enable( [
	'exclude'  => 'jquery-core', // Should be array, but specifying string for fallback check.
	'in_login' => false,
] );

// Style defer.
StyleLoader::enable( [
	'exclude'  => StyleLoader::admin_critical( ['twentytwenty-style'] ), // Exclude default style.
	'in_login' => true,
	'in_admin' => true,
] );

// Lazy load images.
ImageLazyLoader::enable();
// If eye catch, they shouldn't be lazy.
add_filter( 'assets_lazy_loader_image', function( $should, $tag ) {
	return false !== strpos( $tag,  'size-post-thumbnail' );
}, 10, 2 );
