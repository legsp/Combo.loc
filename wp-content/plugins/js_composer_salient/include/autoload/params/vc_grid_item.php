<?php
/**
 * Autoload hooks related grids
 *
 * @note we require our autoload files everytime and everywhere after plugin load.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Output for grid item form field.
 *
 * @param array $settings
 * @param int $value
 * @return string
 */
function vc_vc_grid_item_form_field( $settings, $value ) {
	/* nectar addition */ 
	$enable_raw_wpbakery_post_grid = false;
	if( has_filter('salient_enable_core_wpbakery_post_grid') ) {
		$enable_raw_wpbakery_post_grid = apply_filters('salient_enable_core_wpbakery_post_grid', $enable_raw_wpbakery_post_grid);
	}
	
	if( true === $enable_raw_wpbakery_post_grid ) {
		require_once vc_path_dir( 'PARAMS_DIR', 'vc_grid_item/editor/class-vc-grid-item-editor.php' );
		require_once vc_path_dir( 'PARAMS_DIR', 'vc_grid_item/class-vc-grid-item.php' );
	}
	
	/* nectar addition end */ 
	$output = '<div data-vc-grid-element="container">' . '<select data-vc-grid-element="value" type="hidden" name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value wpb-select ' . esc_attr( $settings['param_name'] ) . ' ' . esc_attr( $settings['type'] ) . '_field" ' . '>';
	$vc_grid_item_templates = Vc_Grid_Item::predefinedTemplates();
	if ( is_array( $vc_grid_item_templates ) ) {
		foreach ( $vc_grid_item_templates as $key => $data ) {
			$output .= '<option data-vc-link="' . esc_url( admin_url( 'post-new.php?post_type=vc_grid_item&vc_gitem_template=' . $key ) ) . '" value="' . esc_attr( $key ) . '"' . ( $key === $value ? ' selected="true"' : '' ) . '>' . esc_html( $data['name'] ) . '</option>';
		}
	}

	$grid_item_posts = get_posts( [
		'posts_per_page' => '-1',
		'orderby' => 'post_title',
		'post_type' => Vc_Grid_Item_Editor::postType(),
	] );
	foreach ( $grid_item_posts as $post ) {
		$output .= '<option  data-vc-link="' . esc_url( get_edit_post_link( $post->ID ) ) . '"value="' . esc_attr( $post->ID ) . '"' . ( (string) $post->ID === $value ? ' selected="true"' : '' ) . '>' . esc_html( _draft_or_post_title( $post ) ) . '</option>';
	}
	$output .= '</select></div>';

	return $output;
}

/**
 * Load vc_grid_item param
 */
function vc_load_vc_grid_item_param() {
	/* nectar addition */ 
	$enable_raw_wpbakery_post_grid = false;
	if( has_filter('salient_enable_core_wpbakery_post_grid') ) {
		$enable_raw_wpbakery_post_grid = apply_filters('salient_enable_core_wpbakery_post_grid', $enable_raw_wpbakery_post_grid);
	}
	if( true === $enable_raw_wpbakery_post_grid ) {
		vc_add_shortcode_param(
			'vc_grid_item',
			'vc_vc_grid_item_form_field'
		); 
	}
	/* nectar addition end */ 
}

add_action( 'vc_load_default_params', 'vc_load_vc_grid_item_param' );
/**
 * Add target attribute to link.
 *
 * @param string $target
 * @return string
 */
function vc_gitem_post_data_get_link_target_frontend_editor( $target ) {
	return ' target="_blank"';
}

/**
 * Add rel attribute to link.
 *
 * @param string $rel
 * @return string
 */
function vc_gitem_post_data_get_link_rel_frontend_editor( $rel ) {
	return ' rel="' . esc_attr( $rel ) . '"';
}

/**
 * Create link.
 *
 * @param array $atts
 * @param string $default_class
 * @param string $title
 * @return string
 */
function vc_gitem_create_link( $atts, $default_class = '', $title = '' ) {
	$link = '';
	$target = '';
	$rel = '';
	$title_attr = '';
	$css_class = 'vc_gitem-link' . ( strlen( $default_class ) > 0 ? ' ' . $default_class : '' );
	if ( isset( $atts['link'] ) ) {
		if ( 'custom' === $atts['link'] && ! empty( $atts['url'] ) ) {
			$link = vc_build_link( $atts['url'] );
			if ( strlen( $link['target'] ) ) {
				$target = ' target="' . esc_attr( $link['target'] ) . '"';
			}
			if ( strlen( $link['rel'] ) ) {
				$rel = ' rel="' . esc_attr( $link['rel'] ) . '"';
			}
			if ( strlen( $link['title'] ) ) {
				$title = $link['title'];
			}
			$link = 'a href="' . esc_url( $link['url'] ) . '" class="' . esc_attr( $css_class ) . '"';
		} elseif ( 'post_link' === $atts['link'] ) {
			$target = isset( $atts['link_target'] ) && $atts['link_target'] ? ' target="_blank"' : '';
			$link = 'a href="{{ post_link_url }}" class="' . esc_attr( $css_class ) . '"' . $target;
			if ( ! strlen( $title ) ) {
				$title = '{{ post_title }}';
			}
		} elseif ( 'post_author' === $atts['link'] ) {
			$target = isset( $atts['link_target'] ) && $atts['link_target'] ? ' target="_blank"' : '';
			$link = 'a href="{{ post_author_href }}" class="' . esc_attr( $css_class ) . '"' . $target;
			if ( ! strlen( $title ) ) {
				$title = '{{ post_author }}';
			}
		} elseif ( 'image' === $atts['link'] ) {
			$target = isset( $atts['link_target'] ) && $atts['link_target'] ? ' target="_blank"' : '';
			$link = 'a{{ post_image_url_href }} class="' . esc_attr( $css_class ) . '"' . $target;
		} elseif ( 'image_lightbox' === $atts['link'] ) {
			$target = isset( $atts['link_target'] ) && $atts['link_target'] ? ' target="_blank"' : '';
			$link = 'a{{ post_image_url_attr_prettyphoto:' . $css_class . ' }}' . $target;
		} elseif ( 'image_full' === $atts['link'] ) {
			$target = isset( $atts['link_target'] ) && $atts['link_target'] ? ' target="_blank"' : '';
			$link = 'a{{ post_full_image_url_href }} class="' . esc_attr( $css_class ) . '"' . $target;
		} elseif ( 'image_full_lightbox' === $atts['link'] ) {
			$target = isset( $atts['link_target'] ) && $atts['link_target'] ? ' target="_blank"' : '';
			$link = 'a{{ post_full_image_url_attr_prettyphoto:' . $css_class . ' }}' . $target;
		}
	}
	if ( strlen( $title ) > 0 ) {
		$title_attr = ' title="' . esc_attr( $title ) . '"';
	}

	return apply_filters( 'vc_gitem_post_data_get_link_link', $link, $atts, $css_class ) . apply_filters( 'vc_gitem_post_data_get_link_target', $target, $atts ) . apply_filters( 'vc_gitem_post_data_get_link_rel', $rel, $atts ) . apply_filters( 'vc_gitem_post_data_get_link_title', $title_attr, $atts );
}

/**
 * Create real link.
 *
 * @param array $atts
 * @param WP_Post $post
 * @param string $default_class
 * @param string $title
 * @return string
 */
function vc_gitem_create_link_real( $atts, $post, $default_class = '', $title = '' ) {
	$link = '';
	$target = '';
	$rel = '';
	$title_attr = '';
	$link_css_class = 'vc_gitem-link';
	if ( isset( $atts['link'] ) ) {
		$link_css_class = 'vc_gitem-link' . ( strlen( $default_class ) > 0 ? ' ' . $default_class : '' );
		if ( strlen( $atts['el_class'] ) > 0 ) {
			$link_css_class .= ' ' . $atts['el_class'];
		}
		$link_css_class = trim( preg_replace( '/\s+/', ' ', $link_css_class ) );
		if ( 'custom' === $atts['link'] && ! empty( $atts['url'] ) ) {
			$link = vc_build_link( $atts['url'] );
			if ( strlen( $link['target'] ) ) {
				$target = ' target="' . esc_attr( $link['target'] ) . '"';
			}
			if ( strlen( $link['rel'] ) ) {
				$rel = ' rel="' . esc_attr( $link['rel'] ) . '"';
			}
			if ( strlen( $link['title'] ) ) {
				$title = $link['title'];
			}
			$link = 'a href="' . esc_url( $link['url'] ) . '" class="' . esc_attr( $link_css_class ) . '"';
		} elseif ( 'post_link' === $atts['link'] ) {
			$target = isset( $atts['link_target'] ) && $atts['link_target'] ? ' target="_blank"' : '';
			$link = 'a href="' . esc_url( get_permalink( $post->ID ) ) . '" class="' . esc_attr( $link_css_class ) . '"' . $target;
			if ( ! strlen( $title ) ) {
				$title = the_title( '', '', false );
			}
		} elseif ( 'image' === $atts['link'] ) {
			$target = isset( $atts['link_target'] ) && $atts['link_target'] ? ' target="_blank"' : '';
			$href_link = vc_gitem_template_attribute_post_image_url( '', [
				'post' => $post,
				'data' => '',
			] );
			$link = 'a href="' . esc_url( $href_link ) . '" class="' . esc_attr( $link_css_class ) . '"' . $target;
		} elseif ( 'image_lightbox' === $atts['link'] ) {
			$target = isset( $atts['link_target'] ) && $atts['link_target'] ? ' target="_blank"' : '';
			$link = 'a' . vc_gitem_template_attribute_post_image_url_attr_lightbox( '', [
				'post' => $post,
				'data' => esc_attr( $link_css_class ),
			] ) . $target;
		} elseif ( 'image_full' === $atts['link'] ) {
			$target = isset( $atts['link_target'] ) && $atts['link_target'] ? ' target="_blank"' : '';
			$href_link = vc_gitem_template_attribute_post_full_image_url( '', [
				'post' => $post,
				'data' => '',
			] );
			$link = 'a href="' . esc_url( $href_link ) . '" class="' . esc_attr( $link_css_class ) . '"' . $target;
		} elseif ( 'image_full_lightbox' === $atts['link'] ) {
			$target = isset( $atts['link_target'] ) && $atts['link_target'] ? ' target="_blank"' : '';
			$link = 'a' . vc_gitem_template_attribute_post_full_image_url_attr_lightbox( '', [
				'post' => $post,
				'data' => esc_attr( $link_css_class ),
			] ) . $target;
		}
	}
	if ( strlen( $title ) > 0 ) {
		$title_attr = ' title="' . esc_attr( $title ) . '"';
	}

	return apply_filters( 'vc_gitem_post_data_get_link_real_link', $link, $atts, $post, $link_css_class ) . apply_filters( 'vc_gitem_post_data_get_link_real_target', $target, $atts, $post ) . apply_filters( 'vc_gitem_post_data_get_link_real_rel', $rel, $atts, $post ) . apply_filters( 'vc_gitem_post_data_get_link_real_title', $title_attr, $atts, $post );
}

/**
 * Get link.
 *
 * @param string $link
 * @return string
 */
function vc_gitem_post_data_get_link_link_frontend_editor( $link ) {
	return empty( $link ) ? 'a' : $link;
}

if ( vc_is_page_editable() ) {
	add_filter( 'vc_gitem_post_data_get_link_link', 'vc_gitem_post_data_get_link_link_frontend_editor' );
	add_filter( 'vc_gitem_post_data_get_link_real_link', 'vc_gitem_post_data_get_link_link_frontend_editor' );
	add_filter( 'vc_gitem_post_data_get_link_target', 'vc_gitem_post_data_get_link_target_frontend_editor' );
	add_filter( 'vc_gitem_post_data_get_link_rel', 'vc_gitem_post_data_get_link_rel_frontend_editor' );
	add_filter( 'vc_gitem_post_data_get_link_real_target', 'vc_gitem_post_data_get_link_target_frontend_editor' );
	add_filter( 'vc_gitem_post_data_get_link_real_rel', 'vc_gitem_post_data_get_link_rel_frontend_editor' );
}
