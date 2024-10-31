<?php
$perpage;                  /* posts per page */
$dscc_filter_city         = 0; /* ID city */
$SearchBy                 = 0; /* search by 0-city 1-myradius */
$dscc_filter_ts_start_dfs = (int)strtotime('today'); /* start timestamp */
$dscc_filter_paging       = 1; /* paging int */
$dscc_filter_radius       = \Rent_Angry_Cat_ns\Rent_Angry_Cat::getSearchDefRadius(); /* Radius search */
$dscc_filter_type_show    = \Rent_Angry_Cat_ns\Rent_Angry_Cat::getSearchFilterType(); /* filter type 0-city+radius 1-city 2-radius */


$items = '';
$terms = \Rent_Angry_Cat_ns\Rent_Angry_Cat::getCityList( ['orderby'=>'id','order'=>'ASC',] );
foreach( $terms as $term ){
    if( $dscc_filter_city==0 ){
        $dscc_filter_city = $term->term_id;
    }
    $items .= '<option value="'.esc_attr( $term->term_id ).'">'.esc_html( $term->name.' ('.$term->count.')' ).'</option>';
}

GLOBAL $wp_locale;
$wdays  = $wp_locale->weekday;
$wdaysA = $wp_locale->weekday_abbrev;
$wdaysI = $wp_locale->weekday_initial;
$month  = $wp_locale->month;
$monthA = $wp_locale->month_abbrev;

$resTemplate = '<div class="dscc_filter_box">
                    <table class="dscc_filter_box_table">
                        <tr>
                            '.($dscc_filter_type_show==0 || $dscc_filter_type_show==1
                                ?'<td>
                                      '.esc_html( __('Search by City', 'rent_angry_cat') ).':
                                      <br />
                                      <select id="dscc_filter_city" class="dscc_filter_box_input">
                                          '.$items.'
                                      </select>
                                  </td>'
                                :'<input type="hidden" id="dscc_filter_city" value="0">' ).'
                            '.($dscc_filter_type_show==0 || $dscc_filter_type_show==2
                                ?'<td>
                                      '.esc_html( __('Search by Radius', 'rent_angry_cat') ).':
                                      <br />
                                      <input type="number" id="dscc_filter_radius"       value="'.esc_attr( $dscc_filter_radius ).'" step="1" min="1" max="100" class="dscc_filter_box_input">
                                  </td>'
                                :'<input type="hidden" id="dscc_filter_radius" value="0">' ).'
                            <td>
                                '.esc_html( __('Select date', 'rent_angry_cat') ).':
                                <br />
                                <input type="text"   id="dscc_filter_ts_start"     value="'.esc_attr( date('d.m.Y', $dscc_filter_ts_start_dfs ) ).'" readonly="readonly" class="dscc_filter_box_input">
                                <input type="hidden" id="dscc_filter_ts_start_dfs" value="'.esc_attr( $dscc_filter_ts_start_dfs*1000 ).'">
                                <script>
                                    var dtpsLangTransObj = {
                                                                days:        [\''.$wdays[0].'\', \''.$wdays[1].'\', \''.$wdays[2].'\', \''.$wdays[3].'\', \''.$wdays[4].'\', \''.$wdays[5].'\', \''.$wdays[6].'\'],
                                                                daysShort:   [\''.$wdaysA[$wdays[0]].'\', \''.$wdaysA[$wdays[1]].'\', \''.$wdaysA[$wdays[2]].'\', \''.$wdaysA[$wdays[3]].'\', \''.$wdaysA[$wdays[4]].'\', \''.$wdaysA[$wdays[5]].'\', \''.$wdaysA[$wdays[6]].'\'],
                                                                daysMin:     [\''.$wdaysI[$wdays[0]].'\', \''.$wdaysI[$wdays[1]].'\', \''.$wdaysI[$wdays[2]].'\', \''.$wdaysI[$wdays[3]].'\', \''.$wdaysI[$wdays[4]].'\', \''.$wdaysI[$wdays[5]].'\', \''.$wdaysI[$wdays[6]].'\'],
                                                                months:      [\''.$month['01'].'\',\''.$month['02'].'\',\''.$month['03'].'\',\''.$month['04'].'\',\''.$month['05'].'\',\''.$month['06'].'\', \''.$month['07'].'\',\''.$month['08'].'\',\''.$month['09'].'\',\''.$month['10'].'\',\''.$month['11'].'\',\''.$month['12'].'\'],
                                                                monthsShort: [\''.$monthA[$month['01']].'\', \''.$monthA[$month['02']].'\', \''.$monthA[$month['03']].'\', \''.$monthA[$month['04']].'\', \''.$monthA[$month['05']].'\', \''.$monthA[$month['06']].'\', \''.$monthA[$month['07']].'\', \''.$monthA[$month['08']].'\', \''.$monthA[$month['09']].'\', \''.$monthA[$month['10']].'\', \''.$monthA[$month['11']].'\', \''.$monthA[$month['12']].'\'],
                                                                today:       \''.__('Today', 'rent_angry_cat').'\',
                                                                clear:       \''.__('Clear', 'rent_angry_cat').'\',
                                                                dateFormat:  \'dd/mm/yyyy\',
                                                                timeFormat:  \'hh:ii aa\',
                                                                firstDay:    1,
                                                            };
                                </script>
                                <input type="hidden" id="dscc_filter_paging"       value="'.esc_attr( $dscc_filter_paging ).'">
                                <input type="hidden" id="dscc_filter_searchtype"   value="0">
                            </td>
                        </tr>
                    </table>
                 </div>
                 <div id="dscc_filter_preloader">
                    <!--PRELOADER-->
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
                     <path fill="#000000" d="M31.6,3.5C5.9,13.6-6.6,42.7,3.5,68.4c10.1,25.7,39.2,38.3,64.9,28.1l-3.1-7.9c-21.3,8.4-45.4-2-53.8-23.3 c-8.4-21.3,2-45.4,23.3-53.8L31.6,3.5z">
                          <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="2s" from="0 50 50" to="360 50 50" repeatCount="indefinite"></animateTransform>
                      </path>
                     <path fill="#000000" d="M42.3,39.6c5.7-4.3,13.9-3.1,18.1,2.7c4.3,5.7,3.1,13.9-2.7,18.1l4.1,5.5c8.8-6.5,10.6-19,4.1-27.7 c-6.5-8.8-19-10.6-27.7-4.1L42.3,39.6z">
                          <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s" from="0 50 50" to="-360 50 50" repeatCount="indefinite"></animateTransform>
                      </path>
                     <path fill="#000000" d="M82,35.7C74.1,18,53.4,10.1,35.7,18S10.1,46.6,18,64.3l7.6-3.4c-6-13.5,0-29.3,13.5-35.3s29.3,0,35.3,13.5 L82,35.7z">
                          <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="2s" from="0 50 50" to="360 50 50" repeatCount="indefinite"></animateTransform>
                      </path>
                    </svg>
                    <!--//PRELOADER-->
                 </div>
                 <div id="dscc_filter_box" class="dscc_container">
                    '.\Rent_Angry_Cat_ns\Rent_Angry_Cat::dscc_gettemplate_list( $dscc_filter_city, $dscc_filter_ts_start_dfs, $dscc_filter_paging, $SearchBy, $perpage ).'
                 </div>';
?>