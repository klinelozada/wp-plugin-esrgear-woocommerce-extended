<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://esrgear.com/
 * @since      1.0.0
 *
 * @package    Esr_Woo_Extended
 * @subpackage Esr_Woo_Extended/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Esr_Woo_Extended
 * @subpackage Esr_Woo_Extended/public
 * @author     ESRGear <admin@esrgear.com>
 */
class Esr_Woo_Extended_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// Add Specifications tab in woocommerce single product page
		add_filter( 'woocommerce_product_tabs', array( &$this, 'woocommerce_product_specifications_tab' ));
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Esr_Woo_Extended_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Esr_Woo_Extended_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/esr-woo-extended-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Esr_Woo_Extended_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Esr_Woo_Extended_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/esr-woo-extended-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register Specifications Tab on WooCommerce Single Product Page
	 *
	 * @since    1.0.0
	 */
	public function woocommerce_product_specifications_tab( $tabs ) {

		$tabs['specifications'] = array(
			'title'     => __( 'Specifications', 'woocommerce' ),
			'priority'  => 11,
			'callback'  => function( $i ){
				print_r($this->woocommerce_product_specifications_tab_content());
			}

		);

		return $tabs;

	}

	/**
	 * Register Specifications Tab Content on WooCommerce Product Specifications Tab
	 *
	 * @since    1.0.0
	 */
	public function woocommerce_product_specifications_tab_content()  {

		global $wpdb;
		global $product;

		$product_id = 0;

		if (!empty($product)) {
			$product_id = $product->get_id();
		}

		$sql = "select group_concat(`id` ORDER BY  `id` ASC SEPARATOR ',') as variation_ids from " . $wpdb->prefix . "posts where post_parent=" . $product_id . " and post_status='publish'";

		$result = $wpdb->get_results($sql);

		$sql = "select group_concat(`meta_value` ORDER BY `post_id` ASC SEPARATOR ',') as weight,
					(select group_concat(`meta_value` ORDER BY `post_id` ASC SEPARATOR ',') from " . $wpdb->prefix . "postmeta where post_id in (" . $result[0]->variation_ids . ") and meta_key='_weight_gross' ) as weight_gross,
					(select group_concat(`meta_value` ORDER BY `post_id` ASC SEPARATOR ',') from " . $wpdb->prefix . "postmeta where post_id in (" . $result[0]->variation_ids . ") and meta_key='_weight_unit' ) as weight_unit,
					(select group_concat(`meta_value` ORDER BY `post_id` ASC SEPARATOR ',') from " . $wpdb->prefix . "postmeta where post_id in (" . $result[0]->variation_ids . ") and meta_key='_length' ) as length,
					(select group_concat(`meta_value` ORDER BY `post_id` ASC SEPARATOR ',') from " . $wpdb->prefix . "postmeta where post_id in (" . $result[0]->variation_ids . ") and meta_key='_width' ) as width,
					(select group_concat(`meta_value` ORDER BY `post_id` ASC SEPARATOR ',') from " . $wpdb->prefix . "postmeta where post_id in (" . $result[0]->variation_ids . ") and meta_key='_height' ) as height,
					(select group_concat(`meta_value` ORDER BY `post_id` ASC SEPARATOR ',') from " . $wpdb->prefix . "postmeta where post_id in (" . $result[0]->variation_ids . ") and meta_key='_size_unit' ) as size_unit,
					(select meta_value from " . $wpdb->prefix . "postmeta where post_id=" . $product_id . "  and meta_key='_material' limit 1) as material,
					(select meta_value from " . $wpdb->prefix . "postmeta where post_id=" . $product_id . "  and meta_key='_protection' limit 1) as protection
					from " . $wpdb->prefix . "postmeta
					where post_id in (" . $result[0]->variation_ids . ") and meta_key='_weight'";

		$ret = $wpdb->get_results($sql);

		// Calculations

		$sizeUnit = array_unique(array_filter(explode(",", $ret[0]->size_unit)))[0];

		$weightUnit = array_unique(array_filter(explode(",", $ret[0]->weight_unit)))[0];

		if ($weightUnit == 'kg') {
			$weightUnit = 'g';
			$Weight = array_unique(array_filter(explode(",", $ret[0]->weight)))[0] * 1000;
			$GrossWeight = array_unique(array_filter(explode(",", $ret[0]->weight_gross)))[0] * 1000;
		} else {
			$Weight = array_unique(array_filter(explode(",", $ret[0]->weight)))[0];
			$GrossWeight = array_unique(array_filter(explode(",", $ret[0]->weight_gross)))[0];
		}

		if ($sizeUnit == 'cm') {
			$sizeUnit = 'mm';
			$Len = array_unique(array_filter(explode(",", $ret[0]->length)))[0] * 10;
			$Width = array_unique(array_filter(explode(",", $ret[0]->width)))[0] * 10;
			$Height = array_unique(array_filter(explode(",", $ret[0]->height)))[0] * 10;
		} else {
			$Len = array_unique(array_filter(explode(",", $ret[0]->length)))[0];
			$Width = array_unique(array_filter(explode(",", $ret[0]->width)))[0];
			$Height = array_unique(array_filter(explode(",", $ret[0]->height)))[0];
		}

		$Weight = round($Weight, 0);

		$GrossWeight = round($GrossWeight, 0);

		$Len = round($Len, 1);

		$Width = round($Width, 1);

		$Height = round($Height, 1);

		$wUnitNew = 0.0393700787402;

		$lUnitNew = 0.0352739619496;

		$sizeUnitN = '"';

		$weightUnitN = 'oz';

		$WeightN = sprintf("%.1f", $Weight * $wUnitNew);

		$GrossWeightN = sprintf("%.1f", $GrossWeight * $wUnitNew);

		$LenN = sprintf("%.1f", $Len * $lUnitNew);

		$WidthN = sprintf("%.1f", $Width * $lUnitNew);

		$HeightN = sprintf("%.1f", $Height * $lUnitNew);

		$WeightHtml = $Weight ? ($WeightN . ' ' . $weightUnitN . ' (' . $Weight . ' ' . $weightUnit . ')') : '-'; //3.93"(100.0mm)

		$GrossWeightHtml = $GrossWeight ? ($GrossWeightN . ' ' . $weightUnitN . ' (' . $GrossWeight . ' ' . $weightUnit . ')') : '-';

		$LenHtml = $Len ? ($LenN . $sizeUnitN . ' (' . $Len . ' ' . $sizeUnit . ')') : '-';

		$WidthHtml = $Width ? ($WidthN . $sizeUnitN . ' (' . $Width . ' ' . $sizeUnit . ')') : '-';

		$HeightHtml = $Height ? ($HeightN . $sizeUnitN . ' (' . $Height . ' ' . $sizeUnit . ')') : '-';

		?>

		<?php if ($Weight || $GrossWeight || $Len || $Width || $Height || $ret[0]->protection || $ret[0]->material) { ?>

			<?php

				// Types

				$types = array(

					'net-weight' => array(
						'name' 			=> 'Net Weight',
						'description' 	=> 'Weight of the product without packaging',
						'value'			=> $WeightHtml,
						'image'			=> '/wp-content/themes/flatsome-child/image/ask.png'
					),

					'gross-weight' => array(
						'name' 			=> 'Gross Weight',
						'description' 	=> 'Weight of the product without packaging',
						'value'			=> $GrossWeightHtml,
						'image'			=> '/wp-content/themes/flatsome-child/image/ask.png'
					),

					'length' => array(
						'name' 			=> 'Length',
						'description' 	=> 'Length',
						'value'			=> $LenHtml,
						'image'			=> ''
					)

				);

				// Sizes

				$sizes = array(

					'width' => array(
						'name' 			=> 'Width',
						'description' 	=> 'Width',
						'value'			=> $WeightHtml
					),

					'height' => array(
						'name' 			=> 'Height',
						'description' 	=> 'Height',
						'value'			=> $HeightHtml
					),

				);

				// Built

				$build = array(
					'protection' => array(
						'description' 	=> 'Width',
						'value'			=> $ret[0]->protection
					),

					'material' => array(
						'description' 	=> 'Height',
						'value'			=> $ret[0]->material
					),
				);

			?>

			<?php
				// Table Layouts

				$html = '';

				$html .= '<div class="esr_cloud_attrs">';

					$html .= '<table class="table woo-esr-extend-table">';

					// Types : Net, Gross & Length

					$html .= '<tr>';

					foreach($types as $type) {

							$html .= '<td>';

								$html .= $type['name'];

								if($type['image']) {

									$html .= ' <img src="' . $type['image'] . '" alt="' . $type['description'] . '" title="' . $type['description'] . '">';

								}

							$html .= '</td>';

							$html .= '<td>' . $type['value'] . '</td>';

					}

					$html .= '</tr>';

					// Sizes : Width & Height

					$html .= '<tr>';

					foreach($sizes as $size) {

						$html .= '<td>' . $size['name'] . '</td>';

						$html .= '<td>' . $size['value'] . '</td>';

					}

					$html .= '<td></td>';

					$html .= '<td></td>';

					$html .= '</tr>';

					// Build : Protection & Material

					if ($ret[0]->protection || $ret[0]->material) {

						$html .= '<tr>';

						foreach($build as $built) {

							$html .= '<td>' . $built['name'] . '</td>';

							$html .= '<td>' . $built['value'] . '</td>';

						}

						$html .= '<td></td>';

						$html .= '<td></td>';

						$html .= '</tr>';

					}

					$html .= '</table>';

				$html .= '</div>';

				return($html);

			?>

		<?php } ?>

		<?php

	}

}

