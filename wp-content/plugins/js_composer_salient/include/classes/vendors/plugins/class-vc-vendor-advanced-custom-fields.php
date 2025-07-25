<?php
/**
 * Backward compatibility with "Advanced custom fields" WordPress plugin.
 *
 * @see https://wordpress.org/plugins/advanced-custom-fields/
 *
 * @since 4.4 vendors initialization moved to hooks in autoload/vendors.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Vendor class for plugin advanced custom fields,
 * Needed to apply extra js when backend/frontend editor rendered.
 * Class Vc_Vendor_AdvancedCustomFields
 *
 * @since 4.3.3
 */
class Vc_Vendor_AdvancedCustomFields {

	/**
	 * Initializing actions when backend/frontend editor renders to enqueue fix-js file
	 *
	 * @since 4.3.3
	 */
	public function load() {
		if ( did_action( 'vc-vendor-acf-load' ) ) {
			return;
		}
		/**
		 * Action when backend editor is rendering
		 *
		 * @see Vc_Backend_Editor::renderEditor wp-content/plugins/js_composer/include/classes/editors/class-vc-backend-editor.php
		 */
		add_action( 'vc_backend_editor_render', [
			$this,
			'enqueueJs',
		] );

		/**
		 * Action when frontend editor is rendering
		 *
		 * @see Vc_Frontend_Editor::renderEditor wp-content/plugins/js_composer/include/classes/editors/class-vc-frontend-editor.php
		 */
		add_action( 'vc_frontend_editor_render', [
			$this,
			'enqueueJs',
		] );
		add_filter( 'vc_grid_item_shortcodes', [
			$this,
			'mapGridItemShortcodes',
		] );
		add_action( 'vc_after_mapping', [
			$this,
			'mapEditorsShortcodes',
		] );
		add_filter( 'acf/ajax/shortcode_capability', [
			$this,
			'acfAjaxShortcodeCapability',
		] );

		do_action( 'vc-vendor-acf-load', $this );
	}

	/**
	 * Set capability for ACF shortcode.
	 *
	 * @param string $cap
	 * @return string
	 */
	public function acfAjaxShortcodeCapability( $cap ) {
        // phpcs:ignore
		if ( isset( $_POST['_vcnonce'] ) && vc_verify_public_nonce() ) {
			return 'exist';
		}

		return $cap;
	}

	/**
	 * Small fix for editor when try to change field
	 *
	 * @since 4.3.3
	 */
	public function enqueueJs() {
		wp_enqueue_script( 'vc_vendor_acf', vc_asset_url( 'js/vendors/advanced_custom_fields.js' ), [ 'jquery-core' ], '1.0', true );
	}

	/**
	 * Map grid item shortcodes.
	 *
	 * @param array $shortcodes
	 * @return array|mixed
	 */
	public function mapGridItemShortcodes( array $shortcodes ) {
		require_once vc_path_dir( 'VENDORS_DIR', 'plugins/acf/class-vc-gitem-acf-shortcode.php' );
		require_once vc_path_dir( 'VENDORS_DIR', 'plugins/acf/grid-item-attributes.php' );
		$wc_shortcodes = include vc_path_dir( 'VENDORS_DIR', 'plugins/acf/grid-item-shortcodes.php' );

		return $shortcodes + $wc_shortcodes;
	}

	/**
	 * Map editors shortcodes.
	 */
	public function mapEditorsShortcodes() {
		// nectar addition todo - this will need to be moved to the salient-core if we want to override it
		require_once vc_path_dir( 'VENDORS_DIR', 'plugins/acf/class-vc-acf-shortcode.php' );
		vc_lean_map( 'vc_acf', null, vc_path_dir( 'VENDORS_DIR', 'plugins/acf/shortcode.php' ) );
	}
}
