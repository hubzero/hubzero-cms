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
		if (history && history.pushState) {
			window.addEventListener("popstate", function(e) {
				$('#hg-dynamic-wrap').html(history.state.html);
			});
		}
		if ($(evt.target).val().replace(/\s+/g, '') == '') {
			tagSugg.hide();
			tagSugg.empty();
			sugg.empty();
		}
		else {
			xhr = $.get('/hubgraph', {
				'task': 'complete',
				'terms': $(evt.target).val()
			}, function(res) {
				if (!res.expression) {
					return;
				}
				var expr = new RegExp(res.expression, 'gi');
				var hl = function(title) {
					if (title.title) {
						title = title.title;
					}
					return title.replace(expr, '<strong>$1</strong>');
				};
				sugg.empty();
				tagSugg.empty();
				$(res.completions).each(function(idx, completion) {
					sugg.append($('<li class="half ' + ((idx/2)&1 ? 'even' : 'odd') + '"><span>' + hl(completion) + '</span></li>').click(function() {
						$(evt.target).val(encodeURIComponent(completion));
						location = location.href.replace(/[?].*/, '') + '?' + $('#search-form').serialize();
					}));
				});
				var baseTags = [], any = false;
				var inv = $('#inventory'), inp = $('.search-bar input');
				var xhr2 = null;
				var ajaxLoad = function() {
					if (xhr2) {
						xhr2.abort();
					}
					inv.addClass('loading');
					xhr2 = $.get(searchBase + '?' + $('#search-form').serialize() + '&task=update', function(res) {
						if (history && history.replaceState) {
							history.replaceState({'html': $('#hg-dynamic-wrap').html()}, null, location.href);
						}
						$('#hg-dynamic-wrap').html(res);
						inv.find('.unapplied').removeClass('unapplied');
						inv.removeClass('loading');
						if (history && history.pushState) {
							history.pushState({'html': res}, null, location.href.replace(/[?].*/, '') + '?' + $('#search-form').serialize());
						}
					});
				};
				var pushInv = function(type, id, title) {
					var dupe = false;
					inv.find('.' + type + ' input').each(function(_idx, ex) {
						if ($(ex).val() == id) {
							dupe = true;
							return false;
						}
					});
					if (dupe) {
						return false;
					}
					var li = $('<li class="' + type + ' unapplied"><input type="hidden" value="' + id + '" name="' + (type == 'section' ? 'domain' : type + 's[]') + '"><label>' + type.charAt(0).toUpperCase() + type.substr(1) + ': </label> ' + title + ' </li>')
						.prepend($('<a class="remove"></a>').click(function() {
							li.remove();
							ajaxLoad();
						}));
					var terms = inp.val().split(/\s+/);
					var newTerms = [];
					for (var idx = 0; idx < terms.length; ++idx) {
						if (title.toLowerCase().indexOf(terms[idx].toLowerCase()) == -1) {
							newTerms.push(terms[idx]);
						}
					}
					inp.val(newTerms.join(' '));
					inp.focus();
					inv.append(li);
					return true;
				};
				var any = false;
				for (var type in res.pins) {
					if (type == 'tags') {
						$(res.pins[type]).each(function(_idx, pin) {
							if (request.tagMap[pin.id]) {
								return;
							}
							any = true;
							tagSugg
								.append(
									$('<li></li>')
										.append(
											$('<button type="submit" name="' + type + '[]" value="' + pin.id + '"></button>')
												.append($('<a></a>').append(hl(pin)))
												.click(function() {
													inp.val(inp.val().replace(/\S+$/, ''));
													if (pushInv(type.replace(/s$/, ''), pin.id, pin.title)) {
														ajaxLoad();
													}
													return false;
												})
										)
								)
							;
						});
					}
					else {
						(function(type) {
							var sel = $('<select></select>')
								.click(function(evt) {
									evt.stopPropagation();
								})
							;
							var li = $('<li></li>')
								.click(function(evt) {
									if (evt.originalEvent && $(evt.originalEvent.target).hasClass('ui-selectmenu-icon')) {
										return;
									}
									var id = sel.val();
									if (pushInv(type.replace(/s$/, ''), id, $('#sugg-' + type + '-' + id.replace(/[~\s]/g, '-')).text())) {
										ajaxLoad();
									}
									return false;
								})
								;
							var btn = $('<div class="quasi-button"></div>')
								.append($('<strong>' + type.charAt(0).toUpperCase() + type.substr(1).replace(/s$/, '') + ': </strong>'))
								.append(sel)
								;	
							var anyThisType = false;
							$(res.pins[type]).each(function(idx, pin) {
								any = anyThisType = true;
								var opt = $('<option value="' + pin.id + '" id="sugg-' + type + '-' + pin.id.replace(/[~\s]/g, '-') + '">' + pin.title + '</option>');
								if (idx == 0) {
									opt.attr('selected', true);
								}
								sel.append(opt);
							});
							if (!anyThisType) {
								return;
							}
							tagSugg.append(li.append(btn));
							tagSugg.show();
							var w = (sel.width() + 20) + 'px';
							sel.css('width', w);
							sel.selectmenu({
								'style': 'popup',
								'format': function(text, item) {
									item.css('width', w);
									return hl(text);
								},
								'menuWidth': w + 20 + 'px',
								'change': function() {
									console.log('change');
									btn.click();
								}
							});
						})(type);
					}
				}
				if (any) {
					tagSugg.prepend($('<li class="label">Suggestions:</li>'));
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
		});
		return false;
	});

	$('.tags.related.cloud').each(function(_, tagEl) {
		tagEl = $(tagEl);
		var cont = $('<div class="tag-graph"></div>');
		try {
			tagEl.replaceWith(cont);

			var tags = [];
			var sourceId = tagEl.attr('data-parent-id');
			var json = {
				nodes: relatedTags.related.map(function(tag) { return { 'name': tag[1], 'id': tag[0], 'base': tag[2] }; }), //[{'name': tagEl.attr('data-parent-name'), 'id': sourceId}],
				links: relatedTags.edges.map(function(edge) { return { 'source': edge[0], 'target': edge[1], 'weight': edge[2] }; })
			};
		
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

			var tick = function() {
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
			};
		
			force
				.nodes(json.nodes)
				.links(json.links)
				.on("tick", tick)
				.start();
		}
		catch (ex) {	
			// doesn't work in some browsers. no big deal though, just show restore the original list
			cont.replaceWith(tagEl);
		}
	});
});
