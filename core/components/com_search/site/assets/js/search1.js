$(document).ready(function() {

var queryObj = {};
var stats = {};
var SearchPage = {
	//
	// Uses Handlebars.js to render the search results
	//

	renderResults: function(results) {
		// Set the context 
		var context = {results};
		var template = $('#result-template').html();
		
		// Create your own helpers!
		Handlebars.registerHelper('link', function(url,text) {
			if (!url)
			{
				return false;
			}

			if (text != url) {
				url = url.replace("127.0.0.1", "kevdev.com");
			}
			else {
				url = url.replace("127.0.0.1", "kevdev.com");
				text = url;
			}

			var result = '<a href="'+url+'">'+text+'</a>';
		  return new Handlebars.SafeString(result);
		});

		Handlebars.registerHelper('authorList', function(authors) {
			var html = '';
			for (var x = 0; x < authors.length; x++)
			{
				html += '<span class="result-author">' + authors[x] + '</span>';
				if (x < authors.length - 1)
				{
					html += ', ';
				}
			}
			return new Handlebars.SafeString(html);
		});

		Handlebars.registerHelper('tagcloud', function(tags) {
				var html = '';
				for (var x = 0; x < tags.length; x++)
				{
					html += '<li><a class="tag" href="/search/?terms=' + tags[x] + '">' + tags[x]  + '</a></li>';
				}
				return new Handlebars.SafeString(html);
		});

		Handlebars.registerHelper('formatDate', function(subject) {
					// Local time conversion
					var date = moment(subject).format('MM-DD-YYYY hh:mm A');
					return date;
		});

		// Compile the template
		var compiled = Handlebars.compile(template);

		// Returns a callback to generate the HTML
		var html = compiled(context);

		// Append content to the container
		$('#results').append(html);
	},

	// Render Categories
	renderCategories: function(filters) {
		filters = {}

		// Set the context
		var context = {categories};
		var template = $('#category-template').html();
		console.log(template);

		// Compile the template
		var compiled = Handlebars.compile(template);

		// Returns a callback to generate the HTML
		var html = compiled(context);

		// Append content to the container
		$('#categories').append(html);
	},


	//
	// Performs the query
	//
	sendQuery: function() {
		// Make a promise, get results.
		var url = '/api/search';
		var termsVal = $('#terms').val();
		var limit = $('#limit').val();
		var start = $('#start').val();
		var type = $('#type').val();

		queryObj = {'terms':termsVal, 'limit':limit, 'start':start, 'type':type};

		$('#paginate-terms').val(termsVal);

		var request = $.ajax({
			url: url,
			async: false,
			method: 'POST',
			data: { terms : termsVal,
							limitstart: 0,
							limit: limit,
							sortBy: 'hubid',
							sortDir: 'DESC',
							type: type,
						},
		});

		this.results = request.done(function(data) {
			return data.results;
		});

		this.results = this.results.responseJSON.results;
		stats.matching = this.results.matching;

		return this.results;
	},

	//
	// Uses Handlebars.js to render the filters
	//
	renderFilters: function(filters) {

		Handlebars.registerHelper('filter-apply', function(type) {
				var currentPath = window.location.href;
				var a = $('<a>', { href:currentPath } )[0];

				var urlString = 'search/';
					var result = {};
					a.search.split("&").forEach(function(part) {
						var item = part.split("=");
						result[item[0].replace("?",'')] = decodeURIComponent(item[1]);
					});
					urlString += '?terms=' + result['terms'];
					urlString += '&type='+type;

				return new Handlebars.SafeString(urlString);
		});

		// Set the context
		var context = {filters};
		var template = $('#filter-template').html();

		// Compile the template
		var compiled = Handlebars.compile(template);

		// Returns a callback to generate the HTML
		var html = compiled(context);

		// Append content to the container
		$('#filters').append(html);

		console.log(queryObj.type);
		if (queryObj.type != '')
		{
			$(".type-facets .active").removeClass('active');
			$("#type-facet-"+queryObj.type).addClass('active');
		}

	},

	refreshQuery: function() {
		$('.result').remove();
		var results = SearchPage.sendQuery();
		this.renderResults(results);
	}
}

	searchPageObj = SearchPage;
	results = SearchPage.sendQuery();

	$.ajax({url:'/api/search/gethubtypes', data:queryObj}).success(function(response){
		var filters = JSON.parse(response.results);
		queryObj.total = response.total;
		$("#type-facet-all span.item-count").text(queryObj.total);
		SearchPage.renderFilters(filters);
	});

	SearchPage.renderResults(results);
});
