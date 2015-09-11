/**
 * @package     hubzero-cms
 * @file        components/com_tools/site/assets/js/pipeline.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// ToolsPipeline admin actions form
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

HUB.ToolsPipeline = {
	form: '#adminCalls',
	statusform: '#statusForm',
	toolid: '#id',
	action: '#action',
	newstate: '#newstate',
	loader: '#ctSending',
	success: '#ctSuccess',
	wrap : '#wrap',
	canceltool : '#ctCancel',
	commentarea : '#ctComment',
	admintools : '#admintools',
	templatepath : '',

	licOptions: function() {
		var sel = $('#t_code').find(":selected").attr('value');

		if (sel == "@OPEN") {
			$("#open-source").show();
			$("#closed-source").hide();
			$('#choice-icon').addClass('opensource');
			$('#choice-icon').removeClass('closedsource');
		} else {
			$("#open-source").hide();
			$("#closed-source").show();
			$('#choice-icon').removeClass('opensource');
			$('#choice-icon').addClass('closedsource');
		}
	}
}

jQuery(document).ready(function($){
	// Create the dropdown base
	$('.entries-menu').each(function(i, el){
		el = $(el);
		el.addClass('js');

		var select = $("<select />").on('change', function() {
			window.location = $(this).find("option:selected").val();
		});

		$("<option />", {
			"value"   : "",
			"text"    : el.attr('data-label')
		}).appendTo(select);

		el.find("a").each(function() {
			var elm = $(this);
			var opts = {
				"value"   : elm.attr("href"),
				"text"    : elm.text()
			};
			if (elm.hasClass('active')) {
				opts.selected = 'selected';
			}
			$("<option />", opts).appendTo(select);
		});

		var li = $("<li />").addClass('option-select');

		select.appendTo(li);
		li.appendTo(el);
	});

	if ($('#ctSending').length > 0) {
		$(HUB.ToolsPipeline.loader).hide();
	}
	if ($('#ctCancel').length > 0) {
		$(HUB.ToolsPipeline.canceltool).hide();
	}
	if ($('#ctComment').length > 0) {
		$(HUB.ToolsPipeline.commentarea).hide();
	}

	if ($('#admintools').length > 0) { // show admin controls from start
		$(HUB.ToolsPipeline.admintools).show();

		$('#ctad').addClass('collapse');
		$('#ctad').on('click', function(){
			if ($('#ctad').hasClass('collapse')) {
				$('#ctad').removeClass('collapse');
				$(HUB.ToolsPipeline.admintools).hide();
				$('#ctad').addClass('expand');
			} else {
				$('#ctad').removeClass('expand');
				$(HUB.ToolsPipeline.admintools).show();
				$('#ctad').addClass('collapse');
			}
			return false;
		});
	}

	var conf = $('.conf');
	var config = $('.config');
	if (config.length) {
		// version page controls
		$('.showcontrols').each(function(i, item) {
			$($(item).find('a')[0]).on('click', function(e) {
				e.preventDefault();

				var vnum = $(this).attr('id').replace('exp_',''); //$(item.getElements('a')[0]).attr('id').replace('exp_','');
				var vtr = '#configure_' + vnum;

				if ($(this).hasClass('collapse')) {
					$('#displays_' + vnum).removeClass('highlighted_upper');
					$('#conftdone_' + vnum).removeClass('highlighted_lower');
					$('#conftdtwo_' + vnum).removeClass('highlighted_lower');
					$(this).removeClass('collapse');
					$(vtr).addClass('hide');
					$(this).addClass('expand');
				} else {
					$('#displays_' + vnum).addClass('highlighted_upper');
					$('#conftdone_' + vnum).addClass('highlighted_lower');
					$('#conftdtwo_' + vnum).addClass('highlighted_lower');
					$(this).removeClass('expand');
					$(vtr).removeClass('hide');
					$(this).addClass('collapse');
				}
				return false;
			});
		});
	}

	// show screenshots for diferent versions
	var ssform = document.getElementById("screenshots-form");
	if (ssform && $('#vchange_dev').length > 0) {
		$('#vchange_dev').on('change', function() {
			ssform.changing_version.value = 1;
			ssform.submit();
		});
		$('#vchange_current').on('change', function() {	
			ssform.changing_version.value = 1;
			ssform.submit();
		});
	}

	// close pop-up window
	if ($('#ss-pop-form').length > 0) {
		$('#ss-pop-form').on('submit', function(e) {
			e.preventDefault();

			$.post($('#ss-pop-form').attr('action'), {}, function(data) {
				window.close();
				if (window.opener && !window.opener.closed) {
					parentssform = window.opener.document.getElementById("screenshots-form");
					parentssform.changing_version.value = 1;
					parentssform.submit();
				}
			});
		});
	}

	// change status
	$('.flip').each(function(i, item) {
		$(item).on('click', function(){
			var newi = $($(this).parent()).attr('id').replace('_', '');
			$('#newstate').val(newi);
			$(HUB.ToolsPipeline.statusform).submit();
			return false;
		});
	});

	// Manage license screen
	if ($('#licenseForm').length) {
		HUB.ToolsPipeline.licOptions();

		// Show options depending on open/closed source selection
		$('#t_code').on('change', function() {
			HUB.ToolsPipeline.licOptions();
		});

		$('#templates').on('change', function() {
			var tmpl = $('#templates').find(":selected").attr('value');
			if (tmpl != 'c1') {
				var t = $('#' + tmpl).text();
				var co = $('#license').val(t);
			}
		});
	}

	// Cancel buttons
	$('.showcancel').on('click', function(e){
		e.preventDefault();
		$(HUB.ToolsPipeline.canceltool).show();
		return false;
	});

	$('.hidecancel').on('click', function(e){
		e.preventDefault();
		$(HUB.ToolsPipeline.canceltool).hide();
		return false;
	});

	$('.showmsg').on('click', function(e){
		e.preventDefault();
		$(HUB.ToolsPipeline.commentarea).show();
		return false;
	});

	$('.hidemsg').on('click', function(e){
		e.preventDefault();
		$(HUB.ToolsPipeline.commentarea).hide();
		return false;
	});

	// show groups
	$('.groupchoices').each(function(i, item) {
		$(item).on('change', function(){
			if (this.selectedIndex == (this.length - 1)) {
				$("#groupname").show();
			} else {
				$("#groupname").hide();
			}
		});
	});

	/*$('script').each(function(i, s) {
		if (s.src && s.src.match(/hub\.jquery\.js(\?.*)?$/)) {
			HUB.ToolsPipeline.templatepath = s.src.replace(/js\/hub\.jquery\.js(\?.*)?$/,'');
		}
	});*/

	var cancelaction = $('#cancel-action');

	if (cancelaction.length > 0) {
		cancelaction
			.unbind()
			.on('click', function(e) {
				$($(this).parent()).animate({opacity:0.0}, 500, function() {
					$($(this).parent()).html('');
				});
			});
	}

	// NEW
	/*
	var adminactions = $('.adminaction');
	if (adminactions.length > 0) {
		adminactions.each(function(i, item) {
			$(item).on('click', function() {

				var outcome = $('#ctOutcome');

				var loading = '<p id="fbwrap">' + 
					'<span id="facebookG">' +
					' <span id="blockG_1" class="facebook_blockG"></span>' +
					' <span id="blockG_2" class="facebook_blockG"></span>' +
					' <span id="blockG_3" class="facebook_blockG"></span> ' +
						'Performing your request' +
					'</span>' +
				'</p>';

				outcome.html(loading);

				$.get($(this).attr('href').nohtml(), {}, function(data){
					outcome
						.html(data)
						.prepend($('<span id="cancel-action">&nbsp;</span>'));

					if (cancelaction.length > 0) {
						cancelaction
							.unbind()
							.on('click', function(e) {
								outcome.animate({opacity:0.0}, 500, function() {
									outcome.html('');
								});
							});
					}
				});

				return false;
			});
		});
	}
	*/

	// admin actions
	var admincalls = $('.admincall');
	if (admincalls.length) {
		admincalls.each(function(i, item) {
			$(item).on('click', function (e){
				e.preventDefault();

				$(HUB.ToolsPipeline.success)
					.hide();

				$(HUB.ToolsPipeline.loader)
					.html('')
					.append('<p class="loading"><span class="spinner"></span>' + $(this).attr('data-action-txt') + '</p>')
					.show();

				$.get($(this).attr('href').nohtml(), {}, function(data){
					$(HUB.ToolsPipeline.success)
						.html(data)
						.show();
					$(HUB.ToolsPipeline.loader)
						.hide();
				});

				return false;
			});
		});
	}
});
