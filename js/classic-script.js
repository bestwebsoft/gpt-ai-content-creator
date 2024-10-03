( function( $ ){
	$( document ).ready( function(){
		$( '.gptcntntcrtr-text-button' ).click( function() {
			if ( 0 == gptcntntcrtr_vars.key_exist )	{
				alert( gptcntntcrtr_vars.empty_key_error );
				return;
			}
			var postEditedTitle = $( '#title' ).val();

			if ( ! postEditedTitle ) {
				alert( gptcntntcrtr_vars.empty_title_error );
				return;
			}

			var dataArray = {
				'action'      : 'gptcntntcrtr_ajax_callback',
				'is_gutenberg': false,
				'post_title'  : postEditedTitle,
				'security'    : gptcntntcrtr_vars.ajax_nonce
			};
			$( '.gptcntntcrtr-text-button svg' ).removeClass( 'hidden' );

			$.post(
				ajaxurl,
				dataArray,
				function( response ) {					
					var responseContent = JSON.parse( response );

					$( '.gptcntntcrtr-text-button svg' ).addClass( 'hidden' );

					if ( '' == responseContent.error ) {
						var win = window.dialogArguments || opener || parent || top;
						win.send_to_editor( responseContent.content );
					} else {
						alert( responseContent.error );
					}
				}
			);
		} );
	});
})(jQuery);