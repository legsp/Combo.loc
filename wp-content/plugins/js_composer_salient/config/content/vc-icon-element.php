<?php
/**
 * Configuration file for [vc_icon] shortcode of 'Icon' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Get shortcode attributes
 *
 * @return array
 */
function vc_icon_element_params() {
	return [
		'name' => esc_html__( 'Icon', 'js_composer' ),
		'base' => 'vc_icon',
		'icon' => 'icon-wpb-vc_icon',
		'element_default_class' => 'vc_do_icon',
		'category' => esc_html__( 'Content', 'js_composer' ),
		/* nectar addition - removing the el from the list but keeping it to parse vc_icon in text with separator */
		'content_element' => false,
		 /* nectar addition end */
		'description' => esc_html__( 'Eye catching icons from libraries', 'js_composer' ),
		'params' => [
			[
				'type' => 'dropdown',
				'heading' => esc_html__( 'Icon library', 'js_composer' ),
				'value' => [
					esc_html__( 'Font Awesome', 'js_composer' ) => 'fontawesome',
					esc_html__( 'Open Iconic', 'js_composer' ) => 'openiconic',
					esc_html__( 'Typicons', 'js_composer' ) => 'typicons',
					esc_html__( 'Entypo', 'js_composer' ) => 'entypo',
					esc_html__( 'Linecons', 'js_composer' ) => 'linecons',
					esc_html__( 'Mono Social', 'js_composer' ) => 'monosocial',
                ],
					/* nectar addition */ 
					/*
					__( 'Material', 'js_composer' ) => 'material',*/
					/* nectar addition end */ 
				'admin_label' => true,
				'param_name' => 'type',
				'description' => esc_html__( 'Select icon library.', 'js_composer' ),
			],
			[
				'type' => 'iconpicker',
				'heading' => esc_html__( 'Icon', 'js_composer' ),
				'param_name' => 'icon_fontawesome',
				'value' => 'fas fa-adjust',
				// default value to backend editor admin_label.
				'settings' => [
					'emptyIcon' => false,
					// default true, display an "EMPTY" icon.
					'iconsPerPage' => 500,
					// default 100, how many icons per/page to display, we use (big number) to display all icons in single page.
				],
				'dependency' => [
					'element' => 'type',
					'value' => 'fontawesome',
				],
				'description' => esc_html__( 'Select icon from library.', 'js_composer' ),
			],
			[
				'type' => 'iconpicker',
				'heading' => esc_html__( 'Icon', 'js_composer' ),
				'param_name' => 'icon_openiconic',
				'value' => 'vc-oi vc-oi-dial',
				// default value to backend editor admin_label.
				'settings' => [
					'emptyIcon' => false,
					// default true, display an "EMPTY" icon.
					'type' => 'openiconic',
					'iconsPerPage' => 4000,
					// default 100, how many icons per/page to display.
				],
				'dependency' => [
					'element' => 'type',
					'value' => 'openiconic',
				],
				'description' => esc_html__( 'Select icon from library.', 'js_composer' ),
			],
			[
				'type' => 'iconpicker',
				'heading' => esc_html__( 'Icon', 'js_composer' ),
				'param_name' => 'icon_typicons',
				'value' => 'typcn typcn-adjust-brightness',
				// default value to backend editor admin_label.
				'settings' => [
					'emptyIcon' => false,
					// default true, display an "EMPTY" icon.
					'type' => 'typicons',
					'iconsPerPage' => 4000,
					// default 100, how many icons per/page to display.
				],
				'dependency' => [
					'element' => 'type',
					'value' => 'typicons',
				],
				'description' => esc_html__( 'Select icon from library.', 'js_composer' ),
			],
			[
				'type' => 'iconpicker',
				'heading' => esc_html__( 'Icon', 'js_composer' ),
				'param_name' => 'icon_entypo',
				'value' => 'entypo-icon entypo-icon-note',
				// default value to backend editor admin_label.
				'settings' => [
					'emptyIcon' => false,
					// default true, display an "EMPTY" icon.
					'type' => 'entypo',
					'iconsPerPage' => 4000,
					// default 100, how many icons per/page to display.
				],
				'dependency' => [
					'element' => 'type',
					'value' => 'entypo',
				],
			],
			[
				'type' => 'iconpicker',
				'heading' => esc_html__( 'Icon', 'js_composer' ),
				'param_name' => 'icon_linecons',
				'value' => 'vc_li vc_li-heart',
				// default value to backend editor admin_label.
				'settings' => [
					'emptyIcon' => false,
					// default true, display an "EMPTY" icon.
					'type' => 'linecons',
					'iconsPerPage' => 4000,
					// default 100, how many icons per/page to display.
				],
				'dependency' => [
					'element' => 'type',
					'value' => 'linecons',
				],
				'description' => esc_html__( 'Select icon from library.', 'js_composer' ),
            ],
			/* nectar addition */ 
			/* 
			array(
				'type' => 'iconpicker',
				'heading' => __( 'Icon', 'js_composer' ),
				'param_name' => 'icon_monosocial',
				'value' => 'vc-mono vc-mono-fivehundredpx',
				// default value to backend editor admin_label.
				'settings' => [
					'emptyIcon' => false,
					// default true, display an "EMPTY" icon.
					'type' => 'monosocial',
					'iconsPerPage' => 4000,
					// default 100, how many icons per/page to display.
				],
				'dependency' => [
					'element' => 'type',
					'value' => 'monosocial',
				),
				'description' => __( 'Select icon from library.', 'js_composer' ),
			),
			array(
				'type' => 'iconpicker',
				'heading' => __( 'Icon', 'js_composer' ),
				'param_name' => 'icon_material',
				'value' => 'vc-material vc-material-cake',
				// default value to backend editor admin_label.
				'settings' => [
					'emptyIcon' => false,
					// default true, display an "EMPTY" icon.
					'type' => 'material',
					'iconsPerPage' => 4000,
					// default 100, how many icons per/page to display.
				],
				'dependency' => [
					'element' => 'type',
					'value' => 'material',
			[
				),
				'description' => __( 'Select icon from library.', 'js_composer' ),
			),
			*/
			/* nectar addition end */ 
			[
				'type' => 'dropdown',
				'heading' => esc_html__( 'Icon color', 'js_composer' ),
				'param_name' => 'color',
				'value' => array_merge( vc_get_shared( 'colors' ), [ esc_html__( 'Custom color', 'js_composer' ) => 'custom' ] ),
				'description' => esc_html__( 'Select icon color.', 'js_composer' ),
				'param_holder_class' => 'vc_colored-dropdown',
			],
			[
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Custom color', 'js_composer' ),
				'param_name' => 'custom_color',
				'default_colorpicker_color' => '#000000',
				'description' => esc_html__( 'Select custom icon color.', 'js_composer' ),
				'dependency' => [
					'element' => 'color',
					'value' => 'custom',
				],
			],
			[
				'type' => 'dropdown',
				'heading' => esc_html__( 'Background shape', 'js_composer' ),
				'param_name' => 'background_style',
				'value' => [
					esc_html__( 'None', 'js_composer' ) => '',
					esc_html__( 'Circle', 'js_composer' ) => 'rounded',
					esc_html__( 'Square', 'js_composer' ) => 'boxed',
					esc_html__( 'Rounded', 'js_composer' ) => 'rounded-less',
					esc_html__( 'Outline Circle', 'js_composer' ) => 'rounded-outline',
					esc_html__( 'Outline Square', 'js_composer' ) => 'boxed-outline',
					esc_html__( 'Outline Rounded', 'js_composer' ) => 'rounded-less-outline',
				],
				'description' => esc_html__( 'Select background shape and style for icon.', 'js_composer' ),
			],
			[
				'type' => 'dropdown',
				'heading' => esc_html__( 'Background color', 'js_composer' ),
				'param_name' => 'background_color',
				'value' => array_merge( vc_get_shared( 'colors' ), [ esc_html__( 'Custom color', 'js_composer' ) => 'custom' ] ),
				'std' => 'grey',
				'description' => esc_html__( 'Select background color for icon.', 'js_composer' ),
				'param_holder_class' => 'vc_colored-dropdown',
				'dependency' => [
					'element' => 'background_style',
					'not_empty' => true,
				],
			],
			[
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Custom background color', 'js_composer' ),
				'param_name' => 'custom_background_color',
				'description' => esc_html__( 'Select custom icon background color.', 'js_composer' ),
				'dependency' => [
					'element' => 'background_color',
					'value' => 'custom',
				],
			],
			[
				'type' => 'dropdown',
				'heading' => esc_html__( 'Size', 'js_composer' ),
				'param_name' => 'size',
				'value' => array_merge( vc_get_shared( 'sizes' ), [ 'Extra Large' => 'xl' ] ),
				'std' => 'md',
				'description' => esc_html__( 'Icon size.', 'js_composer' ),
			],
			[
				'type' => 'dropdown',
				'heading' => esc_html__( 'Icon alignment', 'js_composer' ),
				'param_name' => 'align',
				'value' => [
					esc_html__( 'Left', 'js_composer' ) => 'left',
					esc_html__( 'Right', 'js_composer' ) => 'right',
					esc_html__( 'Center', 'js_composer' ) => 'center',
				],
				'description' => esc_html__( 'Select icon alignment.', 'js_composer' ),
			],
			[
				'type' => 'vc_link',
				'heading' => esc_html__( 'URL (Link)', 'js_composer' ),
				'param_name' => 'link',
				'description' => esc_html__( 'Add link to icon.', 'js_composer' ),
			],
			vc_map_add_css_animation(),
			[
				'type' => 'el_id',
				'heading' => esc_html__( 'Element ID', 'js_composer' ),
				'param_name' => 'el_id',
                'description' => sprintf( esc_html__( 'Enter element ID (Note: make sure it is unique and valid according to %sw3c specification%s).', 'js_composer' ), '<a href="https://www.w3schools.com/tags/att_global_id.asp" target="_blank">', '</a>' ),
            ],
			[
				'type' => 'textfield',
				'heading' => esc_html__( 'Extra class name', 'js_composer' ),
				'param_name' => 'el_class',
				'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
			],
			[
				'type' => 'css_editor',
				'heading' => esc_html__( 'CSS box', 'js_composer' ),
				'param_name' => 'css',
				'group' => esc_html__( 'Design Options', 'js_composer' ),
				'value' => [
					'margin-bottom' => '35px',
				],
			],
		],
		'js_view' => 'VcIconElementView_Backend',
	];
}
