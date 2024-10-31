<?php

namespace Rent_Angry_Cat_ns;

class Rent_Angry_Cat {

    public static function crp_templates_php_path(){
        return __DIR__.'/templates/';
    }

    public static function getPluginName(){
        return 'rent_angry_cat';
    }

    public static function getPluginHumName(){
        return __('Rent angry cat', 'rent_angry_cat');
    }

    public static function devmode(){
        return false;
    }

	public function __construct() {
        $this->thumbnailsRegister();
        $this->runNewProductSetup();

        if( is_admin() ){
            $this->ajaxRegisterActions();
            $this->adminPage();
            //$this->adminPageFromPlagin();
        }else{
            $this->scriptscss();
            $this->addShortCodes();
        }
	}

    public function adminPage() {
        add_action( 'admin_menu', function (){
            add_submenu_page('options-general.php', self::getPluginHumName(), self::getPluginHumName(), 'manage_options', self::getPluginName(), [ &$this, 'admin_submenu_page_callback' ] );
        } );
    }

    /*public function adminPageFromPlagin() {
        add_filter('plugin_action_links_'.self::getPluginName().'/'.self::getPluginName().'.php', [ &$this, 'admin_add_plugin_page_settings_link' ]);
    }
    public function admin_add_plugin_page_settings_link( $links ) {
        $links[] = '<a href="'.admin_url( 'options-general.php?page=rent_angry_cat' ) .'">'.__('Settings', 'rent_angry_cat').'</a>';
        return $links;
    }*/

    public function admin_submenu_page_callback(){
        global $wp;

        if( isset( $_POST['dscc_admin_submit'] ) ){
            $dscc_admin_googleipikey       = ( isset( $_POST['dscc_admin_googleipikey'] )       ?sanitize_text_field( stripcslashes( $_POST['dscc_admin_googleipikey'] ) ) :'' );
            $dscc_admin_listitem_count     = ( isset( $_POST['dscc_admin_listitem_count'] )     ?(int)$_POST['dscc_admin_listitem_count']      :3 );
            $dscc_admin_filter_radius      = ( isset( $_POST['dscc_admin_filter_radius'] )      ?(int)$_POST['dscc_admin_filter_radius']       :3 );
            $dscc_filter_type_show         = ( isset( $_POST['dscc_filter_type_show'] )         ?(int)$_POST['dscc_filter_type_show']          :0 );
            $dscc_admin_lb_pager           = ( isset( $_POST['dscc_admin_lb_pager'] )           ?1 :0 );
            $dscc_admin_lb_autoplay        = ( isset( $_POST['dscc_admin_lb_autoplay'] )        ?1 :0 );
            $dscc_admin_lb_zoom            = ( isset( $_POST['dscc_admin_lb_zoom'] )            ?1 :0 );
            $dscc_admin_lb_fullscreen      = ( isset( $_POST['dscc_admin_lb_fullscreen'] )      ?1 :0 );
            $dscc_admin_lb_hash            = ( isset( $_POST['dscc_admin_lb_hash'] )            ?1 :0 );
            $dscc_admin_lb_share           = ( isset( $_POST['dscc_admin_lb_share'] )           ?1 :0 );
            $dscc_admin_lb_thumbnail       = ( isset( $_POST['dscc_admin_lb_thumbnail'] )       ?1 :0 );
            $dscc_admin_lb_rotate          = ( isset( $_POST['dscc_admin_lb_rotate'] )          ?1 :0 );

            update_option( 'dscc_admin_googleipikey',       $dscc_admin_googleipikey );
            update_option( 'dscc_admin_listitem_count',     $dscc_admin_listitem_count );
            update_option( 'dscc_admin_filter_radius',      $dscc_admin_filter_radius );
            update_option( 'dscc_admin_lb_pager',           $dscc_admin_lb_pager );
            update_option( 'dscc_admin_lb_autoplay',        $dscc_admin_lb_autoplay );
            update_option( 'dscc_admin_lb_zoom',            $dscc_admin_lb_zoom );
            update_option( 'dscc_admin_lb_fullscreen',      $dscc_admin_lb_fullscreen );
            update_option( 'dscc_admin_lb_hash',            $dscc_admin_lb_hash );
            update_option( 'dscc_admin_lb_share',           $dscc_admin_lb_share );
            update_option( 'dscc_admin_lb_thumbnail',       $dscc_admin_lb_thumbnail );
            update_option( 'dscc_admin_lb_rotate',          $dscc_admin_lb_rotate );
            update_option( 'dscc_filter_type_show',         $dscc_filter_type_show );
        }

        echo '<div class="wrap">
                  <h1 class="wp-heading-inline">'.self::getPluginHumName().' <small> echo do_shortcode(\'[rent_angry_cat perpage="5"]\'); </small></h1>
                  <form action="'.esc_url( $wp->request ).'" method="post">
                      <table class="form-table">
                        <tbody>
                            <tr>
                                <td colspan="2">
                                    <h3>'.__('Setup options:', 'rent_angry_cat').'</h3>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    '.__('Google API key for MAP:', 'rent_angry_cat').'
                                </th>
                                <td>
                                    <input type="text"     name="dscc_admin_googleipikey"   value="'.esc_attr( self::getGoogleKey() ).'" class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    '.__('Search items per page:', 'rent_angry_cat').'
                                </th>
                                <td>
                                    <input type="number"   name="dscc_admin_listitem_count" value="'.esc_attr( self::getSearchPostPerPage() ).'" min="1" max="99" step="1" class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    '.__('Search radius default:', 'rent_angry_cat').'
                                </th>
                                <td>
                                    <input type="number"   name="dscc_admin_filter_radius" value="'.esc_attr( self::getSearchDefRadius() ).'" min="1" max="99" step="1" class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    '.__('Filter type (cities,radius,both):', 'rent_angry_cat').'
                                </th>
                                <td>
                                    <select name="dscc_filter_type_show" class="regular-text">
                                        <option value="0" '.( self::getSearchFilterType()==0 ?'selected' :'').'>'.__('Show both', 'rent_angry_cat').'</option>
                                        <option value="1" '.( self::getSearchFilterType()==1 ?'selected' :'').'>'.__('Show cities', 'rent_angry_cat').'</option>
                                        <option value="2" '.( self::getSearchFilterType()==2 ?'selected' :'').'>'.__('Show radius', 'rent_angry_cat').'</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <h3>'.__('Gallery options:', 'rent_angry_cat').'</h3>
                                </td>
                            </tr>
                            <!--<tr>
                                <th>
                                    '.__('Pager:', 'rent_angry_cat').'
                                </th>
                                <td>
                                    <input type="checkbox"  name="dscc_admin_lb_pager" '.( (int)get_option('dscc_admin_lb_pager')==1 ?'checked' :'' ).'>
                                </td>
                            </tr>-->
                            <!--<tr>
                                <th>
                                    '.__('Autoplay slider:', 'rent_angry_cat').'
                                </th>
                                <td>
                                    <input type="checkbox"  name="dscc_admin_lb_autoplay" '.( (int)get_option('dscc_admin_lb_autoplay')==1 ?'checked' :'' ).'>
                                </td>
                            </tr>-->
                            <tr>
                                <th>
                                    '.__('Fullscreen:', 'rent_angry_cat').'
                                </th>
                                <td>
                                    <input type="checkbox"  name="dscc_admin_lb_fullscreen" '.( (int)get_option('dscc_admin_lb_fullscreen')==1 ?'checked' :'' ).'>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    '.__('Zoom:', 'rent_angry_cat').'
                                </th>
                                <td>
                                    <input type="checkbox"  name="dscc_admin_lb_zoom" '.( (int)get_option('dscc_admin_lb_zoom')==1 ?'checked' :'' ).'>
                                </td>
                            </tr>
                            <!--<tr>
                                <th>
                                    '.__('Hash:', 'rent_angry_cat').'
                                </th>
                                <td>
                                    <input type="checkbox"  name="dscc_admin_lb_hash" '.( (int)get_option('dscc_admin_lb_hash')==1 ?'checked' :'' ).'>
                                </td>
                            </tr>-->
                            <!--<tr>
                                <th>
                                    '.__('Share image:', 'rent_angry_cat').'
                                </th>
                                <td>
                                    <input type="checkbox"  name="dscc_admin_lb_share" '.( (int)get_option('dscc_admin_lb_share')==1 ?'checked' :'' ).'>
                                </td>
                            </tr>-->
                            <!--<tr>
                                <th>
                                    '.__('Rotate slide:', 'rent_angry_cat').'
                                </th>
                                <td>
                                    <input type="checkbox"  name="dscc_admin_lb_rotate" '.( (int)get_option('dscc_admin_lb_rotate')==1 ?'checked' :'' ).'>
                                </td>
                            </tr>-->
                            <tr>
                                <th>
                                    '.__('Show thumbnails bottom:', 'rent_angry_cat').'
                                </th>
                                <td>
                                    <input type="checkbox"  name="dscc_admin_lb_thumbnail" '.( (int)get_option('dscc_admin_lb_thumbnail')==1 ?'checked' :'' ).'>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type = "submit"  name="dscc_admin_submit" class="button button-primary" value = "'.esc_attr( __('Save changes', 'rent_angry_cat') ).'">
                                </td>
                            </tr>
                         </tbody>
                      </table>
                  </form>
              </div>';
    }

	public function runNewProductSetup() {

        //=== Register taxonomy City for filter
        add_action( 'init', function (){
            $titles = [
                         'name'          => __( 'Rent in cities', 'rent_angry_cat' ),
                         'singular_name' => __( 'Rent in cities', 'rent_angry_cat' ),
                         'menu_name'     => __( 'Rent in cities', 'rent_angry_cat' ),
                      ];
            $args = [
                'labels'                     => $titles,
                'hierarchical'               => false,
                'public'                     => true,
                'show_ui'                    => true,
                'show_admin_column'          => false,
                'show_in_nav_menus'          => true,
                'show_tagcloud'              => true,
                'show_in_menu'               => true,
                'rewrite'                    => [ 'slug'=>'rentacatcat', 'with_front'=>false ],
            ];
            register_taxonomy('rentacatcat', ['product'], $args );
        });

        /* Cart item disable change quantity */
        add_filter( 'woocommerce_cart_item_quantity', function( $product_quantity, $cart_item_key, $cart_item ){
            $product_id = $cart_item['product_id'];
            $prod = wc_get_product( $product_id );
            if ( $prod->is_type('rentacat') ) {
                return '<h3>' . $cart_item['quantity'] . '</h3>';
            }
            return $product_quantity;
        }, 10, 3);

        //=== 1. Register WOO product type
        add_action('init', 'rent_angry_cat_register_product_type');

        //=== 2. Add new product type to WOO type list (adminpanel)
        add_filter('product_type_selector', function ( $types ){
            $types['rentacat'] = __('Rental product', 'rent_angry_cat');
            return $types;
        });

        //=== 3. Add datatab for new WOO product type
        add_filter( 'woocommerce_product_data_tabs', function ( $original_prodata_tabs ){
            if( isset( $_GET['post'] ) && isset( $_GET['action'] ) ){
                $prod = wc_get_product( $_GET['post'] );
                if( $prod->is_type('rentacat') ){
                    $new_custom_tab['rentacat'] = [
                        'label'	 => __( 'Rental product', 'rent_angry_cat' ),
                        'target' => 'rentacat_product_options',
                        'class'  => 'show_if_rentacat_product',
                    ];
                    $insert_at_position = 1;
                    $tabs = array_slice( $original_prodata_tabs, 0, $insert_at_position, true );
                    $tabs = array_merge( $tabs, $new_custom_tab );
                    $tabs = array_merge( $tabs, array_slice( $original_prodata_tabs, $insert_at_position, null, true ));
                    return $tabs;
                }
            }
            return $original_prodata_tabs;
        });

        //=== 4. Add inputs to new datatab WOO product (adminpanel)
        add_action( 'woocommerce_product_data_panels', function (){

            if( isset( $_GET['post'] ) && isset( $_GET['action'] ) ){
                $prod = wc_get_product( $_GET['post'] );
                if($prod->is_type('rentacat')){
                    $_GET['action'] = (int)$_GET['action']; ?>
                    <div id='rentacat_product_options' class='panel woocommerce_options_panel'>
                        <div class='options_group'><?php
                            woocommerce_wp_text_input([
                                'id'          => '_regular_price',
                                'label'       => __( 'Price per hour', 'rent_angry_cat' ).':',
                                'placeholder' => '',
                                'desc_tip'    => 'true',
                                'description' => __( 'Price per hour', 'rent_angry_cat' ).'.',
                                'type'        => 'text'
                            ]);
                            woocommerce_wp_text_input([
                                'id'          => 'dscc_latitude',
                                'label'       => __( 'Position Latitude', 'rent_angry_cat' ).':',
                                'placeholder' => '',
                                'desc_tip'    => 'true',
                                'description' => __( 'Position Latitude', 'rent_angry_cat' ).'.',
                                'type'        => 'text'
                            ]);
                            woocommerce_wp_text_input([
                                'id'          => 'dscc_longitude',
                                'label'       => __( 'Position Longitude', 'rent_angry_cat' ).':',
                                'placeholder' => '',
                                'desc_tip'    => 'true',
                                'description' => __( 'Position Longitude', 'rent_angry_cat' ).'.',
                                'type'        => 'text'
                            ]);
                            woocommerce_wp_select( [
                                'id'          => 'dscc_tsbletype',
                                'description' => __('Rate Table style', 'rent_angry_cat').'.',
                                'label'       => __('Rate Table style', 'rent_angry_cat').':',
                                'desc_tip'    => 'true',
                                'options' => [
                                    'tablefull'  => __('Hoursly full', 'rent_angry_cat'),
                                    'tableshort' => __('Hoursly short', 'rent_angry_cat'),
                                    'dayly'      => __('Dayly', 'rent_angry_cat'),
                                ],
                            ]);
                            woocommerce_wp_text_input([
                                'id'          => 'dscc_time_from_rate',
                                'label'       => __( 'Rate start time, H', 'rent_angry_cat' ).':',
                                'placeholder' => '',
                                'desc_tip'    => 'true',
                                'description' => __( 'Rate start time, H', 'rent_angry_cat' ).'.',
                                'type'        => 'number',
                                'custom_attributes' => [
                                    'step' 	=> '1',
                                    'min'	=> '0',
                                    'max'	=> '24',
                                ],
                            ]);
                            woocommerce_wp_text_input([
                                'id'          => 'dscc_time_to_rate',
                                'label'       => __( 'Rate and time, H', 'rent_angry_cat' ).':',
                                'placeholder' => '',
                                'desc_tip'    => 'true',
                                'description' => __( 'Rate and time, H', 'rent_angry_cat' ).'.',
                                'type'        => 'number',
                                'custom_attributes' => [
                                    'step' 	=> '1',
                                    'min'	=> '0',
                                    'max'	=> '24',
                                ],
                            ]);

                            $countHours = count( self::get_dscc_timearrea( $_GET['post'] ) );
                            woocommerce_wp_checkbox([
                                'id'          => 'dscc_timearrea_clear',
                                'label'       => sprintf( __( 'Clear all rented time. Count: %s [Hours]', 'rent_angry_cat' ), $countHours ).':',
                                'desc_tip'    => 'true',
                                'description' => __( 'Clear all rented time', 'rent_angry_cat' ).'.',
                            ]);

                            $countDays = count( self::get_dscc_datearrea( $_GET['post'] ) );
                            woocommerce_wp_checkbox([
                                'id'          => 'dscc_datearrea_clear',
                                'label'       => sprintf( __( 'Clear all rented date. Count: %s [Days]', 'rent_angry_cat' ), $countDays ).':',
                                'desc_tip'    => 'true',
                                'description' => __( 'Clear all rented date', 'rent_angry_cat' ).'.',
                            ]);
                            if( self::devmode() ){
                                woocommerce_wp_text_input([
                                    'id'          => 'dscc_timearrea',
                                    'label'       => __( 'Debug time array', 'rent_angry_cat' ).':',
                                    'placeholder' => '',
                                    'desc_tip'    => 'true',
                                    'description' => __( 'Debug time array', 'rent_angry_cat' ).'.',
                                    'type'        => 'text'
                                ]);
                                woocommerce_wp_text_input([
                                    'id'          => 'dscc_datearrea',
                                    'label'       => __( 'Debug date array', 'rent_angry_cat' ).':',
                                    'placeholder' => '',
                                    'desc_tip'    => 'true',
                                    'description' => __( 'Debug date array', 'rent_angry_cat' ).'.',
                                    'type'        => 'text'
                                ]);
                            } ?>
                        </div>
                    </div><?php
                }
            }
        });



        //=== 5. Save inputs value from datatab WOO product (adminpanel)
        add_action( 'woocommerce_process_product_meta', function ( $post_id ) {
            $product = wc_get_product( $post_id );

            if( $product->is_type('rentacat') ){
                $dscc_timearrea      = isset($_POST['dscc_timearrea'])      ?sanitize_text_field( $_POST['dscc_timearrea'] ) :0;
                update_post_meta( $post_id, 'dscc_timearrea', $dscc_timearrea );

                $dscc_datearrea      = isset($_POST['dscc_datearrea'])      ?sanitize_text_field( $_POST['dscc_datearrea'] ) :0;
                update_post_meta( $post_id, 'dscc_datearrea', $dscc_datearrea );

                $dscc_latitude       = isset($_POST['dscc_latitude'])       ?(float)$_POST['dscc_latitude'] :0;
                update_post_meta( $post_id, 'dscc_latitude', $dscc_latitude );

                $dscc_longitude      = isset($_POST['dscc_longitude'])      ?(float)$_POST['dscc_longitude'] :0;
                update_post_meta( $post_id, 'dscc_longitude', $dscc_longitude );

                $dscc_time_from_rate = isset($_POST['dscc_time_from_rate']) ?(int)$_POST['dscc_time_from_rate'] :0;
                update_post_meta( $post_id, 'dscc_time_from_rate', $dscc_time_from_rate );

                $dscc_time_to_rate   = isset($_POST['dscc_time_to_rate'])   ?(int)$_POST['dscc_time_to_rate'] :24;
                update_post_meta( $post_id, 'dscc_time_to_rate', $dscc_time_to_rate );

                $dscc_tsbletype      = isset( $_POST['dscc_tsbletype'] )    ?sanitize_text_field( $_POST['dscc_tsbletype'] ) :'';
                update_post_meta( $post_id, 'dscc_tsbletype', $dscc_tsbletype );

                /* Clear hourly rate array */
                if( isset( $_POST['dscc_timearrea_clear'] ) ){
                    update_post_meta( $post_id, 'dscc_timearrea', '' );
                }

                /* Clear dayly rate array */
                if( isset( $_POST['dscc_datearrea_clear'] ) ){
                    update_post_meta( $post_id, 'dscc_datearrea', '' );
                }

                $product->save();
            }
        });

        //=== 6. Hide not used tabs to product (adminpanel)
        add_filter('woocommerce_product_data_tabs', function ( $tabs ){
            if( isset( $_GET['post'] ) && isset( $_GET['action'] ) ){
                $prod = wc_get_product( $_GET['post'] );
                if( $prod->is_type('rentacat') ){
                    unset( $tabs['general'] );
                    unset( $tabs['linked_product'] );
                    unset( $tabs['advanced'] );
                    unset( $tabs['supplier_tab'] );
                    //unset( $tabs['shipping'] );
                    unset( $tabs['attribute'] );
                }
            }
            return( $tabs );
        });

        /*//=== Show inputs field above Add to Cart===
        add_action( 'woocommerce_before_add_to_cart_button', 'njengah_product_add_on', 9 );
        function njengah_product_add_on() {
            $custom_sp_width  = isset($_POST['custom_sp_width'])  ?(int)$_POST['custom_sp_width']  :0;
            $custom_sp_height = isset($_POST['custom_sp_height']) ?(int)$_POST['custom_sp_height'] :0;
            $custom_sp_square = isset($_POST['custom_sp_square']) ?(int)$_POST['custom_sp_square'] :0;
            echo'<div><label>'.__('Width',  'rent_angry_cat').'</label><input type="number" id="custom_sp_width"  min="0" step="1" name="custom_sp_width"  value="' . $custom_sp_width .  '"></div>';
            echo'<div><label>'.__('Height', 'rent_angry_cat').'</label><input type="number" id="custom_sp_height" min="0" step="1" name="custom_sp_height" value="' . $custom_sp_height . '"></div>';
            echo'<div><label>'.__('Height', 'rent_angry_cat').'</label><input type="number" id="custom_sp_square" min="0" step="1" name="custom_sp_square" value="' . $custom_sp_square . '" readonly></div>';
            echo'<script>
                    const custom_sp_width  = document.getElementById("custom_sp_width");
                    const custom_sp_height = document.getElementById("custom_sp_height");
                    const custom_sp_square = document.getElementById("custom_sp_square");
                    custom_sp_width.addEventListener("change", update_custom_sp_square);
                    custom_sp_height.addEventListener("change", update_custom_sp_square);
                    function update_custom_sp_square(){
                        custom_sp_square.value = parseInt(custom_sp_width.value) * parseInt(custom_sp_height.value);
                    }
                 </script>';
        }

        //=== Throw error if custom input field empty
        add_filter( 'woocommerce_add_to_cart_validation', 'njengah_product_add_on_validation', 10, 3 );
        function njengah_product_add_on_validation( $passed, $product_id, $qty ){
           if( isset( $_POST['custom_sp_width'] )  && (int)$_POST['custom_sp_width'] == 0 ) {
              wc_add_notice( __('Width is a required field', 'rent_angry_cat'), 'error' );
              $passed = false;
           }
           if( isset( $_POST['custom_sp_height'] ) && (int)$_POST['custom_sp_height'] == 0 ) {
              wc_add_notice( __('Height is a required field', 'rent_angry_cat'), 'error' );
              $passed = false;
           }
           if( isset( $_POST['custom_sp_square'] ) && (int)$_POST['custom_sp_square'] < 1 ) {
              wc_add_notice( __('Square is a required field', 'xxx'), 'error' );
              $passed = false;
           }
           return $passed;
        }

        //===  Save custom input field value into cart item data
        add_filter( 'woocommerce_add_cart_item_data', 'njengah_product_add_on_cart_item_data', 10, 2 );
        function njengah_product_add_on_cart_item_data( $cart_item, $product_id ){
            if( isset( $_POST['custom_sp_width'] ) ) {
                $cart_item['custom_sp_width']  = (int)$_POST['custom_sp_width'];
            }
            if( isset( $_POST['custom_sp_height'] ) ) {
                $cart_item['custom_sp_height'] = (int)$_POST['custom_sp_height'];
            }
            if( isset( $_POST['custom_sp_square'] ) ) {
                $cart_item['custom_sp_square'] = (int)$_POST['custom_sp_square'];
            }
            return $cart_item;
        }

        //===RECALC CART-ITEM-PRICE===
        add_action('woocommerce_before_calculate_totals', 'woocommerce_custom_price_to_cart_item', 99);
        function woocommerce_custom_price_to_cart_item( $cart_object ){
            if( !WC()->session->__isset('reload_checkout') ){
                foreach($cart_object->cart_contents as $key => $value){
                    if(isset( $value['custom_price'])){
                        //for woocommerce version lower than 3
                        //$value['data']->price = $value["custom_price"];
                        //for woocommerce version +3
                        $value['data']->set_price($value["custom_price"]);
                    }
                }
            }
        } */

        //=== 7. Save product inputs value into cart item data (front)
        add_filter( 'woocommerce_add_cart_item_data', function ( $cart_item, $product_id ){
            //if( isset( $_POST['dscc_start'] ) ) {
                //$cart_item['dscc_start']  = (int)$_POST['dscc_start'];
            //}
            //if( isset( $_POST['dscc_end'] ) ) {
                //$cart_item['dscc_end'] = (int)$_POST['dscc_end'];
            //}
            return $cart_item;
        }, 10, 2 );

        //=== 8. Display inputs value to Cart item (front)
        add_filter( 'woocommerce_get_item_data', function ( $data, $cart_item ) {
            $prodID         = $cart_item['product_id'];
            $dscc_datearrea = self::get_dscc_datearrea( $prodID );
            $dscc_timearrea = self::get_dscc_timearrea( $prodID );
            $errorDates     = [];
            $errorTimes     = [];

            //===rate by days===
            if( isset( $cart_item['dscc_carorderdys_arr'] ) && is_array( $cart_item['dscc_carorderdys_arr'] ) ){
                foreach( $cart_item['dscc_carorderdys_arr'] as $ts ){
                    if( !in_array( $ts, $dscc_datearrea ) ){
                        $data[] = [
                            'name'  => __( 'Rent day', 'rent_angry_cat' ),
                            'value' => date('d.m', $ts ),
                        ];
                    }else{
                        $data[] = [
                            'name'  => __( 'Error day', 'rent_angry_cat' ),
                            'value' => date('d.m', $ts ),
                        ];
                        $errorDates[] = $ts;
                    }
                }
            }

            //===rate by hours===
            if( isset( $cart_item['dscc_carorderdates_arr'] ) && is_array( $cart_item['dscc_carorderdates_arr'] ) ){
                foreach( $cart_item['dscc_carorderdates_arr'] as $ts ){
                    if( !in_array( $ts, $dscc_timearrea ) ){
                        $data[] = [
                            'name'  => __('Rent hour', 'rent_angry_cat'),
                            'value' => date('d.m H:i', $ts),
                        ];
                    }else{
                        $data[] = [
                            'name'  => __('Error hour', 'rent_angry_cat'),
                            'value' => date('d.m H:i', $ts),
                        ];
                        $errorTimes[] = $ts;
                    }
                }
            }

            return $data;
        }, 10, 2 );

        //=== 9. Save inputs field value into order item meta (front)
        add_action( 'woocommerce_add_order_item_meta', function ( $item_id, $values ) {
            $prodID         = $values['product_id'];
            $dscc_datearrea = self::get_dscc_datearrea( $prodID );
            $dscc_timearrea = self::get_dscc_timearrea( $prodID );
            $errorDates     = [];
            $errorTimes     = [];

            //===rate by days===
            if( isset( $values['dscc_carorderdys_arr'] ) ){
                foreach( $values['dscc_carorderdys_arr'] as $ts ){
                    if( !in_array( $ts, $dscc_datearrea ) ){
                        wc_add_order_item_meta( $item_id, __('Rent day', 'rent_angry_cat'), date('d.m', $ts), false );
                    } else {
                        wc_add_order_item_meta( $item_id, __('Error day', 'rent_angry_cat'), date('d.m', $ts), false );
                        $errorDates[] = $ts;
                    }
                }

                $dscc_datearrea = array_merge( $dscc_datearrea, array_values( $values['dscc_carorderdys_arr'] ) );
                $dscc_datearrea = array_map( 'intval', $dscc_datearrea );
                update_post_meta( $prodID, 'dscc_datearrea', serialize( $dscc_datearrea ) );
            }

            //===rate by hours===
            if( isset( $values['dscc_carorderdates_arr'] ) ){
                foreach( $values['dscc_carorderdates_arr'] as $ts ){
                    if( !in_array( $ts, $dscc_timearrea ) ){
                        wc_add_order_item_meta( $item_id, __('Rent hour', 'rent_angry_cat'), date('d.m H:i', $ts), false );
                    } else {
                        wc_add_order_item_meta( $item_id, __('Error hour', 'rent_angry_cat'), date('d.m H:i', $ts), false );
                        $errorTimes[] = $ts;
                    }
                }

                $dscc_timearrea = array_merge( $dscc_timearrea, array_values( $values['dscc_carorderdates_arr'] ) );
                $dscc_timearrea = array_map( 'intval', $dscc_timearrea );
                update_post_meta( $prodID, 'dscc_timearrea', serialize( $dscc_timearrea ) );
            }

        }, 10, 2 );

        //=== 10. Display inputs field value into order table (front && admin)
        add_filter( 'woocommerce_order_item_product', function ( $cart_item, $order_item ){
            if( isset( $order_item['dscc_carorderdates_arr'] ) ){
                $cart_item['dscc_carorderdates_arr'] = $order_item['dscc_carorderdates_arr'];
            }
            if( isset( $order_item['dscc_carorderdys_arr'] ) ){
                $cart_item['dscc_carorderdys_arr'] = $order_item['dscc_carorderdys_arr'];
            }
            return $cart_item;
        }, 10, 2 );

        //=== 11. Display inputs value into order emails (front)
        add_filter( 'woocommerce_email_order_meta_fields', function ( $fields ) {
            $fields['dscc_carorderdates_arr'] = __('Rent',  'rent_angry_cat');
            $fields['dscc_carorderdys_arr']   = __('Rent day',  'rent_angry_cat');
            return $fields;
        } );

	}

    /* get prev timetable */
    public function dscc_itemcartable_prev(){
		$dscc_deltadays           = (int)( $_POST['dscc_deltadays']           ?? 0 );
        $dscc_filter_ts_start_dfs = (int)( $_POST['dscc_filter_ts_start_dfs'] ?? 0 );
        $dscc_carid               = (int)( $_POST['dscc_carid']               ?? 0 );
		
		$dscc_filter_ts_start_dfs = strtotime( ' -'.$dscc_deltadays.' day', $dscc_filter_ts_start_dfs );
		$dscc_filter_ts_start_dfs = ( $dscc_filter_ts_start_dfs < 0 ?0 :$dscc_filter_ts_start_dfs );
		
        $res = self::dscc_gettemplate_ttable( $dscc_carid, $dscc_filter_ts_start_dfs );
		
        echo json_encode($res);
        die();
    }

    /* get next timetable */
    public function dscc_itemcartable_next(){
		$dscc_deltadays           = (int)( $_POST['dscc_deltadays']           ?? 0 );
        $dscc_filter_ts_start_dfs = (int)( $_POST['dscc_filter_ts_start_dfs'] ?? 0 );
		$dscc_carid               = (int)( $_POST['dscc_carid']               ?? 0 );
		
		$dscc_filter_ts_start_dfs = strtotime( ' +'.$dscc_deltadays.' day', $dscc_filter_ts_start_dfs );
		$dscc_filter_ts_start_dfs = ( $dscc_filter_ts_start_dfs < 0 ?0 :$dscc_filter_ts_start_dfs );

        $res = self::dscc_gettemplate_ttable( $dscc_carid, $dscc_filter_ts_start_dfs );
		
        echo json_encode($res);
        die();
    }

    /* ADD to cart && goto checkout */
    public function dscc_itemcartable_order(){
        $res = ['err'=>1, 'msg'=>'System error:dscc_itemcartable_order!', 'redirecturl'=>wc_get_checkout_url()];

        $dscc_carorderdates_arr = ( $_POST['dscc_carorderdates_arr'] ?? '' );
        $dscc_carorderdys_arr   = ( $_POST['dscc_carorderdys_arr']   ?? '' );
        $dscc_carid             = (int)( $_POST['dscc_carid']        ?? 0 );
		
        $dscc_carorderdates_arr = explode( ',', $dscc_carorderdates_arr );
        $dscc_carorderdys_arr   = explode( ',', $dscc_carorderdys_arr );

        $dscc_carorderdates_arr = array_filter( $dscc_carorderdates_arr, 'strlen' );
        $dscc_carorderdys_arr   = array_filter( $dscc_carorderdys_arr, 'strlen' );

        $product = wc_get_product( $dscc_carid );
        if( !is_a( $product, 'WC_Product' ) ){
            $res['err'] = 1;
            $res['msg'] = __('Error! Product not exists.', 'rent_angry_cat');
            echo json_encode( $res );
            die();
        }

        $product_id     = $product->get_id();
        $prodTableStyle = self::getPostTTableStyle( $product_id );

        //===check error===
        if( ( $prodTableStyle=='tablefull' || $prodTableStyle=='tableshort' ) && $dscc_carorderdys_arr ){
            $res['err'] = 1;
            $res['msg'] = __('Error! Rate not dayly.', 'rent_angry_cat');
            echo json_encode( $res );
            die();
        }

        //===check error===
        if( $prodTableStyle=='dayly' && $dscc_carorderdates_arr ){
            $res['err'] = 1;
            $res['msg'] = __('Error! Rate not hourly.', 'rent_angry_cat');
            echo json_encode( $res );
            die();
        }

        //===if prod by hours===
        if( $prodTableStyle=='tablefull' || $prodTableStyle=='tableshort' ){
            $prodCount = count( $dscc_carorderdates_arr );
            WC()->cart->add_to_cart( $product_id, $prodCount, 0, [], ['dscc_carorderdates_arr'=>$dscc_carorderdates_arr] );
            WC()->cart->calculate_totals();
            WC()->cart->set_session();
            WC()->cart->maybe_set_cart_cookies();

            $res['err'] = 0;
            $res['msg'] = __('Hoursly Rent added to cart.', 'rent_angry_cat');
        }

        //===if prod by days===
        if( $prodTableStyle=='dayly' ){
            $prodCount = count( $dscc_carorderdys_arr );
            WC()->cart->add_to_cart( $product_id, ( $prodCount*24 ), 0, [], ['dscc_carorderdys_arr'=>$dscc_carorderdys_arr] );
            WC()->cart->calculate_totals();
            WC()->cart->set_session();
            WC()->cart->maybe_set_cart_cookies();

            $res['err'] = 0;
            $res['msg'] = __('Dayly Rent added to cart.', 'rent_angry_cat');
        }

        echo json_encode( $res );
        die();
    }

    public static function dscc_filter_start_ajax(){
        $dscc_filter_city         = (int)( $_POST['dscc_filter_city']         ?? 0 );
        $dscc_filter_ts_start_dfs = (int)( $_POST['dscc_filter_ts_start_dfs'] ?? 0 );
        $dscc_filter_ts_start_dfs = strtotime( date('d-m-Y', $dscc_filter_ts_start_dfs ) );
        $dscc_filter_paging       = (int)( $_POST['dscc_filter_paging']       ?? 0 );
        $SearchBy                 = (int)( $_POST['SearchBy']                 ?? 0 );
        $perpage                  = (int)( $_POST['perpage']                  ?? self::getSearchPostPerPage() );
        $dscc_filter_radius       = (int)( $_POST['dscc_filter_radius']       ?? 5 );
        $geoCrdLatitude           = (float)( $_POST['geoCrdLatitude']         ?? 0 );
        $geoCrdLongitude          = (float)( $_POST['geoCrdLongitude']        ?? 0 );
        $geoCrdAccuracy           = (float)( $_POST['geoCrdAccuracy']         ?? 0 );

        $res = self::dscc_gettemplate_list( $dscc_filter_city, $dscc_filter_ts_start_dfs, $dscc_filter_paging, $SearchBy, $perpage, $dscc_filter_radius, $geoCrdLatitude, $geoCrdLongitude, $geoCrdAccuracy );

        echo json_encode( $res );
        die();
    }

    public function ajaxRegisterActions() {
        //===AJAX NEXT-TTABLE===
        add_action( 'wp_ajax_dscc_itemcartable_next',         ['\Rent_Angry_Cat_ns\Rent_Angry_Cat', 'dscc_itemcartable_next'] );
        add_action( 'wp_ajax_nopriv_dscc_itemcartable_next',  ['\Rent_Angry_Cat_ns\Rent_Angry_Cat', 'dscc_itemcartable_next'] );

        //===AJAX PREV-TTABLE===
        add_action( 'wp_ajax_dscc_itemcartable_prev',         ['\Rent_Angry_Cat_ns\Rent_Angry_Cat', 'dscc_itemcartable_prev'] );
        add_action( 'wp_ajax_nopriv_dscc_itemcartable_prev',  ['\Rent_Angry_Cat_ns\Rent_Angry_Cat', 'dscc_itemcartable_prev'] );

        //===AJAX ADD-TTABLE-TO-CART===
        add_action( 'wp_ajax_dscc_itemcartable_order',        ['\Rent_Angry_Cat_ns\Rent_Angry_Cat', 'dscc_itemcartable_order'] );
        add_action( 'wp_ajax_nopriv_dscc_itemcartable_order', ['\Rent_Angry_Cat_ns\Rent_Angry_Cat', 'dscc_itemcartable_order'] );

        //===AJAX SEARCH===
        add_action('wp_ajax_dscc_filter_start',               ['\Rent_Angry_Cat_ns\Rent_Angry_Cat', 'dscc_filter_start_ajax'] );
        add_action('wp_ajax_nopriv_dscc_filter_start',        ['\Rent_Angry_Cat_ns\Rent_Angry_Cat', 'dscc_filter_start_ajax'] );
    }

    public static function getCityList( $args=[] ){
        $arr = [
            'taxonomy'      => [ 'rentacatcat' ],
            'orderby'       => 'id',
            'order'         => 'ASC',
            'hide_empty'    => false,
            'object_ids'    => null,
            'include'       => [],
            'exclude'       => [],
            'exclude_tree'  => [],
            'number'        => '',
            'fields'        => 'all',
            'count'         => true,
            'slug'          => '',
            'parent'        => '',
            'hierarchical'  => true,
            'child_of'      => 0,
            'get'           => '',
            'name__like'    => '',
            'pad_counts'    => false,
            'offset'        => '',
            'search'        => '',
            'cache_domain'  => 'core',
            'name'          => '',
            'childless'     => false,
            'update_term_meta_cache' => true,
            'meta_query'    => '',
        ];
        $arr = wp_parse_args( $args, $arr );
        return get_terms( $arr );
    }

    public static function dscc_gettemplatepage( $perpage=3 ){
        $perpage     = (int)$perpage; /* posts per page */
        $resTemplate = '';

        /* Search template in theme */
        $themeTemplate  = get_template_directory().'/'.self::getPluginName() . '/dscc_gettemplatepage.php';
        if( file_exists( $themeTemplate ) ){
            require_once( $themeTemplate );
        } else {
            /* Search template in plugin */
            $pluginTemplate = self::crp_templates_php_path() . '/dscc_gettemplatepage.php';
            if(file_exists( $pluginTemplate )){
                require_once( $pluginTemplate );
            }
        }
        return $resTemplate;
    }

    public static function dscc_gettemplate_list( $dscc_filter_city=0, $dscc_filter_ts_start_dfs=0, $dscc_filter_paging=1, $SearchBy=0, $perpage=0, $dscc_filter_radius=0, $geoCrdLatitude=0, $geoCrdLongitude=0, $geoCrdAccuracy=0 ){
        GLOBAL $wpdb;
        $resItems                 = '';
        $dscc_filter_city         = (int)$dscc_filter_city;
        $dscc_filter_ts_start_dfs = (int)$dscc_filter_ts_start_dfs;
        $dscc_filter_paging       = (int)$dscc_filter_paging;
        $SearchBy                 = (int)$SearchBy;
        $dscc_filter_radius       = ( (int)$dscc_filter_radius>0 ?(int)$dscc_filter_radius :self::getSearchPostPerPage() );
        $geoCrdAccuracy           = (float)$geoCrdAccuracy;
        $geoCrdLongitude          = (float)$geoCrdLongitude;
        $geoCrdLatitude           = (float)$geoCrdLatitude;
        $perpage                  = (int)( (int)$perpage>0 ?(int)$perpage :self::getSearchPostPerPage() );
        $listID                   = [];

        /* Search by City */
        if( $SearchBy==0 ){
            $args = [   'post_type'      => 'product',
                        'posts_per_page' => -1,
                        'fields'         => 'ids',
                        'tax_query'      => [
                            'relation' => 'AND',
                            [
                                [
                                    'taxonomy' => 'product_type',
                                    'field'    => 'slug',
                                    'terms'    => 'rentacat',
                                ],
                                [
                                    'taxonomy' => 'rentacatcat',
                                    'field'    => 'term_id',
                                    'terms'    => [ $dscc_filter_city ],
                                ]
                            ]
                        ],
                    ];
            $query  = new \WP_Query( $args );
            $listID = $query->posts;
            $dscc_filter_allpostcount = count( $listID );
            $query  = null;
        }

        if( $SearchBy==1 ){
            /* Search by My radius */
            if( $geoCrdLatitude>0 && $geoCrdLongitude>0 ){
                $my_lt   = $geoCrdLatitude;
                $my_ln   = $geoCrdLongitude;
            }else{
                $resItems .= __('You location not detected!', 'rent_angry_cat');
                return $resItems;
            }

            $sql = "SELECT  sd.ID
                    FROM (
                        SELECT  psts.ID        as ID,
                                um1.meta_value as usr_zip_lt,
                                um2.meta_value as usr_zip_ln,
                                (
                                    (
                                        (
                                            acos(
                                                    sin(( $my_lt * pi() / 180)) 
                                                    * sin(( um1.meta_value * pi() / 180)) + cos(( $my_lt * pi() /180 ))
                                                    * cos(( um1.meta_value * pi() / 180)) * cos((( $my_ln - um2.meta_value) * pi()/180))
                                                )
                                        ) * 180/pi()
                                    ) * 60 * 1.609344
                                ) as distance
                        FROM $wpdb->posts psts                    
                        LEFT JOIN $wpdb->postmeta um1 ON psts.ID=um1.post_id && um1.meta_key = 'dscc_latitude'
                        LEFT JOIN $wpdb->postmeta um2 ON psts.ID=um2.post_id && um2.meta_key = 'dscc_longitude'
                        WHERE psts.post_type = 'product' && psts.post_status = 'publish'
                    ) sd
                WHERE distance <= ".$dscc_filter_radius;
            $posts = $wpdb->get_results( $sql, ARRAY_N );
            foreach( $posts as $post ){
                $listID[] = $post[0];
            }
            $dscc_filter_allpostcount = count( $listID );
            $posts = null;
        }

        $dscc_filter_allpagecount = ceil( $dscc_filter_allpostcount / $perpage );
        array_push( $listID, 0 );
        $args = [   'post_type'      => 'product',
                    'posts_per_page' => $perpage,
                    'paged'          => $dscc_filter_paging,
                    'post__in'       => $listID,
                ];
        query_posts( $args );
        /* Search template in theme */
        $themeTemplate  = get_template_directory().'/'.self::getPluginName() . '/dscc_items.php';
        if( file_exists( $themeTemplate ) ){
            require_once( $themeTemplate );
        } else {
            /* Search template in plugin */
            $pluginTemplate = self::crp_templates_php_path() . '/dscc_items.php';
            if (file_exists($pluginTemplate)) {
                require_once($pluginTemplate);
            }
        }
        wp_reset_query();

        return $resItems;
    }

    public static function dscc_gettemplate_ttable( $postID=0, $tsStart=0 ){
        $postID    = (int)$postID;
		$tsStart   = (int)$tsStart; // for dscc_ttable.php
        $resTTable = '';

        /* Search template in theme */
        $themeTemplate  = get_template_directory().'/'.self::getPluginName() . '/dscc_ttable.php';
        if( file_exists( $themeTemplate ) ){
            require( $themeTemplate );
        } else {
            /* Search template in plugin */
            $pluginTemplate = self::crp_templates_php_path() . '/dscc_ttable.php';
            if (file_exists( $pluginTemplate )) {
                require( $pluginTemplate );
            }
        }

        return $resTTable;
    }

    public static function get_dscc_datearrea( $prodID=0 ){
        $prodID = (int)$prodID;
        $res    = @unserialize( get_post_meta( $prodID, 'dscc_datearrea', true ) );
        if( !is_array( $res ) ){
            $res = [];
        }
        return $res;
    }

    public static function get_dscc_timearrea( $prodID=0 ){
        $prodID = (int)$prodID;
        $res    = @unserialize( get_post_meta( $prodID, 'dscc_timearrea', true ) );
        if( !is_array( $res ) ){
            $res = [];
        }
        return $res;
    }

    public static function getCatLT( $postID=0 ){
        return (float)get_post_meta( (int)$postID, 'dscc_latitude', true );
    }

    public static function getCatLN( $postID=0 ){
        return (float)get_post_meta( (int)$postID, 'dscc_longitude', true );
    }

    public static function getPostTimeFromRate( $postID=0 ){
        return (int)get_post_meta( (int)$postID, 'dscc_time_from_rate', true );
    }

    public static function getPostTimeToRate( $postID=0 ){
        return (int)get_post_meta( (int)$postID, 'dscc_time_to_rate', true );
    }

    public static function getSearchDefRadius(){
        return (int)get_option('dscc_admin_filter_radius');
    }

    public static function getSearchFilterType(){
        return (int)get_option('dscc_filter_type_show');
    }

    public static function getSearchPostPerPage(){
        return (int)get_option('dscc_admin_listitem_count');
    }

    public static function getPostTTableStyle( $postID=0 ){
        return get_post_meta( $postID, 'dscc_tsbletype', true );
    }

    public static function getGoogleKey(){
        return get_option('dscc_admin_googleipikey');
    }

    public function addShortCodes(){
        add_shortcode('rent_angry_cat', ['\Rent_Angry_Cat_ns\Rent_Angry_Cat', 'rent_angry_cat_shortcode'] );
    }

    public static function rent_angry_cat_shortcode( $atts, $content, $tag ){
        if(isset( $atts['perpage'] )){
            $perpage = (int)$atts['perpage'];
        }else{
            $perpage = self::getSearchPostPerPage();
        }
        echo self::dscc_gettemplatepage( $perpage );
    }

    public function thumbnailsRegister() {
        add_image_size('thumbnail_250x200',   250,  200,  true);
        add_image_size('thumbnail_1920x1080', 1920, 1080, true);
    }

    public function scriptscss() {
        add_action('wp_enqueue_scripts', function (){
            if( !is_admin() ){

                /* google */
				wp_enqueue_script('gm',          'https://maps.googleapis.com/maps/api/js?key='.self::getGoogleKey().'&libraries=places&language=en',     [], '', true);
				
				/* lightgallery http://sachinchoolur.github.io/lightgallery.js */
                wp_enqueue_style( 'lightgallery',  plugins_url('', __FILE__).'/public/lightgallery/css/lightgallery.css',                     []);
                wp_enqueue_script('lightgallery',  plugins_url('', __FILE__).'/public/lightgallery/js/lightgallery.js',                       [], '', true);

                /*if( (int)get_option('dscc_admin_lb_pager')==1 ) {
                    wp_enqueue_script('lg-pager',      plugins_url('', __FILE__).'/public/lightgallery/js/lg-pager.js',                       [], '', true);
                }*/
               /* if( (int)get_option('dscc_admin_lb_autoplay')==1 ) {
                    wp_enqueue_script('lg-autoplay',   plugins_url('', __FILE__).'/public/lightgallery/js/lg-autoplay.js',                    [], '', true);
                }*/
                if( (int)get_option('dscc_admin_lb_zoom')==1 ) {
                    wp_enqueue_script('lg-zoom',       plugins_url('', __FILE__).'/public/lightgallery/js/lg-zoom.js',                        [], '', true);
                }
                if( (int)get_option('dscc_admin_lb_fullscreen')==1 ) {
                    wp_enqueue_script('lg-fullscreen', plugins_url('', __FILE__).'/public/lightgallery/js/lg-fullscreen.js',                  [], '', true);
                }
                /*if( (int)get_option('dscc_admin_lb_hash')==1 ) {
                    wp_enqueue_script('lg-hash',       plugins_url('', __FILE__).'/public/lightgallery/js/lg-hash.js',                        [], '', true);
                }*/
                /*if( (int)get_option('dscc_admin_lb_share')==1 ) {
                    wp_enqueue_script('lg-share',      plugins_url('', __FILE__).'/public/lightgallery/js/lg-share.js',                       [], '', true);
                }*/
                if( (int)get_option('dscc_admin_lb_thumbnail')==1 ) {
                    wp_enqueue_script('lg-thumbnail',  plugins_url('', __FILE__).'/public/lightgallery/js/lg-thumbnail.js',                   [], '', true);
                }
                /*if( (int)get_option('dscc_admin_lb_rotate')==1 ) {
                    wp_enqueue_script('lg-rotate',     plugins_url('', __FILE__).'/public/lightgallery/js/lg-rotate.js',                      [], '', true);
                }*/

				/* multiselect */
                wp_enqueue_script('multiselect',   plugins_url('', __FILE__).'/public/multiselect/multiselect.min.js',                    ['jquery'], '', true);
                wp_enqueue_style( 'multiselect',   plugins_url('', __FILE__).'/public/multiselect/multiselect.css',                       []);
				
				/* datepicker */
                wp_enqueue_script('airpicker',     plugins_url('', __FILE__).'/public/datepicker/datepicker.min.js',                      [], '', true);
                wp_enqueue_style( 'airpicker',     plugins_url('', __FILE__).'/public/datepicker/datepicker.min.css',                     []);
				
				/* plugin */
                wp_enqueue_style( 'pluginpub',     plugins_url('', __FILE__).'/public/plugin/rent_angry_cat-public.css',                  []);
                wp_enqueue_script('pluginpub',     plugins_url('', __FILE__).'/public/plugin/rent_angry_cat-public.js',                   ['jquery'], '', true);
                wp_localize_script( 'pluginpub',   'rpc_plugin_params', [ 'WPajaxURL'=>admin_url('admin-ajax.php') ] );
            }
        });
    }

}
