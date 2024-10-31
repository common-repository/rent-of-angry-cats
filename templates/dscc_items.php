<?php
$perpage;                  /* posts per page */
$dscc_filter_city;         /* ID city */
$SearchBy;                 /* search by 0-city 1-myradius */
$dscc_filter_ts_start_dfs; /* start timestamp */
$dscc_filter_paging;       /* paging int */
$dscc_filter_type_show;    /* filter type 0-city+radius 1-city 2-radius */
$dscc_filter_allpostcount; /* All finded posts count */
$dscc_filter_allpagecount; /* All finded page count */
$geoCrdAccuracy;           /* Browser geolocation Accuracy */
$geoCrdLongitude;          /* Browser geolocation Longitude */
$geoCrdLatitude;           /* Browser geolocation Latitude */
$dscc_filter_radius;       /* Browser geolocation Radius */

$resItems .= '<div class="dscc_filter_res1">
                    <h3>'.
                        ( $SearchBy==0 ?esc_html(__('City search result', 'rent_angry_cat')) :'').
                        ( $SearchBy==1 ?esc_html(__('My radius result',   'rent_angry_cat')) :'').
                        ' ('.(int)$dscc_filter_allpostcount.')
                    </h3>';

                    $dc_search_map_arr = [];
                    if( have_posts() ){
                        while( have_posts() ){
                            the_post();
                            $thumbURL            = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail_250x200' );
                            $thumbURL            = ( isset( $thumbURL[0] ) ?$thumbURL[0] :wc_placeholder_img_src('woocommerce_thumbnail') );
                            $thumbAlt            = esc_attr( get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) );
                            $thumbAlt            = ( $thumbAlt ?$thumbAlt :esc_attr( get_the_title() ) );
                            $dscc_latitude       = \Rent_Angry_Cat_ns\Rent_Angry_Cat::getCatLT( get_the_id() );
                            $dscc_longitude      = \Rent_Angry_Cat_ns\Rent_Angry_Cat::getCatLN( get_the_id() );
                            $dc_search_map_arr[] = ['lat'=>(float)$dscc_latitude, 'lng'=>(float)$dscc_longitude, 'title'=>get_the_title()];
							$itemID              = 'dynamic_'.get_the_id();
                            $thumbList           = [];
                            $thumbListArr        = [];
							$product             = wc_get_product( get_the_id() );
							$attachment_ids      = $product->get_gallery_image_ids();
							
							foreach( $attachment_ids as $attachment_id ){
								$imgGListItemImg = wp_get_attachment_image_src( $attachment_id, 'thumbnail_1920x1080' );
								$imgGListItemThm = wp_get_attachment_image_src( $attachment_id, 'thumbnail_250x200' );
								$thumbList[] = '{
                                                    "src":     "'.$imgGListItemImg[0].'",
                                                    "thumb":   "'.$imgGListItemThm[0].'",
                                                    "subHtml": "'.get_the_title().'"
                                                }';
                                $thumbListArr[] = [ "src"     => $imgGListItemImg[0],
                                                    "thumb"   => $imgGListItemThm[0],
                                                    "subHtml" => get_the_title(),
                                                  ];
							}
							
							
                            $resItems .=   '<div class="dscc_item_box">
                                                <div class="dscc_itemcar">
                                                   <h4>'.get_the_title().'</h4>
                                                   <img id            = "'.esc_attr( $itemID ).'"
                                                        class         = "dscc_itemcar_sliderclicker"
                                                        src           = "'.esc_url( $thumbURL ).'"
                                                        width         = "250"
                                                        height        = "200"
                                                        alt           = "'.esc_attr( $thumbAlt ).'"
                                                        data-thumbarr = \''.json_encode( $thumbListArr ).'\'
                                                   >
                                                </div>
                                                <div id="car_'.get_the_id().'" class="dscc_itemcartable">
                                                   '.\Rent_Angry_Cat_ns\Rent_Angry_Cat::dscc_gettemplate_ttable( get_the_id(), $dscc_filter_ts_start_dfs ).'
                                                </div>
                                            </div>';
                        }
                    } else {
                        $resItems .= __('Not found.', 'rent_angry_cat');
                    }

    $resItems .= '<div class="dscc_filter_page_box">
                    <div id="dscc_filter_page_prev" class="dscc_filter_page_nextprevnoactive dscc_filter_page_prev">&LT;</div>
                    <div id="dscc_filter_page_oll"  class="dscc_filter_page_oll" data-allpagecount="'.esc_attr( $dscc_filter_allpagecount ).'" data-perpage="'.esc_attr( $perpage ).'">'.esc_html( $dscc_filter_paging.'/'.$dscc_filter_allpagecount ).'</div>
                    <div id="dscc_filter_page_next" class="dscc_filter_page_nextprevnoactive dscc_filter_page_next">&GT;</div>
                  </div>
              </div>
              <div id="dscc_filter_res2" class="dscc_filter_res2">
                 <div id="dscc_filter_map" data-dc_search_map_arr=\''.esc_attr( json_encode( $dc_search_map_arr ) ).'\' data-dc_search_map_myloc="'.esc_attr( __('My location!', 'rent_angry_cat') ).'"></div>
                 <div id="dscc_filter_map_title">'.
                    ( $geoCrdAccuracy && $geoCrdLatitude && $geoCrdLongitude && $SearchBy===1
                        ?__('radius:', 'rent_angry_cat').$dscc_filter_radius.'kM; '.__('lat:', 'rent_angry_cat').$geoCrdLatitude.'; '.__('lng:', 'rent_angry_cat').$geoCrdLongitude.'; '.__('accuracy:', 'rent_angry_cat').$geoCrdAccuracy.';'
                        :''
                    ).'
                </div>
              </div>'; ?>