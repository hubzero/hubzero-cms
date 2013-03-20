jQuery(function($) {
	var throbber = new Image();
	throbber.src = '/components/com_hubgraph/resources/throbber.gif';

	var request = {
		'tagString': '',
		'tags': [],
		'tagMap': {}
	};
	var query = location.search;

	var ma;
	if (ma = query.match(/tags\[\]=([\d,]+)/)) {
		request.tagString = ma[1];
		request.tags = ma[1].split(',');
		$(request.tags).each(function(_, tag) {
			request.tagMap[tag] = true;
		});
	}

	var   sugg = $('#suggestions'),
		tagSugg = $('#tag-suggestions'),
	       xhr;
	$('.search-bar input').keyup(function(evt) {
		if (xhr) {
			xhr.abort();
		}
		if ($(evt.target).val().replace(/\s+/g, '') == '') {
			tagSugg.hide();
			tagSugg.empty();
			sugg.empty();
		}
		else {
			xhr = $.get(searchBase, {
				'task': 'complete',
				'terms': $(evt.target).val()
			}, function(res) {
				sugg.empty();
				tagSugg.empty();
				$(res.completions).each(function(idx, completion) {
					sugg.append($('<li class="half ' + ((idx/2)&1 ? 'even' : 'odd') + '"><span>' + completion + '</span></li>').click(function() {
						var terms = encodeURIComponent(completion.replace(/<.*?>/g, ''));
						if (location.search.indexOf('terms=') == -1) {
							location = location.toString() + (location.search ? '&' : '?') + 'terms=' + terms;
						}
						else {
							location = location.toString().replace(/terms=[^&]*/, 'terms=' + terms);
						}
					}));
				});
				var baseTags = [], any = false;
				$(res.tags).each(function(idx, tag) {
					if (!request.tagMap[tag.id]) {
						any = true;
						var btn = $('<button type="submit" name="tags[]" value="' + tag.id + '">' + tag.title + '</button>').click(function() {
							$('.search-bar input').val('');
						});
						tagSugg.append($('<li></li>').append(btn));
					}
				});
				if (any) {
					tagSugg.show();
				}
				else {
					tagSugg.hide();
				}
			});
		}
	});

	$('.sort').show();
	var currentSort = {};
	$('.sort').click(function(evt) {
		var btn = $(evt.target);
		var prnt = btn.parent();
		var key = prnt.text();
		if (!currentSort[key]) {
			currentSort[key] = ['number', 'desc'];
		}
		var newSort = btn.hasClass('alpha') ? 'alpha' : 'number';
		currentSort[key] = [newSort, newSort == currentSort[key][0] ? (currentSort[key][1] == 'asc' ? 'desc' : 'asc') : newSort == 'alpha' ? 'asc' : 'desc'];
		console.log(currentSort[key]);
		var list = prnt.next();
		var items = list.children('li');
		list.append(items.sort(function(a, b) {
			var at, bt, rv;
			if (currentSort[key][0] == 'number') {
				at = $(a).text().match(/(\d+)$/)[1]*1, bt = $(b).text().match(/(\d+)$/)[1]*1;
				rv = at > bt ? 1 : at == bt ? 0 : -1;
			}
			if (!rv) {
				at = $(a).text().toLowerCase(), bt = $(b).text().toLowerCase();
				rv = at > bt ? 1 : at == bt ? 0 : -1;
				if (currentSort[key][0] == 'number') {
					return rv;
				}
			}
			return rv * (currentSort[key][1] == 'asc' ? 1 : -1);
		}));
		
	});
	$('.domains').css('max-height', 'none');
	$('button.domain').each(function(_, btn) {
		btn = $(btn);
		var prnt = btn.parent();
		var list = prnt.children('ol');
		if (list.length) {
			prnt.addClass('expandable');
			var expand = $('<span class="expand">+</span>');
			prnt.prepend(expand);
			list.hide();
			var toggle = function(animate) {
				if (animate) {
					list.toggle('slow');
				}
				else {
					list.toggle();
				}
				expand.text(expand.text() == '+' ? '-' : '+');
			};
			expand.click(toggle);
			if (btn.hasClass('current')) {
				toggle(false);
			}
		}
	});
	if ($('.search-bar .terms').val() == '') {
		$('.search-bar .terms').focus();
	}

	$('.related').show();
	$('.related').click(function(evt) {
		evt.stopPropagation();
		var btn = $(evt.target);
		btn.addClass('throb');
		var qualifiedId = btn.val().split(':');
		btn.attr('disabled', 'disabled');
		$.get(searchBase, {
			'task': 'getRelated',
			'domain': qualifiedId[0],
			'id': qualifiedId[1]
		}, function(res) {
			var related = $('<ol class="related"></ol>');
			btn.parent().append(related);
			btn.attr('disabled', 'disabled');
			btn.removeClass('throb');
			btn.addClass('used');
			$(res).each(function(_, res) {
				related.append($('<li>' + res.domain[0].toUpperCase() + res.domain.substr(1)  + (res.type ? ' &ndash; ' + res.type : '') + ': <a href="' + res.link +'">' + res.title + '</a></li>'));
			});
			console.log(res);
		});
		return false;
	});

	$('.tags.related').each(function(_, tagEl) {
		var tags = [];
		tagEl = $(tagEl);
		var sourceId = tagEl.attr('data-parent-id');
		var json = {
			nodes: relatedTags.related.map(function(tag) { return { 'name': tag[1], 'id': tag[0], 'base': tag[2] }; }), //[{'name': tagEl.attr('data-parent-name'), 'id': sourceId}],
			links: relatedTags.edges.map(function(edge) { return { 'source': edge[0], 'target': edge[1], 'weight': edge[2] }; })
		};
		
		var cont = $('<div class="tag-graph"></div>');
		tagEl.replaceWith(cont);


var w = cont.width(),
    h = cont.height(),
    r = 6,
    z = d3.scale.category20c();

var force = d3.layout.force()
    .gravity(0.06) // force drawing vertices to clump in the center
    .charge(-30) // repulsion (or if positive, attraction) between vertices
	 .alpha(0.2)
    .linkDistance(function(e) {
		 return h * Math.pow(1 - e.weight, 4);
	 })
    .size([w, h]);

var svg = d3.select(cont[0]).append("svg:svg")
    .attr("width", w)
    .attr("height", h)
  .append("svg:g");

  var link = svg.selectAll("line")
      .data(json.links)
    .enter().append("svg:line");

  var node = svg.selectAll("circle")
      .data(json.nodes)
    .enter() 
	 .append("g")
	 	.attr('class', 'node')
		.on('click', function(d) { 
			var query = decodeURIComponent(location.search);
			if (query.indexOf('tags[]=' + d.id) >= 0) {
				location.search = query.replace(new RegExp('&?tags\\[\\]=' + d.id), '');
			}
			else {
				location.search = query + '&tags[]=' + d.id;
			}
		})
      .call(force.drag);
	 
	 node.append("svg:circle")
      .attr("r", r - .75)
      .style("fill", function(d) { return d.base ? '#006699' : '#88ddff'; })
      .style("stroke", function(d) { return d3.rgb(z(d.group)).darker(); });

node.append('svg:text')
			.attr('font-size', '10px')
			.attr('x', '7')
			.attr('y', '4')
			.text(function(d) { return d.name; });

  force
      .nodes(json.nodes)
      .links(json.links)
      .on("tick", tick)
      .start();

  function tick() {
	  var normX = function(x) {
			return Math.max(r, Math.min(w - r, x));
	  };
	  var normY = function(y) {
			return Math.max(r, Math.min(h - r, y));
	  };
node.attr("transform", function(d) { 
	return "translate(" + normX(d.x) + "," + normY(d.y) + ")"; 
});

    link.attr("x1", function(d) { return normX(d.source.x); })
        .attr("y1", function(d) { return normY(d.source.y); })
        .attr("x2", function(d) { return normX(d.target.x); })
        .attr("y2", function(d) { return normY(d.target.y); });
  }
	});
});
