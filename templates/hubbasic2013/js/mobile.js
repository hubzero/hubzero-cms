/**
 * @package     hubzero-cms
 * @file        templates/hubbasic2013/js/mobile.js
 * @copyright   Copyright 2005-2014 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	if ($("#nav").length) {
		var select = "",
			menu = $("#nav").children("ul");

		menu.children("li").each(function(i, el) {
			var menuItem = $(el),
				name = menuItem.children("a").html(),
				link = menuItem.children("a").attr("href");

			selected = (menuItem.hasClass("active")) ? "selected" : "";
			select += "<option " + selected + " value=\"" + link + "\">" + name + "</option>";

			if (menuItem.hasClass("parent")) {
				var submenu = menuItem.children("ul");

				submenu.children("li").each(function(i, el) {
					var subMenuItem = $(el),
						name = subMenuItem.children("a").html(),
						link = subMenuItem.children("a").attr("href");

					if (name != "" && name != null) {
						select += "<option " + selected + " value=\"" + link + "\"> &mdash; " + name + "</option>";
					}
				});
			}
		});

		// Set the content of the select box
		$("#mobile-nav").html(select);

		// Add on change event to go to new link after changing
		$("#mobile-nav").on("change", function() {
			var index = this.selectedIndex,
				href  = this.options[index].value;

			window.location.href = href
		});
	}
});