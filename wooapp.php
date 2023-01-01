<?php

/**
 * WooApp - Convert WooCommerce Website to Mobile App
 *
 * @link              https://wooapp.liilab.com
 * @since             1.0
 * @package           Wooapp
 *
 * @wordpress-plugin
 * Plugin Name:       WooApp - Convert WooCommerce Website to Mobile App
 * Plugin URI:        https://wooapp.liilab.com
 * Description:       A plugin for converting WooCommerce website to mobile App
 * Version:           1.0
 * Author:            liilab
 * Author URI:        https://liilab.com
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * License:           GPL-2.0+
 * Text Domain:       wooapp
 * Domain Path:       /languages
 */

/**
 * Bootstrap the plugin.
 */


if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * The main plugin class
 */


final class Wooapp
{

    /**
     * Plugin version
     *
     * @var string
     */
    const version = '1.0';

    /**
     * Class constructor
     */
    private function __construct()
    {
        $this->define_constants();

        register_activation_hook(__FILE__, [$this, 'activate']);
        register_uninstall_hook(__FILE__, [$this, 'uninstall_confirmation']);

        add_action('plugins_loaded', [$this, 'init_plugin']);
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'plugin_action_links']);
    }


    /**
     * Plugin action links
     *
     * @param array $links
     *
     * @since  1.0.0
     *
     * @return array
     */
    public function plugin_action_links($links)
    {

        $links[] = '<a href="' . admin_url('admin.php?page=wooapp') . '">' . __('Settings', 'wooapp') . '</a>';
        $links[] = '<a href="https://wooapp.liilab.com/documentation.html" target="_blank">' . __('Documentation', 'wooapp') . '</a>';

        return $links;
    }

    /**
     * Initializes a singleton instance
     *
     * @return \Wooapp
     */

    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Define the required plugin constants
     *
     * @return void
     */
    public function define_constants()
    {
        define('WTA_VERSION', self::version);
        define('WTA_FILE', __FILE__);
        define('WTA_DIR', __DIR__);
        define('WTA_DIR_PATH', plugin_dir_path(__FILE__));
        define('WTA_URL', plugins_url('', WTA_FILE));
        define('WTA_ASSETS', WTA_URL . '/assets');
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin()
    {
        if (is_admin()) {
            WebToApp\Admin::get_instance();
        }
        if (class_exists('WooCommerce')) {
            WebToApp\WtaHelper::get_instance();
            WebToApp\API::get_instance();
            WebToApp\User::get_instance();
        } else {
            add_action('admin_notices', [$this, 'admin_notice']);
        }
    }

    /**
     * Do stuff upon plugin activation
     *
     * @return void
     */
    public function activate()
    {
        $installed = get_option('wta_installed');

        if (!$installed) {
            update_option('wta_installed', time());
        }
        update_option('wta_version', WTA_VERSION);
    }


    /**
     * Do stuff upon plugin deactivation
     *
     * @return void
     */


    public function uninstall_confirmation()
    {
        // Show the confirmation message using Sweet Alert
        echo '
  <script>
    swal({
      title: "Are you sure?",
      text: "This will uninstall the plugin and delete all data associated with it.",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Yes, uninstall it!",
      closeOnConfirm: false
    },
    function(){
      // Uninstall the plugin and delete all data
      // ...
      swal("Uninstalled!", "The plugin has been uninstalled.", "success");
    });
  </script>
  ';
        wp_die();
    }

    /**
     * Show warning if WooCommerce is not installed
     * @return void
     */

    public function admin_notice()
    {
?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e('WooApp requires WooCommerce to be installed and activated!', 'wooapp'); ?></p>
        </div>
<?php
    }
}


/**
 * Initializes the main plugin
 *
 * @return \Wooapp
 */

function Wooapp()
{
    return Wooapp::init();
}

// kick-off the plugin
Wooapp();
