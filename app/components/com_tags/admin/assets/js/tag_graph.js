/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

var jq = jQuery.noConflict();

jQuery(function(jq)
{
	var $ = jq,
	    w = $('#graph').innerWidth(),
	    h = 600;

	window.resourceTypes = new Array;
	var resourcetypes = $('#resource-types');

	if (resourcetypes.length) {
		var cdata = JSON.parse(resourcetypes.html());

		window.resourceTypes = cdata.rtypes;
	}
	
	window.publicationTypes = new Array;
	var	publicationtypes = $('#publication-types');

	if (publicationtypes.length) {
		var cdata = JSON.parse(publicationtypes.html());

		window.publicationTypes = cdata.ptypes;
	}

	var root = $('#tag-sel').length > 0 ? $('#tag-sel').attr('action') : '';
	root += (root.indexOf('?') == -1) ? '?' : '&';

	var tag_editors = function(json)
	{
		var blur_timeout = null;
		$(['labeled', 'labels', 'parents', 'children']).each(function(idx, type)
		{
			var par = $('#' + type);
			par.on('click', function(evt)
			{
				var mi = $('#maininput-actags');
				if (mi) {
					mi.remove();
				}

				var text = $('<input id="maininput-actags" class="maininput" type="text" autocomplete="off" />');
				text.autocomplete({ source: root + 'task=suggest&limit=50' });

				par.append(text);
				text.focus();

				var add = function()
				{
					var tag = text.val().replace(/,+$/, '');
					text.val('');
					if (!tag)
					{
						text.remove();
						return;
					}

					var li = $('<li class="bit-box"></li>');
					li.text(tag);
					var inp = $('<input type="hidden" name="' + type + '[]" value="' + tag + '" />');
					li.append(inp);
					var close = $('<a class="closebutton" href="#"></a>');
					close.on('click', function(evt)
					{
						evt.preventDefault();
						par[0].removeChild(li[0]);
					});
					li.append(close);
					par.append(li);

					par.append(text);
					text.focus();
				};

				text.keyup(function(evt)
				{
					if ((evt.keyCode || evt.which) == 188) {
						return add();
					}
				});
				text.focus(function()
				{
					if (blur_timeout) {
						clearInterval(blur_timeout);
					}
				});
				text.blur(function()
				{
					blur_timeout = setTimeout(function()
					{
						add();
					}, 400);
				});
			});

			$(json[type]).each(function(k, v)
			{
				var li = $('<li class="bit-box"></li>');
				li.text(v);
				var inp = $('<input type="hidden" name="' + type + '[]" value="' + v + '" />');
				li.append(inp);
				var close = $('<a class="closebutton" href="#"></a>');
				li.append(close);
				par.append(li);
				close.on('click', function(evt)
				{
					evt.preventDefault();
					par[0].removeChild(li[0]);
				});
			});
		});
	};

	function dragstart(d) {
		if (!d3.event.active) {
			force.alphaTarget(0.3).restart();
		}
		d.fx = d.x;
		d.fy = d.y;
	}

	function dragmove(d) {
		d.fx = d3.event.x;
		d.fy = d3.event.y;
	}

	function dragend(d) {
		if (!d3.event.active) {
			force.alphaTarget(0);
		}
	}

	var center = function(tag)
	{
		$('#graph, #labels, #labeled, #parents, #children').empty();
		$('#metadata-cont').css('display', 'none');

		var vis = d3.select("#graph")
			.append("svg:svg")
			.attr("width", w)
			.attr("height", h);
		d3.json(root + 'task=implicit&tag=' + tag).then(function(json)
		{
			$('#description').val(json.description);
			$('.tag-id').val(json.id);
			$('.tag-count').text(json.count);
			tag_editors(json);
			$('#metadata-cont').css('display', 'block');
			$('#graph').css('background', '#fff');
			var force = d3.forceSimulation(json.nodes)
				.force("charge", d3.forceManyBody().strength(-312))
				.force("link", d3.forceLink()
					.distance(250)
					.links(json.links))
				//.size([w, h - 50])
				.force("center", d3.forceCenter(w /2, (350 / 2) - 50));
				//.start();

			var link = vis.selectAll("line.link")
				.data(json.links)
				.enter().append("svg:line")
					.attr("class", "link")
					.style("stroke-width", function(d) { return Math.max(1, Math.sqrt(200 * d.value)); })
					.attr("x1", function(d) { return d.source.x; })
					.attr("y1", function(d) { return d.source.y; })
					.attr("x2", function(d) { return d.target.x; })
					.attr("y2", function(d) { return d.target.y; });

			var node = vis.selectAll("circle.node")
				.data(json.nodes)
				.enter().append("svg:ellipse")
					.attr("class", "node")
					.attr("cx", function(d) { return d.x; })
					.attr("cy", function(d) { return d.y; })
					.attr("rx", 5)
					.attr('ry', 5)
					.style("fill", function(d) { return d.tag == $('#center-node').val() || d.raw_tag == $('#center-node').val() ? '#79a' : '#cdf'; })
					.call(d3.drag()
						.on("start", dragstart)
						.on("drag", dragmove)
						.on("end", dragend))
					.on('click', function(n)
					{
						$('#center-node').val(n.raw_tag);
						$('#tag-sel').submit();
					});

			var labels = vis.selectAll('circle.node')
			.data(json.nodes)
			.enter().append('svg:text')
				.attr('font-size', '10px')
				.text(function(d) { return d.raw_tag; });

			node.append("svg:title")
				.text(function(d) { return 'center graph on ' + d.raw_tag; });

			vis.style("opacity", 1e-6)
				.transition()
				.duration(1000)
				.style("opacity", 1);

			force.on("tick", function()
			{
				link
					.attr("x1", function(d) { return d.source.x; })
					.attr("y1", function(d) { return d.source.y; })
					.attr("x2", function(d) { return d.target.x; })
					.attr("y2", function(d) { return d.target.y; });

				node
					.attr("cx", function(d) { return d.x; })
					.attr("cy", function(d) { return d.y; });

				labels
					.attr("x", function(d) { return d.x + 7; })
					.attr("y", function(d) { return d.y + 2.5; });
			});
		});
	};

	var center_hierarchy = function(tag)
	{
		$('#graph, #labels, #labeled, #parents, #children').empty();
		$('#metadata-cont').css('display', 'none');

		var vis = d3.select("#graph")
			.append("svg:svg")
			.attr("width", w)
			.attr("height", 400);

		d3.json(root + 'task=hierarchy&tag=' + tag).then(function(json)
		{

			$('#description').val(json.description);
			$('.tag-id').val(json.id);
			$('.tag-count').text(json.count);
			tag_editors(json);

			$('#metadata-cont').css('display', 'block');
			$('#graph').css('background', '#fff');
			var force = d3.forceSimulation(json.nodes)
				.force("charge", d3.forceManyBody().strength(-100))
				.force("link", d3.forceLink()
					.distance(200)
					.links(json.links))
				.force("center", d3.forceCenter(w /2, 350 / 2));

			var link = vis.selectAll("line.link")
				.data(json.links)
				.enter().append("svg:line")
					.attr("class", "link")
					.style("stroke-width", '1')
					.attr("x1", function(d) { return d.source.x; })
					.attr("y1", function(d) { return d.source.y; })
					.attr("x2", function(d) { return d.target.x; })
					.attr("y2", function(d) { return d.target.y; });

			var visc = document.querySelector("#graph>canvas");

			var node = vis.selectAll("circle.node")
				.data(json.nodes)
				.enter().append("svg:ellipse")
					.attr("class", "node")
					.attr("cx", function(d) { return d.x; })
					.attr("cy", function(d) { return d.y; })
					.attr("rx", 5)
					.attr('ry', 5)
					.style("fill", function(d)
					{
						return d.tag == $('#center-node').val() || d.raw_tag == $('#center-node').val()
							? '#79a'
							: d.type === 'parent'
								? '#fdc'
								: d.type === 'label'
								 ? '#cfd'
								 : '#cdf';
					})
					.call(d3.drag()
						.on("start", dragstart)
						.on("drag", dragmove)
						.on("end", dragend))
					.on('click', function(n)
					{
						$('#center-node').val(n.raw_tag);
						$('#tag-sel').submit();
					});

			var labels = vis.selectAll('circle.node')
				.data(json.nodes)
				.enter().append('svg:text')
					.attr('font-size', '10px')
					.text(function(d) { return d.raw_tag; });

			node.append("svg:title")
				.text(function(d) { return 'center graph on ' + d.raw_tag; });

			vis.style("opacity", 1e-6)
				.transition()
				.duration(1000)
				.style("opacity", 1);

			force.on("tick", function()
			{
				link
					.attr("x1", function(d) { return d.source.x; })
					.attr("y1", function(d) { return d.source.y; })
					.attr("x2", function(d) { return d.target.x; })
					.attr("y2", function(d) { return d.target.y; });

				node
					.attr("cx", function(d) { return d.x; })
					.attr("cy", function(d) { return d.y; });

				labels
					.attr("x", function(d) { return d.x + 7; })
					.attr("y", function(d) { return d.y + 2.5; });
			});
		});
	};

	$('#tag-sel').on('submit', function(evt) {
		evt.preventDefault();
		var tag = $('#center-node').val().toLowerCase().replace(/[^a-z0-9]/g, '');
		if ($("input[name=relationship]:checked").attr('id') === 'implicit') {
			center(tag);
		} else {
			center_hierarchy(tag);
		}
	});
	if ($('#center-node').val()) {
		$('#tag-sel').submit();
	}

	$.ui.autocomplete.prototype._renderItem = function(ul, item) {
		var term = this.term.split(' ').join('|');
		var re = new RegExp("(" + term + ")", "gi");
		return $("<li></li>")
			.data("item.autocomplete", item)
			.append("<a>" + item.label.replace(re, "<span class=\"highlight\">$1</span>") + "</a>")
			.appendTo(ul);
	};
	$(".tag-entry").autocomplete({ source: root + 'task=suggest&limit=50' });

	var form_idx = $('fieldset.adminform').length,
		new_idx = 0;

	$('#add_group').on('click', function(evt) {
		++new_idx;
		++form_idx;
		evt.preventDefault();
		var html = '<fieldset class="adminform" id="group-' + form_idx + '">' +
		'<legend><span>Group</span></legend>' +
			'<div class="input-wrap">' +
				'<label for="name-new-' + new_idx + '">Group name:</label>' +
				'<input type="text" name="name-new-' + new_idx + '" id="name-new-' + new_idx + '" />' +
			'</div>' +
			'<div class="input-wrap">' +
				'<label for="label-new-' + new_idx + '">Label:</label>' +
				'<input type="text" name="label-new-' + new_idx + '" id="label-new-' + new_idx + '" />' +
			'</div>' +
			'<div class="input-wrap">' +
				'<label for="about-new-' + new_idx + '">About:</label>' +
				'<textarea name="about[' + new_idx + ']" id="about- ' + new_idx + '" cols="50" rows="5"></textarea>' +
			'</div>' +
			'<fieldset>' +
				'<legend>Show for resource types:</legend>' +
				'<div class="input-wrap">' +
					'<select name="rtypes-new-' + new_idx + '[]" id="rtypes-new-' + new_idx + '" multiple="multiple" size="' + window.resourceTypes.length + '">';
					$(window.resourceTypes).each(function(_idx, type)
					{
						html += '<option value="' + type.id + '">' + type.type + '</option>';
					});
					html += '</select>' +
					'</div>' +
			'</fieldset>' +
			'<fieldset>' +
				'<legend>Show for publication types:</legend>' +
				'<div class="input-wrap">' +
					'<select name="ptypes-new-' + new_idx + '[]" id="ptypes-new-' + new_idx + '" multiple="multiple" size="' + window.publicationTypes.length + '">';
					$(window.publicationTypes).each(function(_idx, type)
					{
						html += '<option value="' + type.id + '">' + type.type + '</option>';
					});
					html += '</select>' +
				'</div>' +
			'</fieldset>' +
			'<fieldset>' +
				'<legend>Requirement:</legend>' +
				'<div class="input-wrap">' +
					'<label><input type="radio" name="mandatory-new-' + new_idx + '" id="mandatory-new-' + new_idx + '-optional" value="optional" /> optional</label><br />' +
					'<label><input type="radio" name="mandatory-new-' + new_idx + '" id="mandatory-new-' + new_idx + '-mandatory" value="mandatory" /> mandatory</label><br />' +
					'<label><input type="radio" name="mandatory-new-' + new_idx + '" id="mandatory-new-' + new_idx + '-depth" value="depth" /></label> <label for="mandatory-depth-new-' + new_idx + '">until depth:</label><br />' +
					'<input type="text" name="mandatory-depth-new-' + new_idx + '" id="mandatory-depth-new-' + new_idx + '" />' +
				'</div>' +
			'</fieldset>' +
			'<fieldset>' +
				'<legend>Selection type:</legend>' +
				'<div class="input-wrap">' +
					'<label><input type="radio" name="multiple-new-' + new_idx + '" id="multiple-new-' + new_idx + '-multiple" value="multiple" /> multiple-select (checkbox)</label><br />' +
					'<label><input type="radio" name="multiple-new-' + new_idx + '" id="multiple-new-' + new_idx + '-single" value="single" /> single-select (radio) </label><br />' +
					'<label><input type="radio" name="multiple-new-' + new_idx + '" id="multiple-new-' + new_idx + '-depth" value="depth" /> single-select</label> <label for="multiple-depth-new-' + new_idx + '">until depth: </label><br />' +
					'<input type="text" name="multiple-depth-new-' + new_idx + '" id="multiple-depth-new-' + new_idx + '" />' +
				'</div>' +
			'</fieldset>' +
			'<div class="input-wrap">' +
				'<button class="delete-group" id="delete-' + form_idx + '" rel="group-' + form_idx + '">Delete group</button>' +
			'</div>' +
		'</fieldset>';
		var li = $(html);

		$('#fas').append(li);

		$('#delete-' + form_idx).on('click', function(evt) {
			evt.preventDefault();
			li.remove();
		})

		$('#name-new-' + new_idx).focus();
	});
	$('.delete-group').on('click', function(evt) {
		evt.preventDefault();
		$('#' + $(this).attr('rel')).remove();
	});
});
