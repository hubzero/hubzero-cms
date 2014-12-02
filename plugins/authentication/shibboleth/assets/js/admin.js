// auth link invalidation form
jQuery(function($) {
	var prnt = $('.auth-links')
		serialized = prnt.children('.serialized'),
		val = JSON.parse(serialized.val())
		;
	prnt.find('li').each(function(_, li) {
		li = $(li);
		li.append(
			$('<button>Invalidate</button>')
				.attr('title', 'Remove this association so that the domain/email combination in question can be linked to a different account')
				.click(function() {
					val.push(li.data('id'));
					serialized.val(JSON.stringify(val));
					li.remove();
				})
		);
	});
});

// institution management form
jQuery(function($) {
	$('#jform_params_institutions-lbl').hide();

	var prnt = $('.shibboleth'), 
		// control values are stored in a JSON string so they fit in the extensions table
		serialized = $('.shibboleth input.serialized'),
		// initialize from existing params
		val = JSON.parse(serialized.val()),
		// update hidden input to reflect form state
		update = function() {
			serialized.val(JSON.stringify(val));
		},
		// update active idp list state
		updateIdps = function() {
			val.activeIdps = [];
			var anyInvalid = false;
			prnt.find('ul.active li').each(function(_, li) {
				var idp = {}, thisInvalid = false;
				// copy form data to 'val'
				$(li).find('input').each(function(_, inp) {
					inp = $(inp);
					var name = inp.attr('name');
					idp[name] = inp.val();
					thisInvalid = thisInvalid || (name == 'entity_id' && !idp[name].replace(/\s/g, '')) || (name == 'label' && !idp[name].replace(/\s/g, ''));
					anyInvalid = anyInvalid || thisInvalid;
					if (name == 'logo') {
						idp.logo_data = inp.data('logo_data');
					}
				});
				if (!thisInvalid) {
					val.activeIdps.push(idp);
				}
			});
			if (anyInvalid) {
				idpWarning.show();
			}
			else {
				idpWarning.hide();
			}
			// propagate to JSON representation
			update();
		},
		idpWarning = $('<p class="warning">Not all ID providers will be saved! Each entry must have an entity ID and a label.</p>').hide()
		;
	// link xml input to JSON encoding
	$('.shibboleth input[name="xmlPath"]').change(function() {
		val.xmlPath = $(this).val();
		update();
	});

	// make idp attribute keys slightly more presentable
	var keyToLabel = function(str) {
		return str[0].toUpperCase() + str.substr(1).replace('_', ' ') + ': ';
	};

	// try to show a preview of the given logo URL
	var updateLogo = function(li) {
		var logoInp = li.find('input[name="logo"]'),
			href = logoInp ? logoInp.val() : null;
		if (!href || !href.replace(/s+/g, '')) {
			return;
		}
		var imgData;
		li.find('.preview').remove();
		if (true || href != logoInp.data('orig') || !(imgData = logoInp.data('logo_data'))) {
			$.ajax({
				'url': prnt.data('iconify'),
				'data': {'img': href},
				'success': function(res) {
					li.append($('<img class="preview">').attr('src', res));
					logoInp.data('logo_data', res);
					updateIdps();
				},
				'error': updateIdps
			})
		}
		else {
			li.append($('<img class="preview">').attr('src', imgData));
			updateIdps();
		}
	};

	// make a new entry in the idp list
	var newActiveIdp = function(idp, before) {
		var li = $('<li>')
			.append($('<span class="ui-icon ui-icon-arrowthick-2-n-s">'))
			.append($('<span class="remove icon">').click(function() {
				li.remove();
				updateIdps();
			}))
			[before === true ? 'prependTo' : 'appendTo'](existing);
			if (before) {
				li.animate('pulsate', 'slow');
			}
		for (var k in idp) {
			if (k === 'logo_data' || k === 'logoData') {
				continue;
			}
			var control = mkInp(k, idp[k]);
			if (k == 'logo') {
				control.input.change(function() {
					updateLogo(li);
				});
			}
			else {
				control.input.change(updateIdps);
			}
			li.append(control.label);
		}
		if (idp.logo_data) {
			li.find('input[name="logo"]').data('logo_data', idp.logo_data);
		}
		updateLogo(li);
	};

	// list entities read from the shibboleth conf, if any
	if (val.xmlRead) {
		prnt.append($('<h4>ID providers in metadata</h4>'));
		var ul = $('<ul class="metadata">').appendTo(prnt);
		val.idps.forEach(function(idp) {
			var li = $('<li>');
			li.append($('<span class="add icon">').click(function() {
				newActiveIdp(idp, true);
				updateIdps();
			}));
			for (var k in idp) {
				li.append($('<p>').append($('<label>').append($('<span>').text(keyToLabel(k))).append(document.createTextNode(idp[k]))))
			}
			ul.append(li);
			updateLogo(li);
		});
	}
	else {
		$('.shibboleth input[name="xmlPath"]').parent().append($('<p class="warning">').text(val.idps));
	}

	prnt.append($('<hr>'));
	var mkInp = function(lbl, val) {
		var inp = $('<input>').val(val).attr('name', lbl).data('orig', val);
		return {
			'label': $('<p>').append($('<label>').append($('<span>').text(keyToLabel(lbl))).append(inp)),
			'input': inp
		};
	};

	// new idp entry form
	var addNew = $('<div class="new idp">');
	['entity_id', 'label', 'host', 'logo'].forEach(function(key) {
		var inp = mkInp(key);
		addNew.append(inp.label);
	});
	addNew.append($('<button><span class="add icon"></span> Add ID provider</button>').click(function(evt) {
		evt.stopPropagation();
		var idp = {};
		addNew.find('input').each(function(_, inp) {
			inp = $(inp);
			idp[inp.attr('name')] = inp.val();
			inp.val('');
		});
		newActiveIdp(idp, true);
		updateIdps();
		return false;
	}));

	// append existing active providers
	prnt
		.append($('<h4>Active ID providers</h4>'))
		.append(idpWarning)
		.append(addNew);
	var existing = $('<ul class="active">').sortable({'stop': updateIdps}).appendTo(prnt);
	val.activeIdps.forEach(newActiveIdp);
});
