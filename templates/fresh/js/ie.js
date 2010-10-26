//-----------------------------------------------------------
//  Javascript for older IE
//-----------------------------------------------------------
HUB.Base.IE = {
	menu: function() {
		$$('#nav li').each(function(li) {
			li.addEvent('mouseover', function(e) {
				this.addClass('sfhover');
				var uls = $(this).getElementsByTagName('ul');
				for (var i=0; i<uls.length; i++)
				{
					$(uls[i]).setStyle('visibility', 'visible');
				}
			});
			li.addEvent('mouseout', function(e) {
				this.removeClass('sfhover');
				var uls = $(this).getElementsByTagName('ul');
				for (var i=0; i<uls.length; i++)
				{
					$(uls[i]).setStyle('visibility', 'hidden');
				}
			});
		});
	},

	// launch functions
	initialize: function() {
		HUB.Base.IE.menu();
	}
}

//----------------------------------------------------------

window.addEvent('domready', HUB.Base.IE.initialize);