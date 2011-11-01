//-----------------------------------------------------------
//  Create our namespace
//-----------------------------------------------------------
var HUB = {};

HUB.Base = {
	initialize: function() {
		$$('img').each(function(trigger) {
			/* Published status */
			if (trigger.getProperty('src') == 'images/tick.png' || trigger.getProperty('src') == 'images/publish_g.png') {
				$(trigger.parentNode).addClass('published');
			}
			if (trigger.getProperty('src') == 'images/publish_r.png') {
				$(trigger.parentNode).addClass('expired');
			}
			if (trigger.getProperty('src') == 'images/publish_y.png') {
				$(trigger.parentNode).addClass('pending');
			}
			if (trigger.getProperty('src') == 'images/publish_x.png') {
				$(trigger.parentNode).addClass('unpublished');
			}
			if (trigger.getProperty('src') == 'images/disabled.png') {
				$(trigger.parentNode).addClass('archived');
			}
			/* Position arrows */
			if (trigger.getProperty('src') == 'images/uparrow.png') {
				$(trigger.parentNode).addClass('order-up');
			}
			if (trigger.getProperty('src') == 'images/downarrow.png') {
				$(trigger.parentNode).addClass('order-down');
			}
		});
	}
}

//----------------------------------------------------------

//window.addEvent('domready', HUB.Base.initialize);