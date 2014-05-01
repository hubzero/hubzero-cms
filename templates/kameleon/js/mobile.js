/**
 * @package     hubzero-cms
 * @file        templates/kameleon/js/mobile.js
 * @copyright   Copyright 2005-2014 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

jQuery(document).ready(function($){
	if ($("#nav").length) {
		var select = "",
			menu = $("#nav").children("ul");

		menu.children("li").each(function(i, el) {
			var menuItem = $(el),
				name = menuItem.children("a").children("span").html(),
				link = menuItem.children("a").attr("href");

			//support for joomla 2.5
			if ($('html').hasClass('joomla2.5')) {
				name = menuItem.children("a").html();
			}

			selected = (menuItem.hasClass("active")) ? "selected" : "";
			select += "<option " + selected + " value=\"" + link + "\">" + name + "</option>";

			if (menuItem.hasClass("parent")) {
				var submenu = menuItem.children("ul");

				submenu.children("li").each(function(i, el) {
					var subMenuItem = $(el),
						name = subMenuItem.children("a").children("span").html(),
						link = subMenuItem.children("a").attr("href");
						
					//support for joomla 2.5
					if ($('html').hasClass('joomla2.5')) {
						name = subMenuItem.children("a").html();
					}

					if (name != "" && name != null) {
						select += "<option " + selected + " value=\"" + link + "\"> &mdash; " + name + "</option>";
					}
				});
			}
		});

		//set the content of the select box
		$("#mobile-nav").html(select);

		//add on change event to go to new link after changing
		$("#mobile-nav").on("change", function() {
			var index = this.selectedIndex,
				href  = this.options[index].value;

			window.location.href = href
		});
	}
});