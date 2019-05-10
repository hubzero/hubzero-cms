/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
