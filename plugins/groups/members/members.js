//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
if (!HUB.Plugins) {
	HUB.Plugins = {};
}

//----------------------------------------------------------
// Resource Ranking pop-ups
//----------------------------------------------------------
HUB.Plugins.GroupsMembers = {
	initialize: function() {
		if (typeof(SqueezeBoxHub) != "undefined") {
			if (!SqueezeBoxHub) {
				SqueezeBoxHub.initialize({ size: {x: 300, y: 330} });
			}
			
			$$('a.message').each(function(el) {
				if (el.href.indexOf('?') == -1) {
					el.href = el.href + '?no_html=1';
				} else {
					el.href = el.href + '&no_html=1';
				}
				el.addEvent('click', function(e) {
					new Event(e).stop();

					SqueezeBoxHub.fromElement(el,{
						handler: 'url', 
						size: {x: 300, y: 330}, 
						ajaxOptions: {
							method: 'get',
							onComplete: function() {
								frm = $('hubForm-ajax');
								if (frm) {
									frm.addEvent('submit', function(e) {
										new Event(e).stop();
										frm.send({
											onComplete: function() {
												SqueezeBoxHub.close();
											}
								        });
									});
								}
							}
						}
					});
				});
			});
		}
	}
}

window.addEvent('domready', HUB.Plugins.GroupsMembers.initialize);