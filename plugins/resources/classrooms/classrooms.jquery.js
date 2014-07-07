jQuery(function() {
	console.log('...');
	var dataSrc = $('#cluster-data'),
	       prnt = dataSrc.parent(),
	       info = $('<div class="tooltip info"></div>'),
	      nodes = dataSrc.data('seed'),
	   coreTool = dataSrc.data('tool');

	var threshold = $('<select></select>');
	for (var idx = 0; idx <= 100; idx += 5) {
		threshold.append($('<option value=' + idx + '>' + idx + '%</option>'));
	}

	var render = function(nodes) {
		var    margin = { 'top': 0, 'right': 0, 'bottom': 0, 'left': 0 },
		           ex = $('.cluster'),
		     cellDims = { 'x': 6, 'y': 6 },
		          min = Infinity,
		          max = -Infinity,
		   clusterMin = {},
		   clusterIds = {}
		         days = [],
		       dayMap = {},
		      userMap = {},
		      toolMap = {},
		lastInCluster = {},
		        tools = [],
		       matrix = [],
			 clusterId = 0,
		       ratios = {}
			;
		ex.remove();

		nodes.forEach(function(node) {
			if (!ratios[node.cluster]) {
				ratios[node.cluster] = {
					'hit': 0,
					'total': 0
				};
			}
			if (node.tool == coreTool) {
				++ratios[node.cluster].hit;
			}
			++ratios[node.cluster].total;
		});
		var th = threshold.val()/100;
		nodes = nodes.filter(function(node) {
			return ratios[node.cluster].hit/ratios[node.cluster].total >= th;
		});

		nodes.forEach(function(node) {
			// track min and max dates so we know what range to generate for columns
			node.first_use = new Date(node.first_use);
			min = Math.min(min, node.first_use);
			max = Math.max(max, node.first_use);
			// secondary sorting criterion, after cluster size, cluster start date
			if (clusterMin[node.cluster] === undefined) {
				clusterIds[node.cluster] = ++clusterId;
				clusterMin[node.cluster] = Infinity;
			}
			clusterMin[node.cluster] = Math.min(clusterMin[node.cluster], node.first_use);
			// map of tools used to generate legend
			if (toolMap[node.tool] === undefined) {
				toolMap[node.tool] = 1;
				tools.push(node.tool);
			}
		});
		nodes.sort(function(a, b) {
			// in the same cluster, users are ordered by the first time they used a tool
			if (a.cluster == b.cluster) {
				return d3.ascending(a.first_use, b.first_use);
			}
			// between clusters, first we want clusters with the most users
			if (a.size != b.size) {
				return d3.descending(1*a.size, 1*b.size);
			}
			// in the event of a tie, go with the one that had the first usage
			if (clusterMin[a.cluster] < clusterMin[b.cluster]) {
				return -1;
			}
			if (clusterMin[a.cluster] > clusterMin[b.cluster]) {
				return 1;
			}
			// if that's still a tie, just order by the arbitrary cluster name
			return d3.ascending(a.cluster, b.cluster);
		});

		// move the tool represented by this page to the front of the legend so it's always assigned the same color
		tools.sort(function(a, b) {
			if (a == coreTool) {
				return -1;
			}
			if (b == coreTool) {
				return 1;
			}
			return d3.ascending(a, b);
		});
		tools.forEach(function(tool, idx) {
			toolMap[tool] = idx;
		});

		// generate map of day => column index
		for (var dt = new Date(min); dt <= new Date(max); dt.setDate(dt.getDate() + 1)) {
			var name = dt.toLocaleDateString();
			dayMap[name] = days.length;
			days.push(name);
		}

		var used = {};
		nodes.forEach(function(node, i) {
			// generate row for this user
			if (userMap[node.uid] === undefined) {
				userMap[node.uid] = matrix.length;
				matrix.push([]);
			}
			// determine whether node is the last in the cluster, in which case we can style its row a little differently to break them up
			if (lastInCluster[clusterIds[node.cluster]] === undefined) {
				lastInCluster[clusterIds[node.cluster]] = Infinity;
			}
			lastInCluster[clusterIds[node.cluster]] = Math.min(lastInCluster[clusterIds[node.cluster]], userMap[node.uid]);

			var      x = dayMap[node.first_use.toLocaleDateString()], 
				      y = userMap[node.uid]
			shouldDraw = !used[x+':'+y]
				 ;
			if (shouldDraw) {
				used[x+':'+y] = [node.tool];
			}
			else {
				used[x+':'+y].push(node.tool);
				return;
			}

			matrix[userMap[node.uid]].push({
				'x': x,
				'y': y, 
				'toolId': toolMap[node.tool],
				'toolName': node.tool,
				'clusterId': clusterIds[node.cluster],
				'clusterName': node.cluster,
				'size': node.size,
				'uid': node.uid,
				'date': node.first_use.toLocaleDateString(),
				'lastInCluster': lastInCluster[node.uid]
			})
		});
		// dimension and color setup
		var  width = cellDims.x * days.length,
		    height = cellDims.y * matrix.length,
		         x = d3.scale.ordinal().rangeBands([0, width]).domain(d3.range(days.length)),
		         y = d3.scale.ordinal().rangeBands([0, height]).domain(d3.range(matrix.length)),
		         c = d3.scale.category20().domain(d3.range(tools.length)),
		toolLegend = $('<ul id="tool-legend" class="cluster"></ul>');
	
		// draw tool color legend
		tools.forEach(function(tool, idx) {
			toolLegend.append($('<li></li>').css('backgroundColor', c(idx)).text(tool));
		});
		prnt.append(toolLegend);

		// background
		var svg = d3.select(dataSrc.parent()[0]).append("svg")
			.attr('class', 'cluster')
			.attr("width", width + margin.left + margin.right)
			.attr("height", height + margin.top + margin.bottom)
			.style("margin-left", -margin.left + "px")
			.append("g")
				.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
		svg.append("rect")
			.attr("class", "background")
			.attr("width", width)
			.attr("height", height);	

		// column lines
		var column = svg.selectAll(".column")
			.data(days)
				.enter().append("g")
				.attr("class", "column")
				.attr("transform", function(d, i) { return "translate(" + x(i) + ")rotate(-90)"; });
		column.append("line")
			.attr("x1", -height);

		// callbacks to highlight clusters on hover
		var isIn = false,
		mouseover = function(d) {
			// NB: addClass doesn't work on svg elements without including some more libraries
			$('.cluster-' + d.clusterId).attr('class', 'cell cluster-' + d.clusterId + ' highlight');
			info
				.css('left', mouse.x)
				.css('top', mouse.y)
				.empty()
				.append($('<h4></h4>').text(d.clusterName.replace(/[|]/g, ', ').replace(', UNKNOWN', '')))
				.append($('<p><strong>Date: </strong>' + d.date + '</p>'))
				.append($('<p><strong>Tool: </strong>' + used[d.x+':'+d.y].join(', ') + '</p>'))
				.show('fast')
				;
			isIn = true;
		},
		mouseout = function(d) {
			$('.cell').each(function(_, cell) {
				cell = $(cell);
				cell.attr('class', cell.attr('class').replace(' highlight', ''));
			});
			isIn = false;
			setTimeout(function() {
				if (!isIn) {
					info.hide('slow');
				}
			}, 500);
		};

		// rows
		var row = svg.selectAll(".row")
			.data(matrix)
			.enter().append("g")
				.attr("class", function(r) { return 'row cluster-' + r[0].clusterId; })
				.attr("transform", function(d, i) { return "translate(0," + y(i) + ")"; })
				.each(function row(row) {
					// cells
					var cell = d3.select(this).selectAll(".cell")
						.data(row)
							.enter().append("rect")
								.attr("class", function(d) { return 'cell cluster-' + d.clusterId; })
								.attr("x", function(d) { return x(d.x); })
								.attr('rx', 2)
								.attr('ry', 2)
								.attr("width", cellDims.x)
								.attr("height", cellDims.y)
								.style("fill", function(d) { return c(d.toolId); })
								.on('mouseover', mouseover)
								.on('mouseout', mouseout);
				});
		row.append("line")
			.attr("x2", width)
			.attr('class', function(x) {
				return x[0].y == lastInCluster[x[0].clusterId] ? 'last' : '';
			});
		
		// move info box around to an appropriate location for hover
		var svgPrnt = $('svg.cluster').parent(), mouse = {'x': 0, 'y': 0};
		svgPrnt.mousemove(function(evt) {
			var offset = svgPrnt.offset();
			mouse.x = evt.pageX - offset.left + 'px';
			mouse.y = evt.pageY - offset.top + 'px';
		});
		$('.cluster').show('fast');
	};

	if (!nodes) {
		return;
	}
	$('#no-usage').remove();
		
	// do groupings by year and semester, corresponding to selector buttons
	var byYear = {}, lastYear = null;
	nodes.forEach(function(semester) {
		semester.forEach(function(node) {
			if (byYear[node.year] === undefined) {
				byYear[node.year] = {};
			}
			var semesterName = node.cluster.match(/^(.*?)\d{4}/)[1];
			if (byYear[node.year][semesterName] === undefined) {
				byYear[node.year][semesterName] = [];
			}
			byYear[node.year][semesterName].push(node);
			lastYear = node.year;
		});
	});
	// add buttons
	var   yearList = $('<ol id="cluster-years" class="cluster-time-selector year"></ol>'),
	  semesterList = $('<ol id="semester-list" class="cluster-time-selector semester"></ol>'),
	getCurrentYear = function() {
		return $('#cluster-years').children('.selected').text();
	},
	getCurrentSemester = function() {
		return $('#semester-list').children('.selected').text();
	};
	var ma = document.cookie.toString().match(/classroomthreshold=(\d+)/);
	if (ma) {
		threshold.val(ma[1]);
	}
	else {
		threshold.val(5);
	}
	prnt
		.append($('<p class="info">The chart shows the use of this tool, along with other tools, by automatically detected clustered groups exhibiting classroom behavior during the year and semester selected.  Each row represents a user and each column represents a day.  The dots represent use of a tool by that user on that day. The color of the dot indicates the tool used.  The chart is segmented into different usage patterns for these tools over time.</p>').show())
		.append(yearList)
		.append(semesterList)
		.append($('<p>Showing classroom tool usage comproside of at least </p>').append(threshold).append(document.createTextNode(' sessions with this tool')))
		;
	for (var year in byYear) {	
		var yearLi = $('<li>/li>').text(year);
		if (year == lastYear) {
			yearLi.addClass('selected');
		}
		yearList.append(yearLi);
	}

	// callbacks to change viewed cluster set
	yearList.click(function(evt) {
		$('.cluster-time-selector.year li').removeClass('selected');
		$(evt.target).addClass('selected');
		listSemesters();
		draw();
	});
	semesterList.click(function(evt) {
		$('.cluster-time-selector.semester li').removeClass('selected');
		$(evt.target).addClass('selected');
		draw();
	});
	// show the appropriate semesters for a given year, since we might not always have all of them
	var listSemesters = function() {
		var lastSemester = null;
		semesterList.empty();
		for (var semester in byYear[getCurrentYear()]) {
			var sem = $('<li></li>').text(semester)
			if (!lastSemester) {
				lastSemester = sem;
			}
			semesterList.prepend(sem);
		}
		lastSemester.addClass('selected');
	};
	// refresh view
	var draw = function() {
		throbber.css('visibility', 'visible');
		setTimeout(function() {
			render(byYear[getCurrentYear()][getCurrentSemester()]);
			throbber.css('visibility', 'hidden');
		}, 50);
	};
	dataSrc.parent().append(info);
	listSemesters();
	var throbber = $('<img src="/components/com_hubgraph/resources/throbber.gif" class="throbber" />').insertAfter(semesterList);
	threshold.change(function(evt) {
		document.cookie = 'classroomthreshold=' + threshold.val();
		draw();
	});
	draw();
});
