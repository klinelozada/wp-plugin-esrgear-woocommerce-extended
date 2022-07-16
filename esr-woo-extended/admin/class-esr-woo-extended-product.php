<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://esrgear.com/
 * @since      1.0.0
 *
 * @package    Esr_Woo_Extended
 * @subpackage Esr_Woo_Extended/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Esr_Woo_Extended
 * @subpackage Esr_Woo_Extended/admin
 * @author     ESRGear <admin@esrgear.com>
 */
class Esr_Woo_Extended_Product_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;

		$this->version = $version;

        add_filter( 'woocommerce_product_data_tabs', array( &$this, 'woocommerce_product_data_tab_related_products_tab' ) );

        add_action( 'woocommerce_product_data_panels', array( &$this, 'woocommerce_product_data_tab_related_products_panel' ) );

        add_action( 'woocommerce_product_options_general_product_data', array( &$this, 'woocommerce_related_products' ) );

        add_action( 'woocommerce_process_product_meta', array( &$this, 'woocommerce_related_products_save' ) );

        add_shortcode('woo_extend_related_products', array(&$this, 'woocommerce_product_related_links_shortcode') );


	}

	/**
	 * Adding New Data Tab
	 *
	 * @since    1.0.0
	 */

    public function woocommerce_product_data_tab_related_products_tab( $product_data_tabs ) {

        $product_data_tabs['woo-extend-related-products'] = array(
            'label'     =>  __( 'Related Products', 'woo_extend_related_products' ),
            'target'    =>  'woo_extend_related_products',
            'priority'  =>  100,
        );

        return $product_data_tabs;
    }

    function woocommerce_product_data_tab_related_products_panel() {

        global $woocommerce, $post;

        ?>

        <div id="woo_extend_related_products" class="panel woocommerce_options_panel hidden">

            <p class="form-field">

                    <label for="related_product_ids"><?php _e( 'Related Products', 'woocommerce' ); ?></label>

                    <select class="wc-product-search" multiple="multiple" style="width: 50%;" id="related_product_ids" name="related_product_ids[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-exclude="<?php echo intval( $post->ID ); ?>">

                        <?php

                            // $related_product_ids = get_post_meta( $post->ID, 'related_product_ids', true );

                            $related_product_ids = get_post_meta( $post->ID, '_upsell_ids3', true );

                            if($related_product_ids) {

                                $product_ids = ! empty( $related_product_ids ) ? array_map( 'absint',  $related_product_ids ) : null;

                                if ( $product_ids ) {
                                    foreach ( $product_ids as $product_id ) {

                                        $product      = get_product( $product_id );
                                        $product_name = woocommerce_get_formatted_product_name( $product );

                                        echo '<option value="' . esc_attr( $product_id ) . '" selected="selected">' . esc_html( $product_name ) . '</option>';
                                    }
                                }

                            } else {

                                $product_object = new WC_Product($post->ID);

                                $product_ids = $product_object->get_upsell_ids( 'edit' );

                                foreach ( $product_ids as $product_id ) {

                                    $product = wc_get_product( $product_id );

                                    if ( is_object( $product ) ) {
                                        echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
                                    }

                                }

                            }

                        ?>
                    </select> <?php echo wc_help_tip( __( 'Related products are main products that support this product.', 'woocommerce' ) ); ?>

            </p>

        </div>

        <?php

    }

	/**
	 * Save WooCommerce Linked Products from the Product Page
	 *
	 * @since    1.0.0
	 */

    public function woocommerce_related_products_save( $post_id ) {

        $product_field_type =  $_POST['related_product_ids'];

        update_post_meta( $post_id, 'related_product_ids', $product_field_type );

    }

    public function woocommerce_product_related_links_shortcode() {

        // Check if elementor editor

        if($_GET['action'] == 'elementor') {
            return false;
        }


            global $product;

            $id = $product->get_id();

            // $related_product_ids = get_post_meta( $id, 'related_product_ids', true );

            $related_product_ids = get_post_meta( $id, '_upsell_ids3', true );

            if(!$related_product_ids) {

                $related_product_ids = get_post_meta( $post->ID, '_upsell_ids3', true );

            }

            $product_ids = ! empty( $related_product_ids ) ? array_map( 'absint',  $related_product_ids ) : null;

            $html = '<span class="related-products-title">Choose a Device: </span>';

            if ( $product_ids ) {

                $html .= '<select name="related_products" class="" id="related-products-link">';

                $html .= '<option value="#">---</option>';

                    foreach ( $product_ids as $product_id ) {

                        $product      = get_product( $product_id );

                        $product_name = woocommerce_get_formatted_product_name( $product );

                        $html .= '<option value="' . $product->get_permalink() . '">' . esc_html( $product->get_title() ) . '</option>';

                    }

                $html .= '</select>';

            }

            return($html);


    }

}