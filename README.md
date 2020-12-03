# Assets Lazy Loader

Lazy loader for WordPress theme.

## Installation

Use composer.

```
composer require kunoichi/assets-lazy-loader
```

## Usage

Enable each services 1 by 1 in your `functions.php`.

```php
// in your functions.php
require __DIR__ . '/vendor/autoload.php';
```

### ImageLazyLoader

Filter all `img` tag in your HTML and add `loading="lazy"` attributes. If the `img` tag already has `loading` attribute, no more attribute will be added.

```php
// Enable image lazy loader.
Kunoichi\AssetsLazyLoader\ImageLazyLoader::enable();
// If you want exclude some image(e.g. Featured image)
// a filter hook is available.
add_filter( 'assets_lazy_loader_image', function( $should, $tag ) {
	return false !== strpos( $tag,  'size-post-thumbnail' );
}, 10, 2 );
```

### Deferred Scripts

Add `defer` attributes to JavaScripts enqueued with `wp_enqueue_script`.

```php
Kunoichi\AssetsLazyLoader\ScriptsDefer::enable( [
	'exclude'  => ['jquery-core'], // Only jQuery is not deferred.
	'in_login' => true, // Add defer on login screen. Default false.
	'in_admin' => true, // Same as above.
] );
```

Some JavaScripts have following scripts via `wp_add_inline_script`. This may cause critical erros. `ScriptDefer` skips enqueued scripts with `after` section, but for more safety, consider allow list approach.

```php
Kunoichi\AssetsLazyLoader\ScriptsDefer::enable( [
	// Defer scripts only which you know they are safe with defer attribute.
	'exclude'  => [ 'your-js', 'jquery' ], 
] );
```


### CSS Preload

Add `rel="preload"` to `link` tag and fallback scripts.

```php
Kunoichi\AssetsLazyLoader\StyleLoader::enable( [
	'exclude'  => StyleLoader::admin_critical( ['twentytwenty-style'] ), // Exclude default style and login/admin screen.
	'in_login' => true,
	'in_admin' => true,
] )
```

CSS preload caused non styled html in few seconds. To avoid shrinking of the screen by re-rendering, exclude critical css files from preload. In many case, it's the theme's main styelsheed.

`StyleLoader::admin_critical` is helpful for excluding ciritcal css in admin and login screen.

### jQuery Enhancement

The default jQuery bundled with WordPress has some issued.

- Version is old(1.12.4).
- Shipped with jQuery migrate unnecessory for sane plugins and themes.
- Enqueued in `head` tag.

You can assign other version of jQuery and drop `jquery-migrate`.

```php
JqueryOptimizer::enable( [
	'footer'  => true, // Move jQuery to footer.
	'src'     => 'https://code.jquery.com/jquery-3.5.1.slim.js', // Slim version from https://code.jquery.com/
	'version' => '3.5.1', // Specify collect version.
] );
```

## Ackowledgements

- CSS preload depends on [fg-loadcss v2.1.0](https://www.npmjs.com/package/fg-loadcss/v/2.1.0) by Filament Group.

## License

GPL 3.0 or later.
