/**
 * @package	 hubzero-cms
 * @file		plugins/hubzero/autocompleter/autocompleter.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license	 http://opensource.org/licenses/MIT MIT
 */

/* Plugin based off of Tokenizing Autocomplete by James Smith (http://loopj.com) */
(function ($) {
// Default settings
var DEFAULT_SETTINGS = {
	// Search settings
	method: "GET",
	contentType: "json",
	queryParam: "value",
	searchDelay: 300,
	minChars: 1,
	propertyToSearch: "name",
	jsonContainer: null,

	// Display settings
	hintText: "Type in a search term",
	noResultsText: "No results",
	searchingText: "Searching...",
	deleteText: "&times;",
	animateDropdown: true,

	// Tokenization settings
	tokenLimit: null,
	tokenDelimiter: ",",
	preventDuplicates: false,

	// Output settings
	tokenValue: "id",

	// Prepopulation settings
	prePopulate: null,
	processPrePopulate: false,

	// Manipulation settings
	idPrefix: "token-input-",

	// Formatters
	resultsFormatter: function(item){ return "<li>" + item[this.propertyToSearch]+ "</li>" },
	tokenFormatter: function(item) { return "<li><p>" + item[this.propertyToSearch] + "</p></li>" },

	// Callbacks
	onResult: null,
	onAdd: null,
	onDelete: null,
	onReady: null
};

// Default classes to use when theming
var DEFAULT_CLASSES = {
	tokenList: "token-input-list",
	tokenListFocus: "token-input-list-focus",
	token: "token-input-token",
	tokenDelete: "token-input-delete-token",
	selectedToken: "token-input-selected-token",
	highlightedToken: "token-input-highlighted-token",
	dropdown: "token-input-dropdown",
	dropdownItem: "token-input-dropdown-item",
	dropdownItem2: "token-input-dropdown-item2",
	selectedDropdownItem: "token-input-selected-dropdown-item",
	inputToken: "token-input-input-token"
};

// Input box position "enum"
var POSITION = {
	BEFORE: 0,
	AFTER: 1,
	END: 2
};

// Keys "enum"
var KEY = {
	BACKSPACE: 8,
	TAB: 9,
	ENTER: 13,
	ESCAPE: 27,
	SPACE: 32,
	PAGE_UP: 33,
	PAGE_DOWN: 34,
	END: 35,
	HOME: 36,
	LEFT: 37,
	UP: 38,
	RIGHT: 39,
	DOWN: 40,
	NUMPAD_ENTER: 108,
	COMMA: 188,
	SEMICOLON: 186,
	SEMICOLONFIREFOX: 59
};

// Additional public (exposed) methods
var methods = {
	init: function(url_or_data_or_function, options) {
		var settings = $.extend({}, DEFAULT_SETTINGS, options || {});

		return this.each(function () {
			$(this).data("tokenInputObject", new $.TokenList(this, url_or_data_or_function, settings));
		});
	},
	clear: function() {
		this.data("tokenInputObject").clear();
		return this;
	},
	add: function(item) {
		this.data("tokenInputObject").add(item);
		return this;
	},
	remove: function(item) {
		this.data("tokenInputObject").remove(item);
		return this;
	},
	get: function() {
		return this.data("tokenInputObject").getTokens();
   	}
}

// Expose the .tokenInput function to jQuery as a plugin
$.fn.tokenInput = function (method) {
	// Method calling and initialization logic
	if (methods[method]) {
		return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
	} else {
		return methods.init.apply(this, arguments);
	}
};

// TokenList class for each input
$.TokenList = function (input, url_or_data, settings) {
	//
	// Initialization
	//
	isReadOnly = ($(input).attr("readonly")) ? true : false;

	// Configure the data source
	if ($.type(url_or_data) === "string" || $.type(url_or_data) === "function") {
		// Set the url to query against
		settings.url = url_or_data;

		// If the URL is a function, evaluate it here to do our initalization work
		var url = computeURL();

		// Make a smart guess about cross-domain if it wasn't explicitly specified
		if (settings.crossDomain === undefined) {
			if (url.indexOf("://") === -1) {
				settings.crossDomain = false;
			} else {
				settings.crossDomain = (location.href.split(/\/+/g)[1] !== url.split(/\/+/g)[1]);
			}
		}
	} else if (typeof(url_or_data) === "object") {
		// Set the local data to search through
		settings.local_data = url_or_data;
	}

	// Build class names
	if (settings.classes) {
		// Use custom class names
		settings.classes = $.extend({}, DEFAULT_CLASSES, settings.classes);
	} else if (settings.theme) {
		// Use theme-suffixed default class names
		settings.classes = {};
		$.each(DEFAULT_CLASSES, function(key, value) {
			settings.classes[key] = value + "-" + settings.theme;
		});
	} else {
		settings.classes = DEFAULT_CLASSES;
	}


	// Save the tokens
	var saved_tokens = [];

	// Keep track of the number of tokens in the list
	var token_count = 0;

	// Basic cache to save on db hits
	var cache = new $.TokenList.Cache();

	// Keep track of the timeout, old vals
	var timeout;
	var input_val;

	// Create a new text input an attach keyup events
	var input_box = $("<input type=\"text\"  autocomplete=\"off\">")
		.css({
			outline: "none"
		})
		.attr("id", settings.idPrefix + input.id)
		.attr("placeholder", input.placeholder)
		.focus(function () {
			if (settings.tokenLimit === null || settings.tokenLimit !== token_count) {
				if (!isReadOnly) {
					show_dropdown_hint();
				}
			}
		})
		.blur(function () {
			hide_dropdown();
			$(this).closest('ul').removeClass(settings.classes.tokenListFocus);
			var val = $(this).val().replace(/^,|,$/g,'').replace(/^;|;$/g,'');
			if (val) {
				var item = {
					'id': val, 
					'name': val
				};
				add_token(item);
				hidden_input.change();
			}

			$(this).val("");
		})
		.bind("keyup keydown blur update", resize_input)
		.keydown(function (event) {
			var previous_token;
			var next_token;

			switch(event.keyCode) {
				case KEY.LEFT:
				case KEY.RIGHT:
				case KEY.UP:
				case KEY.DOWN:
					if (!$(this).val()) {
						previous_token = input_token.prev();
						next_token = input_token.next();

						if ((previous_token.length && previous_token.get(0) === selected_token) || (next_token.length && next_token.get(0) === selected_token)) {
							// Check if there is a previous/next token and it is selected
							if (event.keyCode === KEY.LEFT || event.keyCode === KEY.UP) {
								deselect_token($(selected_token), POSITION.BEFORE);
							} else {
								deselect_token($(selected_token), POSITION.AFTER);
							}
						} else if ((event.keyCode === KEY.LEFT || event.keyCode === KEY.UP) && previous_token.length) {
							// We are moving left, select the previous token if it exists
							select_token($(previous_token.get(0)));
						} else if ((event.keyCode === KEY.RIGHT || event.keyCode === KEY.DOWN) && next_token.length) {
							// We are moving right, select the next token if it exists
							select_token($(next_token.get(0)));
						}
					} else {
						var dropdown_item = null;

						if (event.keyCode === KEY.DOWN || event.keyCode === KEY.RIGHT) {
							dropdown_item = $(selected_dropdown_item).next();
						} else {
							dropdown_item = $(selected_dropdown_item).prev();
						}

						if (dropdown_item.length) {
							select_dropdown_item(dropdown_item);
						}
						return false;
					}
					break;

				case KEY.BACKSPACE:
					previous_token = input_token.prev();

					if (!$(this).val().length) {
						if (selected_token) {
							delete_token($(selected_token));
							hidden_input.change();
						} else if (previous_token.length) {
							select_token($(previous_token.get(0)));
						}

						return false;
					} else if ($(this).val().length === 1) {
						hide_dropdown();
					} else {
						// set a timeout just long enough to let this function finish.
						setTimeout(function(){do_search();}, 5);
					}
					break;

				case KEY.TAB:
				case KEY.ENTER:
				case KEY.NUMPAD_ENTER:
				case KEY.COMMA:
				case KEY.SEMICOLON:
				case KEY.SEMICOLONFIREFOX:
					if (selected_dropdown_item && $(selected_dropdown_item).data("tokeninput") !== undefined) {
						add_token($(selected_dropdown_item).data("tokeninput"));
						hidden_input.change();
						return false;
					} else if (event.keyCode === KEY.ENTER
						|| event.keyCode === KEY.COMMA
						|| event.keyCode === KEY.SEMICOLON
						|| event.keyCode === KEY.SEMICOLONFIREFOX) 
					{
						var val = $(this).val().replace(/^,|,$/g,'').replace(/^;|;$/g,'');
						if (val) {
							var item = {
								'id': val, 
								'name': val
							};
							add_token(item);
							hidden_input.change();
							return false;
						}
						return false;
					}
					break;

				case KEY.ESCAPE:
					hide_dropdown();
					return true;

				default:
					if (String.fromCharCode(event.which)) {
						// set a timeout just long enough to let this function finish.
						setTimeout(function(){do_search();}, 5);
					}
					break;
			}
		});

	if (isReadOnly) {
		input_box.attr("disabled", "disabled");
	}

	// Keep a reference to the original input box
	var hidden_input = $(input)
							.hide()
							.val("")
							.focus(function () {
								input_box.focus();
							})
							.blur(function () {
								input_box.blur();
							});

	// Keep a reference to the selected token and dropdown item
	var selected_token = null;
	var selected_token_index = 0;
	var selected_dropdown_item = null;

	// The list to store the token items in
	var token_list = $("<ul />")
		.addClass(settings.classes.tokenList)
		.click(function (event) {
			$(this).addClass(settings.classes.tokenListFocus);
			var li = $(event.target).closest("li");
			if (li && li.get(0) && $.data(li.get(0), "tokeninput")) {
				toggle_select_token(li);
			} else {
				// Deselect selected token
				if (selected_token) {
					deselect_token($(selected_token), POSITION.END);
				}

				// Focus input box
				input_box.focus();
			}
		})
		.mouseover(function (event) {
			var li = $(event.target).closest("li");
			if (li && selected_token !== this) {
				li.addClass(settings.classes.highlightedToken);
			}
		})
		.mouseout(function (event) {
			var li = $(event.target).closest("li");
			if (li && selected_token !== this) {
				li.removeClass(settings.classes.highlightedToken);
			}
		})
		.insertBefore(hidden_input);

	// The token holding the input box
	var input_token = $("<li />")
		.addClass(settings.classes.inputToken)
		.appendTo(token_list)
		.append(input_box);

	// The list to store the dropdown items in
	var dropdown = $("<div>")
		.addClass(settings.classes.dropdown)
		.css('width', (token_list.outerWidth(true)-2))
		.appendTo("body")
		.hide();

	// Magic element to help us resize the text input
	var input_resizer = $("<tester/>")
		.insertAfter(input_box)
		.css({
			position: "absolute",
			top: -9999,
			left: -9999,
			width: "auto",
			fontSize: input_box.css("fontSize"),
			fontFamily: input_box.css("fontFamily"),
			fontWeight: input_box.css("fontWeight"),
			letterSpacing: input_box.css("letterSpacing"),
			whiteSpace: "nowrap"
		});

	// Pre-populate list if items exist
	hidden_input.val("");
	var li_data = settings.prePopulate || hidden_input.data("pre");
	if (settings.processPrePopulate && $.isFunction(settings.onResult)) {
		li_data = settings.onResult.call(hidden_input, li_data);
	}
	if (li_data && li_data.length) {
		$.each(li_data, function (index, value) {
			insert_token(value);
			checkTokenLimit();
		});
	}

	// Initialization is done
	if ($.isFunction(settings.onReady)) {
		settings.onReady.call();
	}

	//
	// Public functions
	//

	this.clear = function() {
		token_list.children("li").each(function() {
			if ($(this).children("input").length === 0) {
				delete_token($(this));
			}
		});
	}

	this.add = function(item) {
		add_token(item);
	}

	this.remove = function(item) {
		token_list.children("li").each(function() {
			if ($(this).children("input").length === 0) {
				var currToken = $(this).data("tokeninput");
				var match = true;
				for (var prop in item) {
					if (item[prop] !== currToken[prop]) {
						match = false;
						break;
					}
				}
				if (match) {
					delete_token($(this));
				}
			}
		});
	}

	this.getTokens = function() {
		return saved_tokens;
	}

	//
	// Private functions
	//

	function checkTokenLimit() {
		if (settings.tokenLimit !== null && token_count >= settings.tokenLimit) {
			input_box.hide();
			hide_dropdown();
			return;
		}
	}

	function resize_input() {
		if (input_val === (input_val = input_box.val())) {return;}

		// Enter new content into resizer and resize input accordingly
		var escaped = input_val.replace(/&/g, '&amp;').replace(/\s/g,' ').replace(/</g, '&lt;').replace(/>/g, '&gt;');
		input_resizer.html(escaped);
		//input_box.width(input_resizer.width() + 30);
		//$(input_box.parent()).width(input_resizer.width() + 30);
	}

	function is_printable_character(keycode) {
		return ((keycode >= 48 && keycode <= 90) ||     // 0-1a-z
				(keycode >= 96 && keycode <= 111) ||    // numpad 0-9 + - / * .
				(keycode >= 186 && keycode <= 192) ||   // ; = , - . / ^
				(keycode >= 219 && keycode <= 222));    // ( \ ) '
	}

	// Inner function to a token to the list
	function insert_token(item) {
		var this_token = settings.tokenFormatter(item);
		this_token = $(this_token)
		  .addClass(settings.classes.token);
		for (var key in item)
		{
			this_token.attr('data-' + key, item[key].replace('"', '&quot;'));
		}
		this_token
		  .insertBefore(input_token);

		// The 'delete token' button
		if (!isReadOnly) 
		{
			$("<span>" + settings.deleteText + "</span>")
				.addClass(settings.classes.tokenDelete)
				.appendTo(this_token)
				.click(function () {
					delete_token($(this).parent());
					hidden_input.change();
					return false;
			});
		}

		// Store data on the token
		var token_data = {"id": item.id};
		token_data[settings.propertyToSearch] = item[settings.propertyToSearch];
		$.data(this_token.get(0), "tokeninput", item);

		// Save this token for duplicate checking
		saved_tokens = saved_tokens.slice(0,selected_token_index).concat([token_data]).concat(saved_tokens.slice(selected_token_index));
		selected_token_index++;

		// Update the hidden input
		update_hidden_input(saved_tokens, hidden_input);

		token_count += 1;

		// Check the token limit
		if (settings.tokenLimit !== null && token_count >= settings.tokenLimit) {
			input_box.hide();
			hide_dropdown();
		}

		return this_token;
	}

	// Add a token to the token list based on user input
	function add_token (item) {
		var callback = settings.onAdd;

		// See if the token already exists and select it if we don't want duplicates
		if (token_count > 0 && settings.preventDuplicates) {
			var found_existing_token = null;
			token_list.children().each(function () {
				var existing_token = $(this);
				var existing_data = $.data(existing_token.get(0), "tokeninput");
				if (existing_data && existing_data.id === item.id) {
					found_existing_token = existing_token;
					return false;
				}
			});

			if (found_existing_token) {
				select_token(found_existing_token);
				input_token.insertAfter(found_existing_token);
				input_box.focus();
				return;
			}
		}

		// Insert the new tokens
		if (settings.tokenLimit == null || token_count < settings.tokenLimit) {
			insert_token(item);
			checkTokenLimit();
		}

		// Clear input box
		input_box.val("");

		// Don't show the help dropdown, they've got the idea
		hide_dropdown();

		// Execute the onAdd callback if defined
		if ($.isFunction(callback)) {
			callback.call(hidden_input,item);
		}
	}

	// Select a token in the token list
	function select_token (token) {
		token.addClass(settings.classes.selectedToken);
		selected_token = token.get(0);

		// Hide input box
		input_box.val("");

		// Hide dropdown if it is visible (eg if we clicked to select token)
		hide_dropdown();
	}

	// Deselect a token in the token list
	function deselect_token (token, position) {
		token.removeClass(settings.classes.selectedToken);
		selected_token = null;

		if (position === POSITION.BEFORE) {
			input_token.insertBefore(token);
			selected_token_index--;
		} else if (position === POSITION.AFTER) {
			input_token.insertAfter(token);
			selected_token_index++;
		} else {
			input_token.appendTo(token_list);
			selected_token_index = token_count;
		}

		// Show the input box and give it focus again
		input_box.focus();
	}

	// Toggle selection of a token in the token list
	function toggle_select_token(token) {
		var previous_selected_token = selected_token;

		if (selected_token) {
			deselect_token($(selected_token), POSITION.END);
		}

		if (previous_selected_token === token.get(0)) {
			deselect_token(token, POSITION.END);
		} else {
			select_token(token);
		}
	}

	// Delete a token from the token list
	function delete_token (token) {
		// Remove the id from the saved list
		var token_data = $.data(token.get(0), "tokeninput");
		var callback = settings.onDelete;

		var index = token.prevAll().length;
		if (index > selected_token_index) index--;

		// Delete the token
		token.remove();
		selected_token = null;

		// Show the input box and give it focus again
		input_box.focus();

		// Remove this token from the saved list
		saved_tokens = saved_tokens.slice(0,index).concat(saved_tokens.slice(index+1));
		if (index < selected_token_index) selected_token_index--;

		// Update the hidden input
		update_hidden_input(saved_tokens, hidden_input);

		token_count -= 1;

		if (settings.tokenLimit !== null) {
			input_box
				.show()
				.val("")
				.focus();
		}

		// Execute the onDelete callback if defined
		if ($.isFunction(callback)) {
			callback.call(hidden_input,token_data);
		}
	}

	// Update the hidden input box value
	function update_hidden_input(saved_tokens, hidden_input) {
		var token_values = $.map(saved_tokens, function (el) {
			return el[settings.tokenValue];
		});
		hidden_input.val(token_values.join(settings.tokenDelimiter));
	}

	// Hide and clear the results dropdown
	function hide_dropdown () {
		dropdown.hide().empty();
		selected_dropdown_item = null;
	}

	function show_dropdown() {
		dropdown
			.css({
				position: "absolute",
				top: $(token_list).offset().top + $(token_list).outerHeight(),
				left: $(token_list).offset().left,
				zindex: 999,
				width: (token_list.outerWidth(true)-2)
			})
			.show();
	}

	function show_dropdown_searching () {
		if (settings.searchingText) {
			dropdown.html("<p>"+settings.searchingText+"</p>");
			show_dropdown();
		}
	}

	function show_dropdown_hint () {
		if (settings.hintText) {
			dropdown.html("<p>"+settings.hintText+"</p>");
			show_dropdown();
		}
	}

	function quote_re_term(term) {
		return term.replace(new RegExp('[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\-]', 'g'), '\\$&');
	}

	// Highlight the query part of the search term
	function highlight_term(value, term) {
		return value.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + quote_re_term(term) + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<b>$1</b>");
	}

	function find_value_and_highlight_term(template, value, term) {
	return template.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + quote_re_term(value) + ")(?![^<>]*>)(?![^&;]+;)", "g"), highlight_term(value, term));
	}

	// Populate the results dropdown with some results
	function populate_dropdown (query, results) {
		if (results && results.length) {
			dropdown.empty();
			var dropdown_ul = $("<ul>")
				.appendTo(dropdown)
				.mouseover(function (event) {
					select_dropdown_item($(event.target).closest("li"));
				})
				.mousedown(function (event) {
					add_token($(event.target).closest("li").data("tokeninput"));
					hidden_input.change();
					return false;
				})
				.hide();

			$.each(results, function(index, value) {
				var this_li = settings.resultsFormatter(value);

				this_li = find_value_and_highlight_term(this_li ,value[settings.propertyToSearch], query);

				this_li = $(this_li).appendTo(dropdown_ul);

				if (index % 2) {
					this_li.addClass(settings.classes.dropdownItem);
				} else {
					this_li.addClass(settings.classes.dropdownItem2);
				}

				if (index === 0) {
					select_dropdown_item(this_li);
				}

				$.data(this_li.get(0), "tokeninput", value);
			});

			show_dropdown();

			if (settings.animateDropdown) {
				dropdown_ul.slideDown("fast");
			} else {
				dropdown_ul.show();
			}
		} else {
			if (settings.noResultsText) {
				dropdown.html("<p>"+settings.noResultsText+"</p>");
				show_dropdown();
			}
		}
	}

	// Highlight an item in the results dropdown
	function select_dropdown_item (item) {
		if (item) {
			if (selected_dropdown_item) {
				deselect_dropdown_item($(selected_dropdown_item));
			}

			item.addClass(settings.classes.selectedDropdownItem);
			selected_dropdown_item = item.get(0);
		}
	}

	// Remove highlighting from an item in the results dropdown
	function deselect_dropdown_item (item) {
		item.removeClass(settings.classes.selectedDropdownItem);
		selected_dropdown_item = null;
	}

	// Do a search and show the "searching" dropdown if the input is longer
	// than settings.minChars
	function do_search() {
		var query = input_box.val().toLowerCase();

		if (query && query.length) {
			if (selected_token) {
				deselect_token($(selected_token), POSITION.AFTER);
			}

			if (query.length >= settings.minChars) {
				show_dropdown_searching();
				clearTimeout(timeout);

				timeout = setTimeout(function(){
					run_search(query);
				}, settings.searchDelay);
			} else {
				hide_dropdown();
			}
		}
	}

	// Do the actual search
	function run_search(query) {
		var cache_key = query + computeURL();
		var cached_results = cache.get(cache_key);
		if (cached_results) {
			populate_dropdown(query, cached_results);
		} else {
			// Are we doing an ajax search or local data search?
			if (settings.url) {
				var url = computeURL();
				// Extract exisiting get params
				var ajax_params = {};
				ajax_params.data = {};
				if (url.indexOf("?") > -1) {
					var parts = url.split("?");
					ajax_params.url = parts[0];

					var param_array = parts[1].split("&");
					$.each(param_array, function (index, value) {
						var kv = value.split("=");
						ajax_params.data[kv[0]] = kv[1];
					});
				} else {
					ajax_params.url = url;
				}

				// Prepare the request
				ajax_params.data[settings.queryParam] = query;
				ajax_params.type = settings.method;
				ajax_params.dataType = settings.contentType;
				if (settings.crossDomain) {
					ajax_params.dataType = "jsonp";
				}

				// Attach the success callback
				ajax_params.success = function(results) {
					if ($.isFunction(settings.onResult)) {
						results = settings.onResult.call(hidden_input, results);
					}
					cache.add(cache_key, settings.jsonContainer ? results[settings.jsonContainer] : results);

					// only populate the dropdown if the results are associated with the active search query
					if (input_box.val().toLowerCase() === query) {
						populate_dropdown(query, settings.jsonContainer ? results[settings.jsonContainer] : results);
					}
				};

				// Make the request
				$.ajax(ajax_params);
			} else if (settings.local_data) {
				// Do the search through local data
				var results = $.grep(settings.local_data, function (row) {
					return row[settings.propertyToSearch].toLowerCase().indexOf(query.toLowerCase()) > -1;
				});

				if ($.isFunction(settings.onResult)) {
					results = settings.onResult.call(hidden_input, results);
				}
				cache.add(cache_key, results);
				populate_dropdown(query, results);
			}
		}
	}

	// compute the dynamic URL
	function computeURL() {
		var url = settings.url;
		if (typeof settings.url == 'function') {
			url = settings.url.call();
		}
		return url;
	}
};

// Really basic cache for the results
$.TokenList.Cache = function (options) {
	var settings = $.extend({
		max_size: 500
	}, options);

	var data = {};
	var size = 0;

	var flush = function () {
		data = {};
		size = 0;
	};

	this.add = function (query, results) {
		if (size > settings.max_size) {
			flush();
		}

		if (!data[query]) {
			size += 1;
		}

		data[query] = results;
	};

	this.get = function (query) {
		return data[query];
	};
};
}(jQuery));

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
if (!HUB.Plugins) {
	HUB.Plugins = {};
}

//----------------------------------------------------------
// Tag Autocompleter
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Plugins.Autocomplete = {
	writeSelectList: function(members, id) {
		var $ = jq,
			sel = $('#' + id);

		if (!sel.length) {
			return;
		}

		sel
			.find('option')
			.remove()
			.end();

		sel.append('<option value="">(none)</option>');

		for (var i=0; i<members.length; i++)
		{
			sel.append('<option value="' + members[i].username + '">' + members[i].name + '</option>');
		}
	},

	initialize: function() {
		var $ = jq;

		//var head = document.head;
		var head = document.getElementsByTagName('head')[0];
		var styles = document.createElement('link');
		styles.type = 'text/css';
		styles.rel = 'stylesheet';
		styles.href = plgAutocompleterCss; //$('#plgAutocompleterCss').val();
		if (!styles.href) {
			styles.href = '/core/plugins/hubzero/autocompleter/autocompleter.css';
		}
		head.appendChild(styles);

		$('.autocomplete').each(function(i, input) {
			// Set some defaults
			var option = 'tags',
				type = 'multi',
				actkn = '',
				tagger = null,
				id = null,
				wsel = null,
				showid = false,
				cls = '',
				hint = '',
				limit = null,
				storeRecent = true;

			id = $(input).attr('id');
			if (!id) {
				return;
			}

			if ($(input).attr('data-options')) {
				var params = $(input).attr('data-options').split(',');
				if (params) {
					option = params[0];
					type   = params[1];
					wsel   = params[2];
				}
			}

			// Set the CSS class for the type of autocompleter (affects colors)
			switch (option)
			{
				case 'members':
					cls = 'acm';
					hint = 'Type in a name';
					showid = true;
				break; 
				case 'groups':
					cls = 'acg';
					hint = 'Type in a search term';
				break;
				case 'tags':
				default:
					cls = 'act';
					hint = 'Type in a search term';
				break;
			}

			if ($('#actkn')) {
				actkn = '&admin=true';
			}

			// Are multiple entries allowable?
			if (type == 'multi') {
				limit = null;
			} else {
				limit = 1;
			}

			// recent use storage
			var recentStorageKey = 'autocompleter.recent.' + option;

			value = $('#'+id).val();
			var data = [];
			if (value) {
				if (value.indexOf(',') == -1) {
					var values = [value];
				} else {
					var values = value.split(',');
				}

				$(values).each(function(i, v) {
					v = v.replace(/^\s\s*/, '').replace(/\s\s*$/, '');

					var id = null,
						name = null;

					if (v.match(/(.+?) \((.+?)\)/ig)) {
						id   = v.replace(/(.+?) \((.+?)\)/ig, '$2');
						name = v.replace(/(.+?) \((.+?)\)/ig, '$1');
					}
					id   = (id)   ? id   : v;
					name = (name) ? name : id;

					data[i] = {
						'id': id,
						'name': name
					};
				});
			}

			var src = $(input).attr('data-script');
			src = src ? src : '/index.php';

			$('#'+id).tokenInput(src + '?option=com_'+option+'&no_html=1&task=autocomplete'+actkn, {
				theme: cls,
				hintText: hint,
				prePopulate: data,
				tokenLimit: limit,
				preventDuplicates: true,
				resultsFormatter: function(item){ 
					if (option != 'tags') {
						var html = "<li>";
						if (item['picture']) {
							html += '<img src="'+item['picture']+'" width="30" height="30" alt="picture" />';
						}
						html += item[this.propertyToSearch]+ " ("+item['id']+")";
						if (item['org']) {
							html += '<span>' + item['org'] + '</span>';
						}
						if (item['picture']) {
							html += '<div style="display:inline;clear:left;"></div>';
						}
						html += "</li>";
						return html;
					}
					return "<li>" + item[this.propertyToSearch]+ "</li>";
				},
				onResult: function(results)
				{
					// if we want to use recent & we have stored recent items
					if (storeRecent && localStorage && localStorage.getItem(recentStorageKey))
					{
						// get recent items
						var topResults = [];
						var recent     = JSON.parse(localStorage.getItem(recentStorageKey));
						var items      = recent.items;

						// loop through each recent item & get its value 
						// if we have one in the results set
						for (var i =0; i < items.length; i++)
						{
							for (var j=0; j < results.length; j++)
							{
								// if found push to top results &
								// remove from original result set
								var id = results[j].id;
								if (items[i] == id)
								{
									topResults.push(results[j]);
									results.splice(j, 1);
								}
							}
						}

						// get top results in reverse order because of how 
						// we are going to prepend to results array as we iterate
						topResults = topResults.reverse();
						for (var i=0, n=topResults.length; i < n; i++)
						{
							results.unshift(topResults[i]);
						}
					}

					// return  original results of local storage is not 
					// eligible or user has not existing recent usage 
					return results;
				},
				onAdd: function(item)
				{
					// pasting in comma separated items
					if (item.name.indexOf(',') > -1 || item.name.indexOf(';') > -1)
					{
						// remove original item
						$('#'+id).tokenInput('remove', {id:item.id});

						// split by comma
						var items = item.name.split(/,|;/g);
						for (var i = 0, n = items.length; i < n; i++)
						{
							// add each individual item
							var item = items[i].trim();
							$('#'+id).tokenInput('add', {
								id: item,
								name: item
							});
						}
					}

					if (wsel) {
						$.getJSON('/index.php?option=com_groups&no_html=1&task=memberslist&group=' + $('#'+id).val(), function(data) {
							HUB.Plugins.Autocomplete.writeSelectList(data.members, wsel);
						});
					}

					// if we want to store recent &
					// we have local storage
					if (storeRecent && localStorage)
					{
						// check to see if we have existing recent items
						if (recent = localStorage.getItem(recentStorageKey))
						{
							// parse and add ids to list
							recent = JSON.parse(recent);

							// make sure it only exists once
							// removes it from current location
							var index = recent.items.indexOf(item.id);
							if (index != -1)
							{
								recent.items.splice(index, 1);
							}

							// adds to top of list
							recent.items.unshift(item.id);
						}
						else
						{
							//otherwise create new list
							var recent = { items: [item.id] };
						}

						// set list of recent to local storage
						localStorage.setItem(recentStorageKey, JSON.stringify(recent));
					}
				}
			});
		});
	}
}

jQuery(document).ready(function(jq){
	HUB.Plugins.Autocomplete.initialize();
});
