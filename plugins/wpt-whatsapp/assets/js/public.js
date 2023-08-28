jQuery(function($) {
	
	'use strict';
	
	
	/**
	 * jQuery Tiny Pub/Sub
	 * https://github.com/cowboy/jquery-tiny-pubsub
	 *
	 * Copyright (c) 2013 "Cowboy" Ben Alman
	 * Licensed under the MIT license.
	 **********************************************************************/
	var o = $({});
	$.subscribe = function() {o.on.apply(o, arguments);};
	$.unsubscribe = function() {o.off.apply(o, arguments);};
	$.publish = function() {o.trigger.apply(o, arguments);};
	
	/**
	 * Save the main building block of DOM elements; for the 
	 * sake of succinctness
	 **********************************************************************/
	var DOM = (function ( dom ) {
		
		var dom = dom || {}
			, wptwaFlag = $( '.wptwa-flag' ) 
			;
		
		dom.body = $('body:eq(0)');
		dom.isSmallScreen = ( window.getComputedStyle( wptwaFlag.get(0), ':after' ).content == '"small"' || window.getComputedStyle( wptwaFlag.get(0), ':after' ).content == 'small' ) ? true : false;
		dom.isMobileScreen = ( window.getComputedStyle( wptwaFlag.get(0), ':after' ).content == '"mobile"' || window.getComputedStyle( wptwaFlag.get(0), ':after' ).content == 'mobile' ) ? true : false;
		dom.isLargeScreen = ( window.getComputedStyle( wptwaFlag.get(0), ':after' ).content == '"desktop"' || window.getComputedStyle( wptwaFlag.get(0), ':after' ).content == 'desktop' ) ? true : false;
		
		return dom;
		
	}( DOM ) );
	
	/**
	 * Simple cookie utilities
	 **********************************************************************/
	var COOKIES = (function ( cookies ) {
		
		var cookies = cookies || {};
		
		cookies.setItem = function ( name, value, durationInDays ) {
			var d = new Date();
			d.setTime( d.getTime() + ( durationInDays * 24 * 60 * 60 * 1000 ) );
			var expires = 'expires=' + d.toUTCString();
			document.cookie = name + '=' + value + ';' + expires + ';path=/';
		};
		
		cookies.getItem = function ( name ) {
			var name = name + '=',
				decodedCookie = decodeURIComponent( document.cookie ),
				ca = decodedCookie.split(';');
				
			for ( var i = 0; i < ca.length; i++ ) {
				
				var c = ca[ i ];
				
				while ( c.charAt( 0 ) == ' ' ) {
					c = c.substring(1);
				}
				
				if ( c.indexOf( name ) == 0 ) {
					return c.substring( name.length, c.length );
				}
			}
			return null;
		}
		
		cookies.deleteItem = function ( name ) {
			document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
		};
		
		return cookies;
		
	}( COOKIES ) );
	
	/**
	* Set widget height on mobile
	**********************************************************************/
	(function () {
		
		$.subscribe('wptwa-widget-ready', function () {
			var container = $( '.wptwa-container' )
				, box = container.find( '.wptwa-box' )
				, description = container.find( '.wptwa-description' )
				, toggle = container.find( '.wptwa-toggle' )
				, peopleList = container.find( '.wptwa-people' )
				;
			
			peopleList.css({
				maxHeight: $( window ).height() - ( description.outerHeight() + parseInt( box.css( 'bottom' ) ) + 20 )
			});
			
		});
		
	}());
	
	/**
	* Centralize the process of hide/show of the box.
	**********************************************************************/
	(function () {
		var neverBeenResized = true;
		$.subscribe('wptwa-widget-ready', function () {
			var wptwa = DOM.body.find( '.wptwa-container' ),
				toggleBox = function ( e ) {
					wptwa.toggleClass( 'wptwa-show' );
					
					if ( ! COOKIES.getItem( 'wptwa' ) ) {
						COOKIES.setItem( 'wptwa', 'toggled', 1 );
					}
				};
			$.subscribe('wptwa-toggle-box', toggleBox);
		});
		
	}());
	
	/**
	* Show and hide the box.
	**********************************************************************/
	(function () {
		
		$.subscribe('wptwa-widget-ready', function () {
			
			var wptwaFlag = DOM.body.find( '.wptwa-flag' ),
				wptwa = DOM.body.find( '.wptwa-container' ),
				delayTime = parseInt( wptwa.data( 'delay-time' ) ),
				inactiveTime = parseInt( wptwa.data( 'inactive-time' ) ),
				scrollLength = parseInt( wptwa.data( 'scroll-length' ) ),
				autoDisplayOnMobile = wptwa.data( 'auto-display-on-mobile' ),
				box = wptwa.find( '.wptwa-box' ),
				toggle = wptwa.find( '.wptwa-toggle' ),
				close = wptwa.find( '.wptwa-close' ),
				bottomClose = wptwa.find( '.wptwa-mobile-close' ),
				autoShow
				;
			
			if ( ! wptwa.length || ! wptwaFlag.length ) {
				return;
			}
			
			/* Set the box's display to block. Its visibility still hidden though. */
			box.addClass( 'wptwa-js-ready' );
			
			/* Toggle box on toggle's (or close's) click */
			toggle.add( close ).add( bottomClose ).on( 'click', function () {
				$.publish('wptwa-toggle-box');
			} );
			
			/* We're done here if the screen is small. */
			if ( DOM.isSmallScreen && wptwa.is( '.wptwa-disable-auto-display-on-small-screen' ) ) {
				return;
			}
			
			/* 	Show box after a delay time on page load and only if it has 
				not been shown before.
				*/
			if ( delayTime > 0 ) {
				autoShow = setTimeout( function () {
					if ( ! box.is( '.wptwa-show' ) && ! COOKIES.getItem( 'wptwa' ) ) {
						$.publish('wptwa-toggle-box');
					}
				}, delayTime * 1000 );
			}
			
			/* 	Trigger after inactivity and only if it has not been shown 
				before.
				*/
			var cb,
				executed = false,
				events = 'mousemove mousedown mouseup onkeydown onkeyup focus scroll',
				showAfterInactivity = function () {
					clearTimeout( cb );
					if ( ! executed ) {
						cb = setTimeout(function () {
							if ( ! COOKIES.getItem( 'wptwa' ) && ! box.is( '.wptwa-show' ) ) {
								$.publish('wptwa-toggle-box');
							}
							$( document ).off( events, showAfterInactivity );
						}, inactiveTime * 1000 );
					}
				};
			
			if ( inactiveTime > 0 ) {
				$( document ).on( events, showAfterInactivity );
			}
			
			/* 	Trigger after scrolling.
				Accessing DOM on-scroll is a bad idea. Let's execute the function 
				every half a second during/post scroll instead.
				*/
			var percentage = Math.abs( scrollLength ) / 100,
				scrolling,
				timing = true,
				scrollHandler = function() {
					
					scrolling = true;
					
					if ( timing ) {
						
						setTimeout(function () {
							if ( $( window ).scrollTop() >= ( $( document ).height() - $( window ).height() ) * percentage ){
								if ( ! COOKIES.getItem( 'wptwa' ) && ! box.is( '.wptwa-show' ) ) {
									$.publish('wptwa-toggle-box');
								}
								$( window ).off( 'scroll', scrollHandler );
							}
							timing = true;
							scrolling = false;
						}, 500 );
						
						if ( scrolling ) {
							timing = false;
						}
					}
				}
				;
			
			if ( scrollLength > 0 ) {
				$( window ).on( 'scroll', scrollHandler );
			}
		});
		
	}());
	
	/**
	* If avatar is not provided or provided but error, add a hint to 
	* .wptwa-face so we can show a default image.
	**********************************************************************/
	(function () {
		
		$.subscribe('wptwa-widget-ready', function () {
			DOM.body.find( '.wptwa-container .wptwa-face' ).each(function () {
				var el = $( this ),
					img = el.find( 'img' ),
					noImage = true
					;
				
				if ( img.length ) {
					var url = img.attr( 'src' ),
						tester = new Image();
					tester.src = url;
					
					tester.onerror = function () {
						el.addClass( 'no-image' );
					};
					
				}
				else {
					el.addClass( 'no-image' );
				}
				
			});
		});
		
	}());
	
	/**
	* If we're on desktop, use web.whatsapp.com instead. But if not,
	* remove the target attribute because it will simply open the app.
	**********************************************************************/
	(function () {
		
		var alterURL = function () {
			var wptwaAccounts = DOM.body.find( 'a.wptwa-account' ),
				wptwaFlag = DOM.body.find( '.wptwa-flag' )
				;
			
			if ( ! wptwaFlag.length ) {
				return;
			}
			
			/* Change URL to web.whatsapp.com if the user is using a desktop. */
			if ( DOM.isLargeScreen ) {
				
				wptwaAccounts.each(function () {
					var el = $( this ),
						number = el.data( 'number' ),
						text = el.data( 'auto-text' )
						;
					
					if ( '' === number ) {
						return true;
					}
					el.attr( 'href', 'https://web.whatsapp.com/send?phone=' + number + '&text=' + text );
				});
				
			}
			else {
				if ( window.location === window.parent.location ) {
				  wptwaAccounts.removeAttr( 'target' );
				}
			}
		};
		
		alterURL();
		
		$.subscribe('wptwa-widget-ready', function () {
			alterURL();
		});
		
		$.subscribe('wptwa-woo-ready', function () {
			alterURL();
		});
		
		$.subscribe('wptwa-button-ready', function () {
			alterURL();
		});
		
	}());
	
	/**
	* Get widget via AJAX
	**********************************************************************/
	(function () {
		
		var wptwasw = DOM.body.find( '#wptwa-show-widget' );
		if ( wptwasw.length < 1 ) {
			return;
		}
		
		var data = {
			'action': 'wptwa_display_widget',
			'when': Date.now(),
			'current-language': wptwasw.data( 'current-language' ),
			'ids': wptwasw.data( 'ids' ),
			'page-title': wptwasw.data( 'page-title' ),
			'page-url': wptwasw.data( 'page-url' )
		};
		
		$.post( ajax_object.ajax_url, data, function( response ) {
			
			if ( 'no-show' === response ) {
				return;
			}
			
			var hideOnLargeScreen = true
				, hideOnSmallScreen = true
				;
				
			$( response ).find( '.wptwa-account' ).each(function () {
				var el = $( this );
				
				if ( DOM.isLargeScreen && ! el.is( '.wptwa-hide-on-large-screen' ) ) {
					hideOnLargeScreen = false;
				}
				
				if ( ( DOM.isSmallScreen || DOM.isMobileScreen ) && ! el.is( '.wptwa-hide-on-small-screen' ) ) {
					hideOnSmallScreen = false;
				}
			});
			
			if ( ( DOM.isLargeScreen && ! hideOnLargeScreen ) || ( ( DOM.isMobileScreen || DOM.isSmallScreen ) && ! hideOnSmallScreen ) ) {
				$( response ).appendTo( DOM.body );
			}
			
			setTimeout( function () {
				$.publish('wptwa-widget-ready');
			}, 100 );
		});
		
	}());
	
	/**
	* Send event to Google Analytics
	**********************************************************************/
	(function () {
		
		/* Remove gdpr container if user already gave her consent. */
		$.subscribe('wptwa-widget-ready', function () {
			if ( COOKIES.getItem( 'consent' ) ) {
				DOM.body.find( '.wptwa-gdpr' ).remove();
			}
		});
		
		DOM.body.on( 'click', 'a.wptwa-account', function ( e ) {
			
			var el = $( this )
				, number = parseInt( el.data( 'number' ) )
				, gaLabel = el.data( 'ga-label' )
				, parent = el.parents( '.wptwa-people' )
				, gdpr = parent.find( '.wptwa-gdpr' )
				, confirmation = gdpr.find( '.wptwa-confirmation' )
				, consent = gdpr.find( '#wptwa-consent' )
				, to
				;
			
			/* If consent needed and is not given yet. */
			if ( consent.length && ! consent.is( ':checked' ) ) {
				confirmation.addClass( 'wptwa-blink' );
				
				confirmation.one( 'animationend webkitAnimationEnd oAnimationEnd MSAnimationEnd', function () {
					confirmation.removeClass( 'wptwa-blink' );
				} );
				
				e.preventDefault();
				return false;
			}
			
			/* If the script reaches here, the user gave her consent. */
			if ( el.closest( '.wptwa-container' ).length ) {
				COOKIES.setItem( 'consent', 'acquired', 1 );
				gdpr.remove();
			}
			
			/* Click analytics. Try to send data to Google or Facebook. */
			try {
				gtag( 'event', 'Button Clicked', {
					'event_category': 'WhatsApp Click to Chat',
					'event_label': 'WhatsApp Account: ' + gaLabel
				} );	
			}
			catch ( error ) {
				/* It seems like the site doesn't have Google Analytics installed. */
				window.console && console.log( 'Catched from WhatsApp Click to Chat: ' + error.message );
			}
			
			try {
				ga( 'send', 'event', 'WhatsApp Click to Chat', 'Button Clicked', 'WhatsApp Account: ' + gaLabel );
			}
			catch ( error ) {
				/* It seems like the site doesn't have Google Analytics installed. */
				window.console && console.log( 'Catched from WhatsApp Click to Chat: ' + error.message );
			}
			
			try {
				_gaq.push([ '_trackEvent', 'WhatsApp Click to Chat', 'Button Clicked', 'WhatsApp Account: ' + gaLabel ]);
			}
			catch ( error ) {
				/* It seems like the site doesn't have Google Analytics installed. */
				window.console && console.log( 'Catched from WhatsApp Click to Chat: ' + error.message );
			}
			
			try {
				dataLayer.push({
					'event': 'customEvent',
					'eventCategory': 'WhatsApp Click to Chat',
					'eventAction': 'Button Clicked',
					'eventLabel': 'WhatsApp Account: ' + gaLabel
				});
			}
			catch ( error ) {
				/* It seems like the site doesn't have Google Analytics installed. */
				window.console && console.log( 'Catched from WhatsApp Click to Chat: ' + error.message );
			}
			
			var pixeled = false;
			try {
				/* Facebook pixel yields warning when we initiate fbq multiple times. */
				if ( ! pixeled ) {
					fbq( 'trackCustom', 'ClickToChat', {
						event: 'Chat started',
						account: gaLabel
					});
					pixeled = true;
				}
			}
			catch ( error ) {
				/* It seems like the site doesn't have Google Analytics installed. */
				window.console && console.log( 'Catched from WhatsApp Click to Chat: ' + error.message );
			}
			
		} );
		
	}());
	
	/**
	* Button uses ajax to fetch data so we can have an accurate display
	* even when the page is cached.
	**********************************************************************/
	(function () {
		
		var ids = []
			, pageURL = ''
			, pageTitle = ''
			;
		
		DOM.body.find( '.wptwa-button-container' ).each(function () {
			var el = $( this )
				, id = el.data( 'target-id' )
				;
			
			pageURL = el.data( 'page-url' );
			pageTitle = el.data( 'page-title' );
				
			ids.push( id );
		});
		
		if ( ids.length < 1 ) {
			return;
		}
		
		var data = {
			'action': 'wptwa_display_buttons',
			'when': Date.now(),
			'ids': ids.join( ',' ),
			'page-title': pageTitle,
			'page-url': pageURL
		};
		
		$.post( ajax_object.ajax_url, data, function( response ) {
			
			if ( response === 'none' ) {
				return false;
			}
			
			var data = JSON.parse( response );
			
			for ( var key in data ) {
				if ( data.hasOwnProperty( key ) ) {
					var val = data[ key ];
					DOM.body.find( '.wptwa-button-container#wptwa-button-' + val.id ).append( val.content );
					
					/* 	Remove this item from the ids. Those which are not removed means 
						that it doesn't have the button to show. */
					var index = ids.indexOf( val.id );
					if ( index > -1 ) {
						ids.splice( index, 1 );
					}
				}
			}
			
			/* 	If there's an offline account which doesn't have an offline description, 
				then remove the container. */
			for ( var i = 0, limit = ids.length; i < limit; i++ ) {
				DOM.body.find( '.wptwa-button-container#wptwa-button-' + ids[i] ).remove();
			}
			
			setTimeout( function () {
				$.publish('wptwa-button-ready');
			}, 100 );
			
		});
		
	}());
	
	/**
	* WooCommerce buttons are loaded using AJAX and treated differently 
	* from the other buttons because it has randomize and limit features.
	**********************************************************************/
	(function () {
		
		var ids = ''
			, pageURL = ''
			, pageTitle = ''
			;
		
		DOM.body.find( '.wptwa-wc-buttons-container' ).each(function () {
			var el = $( this );
			
			pageURL = el.data( 'page-url' );
			pageTitle = el.data( 'page-title' );
			ids = el.data( 'ids' );
		});
		
		if ( ids.length < 1 ) {
			return;
		}
		
		var data = {
			'action': 'wptwa_display_buttons',
			'when': Date.now(),
			'ids': ids,
			'type' : 'woocommerce_button',
			'page-title': pageTitle,
			'page-url': pageURL
		};
		
		$.post( ajax_object.ajax_url, data, function( response ) {
			
			if ( response === 'none' ) {
				return false;
			}
			
			var data = JSON.parse( response );
			
			for ( var key in data ) {
				if ( data.hasOwnProperty( key ) ) {
					var val = data[ key ];
					DOM.body.find( '.wptwa-wc-buttons-container' ).append( $( '<p>' + val.content + '</p>' ) );
				}
			}
			
			setTimeout( function () {
				$.publish('wptwa-woo-ready');
			}, 100 );
			
		});
		
	}());
	
	/**
	* 
	**********************************************************************/
	(function () {
		
		
		
	}());
	
});