jQuery(function($) {
	var reQuote = function(str) {
		return str.replace(new RegExp('[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\/-]', 'g'), '\\$&');
	}

	var xhr,
		base = '/search',
		terms = $('.search .terms'),
		linkCats = {};

	$('.complete .cat').each(function(_, cat) {
		cat = $(cat);
		linkCats[cat.attr('class').replace(/^cat\s+/, '')] = $(cat.children('ul'));
	});

	$('.bar .clear').on('click', function (evt) {
		evt.preventDefault();

		$('.complete').hide();

		terms
			.val('')
			.removeClass('with-autocomplete')
			.focus();
	});

	$('#search-form').on('keypress', '.inline.search', function ( e ) {
		if (e.keyCode == 13) {
			e.preventDefault();
		}
	});


	var autocompleter = function() {
		if (xhr) {
			xhr.abort();
		}

		if (terms.val().replace(/\s+/g, '') == '') {
			$('.complete').hide();
			$('.autocorrect-notice').show();
			terms.removeClass('with-autocomplete');
			return;
		}

		$('.complete').css('width', (parseInt($('.terms').outerWidth()) - 4) + 'px');

		xhr = $.get(base, {
			'task': 'complete',
			'terms': terms.val()
		}, function(res) {
			$('.complete').hide();
			$('.autocorrect-notice').show();
			terms.removeClass('with-autocomplete');

			var k;
			if (!(k in res.links) && res.completions.length == 0) {
				return;
			}

			var re = new RegExp('(' + reQuote(terms.val()).split(/\s+/).join('|') + ')', 'gi');
			for (k in linkCats) {
				linkCats[k].empty().parent().hide();
			}
			for (k in res.links) {
				if (res.links[k].length) {
					if (!linkCats[k + 's']) {
						if (console.warn) {
							console.warn('no handler defined for ' + k + ' links');
						}
						continue;
					}
					linkCats[k + 's'].parent().show();
					res.links[k].forEach(function(link) {
						var btn = $('<button type="submit" />')
							.attr('name', k + 's[]')
							.attr('value', link[0])
							.html(link[1].replace(re, '<em>$1</em>'))
							.on('click', function(evt) {
								var ma = $(this).html().match(/<em.*?>(.*?)<\/em>/);
								if (ma) {
									var termAr = terms.val().split(/\s+/);
									if ((new RegExp(ma[1], 'gi')).test(termAr[termAr.length - 1])) {
										terms.val(termAr.splice(0, termAr.length - 1).join(' '));
									}
								}
							});
						linkCats[k + 's'].append($('<li />').append(btn));
					});
				}
			}
			if (res.completions.length > 0) {
				linkCats.text.parent().show();
				res.completions.forEach(function(text) {
					linkCats.text.append($('<li>').append($('<a>').html(text.replace(re, '<em>$1</em>'))));
				});
			}
			$('.autocorrect-notice').hide();
			$('.complete').show();
			terms.addClass('with-autocomplete');
		});
	};

	terms
		.keyup(autocompleter)
		.focus();

	var origSort = function(a, b) {
		return $(a).data('idx') > $(b).data('idx') ? 1 : -1;
	};

	$('.facet').each(function(_, tr) {
		var tds  = $(tr).children('h3'),
			list = $(tr).children('ol');

		if (list.hasClass('timeframe') || list.hasClass('groups')) {
			return;
		}

		var lis = list.find('li'),
			strings = {};

		lis.each(function(idx, li) {
			li = $(li);
			li.data('idx', idx);
			if (!strings[li.text()]) {
				strings[li.text()] = li;
			}
		});

		var inlineSearchTimeout;

		var isearch = $('<span class="inline search"></span>').append(
			$('<input />')
				.attr('type', 'text')
				.attr('placeholder', 'Find...')
				.on('keyup', function(evt) {
					if (inlineSearchTimeout) {
						clearInterval(inlineSearchTimeout);
					}
					inlineSearchTimeout = setTimeout(function() {
						var val = $(evt.target).val();
						if (val.replace(/\s+/, '') == '') {
							for (var string in strings) {
								strings[string].children('button').text(string);
							}
							lis.show();
							lis.sort(origSort);
						}
						else {
							var re = new RegExp('(' + reQuote(val) + ')', 'gi'), reFirst = new RegExp('^' + reQuote(val), 'i');
							for (var string in strings) {
								var hlString = string.replace(re, '<span class="highlight">$1</span>');
								strings[string]
									[string == hlString ? 'hide' : 'show']()
									.children('button').html(hlString);
							}
							lis.sort(function(a, b) {
								var am = reFirst.test($(a).text()), bm = reFirst.test($(b).text());
								if (am && !bm) {
									return -1;
								}
								if (bm && !am) {
									return 1;
								}
								return origSort(a, b);
							});
						}
						list.find('ol').append(lis);
					}, 100);
				})
		);
		isearch.insertAfter(tds);
	});

	var years = [],
		firstYear = null;

	$('.facets .timeframe').children('li').each(function(_, li) {
		li = $(li);
		var year = li.text();
		if (/^\d{4}$/.test(year)) {
			years.push($('<option value="' + year + '">').val(year).text(year));
			if (firstYear == null) {
				firstYear = li;
			}
			else {
				li.remove();
			}
		}
	});

	if (years.length > 1) {
		var sel = $('<select name="timeframe[]">').append('<option value="">by year...</option>')
			.change(function() { 
				$('#search-form').submit();
			});

		$(years).each(function(_, yr) {
			sel.append(yr);
		});
		firstYear.empty().append(sel);
	}

	// load on scroll
	var throbber = new Image();
	throbber.src = '/core/components/com_search/site/assets/hubgraph/throbber.gif';
	throbber = $(throbber).addClass('throbber');

	var pageList = $('.pages'),
		page = pageList.find('.current').text()*1,
		maxPage = pageList.find('li:last-child').text().replace(/\s/g, '')*1,
		working = false;

	$('.results').css('borderBottom', 0);

	pageList
		.css('visibility', 'hidden')
		.bind('inview', function() {
			if (!working && page < maxPage) {
				working = true;
				++page;
				$('.results li.result:last-child').append(throbber);
				var url = '/search' + (location.search ? location.search.toString().replace(/(page|task)=[^&]+/g, '') + '&' : '?') + 'task=page&page=' + page + '&cache=' + $('.results').data('cache');
				$.get(url, function(res) {
					throbber.replaceWith($('<hr />'));
					$('.results').append($(res).css('opacity', 0).animate({'opacity': 1}, 'slow'));
					setTimeout(function() {
						working = false;
					}, 1000);
					activateRelated();
				});
			}
		});

	$(document.body).append('<style type="text/css">.result:hover .related { visibility: visible; }</style>');

	var activateRelated = function() {
		$('.related').on('click', function (evt) {
			var el = $(evt.target),
				throbber = $('<img src="/core/components/com_search/site/assets/hubgraph/throbber.gif" class="throbber related" />');

			el.replaceWith(throbber);

			$.get('/search?task=getRelated&domain=' + encodeURIComponent(el.data('domain')) + '&id=' + encodeURIComponent(el.data('id')), function(res) {
				if (!res || !res.length) {
					throbber.replaceWith('<p>No related results were found.</p>');
				}
				var ul = $('<ul class="related">');
				res.forEach(function(item) {
					ul.append($('<li>').append($('<strong>').text(item[0][0].toUpperCase() + item[0].substr(1, item[0].length - 1) + ': ')).append($('<a href="' + item[1] + '">').text(item[2])));
				});
				throbber.replaceWith(ul);
			});
		});
	};
	activateRelated();
});
