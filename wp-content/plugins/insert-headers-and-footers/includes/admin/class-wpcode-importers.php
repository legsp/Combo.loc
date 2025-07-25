<?php
/**
 * Load classes used for importing data from other plugins.
 *
 * @package WPCode
 */

/**
 * WPCode_Importers class.
 */
class WPCode_Importers {

	/**
	 * Available importers.
	 *
	 * @var WPCode_Importer_Type[]
	 */
	public $importers = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->require_files();
		$this->load_importers();
	}

	/**
	 * Require the importer classes.
	 *
	 * @return void
	 */
	private function require_files() {
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/importers/class-wpcode-importer-type.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/importers/class-wpcode-importer-code-snippets.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/importers/class-wpcode-importer-code-snippets-premium.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/importers/class-wpcode-importer-woody.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/importers/class-wpcode-importer-simple-custom-css-and-js.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/importers/class-wpcode-importer-header-footer-code-manager.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/importers/class-wpcode-importer-post-snippets.php';
	}

	/**
	 * Load the available importers instances.
	 *
	 * @return void
	 */
	private function load_importers() {
		if ( empty( $this->importers ) ) {
			$this->importers = array(
				'code-snippets'              => new WPCode_Importer_Code_Snippets(),
				'code-snippets-pro'          => new WPCode_Importer_Code_Snippets_Pro(),
				'woody'                      => new WPCode_Importer_Woody(),
				'simple-custom-css-js'       => new WPCode_Importer_Simple_Custom_CSS_and_JS(),
				'header-footer-code-manager' => new WPCode_Importer_Header_Footer_Code_Manager(),
				'post-snippets'              => new WPCode_Importer_Post_Snippets(),
			);
		}
	}

	/**
	 * Get the importers with registered data.
	 *
	 * @return array
	 */
	public function get_importers() {

		$importers = array();

		foreach ( $this->importers as $importer ) {
			$importers = $importer->register( $importers );
		}

		return $importers;
	}
}
