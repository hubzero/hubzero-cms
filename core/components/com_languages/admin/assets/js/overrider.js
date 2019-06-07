/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Some state variables for the overrider
 */
Hubzero.overrider = {
	states : {
		refreshing: false,
		refreshed: false,
		counter: 0,
		searchstring: '',
		searchtype: 'value'
	}
};

/**
 * Method for refreshing the database cache of known language strings via Ajax
 *
 * @return  void
 */
Hubzero.overrider.refreshCache = function()
{
	$.ajax({
		url: 'index.php?option=com_languages&controller=overrides&task=refresh&format=json',
		type: 'POST',
		dataType: 'json',
		beforeSend: function() {
			Hubzero.overrider.states.refreshing = true;
			$('#refresh-status').show();
		},
		success: function(r) {
			if (r.error && r.message)
			{
				alert(r.message);
			}
			if (r.messages)
			{
				Hubzero.renderMessages(r.messages);
			}
			$('#refresh-status').hide();
			Hubzero.overrider.states.refreshing = false;
		},
		/*onFailure: function(xhr)
		{
			alert(Hubzero.Lang.txt('COM_LANGUAGES_VIEW_OVERRIDE_REQUEST_ERROR'));
			$('#refresh-status').dissolve();
		}.bind(this),*/
		error: function(text, error) {
			alert(error + "\n\n" + text);
			$('#refresh-status').hide();
		}
	});
};

/**
 * Method for searching known language strings via Ajax
 *
 * @param   int   more  Determines the limit start of the results
 * @return  void
 */
Hubzero.overrider.searchStrings = function(more)
{
	// Prevent searching if the cache is refreshed at the moment
	if (Hubzero.overrider.states.refreshing)
	{
		return;
	}

	// Only update the used searchstring and searchtype if the search button
	// was used to start the search (that will be the case if 'more' is null)
	if (!more)
	{
		Hubzero.overrider.states.searchstring = $('#fields_searchstring').val();
		Hubzero.overrider.states.searchtype   = 'value';
		if ($('#fields_searchtype0').attr('checked'))
		{
			Hubzero.overrider.states.searchtype = 'constant';
		}
	}

	if (!Hubzero.overrider.states.searchstring)
	{
		$('#jform_searchstring').addClass('invalid');

		return;
	}

	$.ajax({
		dataType: 'json',
		type: 'POST',
		url: 'index.php?option=com_languages&controller=overrides&task=search&format=json&searchstring=' + Hubzero.overrider.states.searchstring + '&searchtype=' + Hubzero.overrider.states.searchtype + '&more=' + more,
		beforeSend: function()
		{
			if (more)
			{
				// If 'more' is greater than 0 we have already displayed some results for
				// the current searchstring, so display the spinner at the more link
				$('#more-results').addClass('overrider-spinner');
			}
			else
			{
				// Otherwise it is a new searchstring and we have to remove all previous results first
				$('#more-results').hide();

				$('#results-container div.language-results').remove();
				$('#results-container').addClass('overrider-spinner').show();
			}
		},
		success: function(r) {
			if (r.error && r.message)
			{
				alert(r.message);
			}
			if (r.messages)
			{
				Hubzero.renderMessages(r.messages);
			}
			if (r.data)
			{
				if (r.data.results)
				{
					Hubzero.overrider.insertResults(r.data.results);
				}
				if (r.data.more)
				{
					// If there are more results than the sent ones
					// display the more link
					Hubzero.overrider.states.more = r.data.more;
					$('#more-results').show();
				}
				else
				{
					$('#more-results').hide();
				}
			}
			$('#results-container').removeClass('overrider-spinner');
			$('#more-results').removeClass('overrider-spinner');
		},
		/*onFailure: function(xhr)
		{
			alert(Hubzero.Lang.txt('COM_LANGUAGES_VIEW_OVERRIDE_REQUEST_ERROR'));
			document.id('results-container').removeClass('overrider-spinner');
			document.id('more-results').removeClass('overrider-spinner');
		}.bind(this),*/
		error: function(text, error)
		{
			alert(error + "\n\n" + text);
			$('#results-container').removeClass('overrider-spinner');
			$('#more-results').removeClass('overrider-spinner');
		}
	});
};

/**
 * Method inserting the received results into the results container
 *
 * @param   array  results  An array of search result objects
 * @return  void
 */
Hubzero.overrider.insertResults = function(results)
{
	// For creating an individual ID for each result we use a counter
	Hubzero.overrider.states.counter = Hubzero.overrider.states.counter + 1;

	// Create a container into which all the results will be inserted
	var results_div = $('<div></div>')
		.attr('id', 'language-results' + Hubzero.overrider.states.counter)
		.addClass('language-results')
		.hide();

	// Create some elements for each result and insert it into the container
	$.each(results, function (index, item) {
		var div = $('<div></div>')
			.addClass('result row' + index%2)
			.on('click', function(e) {
				Hubzero.overrider.selectString(String(Hubzero.overrider.states.counter) + String(index));
			});

		var key = $('<div></div>')
			.attr('id', 'override_key' + Hubzero.overrider.states.counter + index)
			.addClass('result-key')
			.html(item.constant)
			.attr('title', item.file);

		key.appendTo(div);

		var string = $('<div></div>')
			.attr('id', 'override_string' + Hubzero.overrider.states.counter + index)
			.addClass('result-string')
			.html(item.string);

		string.appendTo(div);

		div.appendTo(results_div);
	});

	// If there aren't any results display an appropriate message
	if (!results.length)
	{
		var noresult = $('<div></div>').html(Hubzero.Lang.txt('COM_LANGUAGES_VIEW_OVERRIDE_NO_RESULTS'));
		noresult.appendTo(results_div);
	}

	// Finally insert the container afore the more link and reveal it
	results_div.insertBefore($('#more-results'));

	$('#language-results' + Hubzero.overrider.states.counter).show();
};

/**
 * Inserts a specific constant/value pair into the form and scrolls the page back to the top
 *
 * @param   int   id  The ID of the element which was selected for insertion
 * @return  void
 */
Hubzero.overrider.selectString = function(id)
{
	$('#field-key').val($('#override_key' + id).html());
	$('#field-override').val($('#override_string' + id).html());
};
