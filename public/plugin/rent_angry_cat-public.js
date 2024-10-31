(function( $ ) {

	document.addEventListener('DOMContentLoaded', function(event){
		const dscc_filter_box          = document.getElementById('dscc_filter_box');
		const dscc_filter_city         = document.getElementById('dscc_filter_city');
		const dscc_filter_radius       = document.getElementById('dscc_filter_radius');
		const dscc_filter_paging       = document.getElementById('dscc_filter_paging');
		const dscc_filter_preloader    = document.getElementById('dscc_filter_preloader');
		const dscc_filter_searchtype   = document.getElementById('dscc_filter_searchtype');
		const dscc_filter_ts_start_dfs = document.getElementById('dscc_filter_ts_start_dfs');

		/* Start page */
		if( dscc_filter_box !== null ){
			initDTPicker1();
			mapSetMarkers();
			dscc_paging_styled();
			dscc_ttable_selldt_starter();
			navigator.geolocation.getCurrentPosition(geoSuccess, geoError, geoOptions);
		}

		/* DATA-TIME-PICKER STARTER */
		function initDTPicker1(){
			$('#dscc_filter_ts_start').datepicker({minDate:new Date(), timepicker:false, altField:'#dscc_filter_ts_start_dfs', language:dtpsLangTransObj, autoClose:true,
				onSelect: function onSelect(fd, date) { startSearch(); }
			});
		}

		/* START STILED-CHECKBOXES */
		function dscc_ttable_selldt_starter(){
			document.querySelectorAll('.dscc_ttable_selldt').forEach( function(el){
				$( '#'+el.getAttribute('id') ).multiSelect();
			});
		}

		/* getting my location */
		var geoOptions = {
			enableHighAccuracy: true,
			timeout:            5000,
			maximumAge:         60000,
		};
		var geoCrdLatitude  = '';
		var geoCrdLongitude = '';
		var geoCrdAccuracy  = '';
		var geoErrCode      = '';
		var geoErrMessage   = '';

		function geoSuccess(pos) {
			geoCrdLatitude  = pos.coords.latitude;
			geoCrdLongitude = pos.coords.longitude;
			geoCrdAccuracy  = pos.coords.accuracy;
			geoCrdAccuracy  = Math.round( parseFloat( geoCrdAccuracy ) );
			//console.log('===Your current position is===');
			//console.log(`Latitude :   ${geoCrdLatitude}`);
			//console.log(`Longitude:   ${geoCrdLongitude}`);
		 	//console.log(`More or less ${geoCrdAccuracy} meters.`);
		}

		function geoError(err) {
			geoErrCode    = err.code;
			geoErrMessage = err.message;
			console.warn(`ERROR(${geoErrCode}): ${geoErrMessage}`);
		}
		/* //getting my location */

		/* SEARCH */
		document.addEventListener('change', function(e) {
			dscc_filter_paging.value = 1;

			/* search by city */
			if( e.target.id == 'dscc_filter_city' ) {
				dscc_filter_searchtype.value = 0; // sat last search type
				startSearch();
			}

			/* search by my radius */
			if( e.target.id == 'dscc_filter_radius' ) {
				dscc_filter_searchtype.value = 1; // sat last search type
				startSearch();
			}
		});

		/* PAGING++ */
		document.addEventListener('click',function(e){
			if( e.target.classList.contains('dscc_filter_page_next') ) {
				dscc_filter_paging.value = parseInt( dscc_filter_paging.value ) + 1;
				startSearch();
			}
		});

		/* PAGING-- */
		document.addEventListener('click',function(e){
			if( e.target.classList.contains('dscc_filter_page_prev') ) {
				dscc_filter_paging.value = parseInt( dscc_filter_paging.value ) - 1;
				startSearch();
			}
		});

		/* paging styled */
		function dscc_paging_styled(){
			var dscc_filter_page_oll  =  document.getElementById('dscc_filter_page_oll');
			if( dscc_filter_page_oll !== null ){
				dscc_filter_page_oll = parseInt( dscc_filter_page_oll.getAttribute('data-allpagecount' ) );
				var dscc_filter_page_prev = document.getElementById('dscc_filter_page_prev');
				var dscc_filter_page_next = document.getElementById('dscc_filter_page_next');
				if( dscc_filter_paging.value>1 ){
					dscc_filter_page_prev.classList.remove('dscc_filter_page_nextprevnoactive');
				}else{
					dscc_filter_page_prev.classList.add('dscc_filter_page_nextprevnoactive');
				}
				if( dscc_filter_paging.value<dscc_filter_page_oll ){
					dscc_filter_page_next.classList.remove('dscc_filter_page_nextprevnoactive');
				}else{
					dscc_filter_page_next.classList.add('dscc_filter_page_nextprevnoactive');
				}
			}
		}

		/* start search by CITY */
		function startSearch(){
			const SearchBy                     = parseInt( dscc_filter_searchtype.value );
			const dscc_filter_ts_start_dfs_val = parseInt( dscc_filter_ts_start_dfs.value ) / 1000;
			const perpage                      = parseInt( document.getElementById('dscc_filter_page_oll').getAttribute('data-perpage') );
			dscc_filter_box.innerHTML          = dscc_filter_preloader.innerHTML;

			fetch(rpc_plugin_params.WPajaxURL, {
				method:      'POST',
				credentials: 'same-origin',
				headers: {
					'Content-Type':  'application/x-www-form-urlencoded',
					'Cache-Control': 'no-cache',
				},
				body: new URLSearchParams({
					action:                   'dscc_filter_start',
					dscc_filter_ts_start_dfs: dscc_filter_ts_start_dfs_val,
					dscc_filter_city:         dscc_filter_city.value,
					dscc_filter_paging:       dscc_filter_paging.value,
					SearchBy:                 SearchBy,
					perpage:                  perpage,
					dscc_filter_radius:       dscc_filter_radius.value,
					geoCrdLatitude:           geoCrdLatitude,
					geoCrdLongitude:          geoCrdLongitude,
					geoCrdAccuracy:           geoCrdAccuracy,
				})
			})
				.then( response => response.json() )
				.then( response => {
					dscc_filter_box.innerHTML = response;
					dscc_paging_styled();
					mapSetMarkers( SearchBy );
					dscc_ttable_selldt_starter();
				} );
		};

		/* load map && set markers && ( circle if location ) */
		function mapSetMarkers(){
			var dc_search_map_arr = document.getElementById('dscc_filter_map');

			if( dc_search_map_arr  != null ){
				var dc_search_map_myloc = dc_search_map_arr.getAttribute('data-dc_search_map_myloc');
				dc_search_map_arr       = dc_search_map_arr.getAttribute('data-dc_search_map_arr');
				dc_search_map_arr       = JSON.parse(dc_search_map_arr);

				//===MAP INIT===
				var bounds = new google.maps.LatLngBounds();
				var map    = new google.maps.Map(document.getElementById('dscc_filter_map'), {});

				if( dscc_filter_searchtype.value==1 ){
					var radius = parseInt(dscc_filter_radius.value)*1000;
					/* marker myself pos */
					const myLatLng = { lat:geoCrdLatitude, lng:geoCrdLongitude };
                    new google.maps.Marker( { position:myLatLng, map, title:dc_search_map_myloc } );
					bounds.extend(myLatLng);

					/* circle around myself */
					var cityCircle = new google.maps.Circle({
                        strokeColor:   '#FF0000',
                        strokeOpacity: 0.8,
                        strokeWeight:  1,
                        fillColor:     '#FF0000',
                        fillOpacity:   0.15,
                        map,
                        center:        myLatLng,
                        radius:        radius,
                    });
				}

				//===markers===
				dc_search_map_arr.forEach(function(element) {
					var myLatLng1 = { lat: element.lat, lng: element.lng };
					new google.maps.Marker({ position:myLatLng1, map, title:element.title });
					bounds.extend(myLatLng1);
				});
				map.fitBounds(bounds);
			}
		}
		
		/* load prev week */
		document.addEventListener('click', function(e){
			if( e.target.classList.contains('dscc_itemcartable_prev') ) {
				const dscc_filter_ts_start_dfs = parseInt( e.target.getAttribute('data-tsstart') );
				const dscc_carid               = parseInt( e.target.getAttribute('data-carid') );
				const dscc_deltadays           = parseInt( e.target.getAttribute('data-deltadays') );
				const dscc_lodboxid            = document.getElementById( e.target.getAttribute('data-id') );
				
				dscc_lodboxid.innerHTML = dscc_filter_preloader.innerHTML;
				fetch(rpc_plugin_params.WPajaxURL, {
					method: 'POST',
					credentials: 'same-origin',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
						'Cache-Control': 'no-cache',
					},
					body: new URLSearchParams({
						action: 'dscc_itemcartable_prev',
						dscc_filter_ts_start_dfs: dscc_filter_ts_start_dfs,
						dscc_carid:               dscc_carid,
						dscc_deltadays:           dscc_deltadays,
					})
				})
				.then(response => response.json())
				.then(response => {
					dscc_lodboxid.innerHTML = response;
					dscc_ttable_selldt_starter();
				});
			}
		});
		
		/* load next week */
		document.addEventListener('click', function(e){
			if( e.target.classList.contains('dscc_itemcartable_next') ){
				const dscc_filter_ts_start_dfs = parseInt( e.target.getAttribute('data-tsstart') );
				const dscc_carid               = parseInt( e.target.getAttribute('data-carid') );
				const dscc_deltadays           = parseInt( e.target.getAttribute('data-deltadays') );
				const dscc_lodboxid            = document.getElementById( e.target.getAttribute('data-id') );
				
				dscc_lodboxid.innerHTML = dscc_filter_preloader.innerHTML;
				fetch(rpc_plugin_params.WPajaxURL, {
					method:      'POST',
					credentials: 'same-origin',
					headers: {
						'Content-Type':  'application/x-www-form-urlencoded',
						'Cache-Control': 'no-cache',
					},
					body: new URLSearchParams({
						action:                   'dscc_itemcartable_next',
						dscc_filter_ts_start_dfs: dscc_filter_ts_start_dfs,
						dscc_carid:               dscc_carid,
						dscc_deltadays:           dscc_deltadays,
					})
				})
				.then( response => response.json() )
				.then( response => {
					dscc_lodboxid.innerHTML = response;
					dscc_ttable_selldt_starter();
				} );
			}
		});

		/* add product to cart && goto checkout */
		document.addEventListener('click',function(e){
			if( e.target.classList.contains('dscc_itemcartable_order') ) {
				const dscc_carid   = parseInt( e.target.getAttribute('data-carid') );
				const dscc_styleid = e.target.getAttribute('data-styleid'); // rate table type
				const parentboxid  = e.target.getAttribute('data-parentboxid');
				const errorDate    = e.target.getAttribute('data-error_date');

				const dscc_carorderdates_arr = [];
				const dscc_carorderdys_arr = [];

				//===per hours===
				$('#' + parentboxid + ' input[type=checkbox]:checked').each(function (index) {
					if (!$(this).hasClass('car_order_fullday') && !$(this).hasClass('car_order_allfullday')) {
						dscc_carorderdates_arr.push($(this).val());
					}
				});

				//===per days===
				$('#' + parentboxid + ' input[type=checkbox]:checked').each(function (index) {
					if ($(this).hasClass('car_order_allfullday')) {
						dscc_carorderdys_arr.push($(this).val());
					}
				});

				if (dscc_carorderdates_arr.length == 0 && dscc_carorderdys_arr.length == 0) {
					alert(errorDate);
					return;
				}

				fetch(rpc_plugin_params.WPajaxURL, {
					method: 'POST',
					credentials: 'same-origin',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
						'Cache-Control': 'no-cache',
					},
					body: new URLSearchParams({
						action: 'dscc_itemcartable_order',
						dscc_carorderdates_arr: dscc_carorderdates_arr,
						dscc_carorderdys_arr: dscc_carorderdys_arr,
						dscc_carid: dscc_carid,
					})
				})
					.then(response => response.json())
					.then(response => {
						//alert(response.msg);
						if (response.err == 0) {
							top.location.href = response.redirecturl;
						}
					});
			}
		});

		/* select all hours to day */
		document.addEventListener('click',function(e){
			if( e.target.classList.contains('car_order_fullday') ){
				var selectClass = e.target.getAttribute('data-class_for_autoselect');
				var ynchecked   = e.target.checked;

				document.querySelectorAll( '.'+selectClass ).forEach( function(el) {
					if( el.disabled == false ){
						el.checked = ynchecked;
					}
				});
			}
		});

		/* Start slider */
		document.addEventListener('click',function(e){
			if( e.target.classList.contains('dscc_itemcar_sliderclicker') ){
				var thumbarr = e.target.getAttribute('data-thumbarr');
				thumbarr = JSON.parse(thumbarr);
				if(thumbarr.length){
					var elID = document.getElementById( e.target.id );
					var inlineGallery = lightGallery( elID, {
						dynamic:    true,
						dynamicEl:  thumbarr,
						slideDelay: 400,
					});
				}
			}
		});


	});
})( jQuery );

