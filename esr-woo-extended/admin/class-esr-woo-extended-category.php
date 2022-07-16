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
class Esr_Woo_Extended_Product_Category_Admin {

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

        add_action( 'product_cat_add_form_fields', array(&$this, 'woocommerce_product_category_description_additional'), 10, 2 );

        add_action( 'product_cat_edit_form_fields', array(&$this, 'woocommerce_product_category_description_additional_edit'), 10, 2 );

        add_action( 'edit_term', array(&$this, 'woocommerce_product_category_description_additional_save'), 10, 3 );

        add_action( 'created_term', array(&$this, 'woocommerce_product_category_description_additional_save'), 10, 3 );

        add_action( 'woocommerce_after_shop_loop', array(&$this, 'woocommerce_product_category_description_additional_show'), 5 );

        add_shortcode('woo_extend_additional_description', array(&$this, 'woocommerce_product_category_description_additional_shortcode') );

	}

	/**
	 * Add WYSIWYG on the product category of woocommerce
	 *
	 * @since    1.0.0
	 */

    public function woocommerce_product_category_description_additional() {

        ?>

        <div class="form-field">

            <label for="additional_description"><?php echo __( 'Second Description', 'woocommerce' ); ?></label>

            <?php

            $settings = array(
                'textarea_name' => 'additional_description',
                'quicktags' => array( 'buttons' => 'em,strong,link' ),
                'tinymce' => array(
                'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
                'theme_advanced_buttons2' => '',
                ),
                'editor_css' => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>',
            );

            wp_editor( '', 'additional_description', $settings );
          ?>

            <p class="description">
                <?php echo __( 'This is the description that goes BELOW products on the category page', 'woocommerce' ); ?>
            </p>

        </div>

        <?php
    }

	/**
	 * Add WYSIWYG on the product category when editing category
	 *
	 * @since    1.0.0
	 */

    public function woocommerce_product_category_description_additional_edit( $term ) {

        $second_desc = htmlspecialchars_decode( get_woocommerce_term_meta( $term->term_id, 'additional_description', true ) );

        ?>

        <tr class="form-field">

            <th scope="row" valign="top">
                <label for="second-desc">
                    <?php echo __( 'Second Description', 'woocommerce' ); ?>
                </label>
            </th>

            <td>
                <?php

                    $settings = array(
                        'textarea_name' => 'additional_description',
                        'quicktags' =>
                            array(
                                'buttons' => 'em,strong,link'
                            ),
                        'tinymce' =>
                            array(
                                'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
                                'theme_advanced_buttons2' => '',
                            ),
                        'editor_css' => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>',
                    );

                    wp_editor( $second_desc, 'additional_description', $settings );

                ?>

                <p class="description">
                    <?php echo __( 'This is the description that goes BELOW products on the category page', 'woocommerce' ); ?>
                </p>

            </td>

        </tr>

        <?php
    }

	/**
	 * Function responsible to save the data in the `additional description`
	 *
	 * @since    1.0.0
	 */

    public function woocommerce_product_category_description_additional_save( $term_id, $tt_id = '', $taxonomy = '' ) {

        if ( isset( $_POST['additional_description'] ) && 'product_cat' === $taxonomy ) {
           update_woocommerce_term_meta( $term_id, 'additional_description', esc_attr( $_POST['additional_description'] ) );
        }

     }

	/**
	 * Function responsible to show the position of the WYSIWYG
	 *
	 * @since    1.0.0
	 */

    public function woocommerce_product_category_description_additional_show() {

        if ( is_product_taxonomy() ) {

           $term = get_queried_object();
           if ( $term && ! empty( get_woocommerce_term_meta( $term->term_id, 'seconddesc', true ) ) ) {
              echo '<p class="term-description">' . wc_format_content( htmlspecialchars_decode( get_woocommerce_term_meta( $term->term_id, 'seconddesc', true ) ) ) . '</p>';
           }

        }

    }

	/**
	 * Function responsible to add a shortcode and show it on the frontend
	 *
	 * @since    1.0.0
	 */

    public function woocommerce_product_category_description_additional_shortcode() {

        $queried_object = get_queried_object();

        $term_id = $queried_object->term_id;

        $html = htmlspecialchars_decode( get_woocommerce_term_meta( $term_id, 'additional_description', true ) );

        return $html;

    }

}