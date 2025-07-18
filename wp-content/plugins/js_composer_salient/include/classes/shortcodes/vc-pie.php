<?php
/**
 * Class that handles specific [vc_pie] shortcode.
 *
 * @see js_composer/include/templates/shortcodes/vc_pie.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class WPBakeryShortCode_Vc_Pie
 */
class WPBakeryShortCode_Vc_Pie extends WPBakeryShortCode {
	/**
	 * WPBakeryShortCode_Vc_Pie constructor.
	 *
	 * @param array $settings
	 */
	public function __construct( $settings ) {
		parent::__construct( $settings );
		$this->jsScripts();
	}

	/**
	 * Register scripts.
	 */
	public function jsScripts() {
		wp_register_script( 'vc_waypoints', vc_asset_url( 'lib/vc/vc_waypoints/vc-waypoints.min.js' ), [ 'jquery-core' ], WPB_VC_VERSION, true );
		wp_register_script( 'progressCircle', vc_asset_url( 'lib/vendor/progress-circle/progress-circle.min.js' ), [], WPB_VC_VERSION, true );
		wp_register_script( 'vc_pie', vc_asset_url( 'lib/vc/vc_chart/jquery.vc_chart.min.js' ), [
			'jquery-core',
			/* nectar addition */ 
			//'vc_waypoints',
			/* nectar addition end */ 
			'progressCircle',
		], WPB_VC_VERSION, true );
	}

	/**
	 * Convert old color names to new ones for BC
	 *
	 * @param array $atts
	 *
	 * @return array
	 */
	public static function convertOldColorsToNew( $atts ) {
		$map = [
			'btn-primary' => '#0088cc',
			'btn-success' => '#6ab165',
			'btn-warning' => '#ff9900',
			'btn-inverse' => '#555555',
			'btn-danger' => '#ff675b',
			'btn-info' => '#58b9da',
			'primary' => '#0088cc',
			'success' => '#6ab165',
			'warning' => '#ff9900',
			'inverse' => '#555555',
			'danger' => '#ff675b',
			'info' => '#58b9da',
			'default' => '#f7f7f7',
		];

		if ( isset( $atts['color'] ) && isset( $map[ $atts['color'] ] ) ) {
			$atts['custom_color'] = $map[ $atts['color'] ];
			$atts['color'] = 'custom';
		}

		return $atts;
	}
}
