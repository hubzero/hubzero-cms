/**
 * @package     hubzero-cms
 * @file        templates/system/js/group.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}


HUB.template = {};

jQuery(document).ready(function(jq){
	var $ = jq;

	// group pane toggle
	$('#group a.toggle, #group-info .close').on('click', function(event) {
		event.preventDefault();

		$('#group-info').slideToggle('normal');
		$('#group-body').toggleClass('opened');
	});

	// Template
	HUB.template.body = $('body');

	// Account panel
	HUB.template.accountTrigger = $('.user-account-link.loggedin');
	HUB.template.accountPanel = $('.account-details');
	HUB.template.accountCloseTrigger = HUB.template.accountPanel.find('.close');

	$(HUB.template.accountTrigger).on('click', function(e) {
		if(!(HUB.template.accountPanel.hasClass('open'))) {
			HUB.template.closeAllPanels();
			HUB.template.openAccountPanel();
		}
		else {
			HUB.template.closeAllPanels();
		}

		e.preventDefault();
	});

	$(HUB.template.accountCloseTrigger).on('click', function(e) {
		HUB.template.closeAllPanels();
		e.preventDefault();
	});

	HUB.template.openAccountPanel = function() {
		HUB.template.body.addClass('panel-open');
		HUB.template.accountPanel.addClass('open');
	};

	HUB.template.closeAccountPanel = function() {
		HUB.template.accountPanel.removeClass('open');
	};

	// Member dash
	HUB.template.dashTrigger = $('.subnav-membership > .toggle');
	HUB.template.dashPanel = $('.group-dash');
	HUB.template.dashCloseTrigger = HUB.template.dashPanel.find('.close');

	$(HUB.template.dashTrigger).on('click', function(e) {
		if(!(HUB.template.dashPanel.hasClass('open'))) {
			HUB.template.closeAllPanels();
			HUB.template.openDashPanel();
		}
		else {
			HUB.template.closeAllPanels();
		}

		e.preventDefault();
	});

	$(HUB.template.dashCloseTrigger).on('click', function(e) {
		HUB.template.closeAllPanels();
		e.preventDefault();
	});

	HUB.template.openDashPanel = function() {
		HUB.template.body.addClass('panel-open');
		HUB.template.dashPanel.addClass('open');
	};

	HUB.template.closeDashPanel = function() {
		HUB.template.dashPanel.removeClass('open');
	};

	// Escape button to the rescue for those who like to press it in a hope to close whatever is open
	$(document).keyup(function(e) {
		if(e.keyCode == 27) {
			HUB.template.closeAllPanels();
		}
	});

	HUB.template.closeAllPanels = function() {
		HUB.template.closeAccountPanel();
		HUB.template.closeDashPanel();
		HUB.template.body.removeClass('panel-open');
	};

	HUB.template.overlay = $('.hub-overlay');
	$(HUB.template.overlay).on('click', function(e) {
		HUB.template.closeAllPanels();
		e.preventDefault();
	});

	$(window).resize(function() {
		HUB.template.closeAllPanels();
	});

	HUB.template.init = function() {
	};

	HUB.template.init();
});