<?php
/**
 * Autoload hooks related plugin welcome screen.
 *
 * @note we require our autoload files everytime and everywhere after plugin load.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Get welcome pages main slug.
 *
 * @return mixed|string
 * @since 4.5
 */
function vc_page_welcome_slug() {
	$vc_page_welcome_tabs = vc_get_page_welcome_tabs();

	return isset( $vc_page_welcome_tabs ) ? key( $vc_page_welcome_tabs ) : '';
}

/**
 * Get welcome page tab.
 *
 * @return mixed|void
 */
function vc_get_page_welcome_tabs() {
	global $vc_page_welcome_tabs;
	$vc_page_welcome_tabs = apply_filters( 'vc_page-welcome-slugs-list', [
		'vc-welcome' => esc_html__( 'What\'s New', 'js_composer' ),
		'vc-faq' => esc_html__( 'FAQ', 'js_composer' ),
		'vc-resources' => esc_html__( 'Resources', 'js_composer' ),
	] );

	return $vc_page_welcome_tabs;
}


/**
 * Build vc-welcome page block which will be shown after Vc installation.
 *
 * @see vc_filter: vc_page_welcome_render_capabilities
 *
 * @since 4.5
 */
function vc_page_welcome_render() {
	$vc_page_welcome_tabs = vc_get_page_welcome_tabs();
	$slug = vc_page_welcome_slug();
	$tab_slug = vc_get_param( 'tab', $slug );
	// If tab slug in the list please render.
	if ( ! empty( $tab_slug ) && isset( $vc_page_welcome_tabs[ $tab_slug ] ) ) {
		$pages_group = vc_pages_group_build( $slug, $vc_page_welcome_tabs[ $tab_slug ], $tab_slug );
		$pages_group->render();
	}
}

/**
 * Add submenu page.
 */
function vc_page_welcome_add_sub_page() {
	// Add submenu page.
	$page = add_submenu_page( VC_PAGE_MAIN_SLUG, esc_html__( 'About', 'js_composer' ), esc_html__( 'About', 'js_composer' ), 'edit_posts', vc_page_welcome_slug(), 'vc_page_welcome_render' );
	// Css for perfect styling.
	add_action( 'admin_print_styles-' . $page, 'vc_page_css_enqueue' );
}

/**
 * Add hooks for welcome page.
 */
function vc_welcome_menu_hooks() {
	$settings_tab_enabled = vc_user_access()->wpAny( 'manage_options' )->part( 'settings' )->can( 'vc-general-tab' )->get();
	add_action( 'vc_menu_page_build', 'vc_page_welcome_add_sub_page', $settings_tab_enabled ? 11 : 1 );
}

/**
 * Add hooks for welcome page in network admin.
 */
function vc_welcome_menu_hooks_network() {
	if ( ! vc_is_network_plugin() ) {
		return;
	}
	$settings_tab_enabled = vc_user_access()->wpAny( 'manage_options' )->part( 'settings' )->can( 'vc-general-tab' )->get();
	add_action( 'vc_network_menu_page_build', 'vc_page_welcome_add_sub_page', $settings_tab_enabled && ! is_main_site() ? 11 : 1 );
}

add_action( 'admin_menu', 'vc_welcome_menu_hooks', 9 );
add_action( 'network_admin_menu', 'vc_welcome_menu_hooks_network', 9 );

/**
 * Set redirect transition on plugin activation.
 *
 * @since 4.5
 */
function vc_page_welcome_set_redirect() {
	if ( ! is_network_admin() && ! vc_get_param( 'activate-multi' ) ) {
		/* nectar addition */ 
		//set_transient( '_vc_page_welcome_redirect', 1, 30 );
		/* nectar addition end */ 
	}
}

/**
 * Do redirect if required on welcome page
 *
 * @since 4.5
 */
function vc_page_welcome_redirect() {
	$redirect = get_transient( '_vc_page_welcome_redirect' );
	delete_transient( '_vc_page_welcome_redirect' );
	if ( $redirect ) {
		wp_safe_redirect( admin_url( 'admin.php?page=' . rawurlencode( vc_page_welcome_slug() ) ) );
	}
}

// Enables redirect on activation.
add_action( 'vc_activation_hook', 'vc_page_welcome_set_redirect' );
add_action( 'admin_init', 'vc_page_welcome_redirect' );

/**
 * Set promo popup transition on plugin activation.
 *
 * @since 7.3
 * @param object $upgrade_object
 * @param array $options
 */
function vc_set_promo_editor_popup( $upgrade_object, $options ) {
	if ( 'update' !== $options['action'] || 'plugin' !== $options['type'] ) {
		return;
	}
	$plugins = $options['plugins'];
	foreach ( $plugins as $plugin ) {
		if ( 'js_composer/js_composer.php' === $plugin ) {
			set_transient( '_vc_editor_promo_popup', 1, 86400 );
		}
	}
}

// nectar addition
// add_action( 'upgrader_process_complete', 'vc_set_promo_editor_popup', 10, 2 );
// nectar addition end