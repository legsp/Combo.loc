<?php
/**
 * WPBakery Page Builder helpers functions.
 *
 * We use helper functions inside our plugin core for simple duplication actions.
 *
 * @package WPBakeryPageBuilder
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Check if this file is loaded in js_composer.
if ( ! defined( 'WPB_VC_VERSION' ) ) {
	die( '-1' );
}

/**
 * Get image by size.
 *
 * @param array $params
 *
 * @return array|bool
 * @since 4.2
 * vc_filter: vc_wpb_getimagesize - to override output of this function.
 */
function wpb_getImageBySize( $params = array() ) { // phpcs:ignore
	$params = array_merge( [
		'post_id' => null,
		'attach_id' => null,
		'thumb_size' => 'thumbnail',
		'class' => '',
	], $params );

	if ( ! $params['thumb_size'] ) {
		$params['thumb_size'] = 'thumbnail';
	}

	if ( ! $params['attach_id'] && ! $params['post_id'] ) {
		return false;
	}

	$post_id = $params['post_id'];

	$attach_id = $post_id ? get_post_thumbnail_id( $post_id ) : $params['attach_id'];
	$attach_id = apply_filters( 'wpml_object_id', $attach_id, 'attachment', true );
	$thumb_size = $params['thumb_size'];
	$thumb_class = ( isset( $params['class'] ) && '' !== $params['class'] ) ? $params['class'] . ' ' : '';

	global $_wp_additional_image_sizes;
	$thumbnail = '';

	$sizes = [
		'thumbnail',
		'thumb',
		'medium',
		'large',
		'full',
	];
	if ( is_string( $thumb_size ) && ( ( ! empty( $_wp_additional_image_sizes[ $thumb_size ] ) && is_array( $_wp_additional_image_sizes[ $thumb_size ] ) ) || in_array( $thumb_size, $sizes, true ) ) ) {
		$attachment = get_post( $attach_id );
		$title = trim( wp_strip_all_tags( $attachment->post_title ) );
		$attributes = [
			'class' => $thumb_class . 'attachment-' . $thumb_size,
			'title' => $title,
			'alt'   => trim( esc_attr( do_shortcode( get_post_meta( $attach_id, '_wp_attachment_image_alt', true ) ) ) ),
		];

		$thumbnail = wp_get_attachment_image( $attach_id, $thumb_size, false, $attributes );
	} elseif ( $attach_id ) {
		if ( is_string( $thumb_size ) ) {
			preg_match_all( '/\d+/', $thumb_size, $thumb_matches );
			if ( isset( $thumb_matches[0] ) ) {
				$thumb_size = [];
				$count = count( $thumb_matches[0] );
				if ( $count > 1 ) {
					$thumb_size[] = $thumb_matches[0][0]; // width.
					$thumb_size[] = $thumb_matches[0][1]; // height.
				} elseif ( 1 === $count ) {
					$thumb_size[] = $thumb_matches[0][0]; // width.
					$thumb_size[] = $thumb_matches[0][0]; // height.
				} else {
					$thumb_size = false;
				}
			}
		}
		if ( is_array( $thumb_size ) ) {
			// Resize image to custom size.
			$p_img = wpb_resize( $attach_id, null, $thumb_size[0], $thumb_size[1], true );
			$alt = trim( esc_attr( do_shortcode( get_post_meta( $attach_id, '_wp_attachment_image_alt', true ) ) ) );
			$attachment = get_post( $attach_id );
			if ( ! empty( $attachment ) ) {
				$title = trim( wp_strip_all_tags( $attachment->post_title ) );

				if ( empty( $alt ) ) {
					$alt = trim( wp_strip_all_tags( $attachment->post_excerpt ) ); // If not, Use the Caption.
				}
				if ( empty( $alt ) ) {
					$alt = $title;
				}
				if ( $p_img ) {
					$attributes = [
						'class' => $thumb_class,
						'src' => $p_img['url'],
						'width' => $p_img['width'],
						'height' => $p_img['height'],
						'alt' => $alt,
						'title' => $title,
					];

					$attributes = vc_stringify_attributes( vc_add_lazy_loading_attribute( $attributes ) );

					$thumbnail = '<img ' . $attributes . ' />';
				}
			}
		}
	}

	$p_img_large = wp_get_attachment_image_src( $attach_id, 'large' );

	return apply_filters( 'vc_wpb_getimagesize', [
		'thumbnail' => $thumbnail,
		'p_img_large' => $p_img_large,
	], $attach_id, $params );
}

/**
 * Get image data by source where image obtained from.
 *
 * @since 7.4
 * @param string $source
 * @param int $post_id
 * @param int $image_id
 * @param string $img_size
 * @return array
 */
function wpb_get_image_data_by_source( $source, $post_id, $image_id, $img_size ) { // phpcs:ignore:Generic.Metrics.CyclomaticComplexity.TooHigh
	$image_src = '';
	switch ( $source ) {
		case 'media_library':
		case 'featured_image':
			if ( 'featured_image' === $source ) {
				if ( $post_id && has_post_thumbnail( $post_id ) ) {
					$img_id = get_post_thumbnail_id( $post_id );
				} else {
					$img_id = 0;
				}
			} else {
				$img_id = preg_replace( '/[^\d]/', '', $image_id );
			}

			if ( ! $img_size ) {
				$img_size = 'thumbnail';
			}

			if ( $img_id ) {
				$image_src = wp_get_attachment_image_src( $img_id, $img_size );
				if ( $image_src ) {
					$image_src = $image_src[0];
				}
			}
			$alt_text = get_post_meta( $img_id, '_wp_attachment_image_alt', true );

			break;

		case 'external_link':
			if ( ! empty( $params['custom_src'] ) ) {
				$image_src = $params['custom_src'];
			}
			$alt_text = '';
			break;
	}

	return [
		'image_src' => $image_src,
		'image_alt' => $alt_text,
	];
}

/**
 * Add `loading` attribute with param lazy to attribute list.
 *
 * @param array $attributes
 * @return array
 * @since 7.1
 */
function vc_add_lazy_loading_attribute( $attributes ) {
	if ( ! is_array( $attributes ) ) {
		$attributes = [];
	}

	$attributes['loading'] = 'lazy';

	return $attributes;
}

/**
 * Get image by size.
 *
 * @param int $id
 * @param string $size
 * @return array|false|mixed|string
 */
function vc_get_image_by_size( $id, $size ) {
	global $_wp_additional_image_sizes;

	$sizes = [
		'thumbnail',
		'thumb',
		'medium',
		'large',
		'full',
	];
	if ( is_string( $size ) && ( ( ! empty( $_wp_additional_image_sizes[ $size ] ) && is_array( $_wp_additional_image_sizes[ $size ] ) ) || in_array( $size, $sizes, true ) ) ) {
		return wp_get_attachment_image_src( $id, $size );
	} else {
		if ( is_string( $size ) ) {
			preg_match_all( '/\d+/', $size, $thumb_matches );
			if ( isset( $thumb_matches[0] ) ) {
				$size = [];
				$count = count( $thumb_matches[0] );
				if ( $count > 1 ) {
					$size[] = $thumb_matches[0][0]; // width.
					$size[] = $thumb_matches[0][1]; // height.
				} elseif ( 1 === $count ) {
					$size[] = $thumb_matches[0][0]; // width.
					$size[] = $thumb_matches[0][0]; // height.
				} else {
					$size = false;
				}
			}
		}
		if ( is_array( $size ) ) {
			// Resize image to custom size.
			$p_img = wpb_resize( $id, null, $size[0], $size[1], true );

			return $p_img['url'];
		}
	}

	return '';
}

/**
 * Convert vc_col-sm-3 to 1/4
 *
 * @param string $width
 *
 * @return string
 * @since 4.2
 */
function wpb_translateColumnWidthToFractional( $width ) { // phpcs:ignore
	switch ( $width ) {
		case 'vc_col-sm-2':
			$w = '1/6';
			break;
		case 'vc_col-sm-3':
			$w = '1/4';
			break;
		case 'vc_col-sm-4':
			$w = '1/3';
			break;
		case 'vc_col-sm-6':
			$w = '1/2';
			break;
		case 'vc_col-sm-8':
			$w = '2/3';
			break;
		case 'vc_col-sm-9':
			$w = '3/4';
			break;
		case 'vc_col-sm-12':
			$w = '1/1';
			break;

		default:
			$w = is_string( $width ) ? $width : '1/1';
	}

	return $w;
}

/**
 * Column width to span translate.
 *
 * @param string $width
 *
 * @return bool|string
 * @since 4.2
 */
function wpb_translateColumnWidthToSpan( $width ) { // phpcs:ignore
	$output = $width;
	preg_match( '/(\d+)\/(\d+)/', $width, $matches );

	if ( ! empty( $matches ) ) {
		$part_x = (int) $matches[1];
		$part_y = (int) $matches[2];
		if ( $part_x > 0 && $part_y > 0 ) {
			$value = ceil( $part_x / $part_y * 12 );
			if ( $value > 0 && $value <= 12 ) {
				$output = 'vc_col-sm-' . $value;
			}
		}
	}
	if ( preg_match( '/\d+\/5$/', $width ) ) {
		$output = 'vc_col-sm-' . $width;
	}

	return apply_filters( 'vc_translate_column_width_class', $output, $width );
}

/**
 * Remove wpautop from content.
 *
 * @param string $content
 * @param bool $autop
 *
 * @return string
 * @since 4.2
 */
function wpb_js_remove_wpautop( $content, $autop = false ) {

	if ( $autop ) {
		$content = wpautop( preg_replace( '/<\/?p\>/', "\n", $content ) . "\n" );
	}

	return do_shortcode( shortcode_unautop( $content ) );
}

if ( ! function_exists( 'shortcode_exists' ) ) {
	/**
	 * Check if a shortcode is registered in WordPress.
	 *
	 * Examples: shortcode_exists( 'caption' ) - will return true.
	 * shortcode_exists( 'blah' ) - will return false.
	 *
	 * @param bool $shortcode
	 *
	 * @return bool
	 * @since 4.2
	 */
	function shortcode_exists( $shortcode = false ) {
		global $shortcode_tags;

		if ( ! $shortcode ) {
			return false;
		}

		if ( array_key_exists( $shortcode, $shortcode_tags ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'vc_siteAttachedImages' ) ) {
	/**
	 *  Helper function which returns list of site attached images, and if image is attached to the current post it adds class 'added'
	 *
	 * @param array $att_ids
	 *
	 * @return string
	 * @since 4.11
	 */
	function vc_siteAttachedImages( $att_ids = array() ) { // phpcs:ignore
		$output = '';

		$limit = (int) apply_filters( 'vc_site_attached_images_query_limit', - 1 );
		$media_images = get_posts( 'post_type=attachment&orderby=ID&numberposts=' . $limit );
		foreach ( $media_images as $image_post ) {
			$thumb_src = wp_get_attachment_image_src( $image_post->ID, 'thumbnail' );
			$thumb_src = $thumb_src[0];

			$class = ( in_array( $image_post->ID, $att_ids, true ) ) ? ' class="added"' : '';

			$output .= '<li' . $class . '>
						<img rel="' . esc_attr( $image_post->ID ) . '" src="' . esc_url( $thumb_src ) . '" />
						<span class="img-added">' . esc_html__( 'Added', 'js_composer' ) . '</span>
					</li>';
		}

		if ( '' !== $output ) {
			$output = '<ul class="gallery_widget_img_select">' . $output . '</ul>';
		}

		return $output;
	}
}

/**
 * Get attached images to list.
 *
 * @param array $images IDs or srcs of images.
 *
 * @return string
 * @since 5.8
 */
function vc_field_attached_images( $images = [] ) {
	$output = '';

	foreach ( $images as $image ) {
		if ( is_numeric( $image ) ) {
			$thumb_src = wp_get_attachment_image_src( $image, 'thumbnail' );
			/* nectar addition */
			$thumb_alt = get_post_meta($image, '_wp_attachment_image_alt', TRUE);
			/* nectar addition end */
			$thumb_src = isset( $thumb_src[0] ) ? $thumb_src[0] : '';
		} else {
			$thumb_src = $image;
		}

		if ( $thumb_src ) {
			/* nectar addition */
			$output .= '
			<li class="added">
				<img rel="' . esc_attr( $image ) . '" alt="'.esc_html( $thumb_alt ).'" src="' . esc_url( $thumb_src ) . '" />
				<a href="javascript:;" class="vc_icon-remove"><i class="vc-composer-icon vc-c-icon-close"></i></a>
			</li>';
			/* nectar addition end */
		}
	}

	return $output;
}



/* nectar addition */ 
if( !function_exists('fjarrett_get_attachment_id_by_url') ) {
	function fjarrett_get_attachment_id_by_url( $url ) {
	 
		// Split the $url into two parts with the wp-content directory as the separator.
		$parse_url = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );
		 
		// Get the host of the current site and the host of the $url, ignoring www.
		$this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
		$file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );
		 
		// Return nothing if there aren't any $url parts or if the current host and $url host do not match.
		if ( ! isset( $parse_url[1] ) || empty( $parse_url[1] ) || ( $this_host != $file_host ) )
		return;
		
		global $wpdb;
		 
		$prefix = $wpdb->prefix;
		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM " . $prefix . "posts WHERE guid RLIKE %s;", $parse_url[1] ) );
		 
		// Returns null if no attachment is found.
		return $attachment[0];
	}
}
/* nectar addition end */ 

/**
 * Remove not existing image IDs.
 *
 * @param null|string $param_value
 *
 * @return array
 * @since 4.2
 */
function wpb_removeNotExistingImgIDs( $param_value ) { // phpcs:ignore
	$param_value = is_null( $param_value ) ? '' : $param_value;
	$tmp = explode( ',', $param_value );
	$return_ar = [];
	foreach ( $tmp as $id ) {
		if ( wp_get_attachment_image( $id ) ) {
			$return_ar[] = $id;
		}
	}
	$tmp = implode( ',', $return_ar );

	return $tmp;
}

/*
* Resize images dynamically using wp built in functions
* Victor Teixeira
*/
if ( ! function_exists( 'wpb_resize' ) ) {
	/**
	 * Resize image.
	 *
	 * @param int $attach_id
	 * @param string $img_url
	 * @param int $width
	 * @param int $height
	 * @param bool $crop
	 *
	 * @return array
	 * @since 4.2
	 */
	function wpb_resize( $attach_id, $img_url, $width, $height, $crop = false ) { // phpcs:ignore:Generic.Metrics.CyclomaticComplexity.TooHigh
		// this is an attachment, so we have the ID.
		$image_src = [];
		if ( $attach_id ) {
			$image_src = wp_get_attachment_image_src( $attach_id, 'full' );
			$actual_file_path = get_attached_file( $attach_id );
			// this is not an attachment, let's use the image url.
		} elseif ( $img_url ) {
			$file_path = wp_parse_url( $img_url );
			$actual_file_path = rtrim( ABSPATH, '/' ) . $file_path['path'];
			$orig_size = getimagesize( $actual_file_path );
			$image_src[0] = $img_url;
			$image_src[1] = $orig_size[0];
			$image_src[2] = $orig_size[1];
		}
		if ( ! empty( $actual_file_path ) ) {
			$file_info = pathinfo( $actual_file_path );
			$extension = '.' . $file_info['extension'];

			// the image path without the extension.
			$no_ext_path = $file_info['dirname'] . '/' . $file_info['filename'];

			$cropped_img_path = $no_ext_path . '-' . $width . 'x' . $height . $extension;

			// checking if the file size is larger than the target size.
			// if it is smaller or the same size, stop right here and return.
			if ( $image_src[1] > $width || $image_src[2] > $height ) {

				// the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match).
				if ( file_exists( $cropped_img_path ) ) {
					$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
					$vt_image = [
						'url' => $cropped_img_url,
						'width' => $width,
						'height' => $height,
					];

					return $vt_image;
				}

				if ( ! $crop ) {
					// calculate the size proportionally.
					$proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
					$resized_img_path = $no_ext_path . '-' . $proportional_size[0] . 'x' . $proportional_size[1] . $extension;

					// checking if the file already exists.
					if ( file_exists( $resized_img_path ) ) {
						$resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );

						$vt_image = [
							'url' => $resized_img_url,
							'width' => $proportional_size[0],
							'height' => $proportional_size[1],
						];

						return $vt_image;
					}
				}

				// no cache files - let's finally resize it.
				$img_editor = wp_get_image_editor( $actual_file_path );

				if ( is_wp_error( $img_editor ) || is_wp_error( $img_editor->resize( $width, $height, $crop ) ) ) {
					return [
						'url' => '',
						'width' => '',
						'height' => '',
					];
				}

				$new_img_path = $img_editor->generate_filename();

				if ( is_wp_error( $img_editor->save( $new_img_path ) ) ) {
					return [
						'url' => '',
						'width' => '',
						'height' => '',
					];
				}
				if ( ! is_string( $new_img_path ) ) {
					return [
						'url' => '',
						'width' => '',
						'height' => '',
					];
				}

				$new_img_size = getimagesize( $new_img_path );
				$new_img = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

				// resized output.
				$vt_image = [
					'url' => $new_img,
					'width' => $new_img_size[0],
					'height' => $new_img_size[1],
				];

				return $vt_image;
			}

			// default output - without resizing.
			$vt_image = [
				'url' => $image_src[0],
				'width' => $image_src[1],
				'height' => $image_src[2],
			];

			return $vt_image;
		}

		return false;
	}
}

/**
 * Method adds css class to body tag.
 *
 * Hooked class method by body_class WP filter. Method adds custom css class to body tag of the page to help
 * identify and build design specially for VC shortcodes.
 * Used in wp-content/plugins/js_composer/include/classes/core/class-vc-base.php\Vc_Base\bodyClass.
 *
 * @param array $classes
 *
 * @return array
 * @since 4.2
 */
function js_composer_body_class( $classes ) {
	$classes[] = 'wpb-js-composer js-comp-ver-' . WPB_VC_VERSION;
	//nectar addition
	$disable_responsive = '0';
	//nectar addition end
	if ( '1' !== $disable_responsive ) {
		$classes[] = 'vc_responsive';
	} else {
		$classes[] = 'vc_non_responsive';
	}

	return $classes;
}

/**
 * Shortcode converter.
 *
 * @param array $m
 *
 * @return string
 * @since 4.2
 */
/* nectar addition */  
function vc_convert_shortcode( $m ) {
 	
	list($output, $m_one, $tag, $attr_string, $m_four, $content) = $m;
	$result = $width = $el_position = '';
	$shortcode_attr = shortcode_parse_atts( $attr_string );
	extract(shortcode_atts( [
			'width' => '1/1',
			'el_class' => '',
			'el_position' => ''
	], $shortcode_attr ) );
	if($tag == 'vc_row' || $tag == 'full_width_section') return $output;
  
	// Start
	if(preg_match('/first/', $el_position) || empty($shortcode_attr['width']) || $shortcode_attr['width']==='1/1')  {
	   if(!empty($output)) $result = '[vc_row]';
	}
  
	
	if( $tag!='vc_column' && $tag != 'one_half' && $tag != 'one_half_last' && $tag != 'one_third' && $tag != 'one_third_last' && $tag != 'one_fourth' && 
	$tag != 'one_fourth_last' && $tag != 'one_sixth' && $tag != 'one_sixth_last' && $tag != 'two_thirds' && $tag != 'two_thirds_last' && $tag != 'three_fourths' && 
	$tag != 'three_fourtsh_last' && $tag != 'fixth_sixths' && $tag != 'five_sixths_last' && $tag != 'one_whole') {
		$result .= "\n".'[vc_column width="'.$width.'"]';
	}
	
	
	// Tag
	$pattern = get_shortcode_regex();
	if($tag == 'vc_column' || $tag == 'one_half' || $tag == 'one_half_last' || $tag == 'one_third' || $tag == 'one_third_last' || $tag == 'one_fourth' || $tag == 'one_fourth_last' 
	|| $tag == 'one_sixth' || $tag == 'one_sixth_last' || $tag == 'two_thirds' || $tag == 'two_thirds_last' || $tag == 'three_fourths' || $tag == 'three_fourtsh_last' || $tag == 'fixth_sixths'
	|| $tag == 'five_sixths_last' || $tag == 'one_whole') {
			$result .= "[{$m_one}{$tag} {$attr_string}]".preg_replace_callback( "/{$pattern}/s", 'vc_convert_inner_shortcode', $content)."[/{$tag}{$m_four}]";
	} elseif( $tag == 'vc_tabs' || $tag == 'vc_accordion' || $tag == 'vc_tour' || $tag == 'toggle' || $tag == 'tabbed_section' ||  $tag == 'testimonial_slider' ||  $tag == 'clients' ||  $tag == 'pricing_table' ) {
	   
			$result .= "[{$m_one}{$tag} {$attr_string}]".preg_replace_callback( "/{$pattern}/s", 'vc_convert_tab_inner_shortcode', $content)."[/{$tag}{$m_four}]";
			
	} else {
			$result .= preg_replace('/(\"\d\/\d\")/', '"1/1"', $output);
	}
  
  
	// End
	if($tag!='vc_column' && $tag != 'one_half' && $tag != 'one_half_last' && $tag != 'one_third' && 
	$tag != 'one_third_last' && $tag != 'one_fourth' && $tag != 'one_fourth_last' && $tag != 'one_sixth' && 
	$tag != 'one_sixth_last' && $tag != 'two_thirds' && $tag != 'two_thirds_last' && $tag != 'three_fourths' && 
	$tag != 'three_fourtsh_last' && $tag != 'fixth_sixths' && $tag != 'five_sixths_last' && $tag != 'one_whole') {
		$result .= '[/vc_column]';
	}
	
	if(preg_match('/last/', $el_position) || empty($shortcode_attr['width']) || $shortcode_attr['width']==='1/1') {
		   if(!empty($output)) {
			   $result .= '[/vc_row]'."\n";
		   }
  
	}
  
	return $result;
	
   }
   /* nectar addition end */ 

/**
 * Processes shortcode, extracting attributes and recursively converting inner shortcodes.
 * Then reconstructs the modified shortcode string and returns it.
 *
 * @param array $m
 *
 * @return string
 * @since 4.2
 */
function vc_convert_tab_inner_shortcode( $m ) {
	list( $output, $m_one, $tag, $attr_string, $m_four, $content ) = $m;
	$result = '';
	extract( shortcode_atts( [
		'width' => '1/1',
		'el_class' => '',
		'el_position' => '',
	], shortcode_parse_atts( $attr_string ) ) );
	$pattern = get_shortcode_regex();
	$result .= "[{$m_one}{$tag} {$attr_string}]" . preg_replace_callback( "/{$pattern}/s", 'vc_convert_inner_shortcode', $content ) . "[/{$tag}{$m_four}]";

	return $result;
}

/**
 * Convert inner shortcode.
 *
 * @param array $m
 *
 * @return string
 * @since 4.2
 */
function vc_convert_inner_shortcode( $m ) {
	list( $output, $m_one, $tag, $attr_string, $m_four, $content ) = $m;
	$result = '';
	$width = '';
	$el_position = '';
	extract( shortcode_atts( [
		'width' => '1/1',
		'el_class' => '',
		'el_position' => '',
	], shortcode_parse_atts( $attr_string ) ) );
	if ( '1/1' !== $width ) {
		if ( preg_match( '/first/', $el_position ) ) {
			$result .= '[vc_row_inner]';
		}
		$result .= "\n" . '[vc_column_inner width="' . esc_attr( $width ) . '" el_position="' . esc_attr( $el_position ) . '"]';
		$attr = '';
		foreach ( shortcode_parse_atts( $attr_string ) as $key => $value ) {
			if ( 'width' === $key ) {
				$value = '1/1';
			} elseif ( 'el_position' === $key ) {
				$value = 'first last';
			}
			$attr .= ' ' . $key . '="' . $value . '"';
		}
		$result .= "[{$m_one}{$tag} {$attr}]" . $content . "[/{$tag}{$m_four}]";
		$result .= '[/vc_column_inner]';
		if ( preg_match( '/last/', $el_position ) ) {
			$result .= '[/vc_row_inner]' . "\n";
		}
	} else {
		$result = $output;
	}

	return $result;
}

global $vc_row_layouts;
$vc_row_layouts = [
	[
		'cells' => '11',
		'mask' => '12',
		'title' => '1/1',
		'icon_class' => '1-1',
	],
	[
		'cells' => '12_12',
		'mask' => '26',
		'title' => '1/2 + 1/2',
		'icon_class' => '1-2_1-2',
	],
	[
		'cells' => '23_13',
		'mask' => '29',
		'title' => '2/3 + 1/3',
		'icon_class' => '2-3_1-3',
	],
	[
		'cells' => '13_13_13',
		'mask' => '312',
		'title' => '1/3 + 1/3 + 1/3',
		'icon_class' => '1-3_1-3_1-3',
	],
	[
		'cells' => '14_14_14_14',
		'mask' => '420',
		'title' => '1/4 + 1/4 + 1/4 + 1/4',
		'icon_class' => '1-4_1-4_1-4_1-4',
	],
	[
		'cells' => '14_34',
		'mask' => '212',
		'title' => '1/4 + 3/4',
		'icon_class' => '1-4_3-4',
	],
	[
		'cells' => '14_12_14',
		'mask' => '313',
		'title' => '1/4 + 1/2 + 1/4',
		'icon_class' => '1-4_1-2_1-4',
	],
	[
		'cells' => '56_16',
		'mask' => '218',
		'title' => '5/6 + 1/6',
		'icon_class' => '5-6_1-6',
	],
	[
		'cells' => '16_16_16_16_16_16',
		'mask' => '642',
		'title' => '1/6 + 1/6 + 1/6 + 1/6 + 1/6 + 1/6',
		'icon_class' => '1-6_1-6_1-6_1-6_1-6_1-6',
	],
	[
		'cells' => '16_23_16',
		'mask' => '319',
		'title' => '1/6 + 4/6 + 1/6',
		'icon_class' => '1-6_2-3_1-6',
	],
	[
		'cells' => '16_16_16_12',
		'mask' => '424',
		'title' => '1/6 + 1/6 + 1/6 + 1/2',
		'icon_class' => '1-6_1-6_1-6_1-2',
	],
	[
		'cells' => '15_15_15_15_15',
		'mask' => '530',
		'title' => '1/5 + 1/5 + 1/5 + 1/5 + 1/5',
		'icon_class' => 'l_15_15_15_15_15',
	],
];

/**
 * Get indent for column width.
 *
 * @param string $width
 *
 * @return string
 * @since 4.2
 */
function wpb_vc_get_column_width_indent( $width ) {
	$identy = '11';
	if ( 'vc_col-sm-6' === $width ) {
		$identy = '12';
	} elseif ( 'vc_col-sm-3' === $width ) {
		$identy = '14';
	} elseif ( 'vc_col-sm-4' === $width ) {
		$identy = '13';
	} elseif ( 'vc_col-sm-8' === $width ) {
		$identy = '23';
	} elseif ( 'vc_col-sm-9' === $width ) {
		$identy = '34';
	} elseif ( 'vc_col-sm-2' === $width ) {
		$identy = '16'; // TODO: check why there is no "vc_col-sm-1, -5, -6, -7, -11, -12.
	} elseif ( 'vc_col-sm-10' === $width ) {
		$identy = '56';
	}

	return $identy;
}

/**
 * Make any HEX color lighter or darker.
 *
 * @param string $colour
 * @param int $per
 *
 * @return string
 * @since 4.2
 */
function vc_colorCreator( $colour, $per = 10 ) { // phpcs:ignore
	require_once 'class-vc-color-helper.php';
	$color = $colour;
	if ( stripos( $colour, 'rgba(' ) !== false ) {
		$rgb = str_replace( [
			'rgba',
			'rgb',
			'(',
			')',
		], '', $colour );
		$rgb = explode( ',', $rgb );
		$rgb_array = [
			'R' => $rgb[0],
			'G' => $rgb[1],
			'B' => $rgb[2],
		];
		$alpha = $rgb[3];
		try {
			$color = Vc_Color_Helper::rgbToHex( $rgb_array );
			$color_obj = new Vc_Color_Helper( $color );
			if ( $per >= 0 ) {
				$color = $color_obj->lighten( $per );
			} else {
				$color = $color_obj->darken( abs( $per ) );
			}
			$rgba = $color_obj->hexToRgb( $color );
			$rgba[] = $alpha;
			$css_rgba_color = 'rgba(' . implode( ', ', $rgba ) . ')';

			return $css_rgba_color;
		} catch ( Exception $e ) {
			// In case of error return same as given.
			return $colour;
		}
	} elseif ( stripos( $colour, 'rgb(' ) !== false ) {
		$rgb = str_replace( [
			'rgba',
			'rgb',
			'(',
			')',
		], '', $colour );
		$rgb = explode( ',', $rgb );
		$rgb_array = [
			'R' => $rgb[0],
			'G' => $rgb[1],
			'B' => $rgb[2],
		];
		try {
			$color = Vc_Color_Helper::rgbToHex( $rgb_array );
		} catch ( Exception $e ) {
			// In case of error return same as given.
			return $colour;
		}
	}

	try {
		$color_obj = new Vc_Color_Helper( $color );
		if ( $per >= 0 ) {
			$color = $color_obj->lighten( $per );
		} else {
			$color = $color_obj->darken( abs( $per ) );
		}

		return '#' . $color;
	} catch ( Exception $e ) {
		return $colour;
	}
}



/* nectar addition */ 
if( !function_exists('hex2rgba') ) {
	
	function hex2rgba($color, $opacity = false) {

		$default = 'rgb(0,0,0)';

		//Return default if no color provided
		if(empty($color))
	          return $default; 

		//Sanitize $color if "#" is provided 
	        if ($color[0] == '#' ) {
	        	$color = substr( $color, 1 );
	        }

	        //Check if color has 6 or 3 characters and get values
	        if (strlen($color) == 6) {
	                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
	        } elseif ( strlen( $color ) == 3 ) {
	                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
	        } else {
	                return $default;
	        }

	        //Convert hexadec to rgb
	        $rgb =  array_map('hexdec', $hex);

	        //Check if opacity is set(rgba or rgb)
	        if($opacity){
	        	if(abs($opacity) > 1)
	        		$opacity = 1.0;
	        	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
	        } else {
	        	$output = 'rgb('.implode(",",$rgb).')';
	        }

	        //Return rgb(a) color string
	        return $output;
	}
	
}


/**
 * HEX to RGB converter
 *
 * @param string $color
 *
 * @return array|bool
 * @since 4.2
 */
function vc_hex2rgb( $color ) {
	$color = str_replace( '#', '', $color );

	if ( strlen( $color ) === 6 ) {
		list( $r, $g, $b ) = [
			$color[0] . $color[1],
			$color[2] . $color[3],
			$color[4] . $color[5],
		];
	} elseif ( strlen( $color ) === 3 ) {
		list( $r, $g, $b ) = [
			$color[0] . $color[0],
			$color[1] . $color[1],
			$color[2] . $color[2],
		];
	} else {
		return false;
	}

	$r = hexdec( $r );
	$g = hexdec( $g );
	$b = hexdec( $b );

	return [
		$r,
		$g,
		$b,
	];
}

/**
 * Parse string like "title:Hello world|weekday:Monday" to array('title' => 'Hello World', 'weekday' => 'Monday')
 *
 * @param mixed $value
 * @param array $defaults
 *
 * @return array
 * @since 4.2
 */
function vc_parse_multi_attribute( $value, $defaults = [] ) {
	$result = $defaults;
	$params_pairs = is_string( $value ) ? explode( '|', $value ) : [];
	if ( ! empty( $params_pairs ) ) {
		foreach ( $params_pairs as $pair ) {
			$param = preg_split( '/\:/', $pair );
			if ( ! empty( $param[0] ) && isset( $param[1] ) ) {
				$result[ $param[0] ] = trim( rawurldecode( $param[1] ) );
			}
		}
	}

	return $result;
}

/**
 * Decode params options.
 *
 * @param string $v
 *
 * @return string
 * @since 4.2
 */
function vc_param_options_parse_values( $v ) {
	return rawurldecode( $v );
}

/**
 * Get specific param from settings list.
 *
 * @param string $name
 * @param string $settings
 *
 * @return bool
 * @since 4.2
 */
function vc_param_options_get_settings( $name, $settings ) {
	if ( is_array( $settings ) ) {
		foreach ( $settings as $params ) {
			if ( isset( $params['name'] ) && $params['name'] === $name && isset( $params['type'] ) ) {
				return $params;
			}
		}
	}

	return false;
}

/**
 * Convert array to string.
 *
 * @param array $atts
 *
 * @return string
 * @since 4.2
 */
function vc_convert_atts_to_string( $atts ) {
	$output = '';
	foreach ( $atts as $key => $value ) {
		$output .= ' ' . $key . '="' . $value . '"';
	}

	return $output;
}

/**
 * String parser for options.
 *
 * @param string $initial_string
 * @param string $tag
 * @param string $param
 *
 * @return array
 * @throws \Exception
 * @since 4.2
 */
function vc_parse_options_string( $initial_string, $tag, $param ) { // phpcs:ignore:Generic.Metrics.CyclomaticComplexity.TooHigh
	$options = [];
	$option_settings_list = [];
	$settings = WPBMap::getParam( $tag, $param );

	foreach ( preg_split( '/\|/', $initial_string ) as $value ) {
		if ( preg_match( '/\:/', $value ) ) {
			$split = preg_split( '/\:/', $value );
			$option_name = $split[0];
			$option_settings = vc_param_options_get_settings( $option_name, $settings['options'] );
			$option_settings_list[ $option_name ] = $option_settings;
			if ( isset( $option_settings['type'] ) && 'checkbox' === $option_settings['type'] ) {
				$option_value = array_map( 'vc_param_options_parse_values', preg_split( '/\,/', $split[1] ) );
			} else {
				$option_value = rawurldecode( $split[1] );
			}
			$options[ $option_name ] = $option_value;
		}
	}
	if ( isset( $settings['options'] ) ) {
		foreach ( $settings['options'] as $setting_option ) {
			if ( 'separator' !== $setting_option['type'] && isset( $setting_option['value'] ) && empty( $options[ $setting_option['name'] ] ) ) {
				$options[ $setting_option['name'] ] = 'checkbox' === $setting_option['type'] ? preg_split( '/\,/', $setting_option['value'] ) : $setting_option['value'];
			}
			if ( isset( $setting_option['name'] ) && isset( $options[ $setting_option['name'] ] ) && isset( $setting_option['value_type'] ) ) {
				if ( 'integer' === $setting_option['value_type'] ) {
					$options[ $setting_option['name'] ] = (int) $options[ $setting_option['name'] ];
				} elseif ( 'float' === $setting_option['value_type'] ) {
					$options[ $setting_option['name'] ] = (float) $options[ $setting_option['name'] ];
				} elseif ( 'boolean' === $setting_option['value_type'] ) {
					$options[ $setting_option['name'] ] = (bool) $options[ $setting_option['name'] ];
				}
			}
		}
	}

	return $options;
}

/**
 * Convert string to a valid css class name.
 *
 * @param string $class_name
 *
 * @return string
 * @since 4.3
 */
function vc_build_safe_css_class( $class_name ) {
	return preg_replace( '/\W+/', '', strtolower( str_replace( ' ', '_', wp_strip_all_tags( $class_name ) ) ) );
}

/**
 * Include template from templates dir.
 *
 * @param string $template
 * @param array $variables - passed variables to the template.
 *
 * @param bool $once
 *
 * @return mixed
 * @since 4.3
 */
function vc_include_template( $template, $variables = [], $once = false ) {
	is_array( $variables ) && extract( $variables );
	if ( $once ) {
		return require_once vc_template( $template );
	} else {
		return require vc_template( $template );
	}
}

/**
 * Output template from templates dir.
 *
 * @param string $template
 * @param array $variables - passed variables to the template.
 *
 * @param bool $once
 *
 * @return string
 * @since 4.4
 */
function vc_get_template( $template, $variables = [], $once = false ) {
	ob_start();
	$output = vc_include_template( $template, $variables, $once );

	if ( 1 === $output ) {
		$output = ob_get_contents();
	}

	ob_end_clean();

	return $output;
}

if ( ! function_exists( 'lcfirst' ) ) {
	/**
	 * Backward compatibility for mb_strtolower function.
	 * If php version < 5.3 this function is required.
	 *
	 * @param string $str
	 *
	 * @return mixed
	 * @since 4.3, fix #1093
	 */
	function lcfirst( $str ) {
		$str[0] = function_exists( 'mb_strtolower' ) ? mb_strtolower( $str[0] ) : strtolower( $str[0] );

		return $str;
	}
}
/**
 * VC Convert a value to studly caps case.
 *
 * @param string $value
 *
 * @return string
 * @since 4.3
 */
function vc_studly( $value ) {
	$value = ucwords( str_replace( [
		'-',
		'_',
	], ' ', $value ) );

	return str_replace( ' ', '', $value );
}

/**
 * VC Convert a value to camel case.
 *
 * @param string $value
 *
 * @return string
 * @since 4.3
 */
function vc_camel_case( $value ) {
	return lcfirst( vc_studly( $value ) );
}

/**
 * Enqueue icon element font
 *
 * @param string $font
 * @since 4.4
 *
 * @todo move to separate folder
 */
function vc_icon_element_fonts_enqueue( $font ) {
	switch ( $font ) {
		case 'fontawesome':
			wp_enqueue_style( 'vc_font_awesome_6' );
			break;
		case 'openiconic':
			wp_enqueue_style( 'vc_openiconic' );
			break;
		case 'typicons':
			wp_enqueue_style( 'vc_typicons' );
			break;
		case 'entypo':
			wp_enqueue_style( 'vc_entypo' );
			break;
		case 'linecons':
			wp_enqueue_style( 'vc_linecons' );
			break;
		case 'monosocial':
			wp_enqueue_style( 'vc_monosocialiconsfont' );
			break;
		/*nectar addition */
			/*
			case 'material':
				wp_enqueue_style( 'vc_material' );
				break;
				*/
				/*nectar addition end */
		default:
			do_action( 'vc_enqueue_font_icon_element', $font ); // hook to custom do enqueue style.
	}
}

/**
 * Function merges defaults attributes in attributes by keeping it values
 *
 * Example
 *      array defaults     |   array attributes     |    result array
 *      'color'=>'black',         -                   'color'=>'black',
 *      'target'=>'_self',      'target'=>'_blank',   'target'=>'_blank',
 *             -                'link'=>'google.com'  'link'=>'google.com'
 *
 * @param array $defaults
 * @param array $attributes
 *
 * @return array - merged attributes.
 *
 * @since 4.4
 *
 * @see vc_map_get_attributes
 */
function vc_shortcode_attribute_parse( $defaults = [], $attributes = [] ) {
	$atts = $attributes + shortcode_atts( $defaults, $attributes );

	return $atts;
}

/**
 * Get shortcode regex.
 *
 * @param string $tagregexp
 * @return string
 */
function vc_get_shortcode_regex( $tagregexp = '' ) {
	if ( 0 === strlen( $tagregexp ) ) {
		return get_shortcode_regex();
	}

    //phpcs:disable:Generic.Strings.UnnecessaryStringConcat.Found
	return '\\['                              // Opening bracket.
		. '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]].
		. "($tagregexp)"                     // 2: Shortcode name.
		. '(?![\\w\-])'                       // Not followed by word character or hyphen.
		. '('                                // 3: Unroll the loop: Inside the opening shortcode tag.
		. '[^\\]\\/]*'                   // Not a closing bracket or forward slash.
		. '(?:' . '\\/(?!\\])'               // A forward slash not followed by a closing bracket.
		. '[^\\]\\/]*'               // Not a closing bracket or forward slash.
		. ')*?' . ')' . '(?:' . '(\\/)'                        // 4: Self closing tag .
		. '\\]'                          // ... and closing bracket.
		. '|' . '\\]'                          // Closing bracket.
		. '(?:' . '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags.
		. '[^\\[]*+'             // Not an opening bracket.
		. '(?:' . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag.
		. '[^\\[]*+'         // Not an opening bracket.
		. ')*+' . ')' . '\\[\\/\\2\\]'             // Closing shortcode tag.
		. ')?' . ')' . '(\\]?)';
    //phpcs:enable:Generic.Strings.UnnecessaryStringConcat.Found
}

/**
 * Used to send warning message
 *
 * @param string $message
 *
 * @return string
 * @since 4.5
 */
function vc_message_warning( $message ) {
	return '<div class="wpb_element_wrapper"><div class="vc_message_box vc_message_box-standard vc_message_box-rounded vc_color-warning">
	<div class="vc_message_box-icon"><i class="fa fa-exclamation-triangle"></i>
	</div><p class="messagebox_text">' . $message . '</p>
</div></div>';
}

/**
 * Extract video ID from youtube url
 *
 * @param string $url Youtube url.
 *
 * @return string
 */
function vc_extract_youtube_id( $url ) {
	$url = wp_parse_url( $url, PHP_URL_QUERY );

	if ( ! is_string( $url ) ) {
		return '';
	}

	parse_str( $url, $vars );

	if ( ! isset( $vars['v'] ) ) {
		return '';
	}

	return $vars['v'];
}

/**
 * Get taxonomies for specific post type.
 *
 * @param string|null $post_type
 *
 * @return string[]|\WP_Taxonomy[]
 */
function vc_taxonomies_types( $post_type = null ) {
	global $vc_taxonomies_types;
	if ( is_null( $vc_taxonomies_types ) || $post_type ) {
		$query = [ 'public' => true ];
		$vc_taxonomies_types = get_taxonomies( $query, 'objects' );
		if ( ! empty( $post_type ) && is_array( $vc_taxonomies_types ) ) {
			foreach ( $vc_taxonomies_types as $key => $taxonomy ) {
				$arr = (array) $taxonomy;
				if ( isset( $arr['object_type'] ) && ! in_array( $post_type, $arr['object_type'] ) ) {
					unset( $vc_taxonomies_types[ $key ] );
				}
			}
		}
	}

	return $vc_taxonomies_types;
}

/**
 * Get term object.
 *
 * @param WP_Term $term
 *
 * @return array
 * @since 4.5.3
 */
function vc_get_term_object( $term ) {
	$vc_taxonomies_types = vc_taxonomies_types();

	return [
		'label' => $term->name,
		'value' => $term->term_id,
		'group_id' => $term->taxonomy,
		'group' => isset( $vc_taxonomies_types[ $term->taxonomy ], $vc_taxonomies_types[ $term->taxonomy ]->labels, $vc_taxonomies_types[ $term->taxonomy ]->labels->name ) ? $vc_taxonomies_types[ $term->taxonomy ]->labels->name : esc_html__( 'Taxonomies', 'js_composer' ),
	];
}

/**
 * Check if element has specific class.
 *
 * E.g. f('foo', 'foo bar baz') -> true.
 *
 * @param string $class_name Class to check for.
 * @param string $classes Classes separated by space(s).
 *
 * @return bool
 */
function vc_has_class( $class_name, $classes ) {
	return in_array( $class_name, explode( ' ', strtolower( $classes ), true ), true );
}

/**
 * Remove specific class from classes string.
 *
 * E.g. f('foo', 'foo bar baz') -> 'bar baz'.
 *
 * @param string $class_name Class to remove.
 * @param string $classes Classes separated by space(s).
 *
 * @return string
 */
function vc_remove_class( $class_name, $classes ) {
	$list_classes = explode( ' ', strtolower( $classes ) );

	$key = array_search( $class_name, $list_classes, true );

	if ( false === $key ) {
		return $classes;
	}

	unset( $list_classes[ $key ] );

	return implode( ' ', $list_classes );
}

/**
 * Convert array of named params to string version.
 * All values will be escaped.
 *
 * E.g. f(array('name' => 'foo', 'id' => 'bar')) -> 'name="foo" id="bar"'.
 *
 * @param array $attributes
 *
 * @return string
 */
function vc_stringify_attributes( $attributes ) {
	$atts = [];
	foreach ( $attributes as $name => $value ) {
		$atts[] = $name . '="' . esc_attr( $value ) . '"';
	}

	return implode( ' ', $atts );
}

/**
 * Check if plugin no_resonsive_css settings is disabled.
 *
 * @return bool
 */
function vc_is_responsive_disabled() {
	//nectar addition
	$disable_responsive = '0';
	//nectar addition end
	return '1' === $disable_responsive;
}

/**
 * Do shortcode single render point.
 *
 * @param array $atts
 * @param null $content
 * @param null $tag
 *
 * @return string
 * @throws \Exception
 */
function vc_do_shortcode( $atts, $content = null, $tag = null ) {
	ob_start();
	// @codingStandardsIgnoreStart
	echo Vc_Shortcodes_Manager::getInstance()->getElementClass( $tag )->output( $atts, $content );
	$content = ob_get_clean();
	global $wp_embed;
	if ( is_object( $wp_embed ) ) {
		$content = $wp_embed->run_shortcode( $content );
		$content = $wp_embed->autoembed( $content );
	}
    // @codingStandardsIgnoreEnd

	return $content;
}

/**
 * Return random string
 *
 * @param int $length
 *
 * @return string
 */
function vc_random_string( $length = 10 ) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$len = strlen( $characters );
	$str = '';
	for ( $i = 0; $i < $length; $i++ ) {
		$str .= $characters[ wp_rand( 0, $len - 1 ) ];
	}

	return $str;
}

/**
 * Slugify string, remove all unwanted characters.
 *
 * @param string $init_str
 * @return string|string[]|null
 */
function vc_slugify( $init_str ) {
	$init_str = strtolower( $init_str );
	$init_str = html_entity_decode( $init_str );
	$init_str = preg_replace( '/[^\w ]+/', '', $init_str );
	$init_str = preg_replace( '/ +/', '-', $init_str );

	return $init_str;
}

/**
 * This filter should be applied to all content elements titles.
 *
 * $params['extraclass'] Extra class name will be added.
 *
 *
 * To override content element title default html markup, paste this code in your theme's functions.php file
 * vc_filter: wpb_widget_title
 * add_filter('wpb_widget_title', 'override_widget_title', 10, 2);
 * function override_widget_title($output = '', $params = array('')) {
 *    $extraclass = (isset($params['extraclass'])) ? " ".$params['extraclass'] : "";
 *    return '<h1 class="entry-title'.$extraclass.'">'.$params['title'].'</h1>';
 * }
 *
 * @param array $params
 *
 * @return mixed|string
 */
function wpb_widget_title( $params = [ 'title' => '' ] ) {
	if ( '' === $params['title'] ) {
		return '';
	}

	$extraclass = ( isset( $params['extraclass'] ) ) ? ' ' . $params['extraclass'] : '';
	$output = '<h2 class="wpb_heading' . esc_attr( $extraclass ) . '">' . esc_html( $params['title'] ) . '</h2>';

	return apply_filters( 'wpb_widget_title', $output, $params );
}

/**
 * Used to remove raw_html/raw_js elements from content.
 *
 * @param string $content
 * @return string|string[]|null
 * @since 6.3.0
 */
function wpb_remove_custom_html( $content ) {
	$is_rest_request = ( defined( 'REST_REQUEST' ) && REST_REQUEST );
	if ( ! empty( $content ) && ! $is_rest_request && ! vc_user_access()->part( 'unfiltered_html' )->checkStateAny( true, null )->get() ) {
		// html encoded shortcodes.
		$regex = vc_get_shortcode_regex( implode( '|', wpb_get_elements_with_custom_html() ) );

		// custom on click.
		$button_regex = vc_get_shortcode_regex( 'vc_btn' );
		$content = preg_replace_callback( '/' . $button_regex . '/', 'wpb_remove_custom_onclick', $content );

		$content = preg_replace( '/' . $regex . '/', '', $content );
	}

	return $content;
}

/**
 * Remove custom onclick.
 *
 * @param array $match_list
 * @return string
 */
function wpb_remove_custom_onclick( $match_list ) {
	if ( strpos( $match_list[3], 'custom_onclick' ) !== false ) {
		return '';
	}

	return $match_list[0];
}

/**
 * We use it only to check is current environment is wordpress.com.
 *
 * @since 6.2
 *
 * @return bool
 */
function wpb_check_wordpress_com_env() {
	return defined( 'IS_ATOMIC' ) &&
		IS_ATOMIC &&
		defined( 'ATOMIC_CLIENT_ID' ) &&
		'2' === ATOMIC_CLIENT_ID;
}

if ( ! function_exists( 'wpb_get_post_id' ) ) {
	/**
	 * Get current post id for plugin custom output like css and js.
	 *
	 * @since  7.7
	 * @return false|int
	 */
	function wpb_get_post_id_for_custom_output() {
		$id = false;
		if ( is_front_page() || is_home() ) {
			$id = get_queried_object_id();
		} elseif ( is_singular() ) {
			$id = get_the_ID();
		}

		return $id;
	}
}

if ( ! function_exists( 'wpb_get_elements_with_custom_html' ) ) {
	/**
	 * Get elements list that use custom html in our plugin core.
	 *
	 * @note it's elements lists that can edit only users roles that have unfiltered_html capability.
	 * @note admin can set access to unfiltered_html cap in our role manager plugin settings for individual roles.
	 *
	 * @return array
	 * @since 7.8
	 */
	function wpb_get_elements_with_custom_html() {
		return apply_filters('wpb_custom_html_elements', [
			'vc_raw_html',
			'vc_raw_js',
			'vc_gmaps',
		]);
	}
}

if ( ! function_exists( 'wpb_is_regex_valid' ) ) {
	/**
	 * Check if regex string is valid.
	 *
	 * @param string $regex
	 * @since  7.8
	 * @return bool
	 */
	function wpb_is_regex_valid( $regex ) {
        // @phpcs:ignore
        return false !== @preg_match( $regex, '' );
	}
}

if ( ! function_exists( 'wpb_get_editor_status' ) ) {
	/**
	 * Get post plugin editor status for post.
	 *
	 * @param int $post_id
	 *
	 * @since  7.8
	 * @return bool
	 */
	function wpb_get_post_editor_status( $post_id ) {
		if ( ! is_int( $post_id ) ) {
			return false;
		}

		$status = get_post_meta( $post_id, '_wpb_vc_js_status', true );

		if ( 'true' === $status ) {
			return true;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'wpb_format_with_css_unit' ) ) {
	/**
	 * Formats a CSS value by ensuring it has a valid unit.
	 * If no unit is provided, defaults to 'px'.
	 *
	 * @param string $value The CSS value to format, e.g., '20', '15em'.
	 * @return string The formatted value with a unit, e.g., '20px', '15em'.
	 * @since 7.9
	 */
	function wpb_format_with_css_unit( $value ) {
		$value = preg_replace( '/\s+/', '', $value );
		$pattern = '/^(\d*(?:\.\d+)?)\s*(px|\%|in|cm|mm|em|rem|ex|pt|pc|vw|vh|vmin|vmax)?$/';
		preg_match( $pattern, $value, $matches );
		$numeric_value = isset( $matches[1] ) ? (float) $matches[1] : (float) $value;
		$unit = isset( $matches[2] ) ? $matches[2] : 'px';
		return $numeric_value . $unit;
	}
}

if ( ! function_exists( 'wpb_is_hide_title' ) ) {
	/**
	 * Check if we should hide title for post.
	 *
	 * @since 8.2
	 * @param int $post_id
	 * @return bool
	 */
	function wpb_is_hide_title( $post_id ) {
		$vc_settings = get_post_meta( $post_id, '_vc_post_settings', true );

		return ! empty( $vc_settings['is_hide_title'] );
	}
}

if ( ! function_exists( 'wpb_is_preview' ) ) {
	/**
	 * Check if current loaded page is preview.
	 *
	 * @since 8.3
	 * @return bool
	 */
	function wpb_is_preview() {
		// there is some cases when we see preview but don't have nonce to verify it.
		$preview = isset( $_GET['preview'] ) ? sanitize_text_field( wp_unslash( $_GET['preview'] ) ) : '';
		$wp_preview = isset( $_GET['wp-preview'] ) ? sanitize_text_field( wp_unslash( $_GET['wp-preview'] ) ) : '';

		return ( 'true' === $preview ) || ( 'dopreview' === $wp_preview );
	}
}

if ( ! function_exists( 'wpb_update_id_with_preview_id' ) ) {
	/**
	 * Check if current page is preview and if it is, update post id with preview post id.
	 *
	 * @param int $post_id
	 * @since 8.3
	 * @return int
	 */
	function wpb_update_id_with_preview_id( $post_id ) {
		if ( ! wpb_is_preview() ) {
			return $post_id;
		}

		if ( get_post_status( $post_id ) === 'draft' ) {
			$preview = wp_get_latest_revision_id_and_total_count( $post_id );
			if ( ! is_wp_error( $preview ) && ! empty( $preview['latest_id'] ) ) {
				$post_id = $preview['latest_id'];
			}
		} else {
			$preview = wp_get_post_autosave( $post_id );
			if ( $preview ) {
				$post_id = $preview->ID;
			}
		}

		return $post_id;
	}
}
