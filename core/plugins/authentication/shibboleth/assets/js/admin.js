// auth link invalidation form
jQuery(function($) {
	var prnt = $('.shibboleth')
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

	var serialized = $('.shibboleth input.serialized');
	if (!serialized.length) { console.log('[shib] no serialized input found'); return; }
	console.log('[shib] init, serialized name=', serialized.attr('name'));
	console.log('[shib] init, serialized initial value=', serialized.val());
	var val;
	try {
		val = JSON.parse(serialized.val());
	} catch (e) {
		console.error('[shib] failed to parse serialized value:', e, serialized.val());
		val = { xmlPath: '', activeIdps: [] };
	}
	console.log('[shib] init, parsed val=', val);

	// xmlPath field change
	$('#shib-xmlpath').on('change', function() {
		val.xmlPath = this.value;
		serialized.val(JSON.stringify(val));
		console.log('[shib] xmlPath changed; serialized now=', serialized.val());
	});

	// Rebuild activeIdps from currently-checked boxes and sync to hidden input
	function updateActiveIdps(reason) {
		val.activeIdps = [];
		$('.shib-idp-checkbox:checked').each(function() {
			var entityId = $(this).val();
			var label    = $(this).data('label');
			var m        = entityId.match(/([^.\/:]+\.[^.\/:]+?)(?:\/|$)/);
			val.activeIdps.push({
				entity_id: entityId,
				label:     label,
				host:      m ? m[1] : ''
			});
		});
		serialized.val(JSON.stringify(val));
		console.log('[shib] updateActiveIdps (' + reason + '): ' + val.activeIdps.length + ' active; hidden input value length=' + serialized.val().length);
		console.log('[shib] hidden input current value=', serialized.val());
	}

	// Pre-check boxes for already-active IDPs
	var activeIds = {};
	(val.activeIdps || []).forEach(function(idp) {
		activeIds[idp.entity_id] = true;
	});
	var preChecked = 0;
	$('.shib-idp-checkbox').each(function() {
		if (activeIds[$(this).val()]) {
			$(this).prop('checked', true);
			preChecked++;
		}
	});
	console.log('[shib] pre-checked ' + preChecked + ' of ' + Object.keys(activeIds).length + ' saved active IDPs');

	$(document).on('change', '.shib-idp-checkbox', function() {
		console.log('[shib] checkbox change: ' + $(this).val() + ' -> ' + this.checked);
		updateActiveIdps('change');
	});

	$('#item-form').on('submit', function() {
		console.log('[shib] item-form submit handler firing');
		updateActiveIdps('submit');
	});

	// Backstop: also intercept the toolbar Save button click directly,
	// since Hubzero.submitform may bypass jQuery submit handlers.
	$(document).on('click', 'button.toolbar-submit, a.toolbar-submit, .toolbar button, .toolbar a', function() {
		console.log('[shib] toolbar click detected on:', this);
		updateActiveIdps('toolbar-click');
	});

	// Search / filter checkboxes by display name
	$('#shib-idp-search').on('input', function() {
		var q = this.value.toLowerCase();
		$('.idp-list label').each(function() {
			$(this).toggle(!q || $(this).text().toLowerCase().indexOf(q) !== -1);
		});
	});
});
