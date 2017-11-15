jQuery(document).ready(function (jq) {
	var $        = jq,
		terms = $('#terms');

	terms.on('keyup', function(){
		// Create an ajax call to check the potential password
		terms.autocomplete({
			source: function (req, res) {
				$.ajax({
					url: "/api/search/typeSuggestions?terms="+req.term,
					type: "GET",
					success: function(json) {
						var suggestions = json.results;
						res(JSON.parse(suggestions));
					}
				});
			},
			_renderMenu: function( ul, items ) {
				var self = this;
				$.each( items, function( index, item ) {
					ul.append("<li class='suggestion-dropdown'>" + item + "</li>");
					self._renderItem( ul, item );
				});

			}
		});
	});

});

