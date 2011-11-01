if (!HUB) {
	var HUB = {};
}

HUB.Stripe = {
	initialize: function() {
		$$('.adminlist').each(function(tbody) {
			var trs = tbody.getElementsByTagName('tr');
			cls = 'row1';
			for (var i = 0; i < trs.length; i++) {
				if ($(trs[i]).hasClass('row0') || $(trs[i]).hasClass('row1')) {
					continue;
				}
				cls = (cls == 'row1') ? 'row0' : 'row1';
				$(trs[i]).addClass(cls);
			}
		});
		$$('.paramlist').each(function(tbody) {
			var trs = tbody.getElementsByTagName('tr');
			cls = 'row1';
			for (var i = 0; i < trs.length; i++) {
				if ($(trs[i]).hasClass('row0') || $(trs[i]).hasClass('row1')) {
					continue;
				}
				cls = (cls == 'row1') ? 'row0' : 'row1';
				$(trs[i]).addClass(cls);
			}
		});
	}
}

//----------------------------------------------------------

window.addEvent('domready', HUB.Stripe.initialize);