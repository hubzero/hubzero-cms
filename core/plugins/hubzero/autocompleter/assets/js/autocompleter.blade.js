/**
 * Autocompleter — daisyUI override.
 *
 * Loaded after autocompleter.js when the active CSS framework is daisyUI.
 * Replaces HUB.Plugins.Autocomplete.initialize() so that tokens render
 * as `badge badge-soft badge-primary` pills instead of the legacy
 * yellow/green/blue token-input styles.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
jQuery(document).ready(function ($) {
	// The legacy autocompleter.js already ran initialize() on
	// document.ready, creating token lists with legacy classes.
	// We need to tear those down and re-create with daisyUI badges.

	// Destroy existing tokenInput instances created by the legacy init.
	// Save original values so we can re-populate after teardown.
	$('.autocomplete').each(function () {
		var $el = $(this);
		if ($el.data('tokenInputObject')) {
			// Grab the current token values from the hidden input
			// that tokenInput maintains (sibling of the generated UL).
			var hiddenInput = $el.prev('ul[class*="token-input-list"]')
				.next('input[type="hidden"][id="token-input-' + $el.attr('id') + '"]');
			var savedValue = hiddenInput.length ? hiddenInput.val() : '';

			// Tear down: remove the UL, hidden input, and data binding
			$el.prev('ul[class*="token-input-list"]').remove();
			hiddenInput.remove();
			$el.removeData('tokenInputObject');
			$el.show();

			// Restore the original value so re-init can read it
			if (savedValue) {
				$el.val(savedValue);
			}
		}
	});

	// Also remove any legacy CSS link that was injected
	$('link[href*="autocompleter.css"]').remove();

	HUB.Plugins.Autocomplete.initialize = function () {
		// Skip legacy CSS injection — daisyUI styles handle everything.

		$('.autocomplete').each(function (i, input) {
			var option = 'tags',
				type = 'multi',
				actkn = '',
				id = null,
				wsel = null,
				cls = '',
				hint = '',
				limit = null,
				storeRecent = true;

			id = $(input).attr('id');
			if (!id) {
				return;
			}

			if ($(input).attr('data-options')) {
				var params = $(input).attr('data-options').split(',');
				if (params) {
					option = params[0];
					type   = params[1];
					wsel   = params[2];
				}
			}

			switch (option) {
				case 'members':
					cls = 'acm';
					hint = 'Type in name or email';
					break;
				case 'groups':
					cls = 'acg';
					hint = 'Type in a search term';
					break;
				case 'tags':
				default:
					cls = 'act';
					hint = 'Type in a search term';
					break;
			}

			if ($('#actkn').length) {
				actkn = '&admin=true';
			}

			limit = (type === 'multi') ? null : 1;

			var recentStorageKey = 'autocompleter.recent.' + option;

			var value = $('#' + id).val();
			var data = [];
			if (value) {
				var values = (value.indexOf(',') === -1) ? [value] : value.split(',');

				$(values).each(function (i, v) {
					v = v.replace(/^\s+/, '').replace(/\s+$/, '');

					var itemId = null, name = null;
					if (option !== 'tags' && v.match(/(.+?) \((.+?)\)/ig)) {
						itemId = v.replace(/(.+?) \((.+?)\)/ig, '$2');
						name   = v.replace(/(.+?) \((.+?)\)/ig, '$1');
					}
					itemId = itemId || v;
					name   = name   || itemId;

					data[i] = { id: itemId, name: name };
				});
			}

			var src = $(input).attr('data-script') || '/index.php';

			$('#' + id).tokenInput(src + '?option=com_' + option + '&no_html=1&task=autocomplete' + actkn, {
				theme: cls,
				hintText: hint,
				prePopulate: data,
				tokenLimit: limit,
				preventDuplicates: true,

				// --- daisyUI token formatter ---
				tokenFormatter: function (item) {
					return '<li class="badge badge-soft badge-primary gap-1">' +
						'<p>' + item[this.propertyToSearch] + '</p></li>';
				},

				resultsFormatter: function (item) {
					if (option !== 'tags') {
						var html = '<li>';
						if (item.picture) {
							html += '<img src="' + item.picture + '" width="30" height="30" alt="picture" />';
						}
						html += item[this.propertyToSearch] + ' (' + item.id + ')';
						if (item.org) {
							html += '<span>' + item.org + '</span>';
						}
						if (item.picture) {
							html += '<div style="display:inline;clear:left;"></div>';
						}
						html += '</li>';
						return html;
					}
					return '<li>' + item[this.propertyToSearch] + '</li>';
				},

				onResult: function (results) {
					if (storeRecent && localStorage && localStorage.getItem(recentStorageKey)) {
						var topResults = [];
						var recent = JSON.parse(localStorage.getItem(recentStorageKey));
						var items = recent.items;

						for (var i = 0; i < items.length; i++) {
							for (var j = 0; j < results.length; j++) {
								if (items[i] === results[j].id) {
									topResults.push(results[j]);
									results.splice(j, 1);
								}
							}
						}

						topResults = topResults.reverse();
						for (var k = 0; k < topResults.length; k++) {
							results.unshift(topResults[k]);
						}
					}
					return results;
				},

				onAdd: function (item) {
					if (item.name.indexOf(',') > -1 || item.name.indexOf(';') > -1) {
						$('#' + id).tokenInput('remove', { id: item.id });
						var items = item.name.split(/,|;/g);
						for (var n = 0; n < items.length; n++) {
							var trimmed = items[n].trim();
							$('#' + id).tokenInput('add', { id: trimmed, name: trimmed });
						}
					}

					if (wsel) {
						$.getJSON('/index.php?option=com_groups&no_html=1&task=memberslist&group=' + $('#' + id).val(), function (data) {
							HUB.Plugins.Autocomplete.writeSelectList(data.members, wsel);
						});
					}

					if (storeRecent && localStorage) {
						var recent;
						if (localStorage.getItem(recentStorageKey)) {
							recent = JSON.parse(localStorage.getItem(recentStorageKey));
							var index = recent.items.indexOf(item.id);
							if (index !== -1) {
								recent.items.splice(index, 1);
							}
							recent.items.unshift(item.id);
						} else {
							recent = { items: [item.id] };
						}
						localStorage.setItem(recentStorageKey, JSON.stringify(recent));
					}
				}
			});
		});
	};

	// Re-run with the new daisyUI-aware initialize
	HUB.Plugins.Autocomplete.initialize();
});
