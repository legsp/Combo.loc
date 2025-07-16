<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * OMW plugin main class
 *
 * Class to initialize the plugin.
 *
 * @since 2.8
 */
final class OMW_Plugin {
	/**
	 * Instance
	 *
	 * @since 2.2
	 *
	 * @access private
	 * @static
	 *
	 * @var OMW_Init The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Button in product page option
	 *
	 * @since 2.8
	 * @var string
	 */
	public $button_in_product_page;

	/**
	 * Button in cart page option
	 *
	 * @since 2.8
	 * @var string
	 */
	public $button_in_cart_page;

	/**
	 * Phone option
	 *
	 * @since 2.8
	 * @var int
	 */
	public $phone_number;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 2.2
	 *
	 * @access public
	 * @static
	 *
	 * @return OMW_Init An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * TODO: wakeup and clone functions
	 */

	/**
	 * Constructor
	 *
	 * Private method for prevent instance outsite the class.
	 *
	 * @since 2.2
	 *
	 * @access private
	 */
	private function __construct() {
		$this->button_in_product_page = get_option( 'evwapp_opiton_show_btn_single');
		$this->button_in_cart_page = get_option( 'evwapp_opiton_show_cart' );
		$this->phone_number = get_option( 'evwapp_opiton_phone_number' );

		/**
		 * Do action for pro version check loaded
		 *
		 * @since 2.0
		 */
		do_action( 'omw_plugin_loaded' );
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin and all classes after WooCommerce and other plugins is loaded.
	 *
	 * @since 2.2
	 *
	 * @access public
	 */
	public function init() {
		if( ! $this->plugin_is_active( 'woocommerce/woocommerce.php' ) ) {
			add_action( 'admin_notcies', [ $this, 'notice_woo_inactived' ] );
			return;
			if ( get_option('evwapp_opiton_show_thank') === 'yes' ) {
			add_action( 'woocommerce_thankyou', [ $this, 'redirect_to_whatsapp_after_checkout' ], 10, 1 );
		}
		}

		/**
		 * Do action for init other extensions
		 *
		 * @since 2.0
		 */
		do_action( 'omw_plugin_init' );

		/**
		 * Include initial required files
		 */
		include_once OMW_PLUGIN_PATH . 'includes/class-utils.php';
		include_once OMW_PLUGIN_PATH . 'includes/abstract-class-button.php';

		/**
		 * TODO: Check basic if basic settings are selected
		 */

		/**
		 * Include admin class
		 */
		if( is_admin() ) {
			include_once OMW_PLUGIN_PATH . 'includes/class-admin.php';

			$admin = new OMW_Admin;
			add_action( 'admin_init', [ $admin, 'register_settings' ] );
			add_action( 'admin_menu', [ $admin, 'add_admin_page' ] );
		}

		/**
		 * Check option and include product page btn class
		 */
		if( $this->button_in_product_page === 'yes' ) {
			include_once OMW_PLUGIN_PATH . 'includes/class-button-product-page.php';

			$button_product_page = new OMW_Button_Product_Page;
			add_action( 'wp_head', [ $button_product_page, 'hide_woo_elements' ] );
			add_action( 'woocommerce_after_add_to_cart_form', [ $button_product_page, 'output_btn' ] );
			add_shortcode( 'woo-order-on-whatsapp', [ $button_product_page, 'output_btn' ] );
		}

		/**
		 * Check option and include car page btn class
		 */
		if( $this->button_in_cart_page === 'yes' ) {
			include_once OMW_PLUGIN_PATH . 'includes/class-button-cart.php';

			$button_cart = new OMW_Button_Cart;
			add_action('wp_footer', [ $button_cart, 'output_btn' ] );
		}

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_plugin_css' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_plugin_js' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_plugin_css' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_plugin_js' ] );
	}

	/**
	 * Admin notice - WooCommerce
	 *
	 * Warning when the site doesn't have WooCommerce activated.
	 *
	 * @since 2.2
	 *
	 * @access public
	 */
	public function notice_woo_inactived() {
		$message = sprintf(
			esc_html__( '%1$s requires WooCommerce to be installed and activated.', 'woo-order-on-whatsapp' ),
			'<strong>Order on WhatsApp for WooCommerce</strong>'
		);

		$html_message = sprintf( '<div class="notice notice-error"><p>%1$s</p></div>', $message );

		echo wp_kses_post( $html_message );
	}

	/**
	 * Enqueue CSS
	 *
	 * Register and enqueue CSS.
	 *
	 * @since 2.2
	 *
	 * @access public
	 */
	public function enqueue_plugin_css() {
		wp_enqueue_style( 'omw_style',  OMW_PLUGN_URL . '/assets/css/style.min.css', array(), OMW_VERSION );
	}

	/**
	 * Enqueue JS
	 *
	 * Register and enqueue JS.
	 *
	 * @since 2.2
	 *
	 * @access public
	 */
	public function enqueue_plugin_js() {
		wp_enqueue_script( 'omw_script',  OMW_PLUGN_URL . '/assets/js/front-js.min.js', array('jquery'), OMW_VERSION, true );
	}

	/**
	 * Enqueue ADMIN CSS
	 *
	 * Register and enqueue CSS.
	 *
	 * @since 2.2
	 *
	 * @access public
	 */
	public function enqueue_admin_plugin_css() {
		wp_enqueue_style( 'omw_admin_style',  OMW_PLUGN_URL . '/assets/css/admin/admin-style.min.css', array(), OMW_VERSION );
	}

	/**
	 * Enqueue ADMIN JS
	 *
	 * Register and enqueue JS.
	 *
	 * @since 2.2
	 *
	 * @access public
	 */
	public function enqueue_admin_plugin_js() {
		wp_enqueue_script( 'omw_admin_script', OMW_PLUGN_URL . '/assets/js/admin/admin-settings.min.js', [], OMW_VERSION, true );
	}

    /**
	 * Redirect to WhatsApp after checkout.
	 *
	 * @param int $order_id The ID of the order.
	 */
	public function redirect_to_whatsapp_after_checkout( $order_id ) {
		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		$phone_number = get_option( 'evwapp_opiton_phone_number' );
		$product_message_template = get_option( 'evwapp_opiton_product_order_message' );
		$order_message_template = get_option( 'evwapp_opiton_order_message' );

		$products_message = '';
		$items = $order->get_items();

		foreach ( $items as $item ) {
			$product = $item->get_product();
			$product_name = $item->get_name();
			$product_qty = $item->get_quantity();
			$product_price = wc_price( $item->get_total() );
			$product_sku = $product->get_sku();

			$product_attributes = '';
			if ( $product->is_type( 'variation' ) ) {
				$variation_attributes = $product->get_variation_attributes();
				$product_attributes = implode( ', ', $variation_attributes );
			}

			$product_message = str_replace(
				[ '{product-name}', '{product-price}', '{product-qty}', '{product-sku}', '{product-atributes}' ],
				[ $product_name, $product_price, $product_qty, $product_sku, $product_attributes ],
				$product_message_template
			);
			$products_message .= $product_message . PHP_EOL;
		}

		$customer_name = $order->get_formatted_billing_full_name();
		$customer_phone = $order->get_billing_phone();
		$customer_email = $order->get_billing_email();
		$customer_address = $order->get_formatted_billing_address();
		$customer_state = $order->get_billing_state();
		$customer_city = $order->get_billing_city();
		$customer_zip = $order->get_billing_postcode();
		
		$final_message = str_replace(
			[
				'{order-number}',
				'{order-payment}',
				'{order-subtotal}',
				'{order-total}',
				'{order-note}',
				'{order-products}',
				'{customer-name}',
				'{customer-phone}',
				'{customer-mail}',
				'{customer-address}',
				'{customer-state}',
				'{customer-city}',
				'{customer-zipcode}',
				'{shipping-total}',
				'{shipping-method}',
			],
			[
				$order->get_order_number(),
				$order->get_payment_method_title(),
				$order->get_subtotal_to_display(),
				$order->get_formatted_order_total(),
				$order->get_customer_note(),
				$products_message,
				$customer_name,
				$customer_phone,
				$customer_email,
				$customer_address,
				$customer_state,
				$customer_city,
				$customer_zip,
				$order->get_shipping_total(),
				$order->get_shipping_method(),
			],
			$order_message_template
		);

		// Handle meta fields
		preg_match_all('/{meta-(.*?)}/', $final_message, $matches);
		if ($matches) {
			foreach ($matches[1] as $match) {
				$meta_value = $order->get_meta($match, true);
				$final_message = str_replace('{meta-' . $match . '}', $meta_value, $final_message);
			}
		}

		$whatsapp_url = 'https://wa.me/' . $phone_number . '?text=' . urlencode( $final_message );

		wp_redirect( $whatsapp_url );
		exit;
	}
	/**
	 * Check plugin is activated
	 *
	 * @since 2.8
	 * @return boolean
	 * @param string $plugin
	 */
	public function plugin_is_active( $plugin ) {
		return function_exists( 'is_plugin_active' ) ? is_plugin_active( $plugin ) : in_array( $plugin, (array) get_option( 'active_plugins', array() ), true );
	}
}
