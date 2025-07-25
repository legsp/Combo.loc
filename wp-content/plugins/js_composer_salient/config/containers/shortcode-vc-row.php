<?php
/**
 * Configuration file for [vc_row] shortcode of 'Row' element.
 *
 * @see https://kb.wpbakery.com/docs/inner-api/vc_map/ for more detailed information about element attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

return [
	'name' => esc_html__( 'Row', 'js_composer' ),
	'is_container' => true,
	'icon' => 'icon-wpb-row',
	'show_settings_on_create' => false,
	'category' => esc_html__( 'Content', 'js_composer' ),
	'class' => 'vc_main-sortable-element',
	'description' => esc_html__( 'Place content elements inside the row', 'js_composer' ),
	'params' => [
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Row Title', 'js_composer' ),
			'param_name' => 'row_title',
			'description' => esc_html__( 'This title is visible only in the admin area and helps site editors differentiate rows.', 'js_composer' ),
		],
		[
			'type' => 'dropdown',
			'heading' => esc_html__( 'Row stretch', 'js_composer' ),
			'param_name' => 'full_width',
			'value' => [
				esc_html__( 'Default', 'js_composer' ) => '',
				esc_html__( 'Stretch row', 'js_composer' ) => 'stretch_row',
				esc_html__( 'Stretch row and content', 'js_composer' ) => 'stretch_row_content',
				esc_html__( 'Stretch row and content (no paddings)', 'js_composer' ) => 'stretch_row_content_no_spaces',
			],
			'description' => esc_html__( 'Select stretching options for row and content (Note: stretched may not work properly if parent container has "overflow: hidden" CSS property).', 'js_composer' ),
		],
		[
			'type' => 'dropdown',
			'heading' => esc_html__( 'Columns gap', 'js_composer' ),
			'param_name' => 'gap',
			'value' => [
				'0px' => '0',
				'1px' => '1',
				'2px' => '2',
				'3px' => '3',
				'4px' => '4',
				'5px' => '5',
				'10px' => '10',
				'15px' => '15',
				'20px' => '20',
				'25px' => '25',
				'30px' => '30',
				'35px' => '35',
			],
			'std' => '0',
			'description' => esc_html__( 'Select gap between columns in row.', 'js_composer' ),
		],
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Minimum height', 'js_composer' ),
			'param_name' => 'min_height',
			'description' => sprintf( esc_html__( 'Set minimum height for the container.', 'js_composer' ) ),
		],
		[
			'type' => 'checkbox',
			'heading' => esc_html__( 'Full height row?', 'js_composer' ),
			'param_name' => 'full_height',
			'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox', /* nectar addition - class */
			'description' => esc_html__( 'If checked row will be set to full height.', 'js_composer' ),
			'value' => [ esc_html__( 'Yes', 'js_composer' ) => 'yes' ],
		],
		[
			'type' => 'dropdown',
			'heading' => esc_html__( 'Columns position', 'js_composer' ),
			'param_name' => 'columns_placement',
			'value' => [
				esc_html__( 'Middle', 'js_composer' ) => 'middle',
				esc_html__( 'Top', 'js_composer' ) => 'top',
				esc_html__( 'Bottom', 'js_composer' ) => 'bottom',
				esc_html__( 'Stretch', 'js_composer' ) => 'stretch',
			],
			'description' => esc_html__( 'Select columns position within row.', 'js_composer' ),
			'dependency' => [
				'element' => 'full_height',
				'not_empty' => true,
			],
		],
		[
			'type' => 'checkbox',
			'heading' => esc_html__( 'Equal height', 'js_composer' ),
			'param_name' => 'equal_height',
			'edit_field_class' => 'vc_col-xs-12 salient-fancy-checkbox', /* nectar addition - class */
			'description' => esc_html__( 'If checked columns will be set to equal height.', 'js_composer' ),
			'value' => array( esc_html__( 'Yes', 'js_composer' ) => 'yes' ),
        ],
		/* nectar addition */ 
		/*
		array(
			'type' => 'checkbox',
			'heading' => __( 'Reverse columns in RTL', 'js_composer' ),
			'param_name' => 'rtl_reverse',
			'description' => __( 'If checked columns will be reversed in RTL.', 'js_composer' ),
			'value' => array( __( 'Yes', 'js_composer' ) => 'yes' ),
		),
		*/
		[
			'type' => 'dropdown',
			'heading' => esc_html__( 'Content position', 'js_composer' ),
			'param_name' => 'content_placement',
			'value' => [
				esc_html__( 'Default', 'js_composer' ) => '',
				esc_html__( 'Top', 'js_composer' ) => 'top',
				esc_html__( 'Middle', 'js_composer' ) => 'middle',
				esc_html__( 'Bottom', 'js_composer' ) => 'bottom',
			],
			'description' => esc_html__( 'Select content position within columns.', 'js_composer' ),
		],
		[
			'type' => 'checkbox',
			'heading' => esc_html__( 'Use video background?', 'js_composer' ),
			'param_name' => 'video_bg',
			'description' => esc_html__( 'If checked, video will be used as row background.', 'js_composer' ),
			'value' => [ esc_html__( 'Yes', 'js_composer' ) => 'yes' ],
		],
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'YouTube link', 'js_composer' ),
			'param_name' => 'video_bg_url',
			'value' => 'https://www.youtube.com/watch?v=lMJXxhRFO1k',
			// default video url.
			'description' => esc_html__( 'Add YouTube link.', 'js_composer' ),
			'dependency' => [
				'element' => 'video_bg',
				'not_empty' => true,
			],
		],
		[
			'type' => 'dropdown',
			'heading' => esc_html__( 'Parallax', 'js_composer' ),
			'param_name' => 'video_bg_parallax',
			'value' => [
				esc_html__( 'None', 'js_composer' ) => '',
				esc_html__( 'Simple', 'js_composer' ) => 'content-moving',
				esc_html__( 'With fade', 'js_composer' ) => 'content-moving-fade',
			],
			'description' => esc_html__( 'Add parallax type background for row.', 'js_composer' ),
			'dependency' => [
				'element' => 'video_bg',
				'not_empty' => true,
			],
		],
		[
			'type' => 'dropdown',
			'heading' => esc_html__( 'Parallax', 'js_composer' ),
			'param_name' => 'parallax',
			'value' => [
				esc_html__( 'None', 'js_composer' ) => '',
				esc_html__( 'Simple', 'js_composer' ) => 'content-moving',
				esc_html__( 'With fade', 'js_composer' ) => 'content-moving-fade',
			],
			'description' => esc_html__( 'Add parallax type background for row (Note: If no image is specified, parallax will use background image from Design Options).', 'js_composer' ),
			'dependency' => [
				'element' => 'video_bg',
				'is_empty' => true,
			],
		],
		[
			'type' => 'attach_image',
			'heading' => esc_html__( 'Image', 'js_composer' ),
			'param_name' => 'parallax_image',
			'value' => '',
			'description' => esc_html__( 'Select image from media library.', 'js_composer' ),
			'dependency' => [
				'element' => 'parallax',
				'not_empty' => true,
			],
		],
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Parallax speed', 'js_composer' ),
			'param_name' => 'parallax_speed_video',
			'value' => '1.5',
			'description' => esc_html__( 'Enter parallax speed ratio (Note: Default value is 1.5, min value is 1)', 'js_composer' ),
			'dependency' => [
				'element' => 'video_bg_parallax',
				'not_empty' => true,
			],
		],
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Parallax speed', 'js_composer' ),
			'param_name' => 'parallax_speed_bg',
			'value' => '1.5',
			'description' => esc_html__( 'Enter parallax speed ratio (Note: Default value is 1.5, min value is 1)', 'js_composer' ),
			'dependency' => [
				'element' => 'parallax',
				'not_empty' => true,
			],
		],
		vc_map_add_css_animation( false ),
		[
			'type' => 'el_id',
			'heading' => esc_html__( 'Row ID', 'js_composer' ),
			'param_name' => 'el_id',
			'description' => sprintf( esc_html__( 'Enter element ID (Note: make sure it is unique and valid according to %1$sw3c specification%2$s).', 'js_composer' ), '<a href="https://www.w3schools.com/tags/att_global_id.asp" target="_blank">', '</a>' ),
		],
		[
			'type' => 'checkbox',
			'heading' => esc_html__( 'Disable row', 'js_composer' ),
			'param_name' => 'disable_element',
			// Inner param name.
			'description' => esc_html__( 'If checked the row won\'t be visible on the public side of your website. You can switch it back any time.', 'js_composer' ),
			'value' => [ esc_html__( 'Yes', 'js_composer' ) => 'yes' ],
		],
		[
			'type' => 'textfield',
			'heading' => esc_html__( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
        ],
		/* nectar addition */ 
		/*
		array(
			'type' => 'css_editor',
			'heading' => __( 'CSS box', 'js_composer' ),
			'param_name' => 'css',
			'group' => __( 'Design Options', 'js_composer' ),
		),
		*/
		/* nectar addition end */
    ],
	'js_view' => 'VcRowView',
];
