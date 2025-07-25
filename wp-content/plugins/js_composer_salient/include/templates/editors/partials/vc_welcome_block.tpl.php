<?php
/**
 * Welcome block template.
 *
 * @var string $editor
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$total_templates = wpbakery()->templatesPanelEditor()->loadDefaultTemplates();
$templates_total_count = ($total_templates) ? count( $total_templates ) : 0; // nectar addition

if ( vc_modules_manager()->is_module_on( 'vc-post-custom-layout' ) ) {
	$custom_layout = vc_modules_manager()->get_module( 'vc-post-custom-layout' );
	if ( $custom_layout->get_custom_layout_name() ) {
		$template_class = ' vc_post-custom-layout-selected';
	} else {
		$template_class = '';
	}
} else {
	$template_class = ' vc_post-custom-layout-selected';
}
?>
<div id="vc_no-content-helper" class="vc_welcome vc_ui-font-open-sans <?php echo esc_attr( $template_class ); ?>">
	<?php
	if ( vc_user_access()->part( 'shortcodes' )->checkStateAny( true, 'custom', null )->get() && vc_user_access_check_shortcode_all( 'vc_row' ) && vc_user_access_check_shortcode_all( 'vc_column' ) ) :
		vc_include_template(
			'editors/partials/start-logo.tpl.php'
		);
		vc_include_template(
			'editors/partials/start-add-element-title.tpl.php'
		);
		if ( 'backend' === $editor ) {
			vc_include_template(
				'editors/partials/start-select-layout-title.tpl.php'
			);

			if ( vc_modules_manager()->is_module_on( 'vc-post-custom-layout' ) ) {
				vc_include_template(
					'editors/partials/vc_post_custom_layout.tpl.php',
					[ 'location' => 'welcome' ]
				);
			}
		}
		?>

		<div class="vc_ui-btn-group vc_welcome-visible-e vc_selected-post-custom-layout-visible-e">
			<?php
			if ( vc_user_access()->part( 'shortcodes' )->checkStateAny( true, 'custom', null )->get() && vc_user_access_check_shortcode_all( 'vc_row' ) && vc_user_access_check_shortcode_all( 'vc_column' ) ) :
				?>
				<a id="vc_no-content-add-element"
					class="vc_general vc_ui-button vc_ui-button-shape-rounded vc_ui-button-info vc_welcome-visible-e"
					title="<?php esc_attr_e( 'Add Element', 'js_composer' ); ?>"
					data-vc-element="add-element-action" href="#">
					<i class="vc-composer-icon vc-c-icon-add_element"></i>
					<span><?php esc_html_e( 'Add element', 'js_composer' ); ?></span>
				</a>
				<?php
			endif;
			if ( vc_user_access()->part( 'shortcodes' )->can( 'vc_column_text_all' )->get() ) :
				?>
				<a id="vc_no-content-add-text-block"
					class="vc_general vc_ui-button vc_ui-button-shape-rounded vc_ui-button-info vc_ui-button-info vc_welcome-visible-e"
					data-vc-element="add-text-block-action" href="#"
					title="<?php esc_attr_e( 'Add text block', 'js_composer' ); ?>">
					<i class="vc-composer-icon vc-c-icon-text-block"></i>
					<span><?php esc_html_e( 'Add Text Block', 'js_composer' ); ?></span>
				</a>
			<?php endif; ?>
			<?php
			if ( $templates_total_count > 0 && vc_user_access()->part( 'templates' )->can()->get() ) :
				?>
				<a id="vc_templates-more-layouts" class="vc_general vc_ui-button vc_ui-button-shape-rounded vc_ui-button-info  vc_ui-button-info vc_welcome-visible-e" href="#">
					<i class="vc-composer-icon vc-c-icon-add_template"></i>
					<span><?php esc_html_e( 'Add template', 'js_composer' ); ?></span>
				</a>
			<?php endif; ?>
		</div>
		<?php
		if ( vc_user_access()->part( 'shortcodes' )->checkStateAny( true, 'custom', null )->get() && vc_user_access_check_shortcode_all( 'vc_row' ) && vc_user_access_check_shortcode_all( 'vc_column' ) ) :
			?>
			<div class="vc_welcome-visible-ne">
				<a id="vc_not-empty-add-element" class="vc_add-element-not-empty-button"
					title="<?php esc_attr_e( 'Add Element', 'js_composer' ); ?>" data-vc-element="add-element-action">
					<i class="vc-composer-icon vc-c-icon-add"></i>
				</a>
			</div>
		<?php endif; ?>
		<p class="vc_ui-help-block vc_welcome-visible-e">
		<?php 
			// nectar addition
			echo sprintf( esc_html__( 'Don\'t know where to start? Visit our %s.', 'js_composer' ), '<a href="https://themenectar.com/docs/salient" target="_blank">' . esc_html__( 'knowledge base', 'js_composer' ) . '</a>' ); 
			// nectar addition end 
			?>
		</p>
	<?php endif; ?>
</div>

