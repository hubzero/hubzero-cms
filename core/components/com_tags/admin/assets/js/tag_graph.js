var jq = jQuery.noConflict();

jQuery(function(jq)
{
	var $ = jq,
	    w = $('#graph').innerWidth(),
	    h = 600;

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
				text.autocomplete({ source: 'index.php?option=com_tags&controller=relationships&task=suggest&limit=50' });

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

	var center = function(tag)
	{
		$('#graph, #labels, #labeled, #parents, #children').empty();
		$('#metadata-cont').css('display', 'none');
		$('#graph').css('background', 'url(\'/core/components/com_tags/admin/assets/img/throbber.gif\') no-repeat top left');

		var vis = d3.select("#graph")
			.append("svg:svg")
			.attr("width", w)
			.attr("height", h);
		d3.json("/administrator/index.php?option=com_tags&controller=relationships&task=implicit&tag=" + tag, function(json) 
		{
			$('#description').val(json.description);
			$('.tag-id').val(json.id);
			$('.tag-count').text(json.count);
			tag_editors(json);
			$('#metadata-cont').css('display', 'block');
			$('#graph').css('background', '#fff');
			var force = d3.layout.force()
				.charge(-312)
				.linkDistance(250)
				.nodes(json.nodes)
				.links(json.links)
				.size([w, h - 50])
				.start();

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
					.call(force.drag)
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
		$('#graph').css('background', 'url(\'/core/components/com_tags/admin/assets/img/throbber.gif\') no-repeat top left');

		var vis = d3.select("#graph")
			.append("svg:svg")
			.attr("width", w)
			.attr("height", 400);
		console.log(tag);
		d3.json("/administrator/index.php?option=com_tags&controller=relationships&task=hierarchy&tag=" + tag, function(json) 
		{
			$('#description').val(json.description);
			$('.tag-id').val(json.id);
			$('.tag-count').text(json.count);
			tag_editors(json);

			$('#metadata-cont').css('display', 'block');
			$('#graph').css('background', '#fff');
			var force = d3.layout.force()
				.charge(-100)
				.linkDistance(200)
				.nodes(json.nodes)
				.links(json.links)
				.size([w, 350])
				.start();

			var link = vis.selectAll("line.link")
				.data(json.links)
				.enter().append("svg:line")
					.attr("class", "link")
					.style("stroke-width", '1')
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
					.call(force.drag)
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
	$(".tag-entry").autocomplete({ source: 'index.php?option=com_tags&controller=relationships&task=suggest&limit=50' });

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
			'<fieldset>' + 
				'<legend>Show for resource types:</legend>' + 
				'<div class="input-wrap">' + 
					'<select name="types-new-' + new_idx + '[]" id="types-new-' + new_idx + '" multiple="multiple" size="' + window.resourceTypes.length + '">';
					$(window.resourceTypes).each(function(_idx, type)
					{
						html += '<option value="' + type.id + '">' + type.type + '</option>';
					});
					html += '</select>' + 
					'<label><input type="radio" name="mandatory-new-' + new_idx + '" id="mandatory-new-' + new_idx + '-optional" value="optional" /> optional</label><br />' +
					'<label><input type="radio" name="mandatory-new-' + new_idx + '" id="mandatory-new-' + new_idx + '-mandatory" value="mandatory" /> mandatory</label><br />' + 
					'<label><input type="radio" name="mandatory-new-' + new_idx + '" id="mandatory-new-' + new_idx + '-depth" value="depth" /></label> <label for="mandatory-depth-new-' + new_idx + '">until depth:</label><br />' + 
					'<input type="text" name="mandatory-depth-new-' + new_idx + '" id="mandatory-depth-new-' + new_idx + '" />' + 
				'</div>' + 
			'</fieldset>' + 
			'<fieldset>' + 
				'<legend>Selection type:</legend>' + 
				'<div class="input-wrap">' + 
					'<label><input type="radio" name="multiple-new-' + new_idx + '" id="multiple-new-' + new_idx + '-optional" value="optional" /> multiple-select (checkbox)</label><br />' + 
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
