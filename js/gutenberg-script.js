/**
 * Gutenberg script
 *
 * @since V1.3.6
 *
 * Notice: "Translation Saving Mode" plugin option is ignored.
 */

(function( i18n, element, components, compose, blocks, data ) {
	(function( $ ) {

		$( window ).on(
			'load',
			function() {
				var gptcntntcrtrIcon = element.createElement(
					'svg',
					{ width: 24, height: 24, className: 'hidden' },
					element.createElement(
						'path',
						{
							opacity: "0.5",
							fill: "#fff",
							d: "M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z"
						}
					)
				);
				var promptText = '';
				var postId = 0;

				/* Adding switcher to the Gutenberg editor */
				var createButton = function() {
					return element.createElement(
						components.Button,
						{
							icon     : gptcntntcrtrIcon,
							isPrimary: true,
							onClick  : generateContent,
							label    : gptcntntcrtr_vars.add_content_text,
							className: 'gptcntntcrtr-button gptcntntcrtr-text-button'
						},
						gptcntntcrtr_vars.add_content_text
					);
				};

				var generateContent = function( newLang ) {
					if ( 0 == gptcntntcrtr_vars.key_exist )	{
						alert( gptcntntcrtr_vars.empty_key_error );
						return;
					}
					var getPost = data.select( 'core/editor' ),
					editPost    = data.dispatch( 'core/editor' ),
					dataArray;
					var postEditedTitle = getPost.getEditedPostAttribute( 'title' );

					if ( ! postEditedTitle ) {
						alert( gptcntntcrtr_vars.empty_title_error );
						return;
					}

					dataArray = {
						'action'      : 'gptcntntcrtr_ajax_callback',
						'is_gutenberg': true,
						'post_title'  : postEditedTitle,
						'security'    : gptcntntcrtr_vars.ajax_nonce
					};
					$( '.gptcntntcrtr-text-button svg' ).removeClass( 'hidden' );

					$.post(
						ajaxurl,
						dataArray,
						function( response ) {
							const { parse }    = blocks;
							const { dispatch } = data;

							var responseContent = JSON.parse( response );

							$( '.gptcntntcrtr-text-button svg' ).addClass( 'hidden' );

							if ( '' == responseContent.error ) {
								dispatch( 'core/editor' ).insertBlocks( parse( responseContent.content ) );
							} else {
								alert( responseContent.error );
							}
						}
					);
				}

				if ( ! document.body.classList.contains( 'widgets-php' ) ) {
					setTimeout(
						function() {
							e = $( '.edit-post-header__toolbar' );
							if ( e ) {
								var t = document.createElement( 'div' );
								if ( element.createRoot ) {
									e.append( t ), element.createRoot( t ).render( element.createElement( createButton, null ) )
								} else {
									e.append( t ), Object( element.render )( element.createElement( createButton , null ), t )
								}
							}
						},
						500
					);
				}
			}
		);
	})( jQuery );
} )(
	window.wp.i18n,
	window.wp.element,
	window.wp.components,
	window.wp.compose,
	window.wp.blocks,
	window.wp.data
);
