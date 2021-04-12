<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Importer
 * @subpackage Importer/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Importer
 * @subpackage Importer/admin
 * @author     Makkssimka
 */
class Importer_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $importer    The ID of this plugin.
	 */
	private $importer;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $importer       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */

	public function __construct( $importer, $version ) {

		$this->importer = $importer;
		$this->version = $version;

        add_action( 'wp_ajax_sku_generated', array($this, "sku_ajax_request"));

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Importer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Importer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->importer, plugin_dir_url( __FILE__ ) . 'css/importer-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Importer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Importer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->importer, plugin_dir_url( __FILE__ ) . 'js/importer-admin.js', array( 'jquery' ), $this->version, false );

	}

    public function importer_menu() {
        add_menu_page("Импорт 1С", "Импорт 1С", "manage_options", "import-1c", null, 'dashicons-download', 58);

        //create submenu
        add_submenu_page("import-1c", "Ручное обновление", "Ручное обновление", "manage_options", "import-1c", array($this, "import_update"));
        add_submenu_page("import-1c", "Логи импорта", "Логи импорта", "manage_options", "import-log", array($this, "import_log"));
        add_submenu_page("import-1c", "Генератор артикула", "Генератор артикула", "manage_options", "import_sku", array($this, "import_sku"));
    }

    public function import_update() {

        //run action
        if (isset($_GET['action']) && $_GET['action'] == "test-import") {
            $action = new Importer_Action($_GET['action']);
            $action->run_action();

            $files_import = new FilesImporter();
            $files_import->get_product();
            $files_import->test_product();
        }

        if (isset($_GET['action']) && $_GET['action'] == "update-import") {
            $action = new Importer_Action($_GET['action']);
            $action->run_action();

            $files_import = new FilesImporter();
            $files_import->get_product();
            $files_import->test_product();
            $files_import->add_new_product();
        }

        //template include
        ob_start();
        include_once(IMPORTER_PLUGIN_PATH."admin/partials/importer-update-template.php");
        $template = ob_get_contents();
        ob_end_clean();

        echo $template;
    }

    public function import_log() {

        //run action
        if (isset($_GET['action'])) {
            $action = new Importer_Action($_GET['action']);
            $action->run_action();
        }

        $log = new LogImporter();
        $file_path = $log->get_path();
        $file_url = $log->get_url();
        $file_data = $log->get_value();

        //template include
        ob_start();
        include_once(IMPORTER_PLUGIN_PATH."admin/partials/importer-log-template.php");
        $template = ob_get_contents();
        ob_end_clean();

        echo $template;
    }

    public function import_sku() {

	    $products_not_sku_counter = SkuImporter::getProductNotSku();

        //template include
        ob_start();
        include_once(IMPORTER_PLUGIN_PATH."admin/partials/importer-sku-template.php");
        $template = ob_get_contents();
        ob_end_clean();

        echo $template;

    }

    //ajax add call request
    public function sku_ajax_request()
    {
        $products = wc_get_products(array(
            'limit'  => -1,
            'meta_key' => '_sku',
            'meta_value' => 'undefined'
        ));

        $sku_count = 0;

        $skuGenerater = new SkuImporter();
        foreach ($products as $product) {
            $sku_count++;
            $sku = $skuGenerater->getGeneratedSku();
            $product->set_sku($sku);
            $product->save();
        }
        $skuGenerater->setSkuConfig();

        echo json_encode($sku_count);
        wp_die();
    }

}
