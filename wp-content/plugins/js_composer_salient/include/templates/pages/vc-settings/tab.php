<?php
/**
 * Settings tab template.
 *
 * @var Vc_Page $page
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$tab = esc_attr( preg_replace( '/^vc\-/', '', $page->getSlug() ) );
$use_custom = get_option( vc_settings()->getFieldPrefix() . 'use_custom' );
$css = ( ( 'color' === $tab ) && $use_custom ) ? ' color_enabled' : '';
$dev_environment = vc_license()->isDevEnvironment();
$license_key = vc_license()->getLicenseKey();

$classes = 'vc_settings-tab-content vc_settings-tab-content-active ' . esc_attr( $css );
$custom_tag = 'script';
?>
<<?php echo esc_attr( $custom_tag ); ?>>
	window.vcAdminNonce = '<?php echo esc_js( vc_generate_nonce( 'vc-admin-nonce' ) ); ?>';
</<?php echo esc_attr( $custom_tag ); ?>>
<?php if ( 'updater' === $tab && $dev_environment && ! vc_license()->isActivated() ) : ?>
	<br/>
	<div class="updated vc_updater-result-message">
		<p>
			<strong>
				<?php esc_html_e( 'It is optional to activate license on localhost development environment. You can still activate license on localhost to receive plugin updates and get access to template library.', 'js_composer' ); ?>
			</strong>
		</p>
	</div>
<?php endif ?>

<?php if ( 'updater' === $tab && vc_license()->isActivated() && vc_license()->isExpired() ) : ?>
	<div class="wpb_message_placeholder notice notice-success" style="display:none"><p></p></div>
	<div class="wpb_message_placeholder notice notice-error" style="display:none"><p></p></div>
<?php endif ?>

<form action="options.php"
		method="post"
		id="vc_settings-<?php echo esc_attr( $tab ); ?>"
		data-vc-ui-element="settings-tab-<?php echo esc_attr( $tab ); ?>"
		class="<?php echo esc_attr( $classes ); ?>"
		<?php echo apply_filters( 'vc_setting-tab-form-' . esc_attr( $tab ), '' ); // phpcs:ignore ?>
>
	<?php settings_fields( vc_settings()->getOptionGroup() . '_' . $tab ); ?>
	<?php do_settings_sections( vc_settings()->page() . '_' . $tab ); ?>
	<?php if ( 'general' === $tab && vc_pointers_is_dismissed() ) : ?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<span><?php esc_html_e( 'Guide tours', 'js_composer' ); ?></span>
				</th>
				<td>
					<?php vc_include_template( 'editors/partials/param-info.tpl.php', [ 'description' => esc_html__( 'Guide tours are shown in WPBakery editors to help you to start working with editors. You can see them again by clicking button above.', 'js_composer' ) ] ); ?>
					<a href="#" class="button vc_pointers-reset-button"
							id="vc_settings-vc-pointers-reset"
							data-vc-done-txt="<?php esc_attr_e( 'Done', 'js_composer' ); ?>"><?php esc_html_e( 'Reset', 'js_composer' ); ?></a>
				</td>
			</tr>
		</table>
	<?php endif ?>

	<?php

	$submit_button_attributes = [];
    // phpcs:ignore:WordPress.NamingConventions.ValidHookName.UseUnderscores
	$submit_button_attributes = apply_filters( 'vc_settings-tab-submit-button-attributes', $submit_button_attributes, $tab );
    // phpcs:ignore:WordPress.NamingConventions.ValidHookName.UseUnderscores
	$submit_button_attributes = apply_filters( 'vc_settings-tab-submit-button-attributes-' . $tab, $submit_button_attributes, $tab );

	?>

	<?php if ( 'updater' !== $tab && ! $page->get_ajax_save() ) : ?>
		<?php submit_button( esc_html__( 'Save Changes', 'js_composer' ), 'primary', 'submit_btn', true, $submit_button_attributes ); ?>
	<?php endif ?>

	<input type="hidden" name="vc_action" value="vc_action-<?php echo esc_attr( $tab ); ?>"
			id="vc_settings-<?php echo esc_attr( $tab ); ?>-action"/>

	<?php if ( 'color' === $tab ) : ?>
		<a href="#" class="button vc_restore-button" id="vc_settings-color-restore-default">
			<?php echo esc_html__( 'Restore Default', 'js_composer' ); ?>
		</a>
	<?php elseif ( 'color-picker' === $tab ) : ?>
	<a href="#" class="button vc_restore-button" id="vc_settings-color-picker-restore-default">
		<?php echo esc_html__( 'Restore Default', 'js_composer' ); ?>
	</a>
	<?php endif ?>
	<?php if ( 'updater' === $tab ) : ?>

		<div class="vc_settings-activation-deactivation">
			<?php if ( vc_license()->isActivated() ) : ?>
				<?php if ( vc_license()->isExpired() ) : ?>
					<p>
						<?php printf( ' ' . esc_html__( 'Your WPBakery Page Builder license is activated. Automatic updates for the plugin are not available. To enable automatic updates, %1$ssynchronize%2$s your license and ensure you have a valid plugin support period - you can renew the support period %3$shere.%4$s To update manually, visit our customer center to download the latest version. Thank You for choosing WPBakery Page Builder.', 'js_composer' ), '<a href="javascript:void(0)" id="vc_settings-sync-button">', '</a>', '<a href="https://support.wpbakery.com" target="_blank">', '</a>' ); ?>
					</p>
				<?php else : ?>
					<p>
						<?php echo esc_html__( 'You have activated WPBakery Page Builder version which allows you to access all the customer benefits. Thank you for choosing WPBakery Page Builder as your page builder. If you do not wish to use WPBakery Page Builder on this WordPress site you can deactivate your license below.', 'js_composer' ); ?>
					</p>
				<?php endif; ?>

				<br/>

				<p>
					<button
							class="button button-primary button-hero button-updater"
							data-vc-action="deactivation"
							type="button"
							id="vc_settings-updater-button">
						<?php echo esc_html__( 'Deactivate WPBakery Page Builder', 'js_composer' ); ?>
					</button>

					<img src="<?php echo esc_url( get_admin_url() ); ?>/images/wpspin_light.gif" class="vc_updater-spinner"
							id="vc_updater-spinner" width="16" height="16" alt="spinner"/>
				</p>

			<?php else : ?>

				<p>
				<?php 
					// nectar addition.
					echo esc_html__( 'When using the Salient WPBakery page builder, a direct license is not required for WPBakery. However, in order to utilize the WPBakery Page Builder AI functionality, you\'ll need an active license of the core plugin.', 'js_composer' ); 
					// nectar addition end.
					?>
				</p>

				<br/>

				<p>
					<button
							class="button button-primary button-hero button-updater"
							data-vc-action="activation"
							type="button"
							id="vc_settings-updater-button">
						<?php echo esc_html__( 'Activate WPBakery Page Builder', 'js_composer' ); ?>
					</button>

					<img src="<?php echo esc_url( get_admin_url() ); ?>/images/wpspin_light.gif" class="vc_updater-spinner"
							id="vc_updater-spinner" width="16" height="16" alt="spinner"/>
				</p>

				<p class="description">
					<?php printf( esc_html__( 'Don\'t have direct license yet? %1$sPurchase WPBakery Page Builder license%2$s.', 'js_composer' ), '<a href="' . esc_url( 'https://go.wpbakery.com/wpb-buy' ) . '" target="_blank">', '</a>' ); ?>
				</p>

			<?php endif ?>
		</div>

	<?php endif ?>
</form>

<?php
// [modal ai render]
vc_include_template( 'editors/popups/ai/modal.tpl.php' );
?>
