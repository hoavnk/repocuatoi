<?php
/**
 * Default header
 */
return array(
	'title'      => __( 'Default header', 'porto' ),
	'categories' => array( 'header' ),
	'blockTypes' => array( 'core/template-part/header' ),
	'content'    => '<!-- wp:group {"className":"container header-col","layout":{"type":"flex","flexWrap":"nowrap"}} -->
	<div class="wp-block-group container header-col"><!-- wp:porto/porto-heading {"title":"FREE RETURNS. STANDARD SHIPPING ORDERS $99+","font_family":"Poppins","font_size":"11px","font_weight":600,"letter_spacing":"0.275px","color":"#777777","tag":"p","style_options":{"padding":{"top":"7px","bottom":"7px"}},"className":"mb-0 mr-md-auto d-none d-md-block"} /-->
	
		<!-- wp:porto-hb/porto-divider {"width":"1px","height":"1.2em","color":"#f5f5f5","className":"d-none d-lg-block ml-1"} /-->
		
		<!-- wp:porto-hb/porto-switcher {"type":"language-switcher","text_transform":"uppercase","letter_spacing":"0.275px","color":"#777777","className":"menu-switcher p-l-sm"} /-->
		
		<!-- wp:porto-hb/porto-switcher {"type":"currency-switcher","letter_spacing":"0.275px","color":"","className":"menu-switcher "} /-->
		
		<!-- wp:porto-hb/porto-divider {"width":"1px","height":"1.2em","color":"#f5f5f5","className":"d-none d-md-block"} /-->
		
		<!-- wp:porto-hb/porto-social {"icon_size":"12.8px","icon_border_radius":"26px","icon_border_spacing":"28px","spacing":"2px"} /--></div>
		<!-- /wp:group -->
		
		<!-- wp:separator {"style":{"color":{"background":"#e7e7e7"}},"className":"is-style-wide my-0"} -->
		<hr class="wp-block-separator has-text-color has-alpha-channel-opacity has-background is-style-wide my-0" style="background-color:#e7e7e7;color:#e7e7e7"/>
		<!-- /wp:separator -->
		
		<!-- wp:porto/porto-section {"tag":"div","style_options":{"padding":{"top":"27px","bottom":"27px","right":""}}} -->
		<!-- wp:group {"className":"container header-row","layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group container header-row"><!-- wp:group {"className":"header-left","layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group header-left"><!-- wp:porto-hb/porto-menu-icon /-->
		
		<!-- wp:site-title /--></div>
		<!-- /wp:group -->
		
		<!-- wp:group {"className":"flex-grow-1 header-right header-col ps-xl-5 ms-xl-4","layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group flex-grow-1 header-right header-col ps-xl-5 ms-xl-4"><!-- wp:porto/porto-info-box {"icon":"porto-icon-phone-1","icon_color":"#222529","title":"Call Us Now","subtitle":"+123 5678 890","link":"tel:01235678890","read_more":"box","title_font_style":600,"title_font_size":"11px","title_text_transform":"uppercase","title_font_line_height":"11px","title_font_letter_spacing":"-0.55px","title_font_color":"#777777","subtitle_font_style":700,"subtitle_font_size":"18px","subtitle_font_line_height":"18px","subtitle_font_letter_spacing":"0px","subtitle_font_color":"#222529","desc_font_style":700,"desc_font_color":"#222529","icon_margin_right":"7px","className":"mb-0 mr-4 text-left"} /-->
		
		<!-- wp:porto-hb/porto-myaccount {"size":"26"} /-->
		
		<!-- wp:porto-hb/porto-wishlist {"size":"26px","color":"#222529"} /-->
		
		<!-- wp:porto-hb/porto-mini-cart {"type":"minicart-arrow-alt","content_type":"offcanvas","icon_cl":"porto-icon-cart-thick","icon_color":"#222529"} /--></div>
		<!-- /wp:group --></div>
		<!-- /wp:group -->
		<!-- /wp:porto/porto-section -->',
);
