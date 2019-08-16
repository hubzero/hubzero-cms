

var Bulk = {
	initialize:function(){
		var self = this;
		$('.bulk').each(function(i, el){
			$(el).on('change', function(e){
					self.importvalues(this);
				})
				.on('paste', function(e){
					self.importvalues(this);
				});
		});
	},

	importvalues:function(bulk){
		if (!bulk.val().length) {
			return;
		}

		bulkprefix = bulk.attr('id').replace('bulk','').replace('jform_params_','');
		bulkprefix = bulkprefix.length ? bulkprefix : '';

		typeset = $('#' + bulk.attr('id') + 'type');

		type = false;

		typeset.find('input').each(function(i, el){
			if (el.checked) {
				type = el.val();
			}
		})

		if (!type) {
			return;
		}

		destination = $('#fields_params_'+bulkprefix+type+'-'+type);
		button = $('#fields_params_'+bulkprefix+type+'-save');

		var entries = bulk.val().split("\n");

		$.each(entries, function(i, entry){
			destination.val(entry);

			button.trigger('click');
		})

		bulk.value = '';
	}
}

jQuery(document).ready(function ($) {
	Object.append(Element.NativeEvents, {
		'paste': 2,
		'input': 2
	});

	Element.Events.paste = {
		base : (Browser.opera || (Browser.firefox && Browser.version < 2)) ? 'input': 'paste',
		condition: function(e) {
			this.fireEvent('paste', e, 1);
			return false;
		}
	};

	Bulk.initialize();
});
