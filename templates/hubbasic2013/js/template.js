/**
 * @package     hubzero-cms
 * @file        templates/hubbasic2013/js/template.js
 * @copyright   Copyright 2005-2014 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

var hubbasic2013 = {};

jQuery(document).ready(function($) {
	// Set the vars
	hubbasic2013.mobileNav = $('#mobile-nav');
	hubbasic2013.nav = $('nav#main-navigation');
	hubbasic2013.navIcon = $('#nav-icon');
	hubbasic2013.navHeight;
	hubbasic2013.windowState = hubbasic2013.mobileNav.css('display');

	$('#mobile-menu').on('click', function(e) {
		if (!(hubbasic2013.nav.hasClass('open'))) {
			hubbasic2013.nav.css('max-height', '1500px').addClass('open');
			hubbasic2013.navIcon.addClass('open');
		} else {
			hubbasic2013.nav.css('max-height', '0').removeClass('open');
			hubbasic2013.navIcon.removeClass('open');
		}

		e.preventDefault();	
	});
});

$(window).resize(function() {
	// Check if the state changed, do something then)
	if (hubbasic2013.mobileNav.css('display') != hubbasic2013.windowState) {
		// update window state to the current one
		hubbasic2013.windowState = hubbasic2013.mobileNav.css('display');

		if (hubbasic2013.mobileNav.css('display') == 'none') {
			hubbasic2013.nav.css('max-height', '');
		} else {
			hubbasic2013.nav.removeClass('open');
			hubbasic2013.navIcon.removeClass('open');
		}
	}
});