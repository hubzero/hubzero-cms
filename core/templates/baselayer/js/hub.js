/**
 * @package     hubzero-cms
 * @file        templates/baselayer/js/hub.js
 * @copyright   Copyright 2005-2014 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

var baselayer = {};

jQuery(document).ready(function($) {
	// Set the vars
	baselayer.mobileNav = $('#mobile-nav');
	baselayer.searchBox = $('#search-box');
	baselayer.nav = $('header nav#main-navigation');
	baselayer.searchBoxHeight;
	baselayer.navHeight;
	baselayer.windowState = baselayer.mobileNav.css('display');

	$('#mobile-search').on('click', function(e) {
		if (!(baselayer.searchBox.hasClass('open'))) {
			baselayer.searchBox.css('max-height', '100px').addClass('open');
			$('#mod_search_searchword').focus();
		} else {
			baselayer.searchBox.css('max-height', '0').removeClass('open');
		}

		e.preventDefault();	
	});

	$('#mobile-menu').on('click', function(e) {
		if (!(baselayer.nav.hasClass('open'))) {
			baselayer.nav.css('max-height', '1500px').addClass('open');
		} else {
			baselayer.nav.css('max-height', '0').removeClass('open');
		}

		e.preventDefault();	
	});
});

$(window).resize(function() {
	// Check if the state changed, do something then)
	if (baselayer.mobileNav.css('display') != baselayer.windowState) {
		// update window state to the current one
		baselayer.windowState = baselayer.mobileNav.css('display');

		if (baselayer.mobileNav.css('display') == 'none') {
			baselayer.searchBox.css('max-height', '');
			baselayer.nav.css('max-height', '');
		} else {
			baselayer.searchBox.removeClass('open');
			baselayer.nav.removeClass('open');
		}
	}
});