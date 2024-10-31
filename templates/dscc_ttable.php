<?php
$postID;                   /* Post ID */
$tsStart;                  /* start timestamp */
$dscc_timearrea            = \Rent_Angry_Cat_ns\Rent_Angry_Cat::get_dscc_timearrea( $postID ); /* post rated hours */
$dscc_datearrea            = \Rent_Angry_Cat_ns\Rent_Angry_Cat::get_dscc_datearrea( $postID ); /* post rated days */
$dscc_time_from_rate       = \Rent_Angry_Cat_ns\Rent_Angry_Cat::getPostTimeFromRate( $postID ); /* rate start time */
$dscc_time_to_rate         = \Rent_Angry_Cat_ns\Rent_Angry_Cat::getPostTimeToRate( $postID ); /* rate finish time */
$tableStyle                = \Rent_Angry_Cat_ns\Rent_Angry_Cat::getPostTTableStyle( $postID ); /* rate table style */
$todayTS                   = strtotime('today'); /* timestamp today */

$parentID                  = 'dscc_daybox_id_'.$postID;
$img                       = wp_get_attachment_image_src( get_post_thumbnail_id( $postID ), 'thumbnail_250x200' );

$resTTable .= '<a href             = "#uID"
				 class            = "dscc_itemcartable_prev"
				 data-carid       = "'.esc_attr( $postID ).'"
				 data-tsstart     = "'.esc_attr( $tsStart ).'"
				 data-id          = "'.esc_attr( 'car_'.$postID ).'"
				 data-deltadays   = "7"
			  >
				&LT;'.esc_html( __('Prev', 'rent_angry_cat') ).'
			  </a>
			  <a href             = "#uID"
				 class            = "dscc_itemcartable_next"
				 data-carid       = "'.esc_attr( $postID ).'" 
				 data-tsstart     = "'.esc_attr( $tsStart ).'"
				 data-id          = "'.esc_attr( 'car_'.$postID ).'"
				 data-deltadays   = "7"
			  >
				'.esc_html( __('Next', 'rent_angry_cat') ).'&GT;
			  </a>
              <a href             = "#uID"
			     class            = "dscc_itemcartable_order" 
                 data-styleid     = "'.esc_attr( $tableStyle ).'"
                 data-carid       = "'.esc_attr( $postID ).'"
                 data-parentboxid = "'.esc_attr( $parentID ).'"
                 data-error_date  = "'.esc_attr( __('Select Date/Time!', 'rent_angry_cat') ).'"
              >
				'.esc_html( __('Order', 'rent_angry_cat') ).'
			  </a>
              <table  id="'.$parentID.'" border="1">
                <tr>';
                    for($i=0;$i<9;$i++){
                        $setday     = strtotime(' +'.$i.' day',  $tsStart);
                        $freeDay    = !in_array( $setday, $dscc_datearrea );
                        $featured   = ( $setday >= $todayTS ?true :false );

                        $resTTable .= '<td class="'.( $freeDay && $featured ?'dscc_bg_yes' :'dscc_bg_none' ).'">
                                          <b>'.date('d.m', strtotime(' +'.$i.' day', $tsStart )).'</b>';

                        if( $tableStyle=='tablefull' ){
                            $disable = ( $freeDay && $featured ?'' :'disabled' );
                            $resTTable .= '<input type="checkbox" class="car_order_fullday" data-class_for_autoselect="car_'.esc_attr( $postID ).'_order_day_'.esc_attr( $setday ).'" '.esc_attr( $disable ).'>';
                        }

                        if( $tableStyle=='tableshort' ){
                            $resTTable .=    '<select id="car_'.$postID.'_order_day_'.$setday.'" class="dscc_ttable_selldt" multiple>';
                            for( $ii=$dscc_time_from_rate;$ii<$dscc_time_to_rate;$ii++ ){
                                $sethour    = strtotime(' +'.$ii.'  hour', $setday);
                                $freeHour   = !in_array( $sethour, $dscc_timearrea );
                                $disabled   = ( $freeDay && $freeHour && $featured ?'' :'disabled' );
                                $resTTable .=    '<option value="'.esc_attr( $sethour ).'" '.$disabled.'>'.esc_html( ( $ii<10 ?0 :'' ).$ii.'.00' ).'</option>';
                            }
                            $resTTable .=    '</select>';
                        }

                        if( $tableStyle=='dayly' ){
                            $resTTable .= '<br /><input type="checkbox" class="car_order_allfullday" value="'.esc_attr( $setday ).'" '.( in_array( $setday, $dscc_datearrea ) || !$featured ?'disabled' :'' ).'>';
                        }

                        $resTTable .= '</td>';
                    }
$resTTable .=  '</tr>';

if( $tableStyle=='tablefull' ) {
    for ($i = $dscc_time_from_rate; $i < $dscc_time_to_rate; $i++) {
        $resTTable .= '<tr>';
        for ($ii = 0; $ii < 9; $ii++) {
            $setday     = strtotime(' +' . $ii . ' day', $tsStart);
            $sethour    = strtotime(' +' . $i . '  hour', $setday);
            $ynDay      = in_array( $setday, $dscc_datearrea );
            $ynHour     = in_array( $sethour, $dscc_timearrea );
            $featured   = ( $setday >= $todayTS ?true :false );
            $freeHour   = ( $ynDay || $ynHour ?false :true );
            $resTTable .= '<td class="' . ( $freeHour && $featured ?'dscc_bg_yes' :'dscc_bg_none' ) . '">
                                ' . ($i<10 ?0 :'') . $i . '.00 ' . '<input type="checkbox" class="car_order_' . esc_attr( $postID ) . ' car_' . esc_attr( $postID ) . '_order_day_' . esc_attr( $setday ) . '" value="' . esc_attr( $sethour ) . '" '.($freeHour && $featured ?'' :'disabled').' >
                           </td>';
        }
        $resTTable .= '</tr>';
    }
}

$resTTable .= '</table>
               <div class="car_shortcontent">'.get_the_excerpt( $postID ).'</div>';