<?php
/**
 * Main plugin class
 *
 * Plugin Name: Sales workflow Addon for EDD and AppSumo
 * Plugin URI: https://owleads.com
 * Description: A WordPress plugin that seamlessly combines Easy Digital Downloads with the AppSumo sales workflow, enabling smooth handling of the redemption process for AppSumo discount codes.
 * Author: Owleads
 * Version: 1.0.2
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

register_deactivation_hook( __FILE__, array( 'Appsumo', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Appsumo', 'get_instance' ), 10, 0 );


register_activation_hook(__FILE__, 'appsumo_sales_wk_addon_edd_appsumo');

function appsumo_sales_wk_addon_edd_appsumo() {

    /* if (!class_exists('Easy_Digital_Downloads')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die('This plugin requires the Easy Digital Downloads plugin to be installed and active.');
    } */

    Appsumo::activate();
}

/**
 * Main Appsumo plugin class
 */
class Appsumo {

	/**
	 * Hold instance of this class.
	 *
	 * @var null|Appsumo
	 */
	protected static $instance = null;

	/**
	 * Class constructor
	 */
	public function __construct() {

		$this->includes();
		$this->init();
	}

	/**
	 * Register hooks and plugin constants
	 */
	public function init() {

		define( 'APPSUMO_VERSION', '1.0.2' );
		define( 'APPSUMO_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
		define( 'APPSUMO_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'init', array( $this, 'rewrites_init' ) );
		add_filter( 'query_vars', array( $this, 'query_vars' ) );

		if (!class_exists('Easy_Digital_Downloads')) {
			add_action('admin_notices', array($this, 'display_requirements'));
		}
	}

	/**
	 * Register new query vars for appsumo landing page
	 *
	 * @param array $query_vars Existing query vars.
	 *
	 * @return array
	 */
	public function query_vars( $query_vars ) {

		$query_vars[] = 'appsumo_code';
		return $query_vars;
	}


	/**
	 * Add new rewrite rule and appsumo landing page
	 */
	public function rewrites_init() {

		$page_id = appsumo_get_landing_page_id();

		add_rewrite_rule(
		    '^appsumo/([^/]+)$',
		    'index.php?page_id=' . $page_id . '&appsumo_code=$matches[1]',
		    'top'
		);

		add_rewrite_rule(
		    '^appsumo$',
		    'index.php?page_id=' . $page_id . '&appsumo_code=',
		    'top'
		);

		flush_rewrite_rules();

		if ( get_option( 'appsumo_flush_rewrite_rules' ) ) {
			flush_rewrite_rules();
			delete_option( 'appsumo_flush_rewrite_rules' );
		}

	}

	/**
	 * Enqueue admin style and script file
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_script( 'appsumo-admin-script', APPSUMO_URL . 'assets/js/script.js', array( 'jquery' ), APPSUMO_VERSION, true );
		wp_enqueue_style( 'appsumo-admin-style', APPSUMO_URL . 'assets/css/style.css', array(), APPSUMO_VERSION );
	}

	/**
	 * Create new database table to store appsumo codes
	 *
	 * @global object $wpdb
	 */
	public static function activate() {

		global $wpdb;

		$table_name = $wpdb->prefix . 'appsumo_codes';
		$sql        = "CREATE TABLE IF NOT EXISTS {$table_name} (
            `id` int(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `download_id` int(11) NOT NULL,
			`price_id` int(11) NULL DEFAULT '0',
            `code` varchar(24) NOT NULL UNIQUE, 
            `redeemed` int(1) NOT NULL DEFAULT '0'
             ) ENGINE=InnoDB;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		// Set the desired slug
		$slug = 'appsumo-redeem-page';

		// Check if a page with the specified slug already exists
		$page = get_page_by_path($slug, OBJECT, 'page');

		if ($page) {
		    // If the page exists, get its ID
		    $page_id = $page->ID;
		} else {
		    // If the page doesn't exist, create a new one
		    $landing_page = array(
		        'post_title'   => __( 'Appsumo Redeem Page', 'sales-wk-addon-edd-appsumo' ),
		        'post_content' => '[appsumo-landingpage]',
		        'post_status'  => 'publish',
		        'post_author'  => 1,
		        'post_type'    => 'page',
		        'post_name'    => $slug,  // Set the post_name (slug),
		        'page_template' => 'blank', 
		    );

		    $page_id = wp_insert_post($landing_page);
		}

		update_option( 'appsumo_landing_page', $page_id );
		update_option( 'appsumo_flush_rewrite_rules', true );

	}


	/**
	 * Delete landing page after plugin deactivated
	 */
	public static function deactivate() {
	}

	/**
	 * Return single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
				self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Include plugin files
	 */
	public function includes() {
		require_once 'includes/functions.php';
		require_once 'includes/settings.php';

		require_once 'includes/class-appsumo-purchase.php';
		require_once 'includes/landing-page.php';
	}

	function display_requirements( ){

		// If user click on "I Already Did" or "Hide" 
		if (isset($_GET['EDDAPPSUMO_hide_warning']) && $_GET['EDDAPPSUMO_hide_warning'] == 0 ) 
			update_option ( 'EDDAPPSUMO_STOP_WARNING', 1 );

		// If there is no option EDDAPPSUMO_STP_RTG in the database, so user has not clicked on the button yet
		if ( !get_option ( 'EDDAPPSUMO_STOP_WARNING' ) )
			update_option ( 'EDDAPPSUMO_STOP_WARNING', -1 );
		
		// If the option 'EDDAPPSUMO_STOP_WARNING' exists in the database and is set to -1, we don't display the message
		/*if ( get_option ( 'EDDAPPSUMO_STOP_WARNING' ) == 1 )
			return;*/
		
		$EDDAPPSUMO_new_URI = $_SERVER['REQUEST_URI'];
		$EDDAPPSUMO_new_URI = add_query_arg('EDDAPPSUMO_hide_warning', "0", $EDDAPPSUMO_new_URI);

		?><style>
			.edd-appsumo-container {
				max-width: 1500px;
				margin: 0 auto;
				position: relative;
				color: #06283D;
			}
			.flex {
				display: flex;
			}
			.flex-wrap {
				flex-wrap: wrap;
			}
			.items-center {
				align-items: center;
			}
			.gap-3 {
				gap: 12px;
			}
			.rounded {
				border-radius: 4px;
			}
			.edd-appsumo-notice-button {
			    display:inline-flex;
			    align-items:center;
			    justify-content:center;
			    gap:.5rem;
			    border-width:1px;
			    border-color:#000;
			    background-color:initial;
			    padding:.25rem .75rem;
			    text-align:center;
			    font-size:13px;
			    font-weight:400;
			    color:inherit;
			    transition-property:all;
			    transition-duration:.15s;
			    transition-timing-function:cubic-bezier(.4,0,.2,1);
				text-decoration: none;
			}
			.edd-appsumo-notice-button-outline-primary {
				border-color: #822abb;
				color: #822abb;
			}
			.edd-appsumo-notice-button-outline-primary:focus,
			.edd-appsumo-notice-button-outline-primary:hover {
				color: #fff;
				border-color: #581c87;
				background-color: #581c87;
			}
			.edd-appsumo-notice-button-success {
				color: #fff;
				border-color: #37ae53;
				background-color: #37ae53;
			}
			.edd-appsumo-notice-button-success:focus,
			.edd-appsumo-notice-button-success:hover {
				color: #fff;
				border-color: #15803d;
				background-color: #15803d;
			}
			.edd-appsumo-notice-button-link {
				display:inline-flex;
				color: #dba617 !important;
				border: 0;
				border-bottom: 1px solid #dba617;
			    background-color:initial;
			    padding: 0;
				padding-bottom: 2px;
			    text-align:center;
			    font-size:13px;
			    font-weight:400;
			    color:inherit;
			    transition-property:all;
			    transition-duration:.15s;
			    transition-timing-function:cubic-bezier(.4,0,.2,1);
				text-decoration: none;
			}
			.edd-appsumo-notice-button-link:hover,
			.edd-appsumo-notice-button-link:focus {
				color: #581c87 !important;
				border-bottom-color: #581c87
			}
		</style>
		<div class="edd-appsumo-container">
			<div class="notice notice-warning"style="padding:10px 30px;border-top:0;border-right:0;border-bottom:0;margin:1rem">
			<!--div style="padding:15px !important;" class="updated DBR-top-main-msg is-dismissible"-->
				<span style="font-size:20px;color:#dba617;font-weight:bold;display:block"><?php _e("Warning", 'sales-wk-addon-edd-appsumo'); ?></span>
				<p style="font-size:14px;line-height:30px;color:#06283D">
					<?php _e('The Sales Workflow Addon for EDD and APPSUMO requires the Easy Digital Downloads plugin to be installed and active.', 'sales-wk-addon-edd-appsumo'); ?>
					<br/>
					<div style="font-size:14px;margin-top:10px;" class="flex flex-wrap gap-3 items-center">
						<a class="edd-appsumo-notice-button-link" href="<?php echo esc_url( $EDDAPPSUMO_new_URI ); ?>"><?php _e('HIDE', 'sales-wk-addon-edd-appsumo'); ?></a>
					</div>
				</p>
			</div>
		</div>
	<?php
	}

}