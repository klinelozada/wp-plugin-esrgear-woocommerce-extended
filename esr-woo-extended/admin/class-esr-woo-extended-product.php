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

        add_filter( 'woocommerce_product_data_tabs', array( &$this, 'woocommerce_product_data_tabs' ) );

        // ========= Data Panels =========

            // > Related Products
            add_action( 'woocommerce_product_data_panels', array( &$this, 'woocommerce_product_data_tab_related_products_panel' ) );

            // > Featured Video
            add_action( 'woocommerce_product_data_panels', array( &$this, 'woocommerce_product_data_tab_featured_video_panel' ) );

        // ========= Data Tabs =========

            // > Related Products
            add_action( 'woocommerce_product_options_general_product_data', array( &$this, 'woocommerce_related_products' ) );

            // > Featured Video
            // add_action( 'woocommerce_product_options_general_product_data', array( &$this, 'woocommerce_related_products' ) );

        // Data Save

        add_action( 'woocommerce_process_product_meta', array( &$this, 'woocommerce_data_save' ) );

        add_shortcode('woo_extend_related_products', array(&$this, 'woocommerce_product_related_links_shortcode') );

        add_action( 'wp_footer', array(&$this, 'woocommerce_product_insert_featured_video_thumbnail_script') );

	}

	/**
	 * Adding New Data Tab
	 *
     * Added:
     * - Related Products Tabs
     * - Featured Video Tabs
     *
	 * @since    1.0.0
	 */

    public function woocommerce_product_data_tabs( $product_data_tabs ) {

        $product_data_tabs['woo-extend-related-products'] = array(
            'label'     =>  __( 'Related Products', 'woo_extend_related_products' ),
            'target'    =>  'woo_extend_related_products',
            'priority'  =>  100,
        );

        $product_data_tabs['woo-extend-featured-product-video'] = array(
            'label'     =>  __( 'Featured Video', 'woo_extend_featured_product_video' ),
            'target'    =>  'woo_extend_featured_product_video',
            'priority'  =>  100,
        );

        return $product_data_tabs;
    }

	/**
	 * Related Products Panel
	 *
	 * @since    1.0.0
	 */
    public function woocommerce_product_data_tab_related_products_panel() {

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
	 * Create Related Links Shortcode
	 *
	 * @since    1.0.0
	 */
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

            if ( $product_ids ) {

                $html = '<span class="related-products-title">Choose a Device: </span>';

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

	/**
	 * Related Products Panel
	 *
	 * @since    1.0.0
	 */
    public function woocommerce_product_data_tab_featured_video_panel() {

        global $woocommerce, $post;

        ?>

        <div id="woo_extend_featured_product_video" class="panel woocommerce_options_panel hidden">

            <p class="form-field">

                    <label for="featured_video"><?php _e( 'Featured Video', 'woocommerce' ); ?></label>

                    <?php

                        $data = get_post_meta( get_the_ID(), 'wc_productdata_options', true );

                        woocommerce_wp_text_input(
                            array(
                                'id'          => 'featured_product_video_url',
                                'value'       => $data[0]['_product_video'],
                                'label'       => __( 'Video URL', 'woo-extend-featured-product-video' ),
                                'description' => __( 'Paste Video Url', 'woo-extend-featured-product-video' ),
                            )
                        );

                    ?>

            </p>

        </div>

        <?php

    }

	/**
	 * Add Video Thumbnail in the WooCommerce Gallery
	 *
	 * @since    1.0.0
	 */
    public function woocommerce_product_insert_featured_video_thumbnail_script() {

        if($_GET['action'] == 'elementor') {
            return false;
        }

        // Get Video Data

        $data = get_post_meta( get_the_ID(), 'wc_productdata_options', true );

        // Target DOM
        $target = '.woolentor-thumb-single.slick-current.slick-active';

        // Class of the DOM
        $class = 'woolentor-thumb-single slick-slide slick-popup-video';

        // Thumbnail for the DOM
        $thumbnail = 'https://static.esrgear.com/wp-content/themes/flatsome-child/image/video-icon.jpg';

        // Vimeo URL Example
        // https://vimeo.com/225434434

        // Youtube URL Example
        // https://www.youtube.com/watch?v=2YBtspm8j8M

        $video_url = 'https://www.youtube.com/watch?v=2YBtspm8j8M';

        $video_url = $data[0]['_product_video'];

        $video_source = $this->woocommerce_generate_video_embed_code($video_url, 560, 315);

        // Get Video ID

        $url_id = '';

        // Youtube Embed Generated
        $url_embed = '<iframe src="https://www.youtube.com/embed/QH2-TGUlwu4" width="560" height="315" title="Nyan Cat [original]" frameborder="0" allowfullscreen></iframe>';

        // Insert script to add in the DOM
        $script = '<div class="' . $class . '" data-slick-index="0" aria-hidden="false" data-video-url="' . $data[0]['_product_video'] . '" style="width: 139px;" tabindex="0" id="woo-extend-featured-video-popup">';

        $script .= '<img width="100" height="100" src="' . $thumbnail . '" class="attachment-woocommerce_gallery_thumbnail size-woocommerce_gallery_thumbnail" alt="" loading="lazy">';

        $script .= '</div>';

        // Creation of DOM
        $dom_script = '<script>jQuery(document).ready(function($) {';

            // Adding a youtube icon in the gallery
            // Status:: Disabled
            // $dom_script .= "$('". $target ."').before('". $script ."');";

            $dom_script .= '$("body #featured-video").on("click", function () { ';

                $dom_script .= 'var video_url = $(this).data("video-url");';

                $dom_script .= '$("body .featured-video-lightbox").show();';

                $dom_script .= '$("body .featured-video-lightbox").append("'.$video_source.'");';

            $dom_script .= '});';

            // Close and remove video on click

            $dom_script .= '$("body #featured-video-box").on("click", function() { ';

                $dom_script .= '$(this).empty();';

                $dom_script .= '$(this).hide();';

            $dom_script .= '});';

        $dom_script .= '});</script>';

        $dom_script .= "<a href='#' class='featured-video-lightbox' id='featured-video-box'>" . $youtube_embed . "</a>";

        print_r($dom_script);

    }

    public function woocommerce_generate_video_embed_code($url, $width, $height) {

        // Default Width
        if(!$width)
            $width = '560';

        // Default Height
        if(!$height)
            $height = '315';

        // Identify Source

        if (strpos($url, 'youtube') > 0) {

            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);

            // Get Youtube ID
            $video_id = $match[1];

            // Generate Embed Code
            $video_embed = "<span><iframe src='https://www.youtube.com/embed/" . $video_id . "' width='" . $width . "' height='" . $height . "' frameborder='0' allowfullscreen id='featured-video-iframe'></iframe></span>";

        } elseif (strpos($url, 'vimeo') > 0) {

            // Get Vimeo ID
            $video_id = preg_replace("/[^\/]+[^0-9]|(\/)/", "", rtrim($url, "/"));

            // Generate Embed Code
            $video_embed = "<span><iframe width='" . $width . "px' height='" . $height . "px' src='https://player.vimeo.com/video/" . $video_id . "' id='featured-video-iframe'></iframe></span>";

        } else {

            $video_id = NULL;

            $video_embed = NULL;

        }

        // Return Generated Embed Code
        return($video_embed);

    }

	/**
	 * Save WooCommerce Linked Products from the Product Page
	 *
	 * @since    1.0.0
	 */
    public function woocommerce_data_save( $post_id ) {

        $product_field_type =  $_POST['related_product_ids'];

        $featured_video_url =  $_POST['featured_video_url'];

        update_post_meta( $post_id, 'related_product_ids', $product_field_type );

        update_post_meta( $post_id, 'featured_video_url', $product_field_type );

    }

}