<?php
/**
 *
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @since   2019-06-01
 * @package WebDevStudios\WPInstaller
 */

namespace JMichaelWard\WPInstaller;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

/**
 * Class Plugin
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @since   2019-06-01
 * @package WebDevStudios\WPInstaller
 */
class Plugin implements PluginInterface, EventSubscriberInterface {
	/**
	 * The dir path of the composer execution.
	 *
	 * @var string
	 * @since 2019-06-01
	 */
	private $dir;

	/**
	 * The vendor directory path.
	 *
	 * @var string
	 * @since 2019-06-01
	 */
	private $vendor_dir;

	/**
	 * Required by the PluginInterface to make plugin modifications to composer.
	 *
	 * @param Composer    $composer
	 * @param IOInterface $io
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-06-01
	 * @return void
	 */
	public function activate( Composer $composer, IOInterface $io ) {
		$this->dir        = exec( 'pwd' );
		$this->vendor_dir = dirname( __DIR__, 3 );
	}

	/**
	 * Get Composer subscribed events.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-06-01
	 * @return array
	 */
	public static function getSubscribedEvents() {
		return [
			'post-install-cmd' => [ 'install_wp_environment' ],
			'post-update-cmd'  => [ 'install_wp_environment' ],
		];
	}

	/**
	 * Run deactivation processes.
	 *
	 * @param Composer    $composer
	 * @param IOInterface $io
	 */
	public function deactivate( Composer $composer, IOInterface $io ) {
		return;
	}

	/**
	 * Run uninstall processes.
	 *
	 * @param Composer    $composer
	 * @param IOInterface $io
	 */
	public function uninstall( Composer $composer, IOInterface $io ) {
		return;
	}

	/**
	 * Install the WordPress environment.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-06-01
	 * @return void
	 */
	public function install_wp_environment() {
		$source      = dirname( __DIR__ );
		$destination = dirname( __DIR__, 4 );

		if ( ! file_exists( "{$destination}/autoloader.php" ) ) {
			copy( "{$source}/inc/autoloader.php", "{$destination}/autoloader.php" );
		}

		$this->install_wp_core();
		$this->setup_wp();
	}

	/**
	 * Install WordPress Core via WP-CLI.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-06-01
	 */
	private function install_wp_core() {
		if ( ! file_exists( "{$this->dir}/wp-includes/" ) ) {
			passthru( "{$this->vendor_dir}/bin/wp core download" );
		}
	}

	/**
	 * Trigger the wp-init script if it's installed.
	 *
	 * @see https://github.com/jmichaelward/wp-setup.git
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-06-01
	 * @return void
	 */
	private function setup_wp() {
		if ( file_exists( "{$this->dir}/wp-config.php" ) ) {
			return;
		}

		if ( file_exists( "{$this->vendor_dir}/bin/wp-init" ) ) {
			passthru( "bash {$this->vendor_dir}/bin/wp-init" );
		}
	}
}
