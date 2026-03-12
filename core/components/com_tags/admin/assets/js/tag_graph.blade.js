/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function() {
	var graphEl = document.getElementById('graph');
	var w = graphEl ? graphEl.clientWidth : 800;
	var h = 600;

	window.resourceTypes = [];
	var resourcetypes = document.getElementById('resource-types');

	if (resourcetypes) {
		var cdata = JSON.parse(resourcetypes.innerHTML);
		window.resourceTypes = cdata.types;
	}

	var tagSel = document.getElementById('tag-sel');
	var root = tagSel ? tagSel.getAttribute('action') : '';
	root += (root.indexOf('?') == -1) ? '?' : '&';

	var tag_editors = function(json) {
		var blur_timeout = null;
		var types = ['labeled', 'labels', 'parents', 'children'];

		types.forEach(function(type) {
			var par = document.getElementById(type);
			if (!par) {
				return;
			}

			par.addEventListener('click', function(evt) {
				var mi = document.getElementById('maininput-actags');
				if (mi) {
					mi.remove();
				}

				var text = document.createElement('input');
				text.id = 'maininput-actags';
				text.className = 'maininput';
				text.type = 'text';
				text.autocomplete = 'off';

				par.appendChild(text);
				text.focus();

				// Simple autocomplete via datalist
				var datalistId = 'actags-datalist-' + type;
				var existingDl = document.getElementById(datalistId);
				if (!existingDl) {
					var dl = document.createElement('datalist');
					dl.id = datalistId;
					document.body.appendChild(dl);
					text.setAttribute('list', datalistId);

					text.addEventListener('input', function() {
						var query = text.value;
						if (query.length < 2) {
							return;
						}
						fetch(
							root + 'task=suggest&limit=50&term=' +
							encodeURIComponent(query)
						)
						.then(function(r) { return r.json(); })
						.then(function(suggestions) {
							dl.innerHTML = '';
							if (Array.isArray(suggestions)) {
								suggestions.forEach(function(s) {
									var opt = document.createElement('option');
									opt.value =
										typeof s === 'string' ? s : s.label;
									dl.appendChild(opt);
								});
							}
						});
					});
				} else {
					text.setAttribute('list', datalistId);
				}

				var add = function() {
					var tag = text.value.replace(/,+$/, '');
					text.value = '';
					if (!tag) {
						text.remove();
						return;
					}

					var li = document.createElement('li');
					li.className = 'bit-box';
					li.textContent = tag;
					var inp = document.createElement('input');
					inp.type = 'hidden';
					inp.name = type + '[]';
					inp.value = tag;
					li.appendChild(inp);
					var close = document.createElement('a');
					close.className = 'closebutton';
					close.href = '#';
					close.addEventListener('click', function(evt2) {
						evt2.preventDefault();
						li.remove();
					});
					li.appendChild(close);
					par.appendChild(li);

					par.appendChild(text);
					text.focus();
				};

				text.addEventListener('keyup', function(evt2) {
					if ((evt2.keyCode || evt2.which) == 188) {
						return add();
					}
				});
				text.addEventListener('focus', function() {
					if (blur_timeout) {
						clearInterval(blur_timeout);
					}
				});
				text.addEventListener('blur', function() {
					blur_timeout = setTimeout(function() {
						add();
					}, 400);
				});
			});

			if (json[type]) {
				json[type].forEach(function(v) {
					var li = document.createElement('li');
					li.className = 'bit-box';
					li.textContent = v;
					var inp = document.createElement('input');
					inp.type = 'hidden';
					inp.name = type + '[]';
					inp.value = v;
					li.appendChild(inp);
					var close = document.createElement('a');
					close.className = 'closebutton';
					close.href = '#';
					li.appendChild(close);
					par.appendChild(li);
					close.addEventListener('click', function(evt) {
						evt.preventDefault();
						li.remove();
					});
				});
			}
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

	var force; // shared reference for drag handlers

	var center = function(tag) {
		['graph', 'labels', 'labeled', 'parents', 'children'].forEach(
			function(id) {
				var el = document.getElementById(id);
				if (el) {
					el.innerHTML = '';
				}
			}
		);
		var metaCont = document.getElementById('metadata-cont');
		if (metaCont) {
			metaCont.style.display = 'none';
		}

		var vis = d3.select("#graph")
			.append("svg:svg")
			.attr("width", w)
			.attr("height", h);

		d3.json(root + 'task=implicit&tag=' + tag).then(function(json) {
			var descEl = document.getElementById('description');
			if (descEl) {
				descEl.value = json.description;
			}
			document.querySelectorAll('.tag-id').forEach(function(el) {
				el.value = json.id;
			});
			document.querySelectorAll('.tag-count').forEach(function(el) {
				el.textContent = json.count;
			});
			tag_editors(json);
			if (metaCont) {
				metaCont.style.display = 'block';
			}
			if (graphEl) {
				graphEl.style.background = '#fff';
			}

			force = d3.forceSimulation(json.nodes)
				.force(
					"charge", d3.forceManyBody().strength(-312)
				)
				.force("link", d3.forceLink()
					.distance(250)
					.links(json.links))
				.force("center", d3.forceCenter(w / 2, (350 / 2) - 50));

			var centerNodeEl = document.getElementById('center-node');

			var link = vis.selectAll("line.link")
				.data(json.links)
				.enter().append("svg:line")
					.attr("class", "link")
					.style("stroke-width", function(d) {
						return Math.max(1, Math.sqrt(200 * d.value));
					})
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
					.style("fill", function(d) {
						var cv = centerNodeEl ? centerNodeEl.value : '';
						return d.tag == cv || d.raw_tag == cv
							? '#79a' : '#cdf';
					})
					.call(d3.drag()
						.on("start", dragstart)
						.on("drag", dragmove)
						.on("end", dragend))
					.on('click', function(n) {
						if (centerNodeEl) {
							centerNodeEl.value = n.raw_tag;
						}
						if (tagSel) {
							tagSel.dispatchEvent(
								new Event('submit', { cancelable: true })
							);
						}
					});

			var labels = vis.selectAll('circle.node')
				.data(json.nodes)
				.enter().append('svg:text')
					.attr('font-size', '10px')
					.text(function(d) { return d.raw_tag; });

			node.append("svg:title")
				.text(function(d) {
					return 'center graph on ' + d.raw_tag;
				});

			vis.style("opacity", 1e-6)
				.transition()
				.duration(1000)
				.style("opacity", 1);

			force.on("tick", function() {
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

	var center_hierarchy = function(tag) {
		['graph', 'labels', 'labeled', 'parents', 'children'].forEach(
			function(id) {
				var el = document.getElementById(id);
				if (el) {
					el.innerHTML = '';
				}
			}
		);
		var metaCont = document.getElementById('metadata-cont');
		if (metaCont) {
			metaCont.style.display = 'none';
		}

		var vis = d3.select("#graph")
			.append("svg:svg")
			.attr("width", w)
			.attr("height", 400);

		d3.json(root + 'task=hierarchy&tag=' + tag).then(function(json) {
			var descEl = document.getElementById('description');
			if (descEl) {
				descEl.value = json.description;
			}
			document.querySelectorAll('.tag-id').forEach(function(el) {
				el.value = json.id;
			});
			document.querySelectorAll('.tag-count').forEach(function(el) {
				el.textContent = json.count;
			});
			tag_editors(json);

			if (metaCont) {
				metaCont.style.display = 'block';
			}
			if (graphEl) {
				graphEl.style.background = '#fff';
			}

			var centerNodeEl = document.getElementById('center-node');

			force = d3.forceSimulation(json.nodes)
				.force(
					"charge", d3.forceManyBody().strength(-100)
				)
				.force("link", d3.forceLink()
					.distance(200)
					.links(json.links))
				.force("center", d3.forceCenter(w / 2, 350 / 2));

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
					.style("fill", function(d) {
						var cv = centerNodeEl ? centerNodeEl.value : '';
						if (d.tag == cv || d.raw_tag == cv) {
							return '#79a';
						}
						if (d.type === 'parent') {
							return '#fdc';
						}
						if (d.type === 'label') {
							return '#cfd';
						}
						return '#cdf';
					})
					.call(d3.drag()
						.on("start", dragstart)
						.on("drag", dragmove)
						.on("end", dragend))
					.on('click', function(n) {
						if (centerNodeEl) {
							centerNodeEl.value = n.raw_tag;
						}
						if (tagSel) {
							tagSel.dispatchEvent(
								new Event('submit', { cancelable: true })
							);
						}
					});

			var labels = vis.selectAll('circle.node')
				.data(json.nodes)
				.enter().append('svg:text')
					.attr('font-size', '10px')
					.text(function(d) { return d.raw_tag; });

			node.append("svg:title")
				.text(function(d) {
					return 'center graph on ' + d.raw_tag;
				});

			vis.style("opacity", 1e-6)
				.transition()
				.duration(1000)
				.style("opacity", 1);

			force.on("tick", function() {
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

	if (tagSel) {
		tagSel.addEventListener('submit', function(evt) {
			evt.preventDefault();
			var centerNodeEl = document.getElementById('center-node');
			var tag = centerNodeEl
				? centerNodeEl.value.toLowerCase().replace(/[^a-z0-9]/g, '')
				: '';
			var implicitRadio = document.getElementById('implicit');
			if (implicitRadio && implicitRadio.checked) {
				center(tag);
			} else {
				center_hierarchy(tag);
			}
		});

		var centerNodeEl = document.getElementById('center-node');
		if (centerNodeEl && centerNodeEl.value) {
			tagSel.dispatchEvent(new Event('submit', { cancelable: true }));
		}
	}

	// Simple autocomplete for .tag-entry inputs using datalist
	var tagEntries = document.querySelectorAll('.tag-entry');
	tagEntries.forEach(function(input, idx) {
		var dlId = 'tag-entry-datalist-' + idx;
		var dl = document.createElement('datalist');
		dl.id = dlId;
		document.body.appendChild(dl);
		input.setAttribute('list', dlId);

		input.addEventListener('input', function() {
			var query = input.value;
			if (query.length < 2) {
				return;
			}
			fetch(
				root + 'task=suggest&limit=50&term=' +
				encodeURIComponent(query)
			)
			.then(function(r) { return r.json(); })
			.then(function(suggestions) {
				dl.innerHTML = '';
				if (Array.isArray(suggestions)) {
					suggestions.forEach(function(s) {
						var opt = document.createElement('option');
						opt.value = typeof s === 'string' ? s : s.label;
						dl.appendChild(opt);
					});
				}
			});
		});
	});

	var formIdx = document.querySelectorAll('fieldset.adminform').length;
	var newIdx = 0;

	var addGroupBtn = document.getElementById('add_group');
	if (addGroupBtn) {
		addGroupBtn.addEventListener('click', function(evt) {
			++newIdx;
			++formIdx;
			evt.preventDefault();
			var html =
				'<fieldset class="adminform" id="group-' + formIdx + '">' +
				'<legend><span>Group</span></legend>' +
				'<div class="input-wrap">' +
					'<label for="name-new-' + newIdx + '">' +
						'Group name:</label>' +
					'<input type="text" name="name-new-' + newIdx +
						'" id="name-new-' + newIdx + '" />' +
				'</div>' +
				'<fieldset>' +
					'<legend>Show for resource types:</legend>' +
					'<div class="input-wrap">' +
						'<select name="types-new-' + newIdx +
							'[]" id="types-new-' + newIdx +
							'" multiple="multiple" size="' +
							window.resourceTypes.length + '">';

			window.resourceTypes.forEach(function(type) {
				html += '<option value="' + type.id + '">' +
					type.type + '</option>';
			});

			html += '</select>' +
				'<label><input type="radio" name="mandatory-new-' + newIdx +
					'" id="mandatory-new-' + newIdx +
					'-optional" value="optional" /> optional</label><br />' +
				'<label><input type="radio" name="mandatory-new-' + newIdx +
					'" id="mandatory-new-' + newIdx +
					'-mandatory" value="mandatory" />' +
					' mandatory</label><br />' +
				'<label><input type="radio" name="mandatory-new-' + newIdx +
					'" id="mandatory-new-' + newIdx +
					'-depth" value="depth" /></label>' +
					' <label for="mandatory-depth-new-' + newIdx +
					'">until depth:</label><br />' +
				'<input type="text" name="mandatory-depth-new-' + newIdx +
					'" id="mandatory-depth-new-' + newIdx + '" />' +
				'</div>' +
				'</fieldset>' +
				'<fieldset>' +
					'<legend>Selection type:</legend>' +
					'<div class="input-wrap">' +
						'<label><input type="radio" name="multiple-new-' +
							newIdx + '" id="multiple-new-' + newIdx +
							'-multiple" value="multiple" />' +
							' multiple-select (checkbox)</label><br />' +
						'<label><input type="radio" name="multiple-new-' +
							newIdx + '" id="multiple-new-' + newIdx +
							'-single" value="single" />' +
							' single-select (radio) </label><br />' +
						'<label><input type="radio" name="multiple-new-' +
							newIdx + '" id="multiple-new-' + newIdx +
							'-depth" value="depth" />' +
							' single-select</label>' +
							' <label for="multiple-depth-new-' + newIdx +
							'">until depth: </label><br />' +
						'<input type="text" name="multiple-depth-new-' +
							newIdx + '" id="multiple-depth-new-' +
							newIdx + '" />' +
					'</div>' +
				'</fieldset>' +
				'<div class="input-wrap">' +
					'<button class="delete-group" id="delete-' + formIdx +
						'" rel="group-' + formIdx + '">' +
						'Delete group</button>' +
				'</div>' +
			'</fieldset>';

			var fas = document.getElementById('fas');
			if (fas) {
				fas.insertAdjacentHTML('beforeend', html);
			}

			var deleteBtn = document.getElementById('delete-' + formIdx);
			if (deleteBtn) {
				deleteBtn.addEventListener('click', function(evt2) {
					evt2.preventDefault();
					var target = document.getElementById(
						deleteBtn.getAttribute('rel')
					);
					if (target) {
						target.remove();
					}
				});
			}

			var nameInput = document.getElementById('name-new-' + newIdx);
			if (nameInput) {
				nameInput.focus();
			}
		});
	}

	var deleteGroupBtns = document.querySelectorAll('.delete-group');
	deleteGroupBtns.forEach(function(btn) {
		btn.addEventListener('click', function(evt) {
			evt.preventDefault();
			var target = document.getElementById(
				btn.getAttribute('rel')
			);
			if (target) {
				target.remove();
			}
		});
	});
});
