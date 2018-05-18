/**
 * @package     hubzero-cms
 * @file        components/com_careerplans/assets/js/goals.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function (jq) {
	var $ = jq;

	var fieldsets = $('.goals');

	if (fieldsets.length > 0) {
		fieldsets.each(function(i, el){
			var fieldset = $(el);

			var btn = $('<p class="btn-wrap"><a class="goals-add add icon-add tooltips" href="#" title="Add goal">Add goal</a></p>').on('click', function(e){
				e.preventDefault();

				/*var grp = fieldset
					.find('.goals-field-wrap')
					.last()
					.clone();
				grp.find('input').each(function(){
					this.name = this.name.replace(/\[(\d+)\]/,function(str,p1){return '[' + (parseInt(p1,10)+1) + ']';});
					this.value = '';
				});
				grp.find('select').each(function(){
					this.name = this.name.replace(/\[(\d+)\]/,function(str,p1){return '[' + (parseInt(p1,10)+1) + ']';});
					this.selectedIndex = 0;
				});
				if (!grp.find('.goals-remove').length) {
					var rmv = $('<a class=\"goals-remove icon-remove\" href=\"#\">Remove</a>');
					grp.append(rmv);
				}
				grp.appendTo(fieldset);

				fieldset.find('.goals-remove')
					.off('click')
					.on('click', function(e){
						e.preventDefault();
						$(this).parent().remove();
					});*/
				var source   = $('#new-goal-row-' + fieldset.attr('data-name')).html(),
					template = Handlebars.compile(source),
					context  = {
						"index"  : fieldset.find('.goals-field-wrap').length
					},
					html = template(context);
					fieldset.append(html);
			});

			fieldset.after(btn);

			fieldset
				.on('click', '.goals-strategy-add', function(e){
					e.preventDefault();

					var wrap = $(this).closest('.goals-strategies'),
						index = parseInt(wrap.attr('data-index'));

					var source   = $('#new-strategy-row-' + fieldset.attr('data-name')).html(),
						template = Handlebars.compile(source),
						context  = {
							"index"   : $(this).attr('data-index'),
							"index2"  : index, //wrap.find('.goal-strategy-row').length, //$($(this).parent().parent()).find('.goal-strategy-row').length,
							"content" : "",
							"completed" : "",
							"id" : ""
						},
						html = template(context);

					//$(html).insertBefore($($(this).parent()));
					wrap.append(html);
					wrap.attr('data-index', index + 1);

					//$('.datepicker').datepicker('refresh');
					initDatepicker();
				})
				.on('click', '.goals-strategy-remove', function(e){
					e.preventDefault();

					$(this).closest('.goal-strategy-row').remove();
				})
				.on('click', '.goals-remove', function(e){
					e.preventDefault();

					$(this).closest('.goals-field-wrap').remove();
				});


			fieldset.find('.goals-strategies').each(function(i, strt){
				strt = $(strt);
				/*if (!strt.find('.goals-strategy-add').length) {
					var add = $('<p class="btn-wrap"><a class="goals-strategy-add add icon-add" data-index="' + i + '" href="#">Add strategy</a></p>');

					strt.append(add);
				}*/
				if (!strt.find('.goals-strategy-add').length) {
					var add = $('<a class="goals-strategy-add add icon-add tooltips" data-index="' + i + '" href="#" title="Add strategy">Add strategy</a>');

					$(strt.find('.omega')[0]).append(add);
				}
			});

			/*fieldset.find('.goals-field-wrap').each(function(i, grp){
				if (i === 0) {
					return;
				}
				grp = $(grp);
				if (!grp.find('.goals-remove').length) {
					var rmv = $('<a class="goals-remove icon-trash delete tooltips" href="#" title="Remove goal">Remove goal</a>').on('click', function(e){
						e.preventDefault();
						console.log('what?');
						//$(this).parent().remove();
						grp.remove();
					});
					grp.append(rmv);
				}
			});*/
		});
	}

	/*$('document').on('focus', '.datepicker', function(){
		$(this).datepicker({
			dateFormat: 'yy-mm-dd'
		});
	});
	$('.datepicker').datepicker({
		dateFormat: 'yy-mm-dd'
	});*/
	function initDatepicker()
	{
		$('.datepicker').datepicker('destroy');
		$('.datepicker').datepicker({
			dateFormat: 'yy-mm-dd'
		});
	}

	initDatepicker();
});
