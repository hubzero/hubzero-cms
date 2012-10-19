if (!HUB) {
	var HUB = {};
}

if (!jq) {
	var jq = $;
}

HUB.Mobile = {
	jQuery: jq,
	
	init: function()
	{
		if( $("#true-menu").length )
		{
			var select = "",
				menu = $("#true-menu").children("ul");
			
			menu.children("li").each(function(i, el) {
				var menuItem = $(el),
					name = menuItem.children("a").children("span").html(),
					link = menuItem.children("a").attr("href");
				
				selected = (menuItem.hasClass("active")) ? "selected" : "";
				select += "<option " + selected + " value=\"" + link + "\">" + name + "</option>";
				
				if(menuItem.hasClass("parent"))
				{
					var submenu = menuItem.children("ul");
					
					submenu.children("li").each(function(i, el) {
						var subMenuItem = $(el),
							name = subMenuItem.children("a").children("span").html(),
							link = subMenuItem.children("a").attr("href");
						if(name != "" && name != null)
						{
							select += "<option " + selected + " value=\"" + link + "\"> &mdash; " + name + "</option>";
						}
					});
				}
			});
			
			//set the content of the select box
			$("#menu").html(select);
			
			//add on change event to go to new link after changing
			$("#menu").on("change", function() {
				var index = this.selectedIndex,
					href  = this.options[index].value;
					
				window.location.href = href
			});
		}
	}
};

jQuery(document).ready(function($){
	HUB.Mobile.init();
});