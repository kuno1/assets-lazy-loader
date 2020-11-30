<?php

namespace Kunoichi\AssetsLazyLoader\Pattern;

/**
 * Abstract Singleton pattern.
 *
 * @package assets-lazy-loader
 * @property-read bool $in_admin
 * @property-read bool $in_login
 */
abstract class Singleton {

	/**
	 * @var static[] Instance holder.
	 */
	private static $instances = [];

	/**
	 * Setting.
	 *
	 * @var array
	 */
	protected $setting = [];

	/**
	 * Parse args before setting.
	 */
	protected function parse_args( $args ) {
		return wp_parse_args( $args, [
			'in_admin' => false,
			'in_login' => false,
		] );
	}

	/**
	 * Singleton constructor.
	 *
	 * @param array $setting
	 */
	final protected function __construct( $setting = [] ) {
		$this->setting = $this->parse_args( $setting );
		$this->init();
	}

	/**
	 * Do something in constructor.
	 */
	protected function init() {
		// Do something in constructor.
	}

	/**
	 * Detect if this is login page.
	 *
	 * @return bool
	 */
	protected function is_login() {
		return isset( $_SERVER['SCRIPT_FILENAME'] ) && ( 'wp-login.php' === basename( $_SERVER['SCRIPT_FILENAME'] ) );
	}

	/**
	 * Enable service.
	 *
	 * @param array $setting
	 */
	public static function enable( $setting = [] ) {
		$class_name = get_called_class();
		if ( ! isset( self::$instances[ $class_name ] ) ) {
			self::$instances[ $class_name ] = new $class_name( $setting );
		}
	}

	/**
	 * Getter
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function __get( $name ) {
		if ( isset( $this->setting[ $name ] ) ) {
			return $this->setting[ $name ];
		} else {
			return null;
		}
	}


}
