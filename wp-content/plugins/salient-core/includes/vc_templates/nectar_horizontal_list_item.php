<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_enqueue_style( 'nectar-element-horizontal-list-item' );

extract(shortcode_atts(array(
	"columns" => "1",
	"column_layout_using_2_columns" => 'even',
	"column_layout_using_3_columns" => 'even',
	"column_layout_using_4_columns" => 'even',
	"col_1_text_align" => "left",
	"col_2_text_align" => "left",
	"col_3_text_align" => "left",
	"col_4_text_align" => "left",
	"col_1_text_element" => "p",
	"col_2_text_element" => "p",
	"col_3_text_element" => "p",
	"col_4_text_element" => "p",
	"col_1_content" => '',
	"col_2_content" => '',
	"col_3_content" => '',
	"col_4_content" => '',
	"cta_1_text" => '',
	"cta_1_url" => '',
	"cta_1_open_new_tab" => '',
	"cta_1_nofollow" => '',
	"cta_2_text" => '',
	"cta_2_url" => '',
	"cta_2_open_new_tab" => '',
	"cta_2_nofollow" => '',
	"open_new_tab" => '',
	"url" => '',
	"hover_effect" => 'default',
	"hover_color" => 'accent-color',
	'font_family' => 'p',
	'border_radius' => '0px',
	'icon_family' => '',
  'icon_fontawesome' => '',
  'icon_linecons' => '',
  'icon_iconsmind' => '',
  'icon_steadysets' => '',
	'icon_size' => '',
	'icon_image' => ''
), $atts));


if( $columns === '2' ) {
	$column_layout_to_use = $column_layout_using_2_columns;
}
else if( $columns === '3' ) {
	$column_layout_to_use = $column_layout_using_3_columns;
}
else if( $columns === '4' ) {
	$column_layout_to_use = $column_layout_using_4_columns;
}
else {
	$column_layout_to_use = 'default';
}

$hasbtn_class = (!empty($cta_1_text) || !empty($cta_2_text)) ? 'has-btn' : null;


switch($icon_family) {
	case 'fontawesome':
		$icon = $icon_fontawesome;
    wp_enqueue_style( 'font-awesome' );
		break;
	case 'steadysets':
		$icon = $icon_steadysets;
		break;
	case 'linea':
		$icon = $icon_linea;
		break;
	case 'linecons':
		$icon = $icon_linecons;
		break;
	case 'iconsmind':
		$icon = $icon_iconsmind;
		break;
	default:
		$icon = '';
		break;
}

$icon_markup = null;
$has_icon    = 'false';

// Dynamic style classes.
if( function_exists('nectar_el_dynamic_classnames') ) {
    $dynamic_el_styles = ' '. nectar_el_dynamic_classnames('nectar_horizontal_list_item', $atts);
} else {
	$dynamic_el_styles = '';
}

// SVG Icon.
if( $icon_family === 'iconsmind' ) {

	$icon_id        = 'nectar-iconsmind-icon-'.uniqid();
	$icon_markup    = '<span class="im-icon-wrap item-icon" data-size="'.esc_attr($icon_size).'"><span>';
	$converted_icon = str_replace('iconsmind-', '', $icon);

	require_once( SALIENT_CORE_ROOT_DIR_PATH.'includes/icons/class-nectar-icon.php' );

  $nectar_icon_class = new Nectar_Icon(array(
  'icon_name' => $converted_icon,
  'icon_library' => 'iconsmind',
  ));

  $icon_markup .= $nectar_icon_class->render_icon();

	$icon_markup .= '</span></span>';
	$has_icon = 'true';
}
// Regular Icon.
else {

	if( !empty($icon_family) && in_array($icon_family, array('fontawesome', 'iconsmind', 'steadysets', 'linecons')) ) {
		$has_icon    = 'true';
		$icon_markup = '<i class="item-icon ' . esc_attr($icon) .'" data-size="'.esc_attr($icon_size).'"></i>';
	}
	else if( !empty($icon_family) && 'custom' === $icon_family ) {
		$icon_img_markup = wp_get_attachment_image($icon_image, 'medium', '', array( 'class' => 'item-icon ' . esc_attr($icon_size) ) );
		if( !empty($icon_img_markup) ) {
			$has_icon    = 'true';
			$icon_markup = $icon_img_markup;
		}
	}
	else {
		$icon_markup = null;
	}
}


echo '<div class="nectar-hor-list-item '.$hasbtn_class.$dynamic_el_styles.'" data-hover-effect="'.esc_attr($hover_effect).'" data-br="'.esc_attr($border_radius).'" data-font-family="'.esc_attr($font_family).'" data-color="'.esc_attr($hover_color).'" data-columns="'.esc_attr($columns).'" data-column-layout="'.esc_attr($column_layout_to_use).'">';

	for($i = 0; $i < intval($columns); $i++) {

		$index_to_grab = $i+1;

		if(!isset($atts['col_'.$index_to_grab.'_text_align'])) {
			$atts['col_'.$index_to_grab.'_text_align'] = null;
		}
		if(!isset($atts['col_'.$index_to_grab.'_text_element'])) {
			$atts['col_'.$index_to_grab.'_text_element'] = null;
		}
		if(!isset($atts['col_'.$index_to_grab.'_content'])) {
			$atts['col_'.$index_to_grab.'_content'] = '';
		}

		$cta_1_markup = $cta_2_markup = '';

		// Add btns into last col.
		if( $index_to_grab == intval($columns) ) {

			if( !empty($cta_1_text) ) {

				$btn_target_markup   = (!empty($cta_1_open_new_tab) && $cta_1_open_new_tab == 'true' ) ? 'target="_blank"' : null;
				$btn_nofollow_markup = (!empty($cta_1_nofollow) && $cta_1_nofollow == 'true' ) ? ' rel="nofollow"' : null;
				$cta_1_markup        = '<a class="nectar-list-item-btn" href="'.esc_url($cta_1_url).'" '.$btn_target_markup . $btn_nofollow_markup.'>'.wp_kses_post($cta_1_text).'</a>';
			}
			if( !empty($cta_2_text) ) {

				$btn_target_markup = (!empty($cta_2_open_new_tab) && $cta_2_open_new_tab == 'true' ) ? 'target="_blank"' : null;
				$btn_nofollow_markup = (!empty($cta_2_nofollow) && $cta_2_nofollow == 'true' ) ? ' rel="nofollow"' : null;
				$cta_2_markup      = '<a class="nectar-list-item-btn second" href="'.esc_url($cta_2_url).'" '.$btn_target_markup . $btn_nofollow_markup.'>'.wp_kses_post($cta_2_text).'</a>';
			}
		}

		// Add icon to first col.
		if( $i == 0 ) {
			$icon_tag = $icon_markup;
			$icon_attr = ' data-icon="'.esc_attr($has_icon).'"';
		} else {
			$icon_attr = null;
			$icon_tag  = null;
		}

		$opening_tag = null;
		$closing_tag = null;

		if( !empty($atts['col_'.$index_to_grab.'_text_element']) && $atts['col_'.$index_to_grab.'_text_element'] !== 'p' ) {

			if (!in_array($atts['col_'.$index_to_grab.'_text_element'], array('h1', 'h2', 'h3', 'h4', 'h5', 'h6','p','span'))) {
				$atts['col_'.$index_to_grab.'_text_element'] = 'p';
			}
			$opening_tag = '<' . $atts['col_'.$index_to_grab.'_text_element'] . '>';
			$closing_tag = '</' . $atts['col_'.$index_to_grab.'_text_element'] . '>';
		}

		echo '<div class="nectar-list-item"'.$icon_attr.' data-text-align="'.esc_attr($atts['col_'.$index_to_grab.'_text_align']).'">'. $icon_tag . $opening_tag . do_shortcode(wp_kses_post($atts['col_'.$index_to_grab.'_content'])) . $closing_tag . $cta_1_markup . $cta_2_markup. '</div>';
	}

$url_markup = '';

if( !empty($url) ) {

	$target = '';
	$aria_label = '';
	if(!empty($open_new_tab) && $open_new_tab === 'true') {
		$target = ' target="_blank"';
	}
	if(isset($atts['col_1_content']) && !empty($atts['col_1_content'])) {
		$aria_label = ' aria-label="'.esc_attr(wp_strip_all_tags($atts['col_1_content'])).'"';
	}
	$url_markup = '<a class="full-link"'.$target.$aria_label.' href="'.esc_url($url).'"> </a>';
}

echo wp_kses_post( $url_markup ).'</div>';

?>