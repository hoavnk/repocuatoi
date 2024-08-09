jQuery( document ).ready( function( $ ) {
	'use strict';

	$( 'body' ).on( 'tabsbeforeactivate', '.wpb_tour_tabs_wrapper', function( e, ui ) {
		ui.oldTab.removeClass( 'active' );
		ui.newTab.addClass( 'active' );
	} );

	$( '.compose-mode .vc_controls-bc .vc_control-btn-append' ).each( function() {
		$( this ).insertAfter( $( this ).closest( '.vc_controls' ).find( '.vc_control-btn-prepend' ) );
	} );

	if ( window.parent.vc && window.parent.vc.events ) {
		window.parent.vc.events.on( 'shortcodes:add', function( model ) {
			var parent_id = model.attributes.parent_id;
			if ( !parent_id ) {
				return;
			}
			var parent = window.parent.vc.shortcodes.get( parent_id );
			if ( parent && 'porto_carousel' == parent.attributes.shortcode ) {
				var $obj = $( '[data-model-id="' + parent.attributes.id + '"]' ).children( '.owl-carousel' );
				if ( $obj.length ) {
					$obj.removeData( '__carousel' );
					$obj.trigger( 'destroy.owl.carousel' );
				}
			}
		} );

		window.parent.vc.events.on( 'shortcodeView:ready', function( e ) {
			var shortcode = e.attributes.shortcode;
			if ( 'porto_scroll_progress' == shortcode ) {
				if ( $( 'script#porto-scroll-progress-js' ).length ) {
					$( document.body ).trigger( 'porto_init_scroll_progress', [e.view.$el] );
				} else {
					$( document.createElement( 'script' ) ).attr( 'id', 'porto-scroll-progress-js' ).appendTo( 'body' ).attr( 'src', js_porto_vars.ajax_loader_url.replace( '/images/ajax-loader@2x.gif', '/js/libs/porto-scroll-progress.min.js' ) ).on( 'load', function() {
						$( document.body ).trigger( 'porto_init_scroll_progress', [e.view.$el] );
					} );
				}
			} else if ( 'vc_row' == shortcode && e.attributes.params ) {
				if ( e.attributes.params.particles_effect && e.attributes.params.particles_img ) {
					e.view.$el.find( '.particles-wrapper:not(:first-child)' ).remove();

					if ( typeof particlesJS == 'undefined' ) {
						$( document.createElement( 'script' ) ).attr( 'id', 'particles-js' ).appendTo( 'body' ).attr( 'src', porto_wpb_vars.shortcodes_url + 'assets/js/particles.min.js' ).on( 'load', function() {
							$( document.createElement( 'script' ) ).attr( 'id', 'porto-particles-loader-js' ).appendTo( 'body' ).attr( 'src', porto_wpb_vars.shortcodes_url + 'assets/js/porto-particles-loader.min.js' ).on( 'load', function() {
								$( document.body ).trigger( 'porto_init_particles_effect', [e.view.$el] );
							} );
						} );
					} else {
						$( document.body ).trigger( 'porto_init_particles_effect', [e.view.$el] );
					}
				} else {
					e.view.$el.find( '.particles-wrapper' ).remove();
				}
			} else if ( 'porto_cursor_effect' == shortcode && e.attributes.params && e.id ) {
				var $shortcode_cls_obj = e.view.$el.find( '.shortcode-class' );
				if ( typeof window.porto_cursor_effects == 'undefined' ) {
					window.porto_cursor_effects = [];
				}
				window.porto_cursor_effects.forEach( function( i, index ) {
					if ( i.model_id && e.id == i.model_id ) {
						window.porto_cursor_effects.splice( index, 1 );
						return false;
					}
				} );

				var inner_icon = e.attributes.params.inner_icon;
				if ( 'simpleline' == e.attributes.params.icon_type ) {
					inner_icon = e.attributes.params.icon_simpleline;
				} else if ( 'porto' == e.attributes.params.icon_type ) {
					inner_icon = e.attributes.params.icon_porto;
				}
				window.porto_cursor_effects.push( { model_id: e.id, id: $shortcode_cls_obj.length ? $shortcode_cls_obj.text() : '', selector: e.attributes.params.selector ? e.attributes.params.selector.replace( '&gt;', '>' ) : '', hover_effect: e.attributes.params.hover_effect || 'plus', icon: inner_icon, cursor_w: e.attributes.params.cursor_w || '' } );
				$shortcode_cls_obj.remove();

				var ins = $( document.body ).data( '__cursorEffect' );
				if ( ins ) {
					ins.destroy();
					$( document.body ).removeData( '__cursorEffect' );

					if ( window.porto_cursor_effects.length && $.fn.themePluginCursorEffect ) {
						$( document.body ).themePluginCursorEffect();
					}
				}
			} else if ( 'vc_pie' == shortcode && e.attributes.params && e.attributes.params.type && 'custom' == e.attributes.params.type ) {
				porto_init( e.view.$el );
			} else if ( 'porto_countdown' == shortcode && e.attributes.params ) {
				var $obj = e.view.$el;
				var $countdown_div = $obj.find( '.porto_countdown-div' );
				if ( $countdown_div.length ) {
					let cdate = new Date(), sdate = cdate.getTime() + parseFloat( $countdown_div.data( 'time-zone' ) ) * 3600 * 1000;
					sdate = new Date( sdate ).toISOString().replace( /(.*)(20[0-9]{2}-[0-9]{2}-[0-9]{2})T([0-9]{2}:[0-9]{2}:[0-9]{2})(.*)/, '$2 $3' );
					$countdown_div.data( 'time-now', sdate.replace( /-/g, '/' ) );
				}
				$( document.body ).trigger( 'porto_init_countdown', [$obj] );
			} else if ( 'porto_image_comparison' == shortcode && e.attributes.params ) {
				var $obj = $( e.view.$el );
				if ( $.fn.portoImageCompare && $obj.find( '.porto-image-comparison' ).length ) {
					$obj.find( '.porto-image-comparison' ).portoImageCompare();
				}
			} else if ( 'porto_blog' == shortcode && e.attributes.params ) {
				var $obj = $( e.view.$el );
				porto_init( $obj );
			} else if ( 'porto_content_box' == shortcode && e.attributes.params ) {
				var $obj = $( e.view.$el );
				var $icon = $obj.find( '.box-content>.icon-featured:not(:first-child)' );
				if ( $icon.length ) {
					$icon.remove();
				}
			}
		} );

		window.parent.vc.events.on( 'shortcodeView:destroy', function( model ) {
			var parent_id = model.attributes.parent_id;
			if ( !parent_id ) {
				return;
			}
			var parent = window.parent.vc.shortcodes.get( parent_id );
			if ( parent ) {
				if ( 'porto_carousel' == parent.attributes.shortcode ) {
					var $obj = $( '[data-model-id="' + parent.attributes.id + '"]' ).children( '.owl-carousel' );
					if ( $obj.length ) {
						$obj.removeData( '__carousel' );
						$obj.trigger( 'destroy.owl.carousel' );
						$obj.children( '.owl-item:empty' ).remove();
						$obj.themeCarousel( $obj.data( 'plugin-options' ) );
					}
				}
			}

			if ( 'porto_cursor_effect' == model.attributes.shortcode && window.porto_cursor_effects && window.porto_cursor_effects.length ) {
				window.porto_cursor_effects.forEach( function( i, index ) {
					if ( i.model_id && model.id == i.model_id ) {
						window.porto_cursor_effects.splice( index, 1 );

						var ins = $( document.body ).data( '__cursorEffect' );
						if ( ins ) {
							ins.destroy();
							$( document.body ).removeData( '__cursorEffect' );

							if ( window.porto_cursor_effects.length && $.fn.themePluginCursorEffect ) {
								$( document.body ).themePluginCursorEffect();
							}
						}
						return false;
					}
				} );
			}
		} );
		window.parent.vc.edit_element_block_view.on( 'afterRender', function() {
			var $el = this.$el,
				widgets = ['porto_ultimate_heading', 'porto_buttons', 'porto_image_comparison', 'porto_interactive_banner', 'vc_custom_heading', 'vc_btn', 'porto_countdown', 'vc_single_image'];
			if ( $.inArray( $el.attr( 'data-vc-shortcode' ), widgets ) >= 0 ) {
				$el.find( 'select' ).each( function() {
					var $this = $( this ),
						el_class = $this.attr( 'class' ),
						index_last = el_class.indexOf( '_dynamic_source' );
					if ( index_last >= 0 ) {
						var index_first = el_class.lastIndexOf( ' ', index_last );
						if ( index_first == -1 ) {
							index_first = 0;
						}
						var field_name = el_class.substring( index_first, index_last ).trim(),
							field_index = field_name.indexOf( '_' ),
							field_type = '';
						if ( field_index > 0 ) {
							field_type = field_name.substring( 0, field_index );
						} else {
							field_type = field_name;
						}
						if ( field_type == 'field' || field_type == 'link' || field_type == 'image' ) {
							porto_wpb_dynamic_execute( $el, field_type, field_name );
						}
					}
				} );
			}
		} );
		function porto_wpb_dynamic_execute( $el, field_type, field_name ) {
			var $dynamic_source_object = $el.find( 'select.' + field_name + '_dynamic_source' ),
				dynamic_source = $dynamic_source_object.val(),
				$dynamic_content = $el.find( 'select.' + field_name + '_dynamic_content' );
			porto_wpb_dyanmic_content( dynamic_source, field_type, $dynamic_content );

			$dynamic_source_object.on( 'change', function() {
				dynamic_source = $( this ).val();
				porto_wpb_dynamic_enable_subcontent( $el, $dynamic_content.val(), 'post_date', 'date_format' );
				porto_wpb_dyanmic_content( dynamic_source, field_type, $dynamic_content );
			} );

			// Format date format
			porto_wpb_dynamic_enable_subcontent( $el, $dynamic_content.attr( 'data-option' ), 'post_date', 'date_format' );
			$dynamic_content.on( 'change', function() {
				porto_wpb_dynamic_enable_subcontent( $el, $dynamic_content.val(), 'post_date', 'date_format' );
			} );

		}

		function porto_wpb_dynamic_enable_subcontent( $el, dynamic_content_option, content_value, shortcode_param ) {
			var $sub_content = $el.find( '[data-vc-shortcode-param-name="' + shortcode_param + '"]' ),
				$sub_content_select = $el.find( '[name="' + shortcode_param + '"]' );
			if ( $sub_content.length ) {
				if ( content_value == dynamic_content_option ) {
					if ( $sub_content.hasClass( 'vc_dependent-hidden' ) ) {
						$sub_content.removeClass( 'vc_dependent-hidden' );
						$sub_content_select.val( $sub_content_select.attr( 'value' ) );
					}
				} else {
					$sub_content.addClass( 'vc_dependent-hidden' );
					$sub_content_select.val( '' );
				}
			}
		}

		function porto_wpb_dyanmic_content( dynamic_source, field_type, $dynamic_content ) {
			$dynamic_content.find( '*' ).remove();
			if ( '' != dynamic_source && 'meta_field' != dynamic_source && $dynamic_content.length && !$dynamic_content.hasClass( '.vc_dependent-hidden' ) && porto_wpb_vars[dynamic_source] ) {
				if ( porto_wpb_vars[dynamic_source][field_type] ) {
					var $contents = porto_wpb_vars[dynamic_source][field_type],
						keys = Object.keys( $contents ),
						attribute = $dynamic_content.attr( 'data-option' ), selected_content = false,
						__ = wp.i18n.__;

					if ( keys.length ) {
						$dynamic_content.append( '<option class="" value="">' + __( 'Select Source...', 'porto' ) + '</option>' );
						for ( let index = 0; index < keys.length; index++ ) {
							var selected = '';
							if ( keys[index] == attribute ) {
								selected = 'selected="selected"';
								selected_content = true;
							}
							$dynamic_content.append( '<option class="' + keys[index] + '" value="' + keys[index] + '" ' + selected + '>' + $contents[keys[index]] + '</option>' );
						}
					}
					if ( selected_content ) {
						$dynamic_content.val( attribute ).addClass( attribute );
					}
				}
			}
		}
	}
} );