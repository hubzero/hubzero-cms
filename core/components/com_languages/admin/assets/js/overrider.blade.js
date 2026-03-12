/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
	var refreshStatus = document.getElementById('refresh-status');

	Hubzero.overrider.states.refreshing = true;
	if (refreshStatus) {
		refreshStatus.style.display = '';
	}

	fetch('index.php?option=com_languages&controller=overrides&task=refresh&format=json', {
		method: 'POST'
	})
	.then(function(response) { return response.json(); })
	.then(function(r) {
		if (r.error && r.message) {
			alert(r.message);
		}
		if (r.messages) {
			Hubzero.renderMessages(r.messages);
		}
		if (refreshStatus) {
			refreshStatus.style.display = 'none';
		}
		Hubzero.overrider.states.refreshing = false;
	})
	.catch(function(error) {
		alert(error);
		if (refreshStatus) {
			refreshStatus.style.display = 'none';
		}
		Hubzero.overrider.states.refreshing = false;
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
	if (Hubzero.overrider.states.refreshing) {
		return;
	}

	// Only update the used searchstring and searchtype if the search button
	// was used to start the search (that will be the case if 'more' is null)
	if (!more) {
		var searchStringEl = document.getElementById('fields_searchstring');
		Hubzero.overrider.states.searchstring = searchStringEl ? searchStringEl.value : '';
		Hubzero.overrider.states.searchtype = 'value';

		var searchType0 = document.getElementById('fields_searchtype0');
		if (searchType0 && searchType0.checked) {
			Hubzero.overrider.states.searchtype = 'constant';
		}
	}

	if (!Hubzero.overrider.states.searchstring) {
		var jformSearch = document.getElementById('jform_searchstring');
		if (jformSearch) {
			jformSearch.classList.add('invalid');
		}
		return;
	}

	var resultsContainer = document.getElementById('results-container');
	var moreResults = document.getElementById('more-results');

	if (more) {
		// If 'more' is greater than 0 we have already displayed some results for
		// the current searchstring, so display the spinner at the more link
		if (moreResults) {
			moreResults.classList.add('overrider-spinner');
		}
	} else {
		// Otherwise it is a new searchstring and we have to remove all previous results first
		if (moreResults) {
			moreResults.style.display = 'none';
		}
		if (resultsContainer) {
			var oldResults = resultsContainer.querySelectorAll('div.language-results');
			oldResults.forEach(function(el) {
				el.parentNode.removeChild(el);
			});
			resultsContainer.classList.add('overrider-spinner');
			resultsContainer.style.display = '';
		}
	}

	var url = 'index.php?option=com_languages&controller=overrides&task=search&format=json'
		+ '&searchstring=' + encodeURIComponent(Hubzero.overrider.states.searchstring)
		+ '&searchtype=' + encodeURIComponent(Hubzero.overrider.states.searchtype)
		+ '&more=' + (more || 0);

	fetch(url, {
		method: 'POST'
	})
	.then(function(response) { return response.json(); })
	.then(function(r) {
		if (r.error && r.message) {
			alert(r.message);
		}
		if (r.messages) {
			Hubzero.renderMessages(r.messages);
		}
		if (r.data) {
			if (r.data.results) {
				Hubzero.overrider.insertResults(r.data.results);
			}
			if (r.data.more) {
				// If there are more results than the sent ones
				// display the more link
				Hubzero.overrider.states.more = r.data.more;
				if (moreResults) {
					moreResults.style.display = '';
				}
			} else {
				if (moreResults) {
					moreResults.style.display = 'none';
				}
			}
		}
		if (resultsContainer) {
			resultsContainer.classList.remove('overrider-spinner');
		}
		if (moreResults) {
			moreResults.classList.remove('overrider-spinner');
		}
	})
	.catch(function(error) {
		alert(error);
		if (resultsContainer) {
			resultsContainer.classList.remove('overrider-spinner');
		}
		if (moreResults) {
			moreResults.classList.remove('overrider-spinner');
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
	var resultsDiv = document.createElement('div');
	resultsDiv.id = 'language-results' + Hubzero.overrider.states.counter;
	resultsDiv.className = 'language-results';
	resultsDiv.style.display = 'none';

	// Create some elements for each result and insert it into the container
	results.forEach(function(item, index) {
		var div = document.createElement('div');
		div.className = 'result row' + (index % 2);
		div.addEventListener('click', function() {
			Hubzero.overrider.selectString(String(Hubzero.overrider.states.counter) + String(index));
		});

		var key = document.createElement('div');
		key.id = 'override_key' + Hubzero.overrider.states.counter + index;
		key.className = 'result-key';
		key.innerHTML = item.constant;
		key.title = item.file;

		div.appendChild(key);

		var string = document.createElement('div');
		string.id = 'override_string' + Hubzero.overrider.states.counter + index;
		string.className = 'result-string';
		string.innerHTML = item.string;

		div.appendChild(string);

		resultsDiv.appendChild(div);
	});

	// If there aren't any results display an appropriate message
	if (!results.length) {
		var noresult = document.createElement('div');
		noresult.textContent = Hubzero.Lang.txt('COM_LANGUAGES_VIEW_OVERRIDE_NO_RESULTS');
		resultsDiv.appendChild(noresult);
	}

	// Finally insert the container before the more link and reveal it
	var moreResults = document.getElementById('more-results');
	if (moreResults && moreResults.parentNode) {
		moreResults.parentNode.insertBefore(resultsDiv, moreResults);
	} else {
		var container = document.getElementById('results-container');
		if (container) {
			container.appendChild(resultsDiv);
		}
	}

	resultsDiv.style.display = '';
};

/**
 * Inserts a specific constant/value pair into the form and scrolls the page back to the top
 *
 * @param   int   id  The ID of the element which was selected for insertion
 * @return  void
 */
Hubzero.overrider.selectString = function(id)
{
	var keyEl = document.getElementById('override_key' + id);
	var stringEl = document.getElementById('override_string' + id);
	var fieldKey = document.getElementById('field-key');
	var fieldOverride = document.getElementById('field-override');

	if (keyEl && fieldKey) {
		fieldKey.value = keyEl.innerHTML;
	}
	if (stringEl && fieldOverride) {
		fieldOverride.value = stringEl.innerHTML;
	}
};
