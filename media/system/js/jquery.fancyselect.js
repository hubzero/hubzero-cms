;(function($, window, document, undefined) {
	var idNumber = 0;
	$.fn.HUBfancyselect = function( method ) {
		if ( methods[method] ) 
		{
			return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} 
		else if ( typeof method === 'object' || ! method ) 
		{
			return methods.init.apply( this, arguments );
		} 
		else 
		{
			$.error( 'Method ' +  method + ' does not exist' );
		}
	};
	
	var methods = {},
		defaults = {
			hideSelect: true,
			showSearch: false,
			searchPlaceholder: 'Search...',
			maxHeightWithSearch: 500,
			onSearch: function() {},
			onSelected: function() {},
		};
	
	methods.init = function( options ) {
		var settings = $.extend({}, defaults, options);
		return this.each(function(){
			var $this = $(this),
				dropdown = '',
				children = $this.children();
			
			// must be a select element
			if (!$this.is('select'))
			{
				return 'Must be <select> element';
			}
			
			idNumber++;
			
			//set random #id
			$this.data('fancyselect', {
				id: idNumber,
				settings: settings
			});

			// get readonly prop
			var cls = $this.attr('readonly');

			// get the elements tab index & set it to the negative version
			// so we can append it to the 
			var tabIndex = $this.attr('tabIndex');
			var tabIndexAttributeText = '';
			if (tabIndex)
			{
				$this.attr('tabIndex', -tabIndex);
				tabIndexAttributeText = 'tabIndex="'+ tabIndex +'"';
			}
			
			// create dropdown
			dropdown = $('<div ' + tabIndexAttributeText + ' class="fs-dropdown ' + cls + '" id="fs-dropdown-' + $this.data('fancyselect').id + '"></div>')
				.append($('<ul></ul>')
					.append($('<li class="fs-dropdown-selected"></li>')
						.append($('<a href="" class="fs-dropdown-selected-item"><span>&nbsp;</span></a>'))
						.append($('<div class="fs-dropdown-options-container"></div>')
							.append(processChildren(children, 'fs-dropdown-options'))
						)
					)
				);
			
			//show search
			if (settings.showSearch) 
			{
				dropdown.find('.fs-dropdown-options-container')
					.prepend($('<div class="fs-dropdown-options-search"></div>')
						.append($('<input type="text" placeholder="' + settings.searchPlaceholder + '" />'))
					)
					.find('.fs-dropdown-options').css('max-height', settings.maxHeightWithSearch)
			}
			
			// append drop down
			$this.before( dropdown );

			// add event triggers
			addEventHooks( $this );
			
			// hide select box
			if(settings.hideSelect)
			{
				$this.hide();
			}
			
			// select selected option
			methods.selectValue.call($this, $this.find('option:selected').val(), false);
		});
	};
		
	methods.selectValue = function( value, callSelected ) {
		return this.each(function(){
			$('#fs-dropdown-' + $(this).data('fancyselect').id)
				.find('.fs-dropdown-option a[data-value="' + value + '"]')
				.trigger('click', callSelected);
		});
	};
	
	methods.selectText = function( value, callSelected ) {
		return this.each(function(){
			$('#fs-dropdown-' + $(this).data('fancyselect').id)
				.find('.fs-dropdown-option a span:contains(' + value + ')')
				.parent('a')
				.trigger('click', callSelected);
		});
	};
	
	methods.clear = function() {
		return this.each(function(){
			$('#fs-dropdown-' + $(this).data('fancyselect').id)
				.find('.fs-dropdown-option a')
				.first()
				.trigger('click');
		});
	};
	
	methods.filterOptions = function( term ) {
		return this.each(function(){
			
			$('#fs-dropdown-' + $(this).data('fancyselect').id)
				.find('li.fs-dropdown-option').hide();
				
			$('#fs-dropdown-' + $(this).data('fancyselect').id)
				.find('li.fs-dropdown-option a[data-value!=\'\']:caseInsensitiveContains("'+term+'")').parents('li').show();
				
			if (term == '')
			{
				methods.filterReset.call($(this));
			}
			// highlight terms
			$('#fs-dropdown-' + $(this).data('fancyselect').id + ' .fs-dropdown-option:visible a span').unhighlight();
			$('#fs-dropdown-' + $(this).data('fancyselect').id + ' .fs-dropdown-option:visible a span').highlight(term);
		});
	};
	
	methods.filterReset = function() {
		return this.each(function(){
			$('#fs-dropdown-' + $(this).data('fancyselect').id)
				 .find('.fs-dropdown-option a span').unhighlight();
			
			$('#fs-dropdown-' + $(this).data('fancyselect').id)
				.find('li.fs-dropdown-option').show();
				
			$('#fs-dropdown-' + $(this).data('fancyselect').id)
				.find('.fs-dropdown-options-search input').val('');
		});
	};
	
	// jquery case insensitve search
	jQuery.expr[':'].caseInsensitiveContains = function(a,i,m) {
		return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0; 
	};
	
	function processChildren( children, className )
	{
		var options = $('<ul class="'+className+'"></ul>');
		children.each(function(i, element){
			var $element = $(element);
			
			if ($element.is('option'))
			{
				 options.append(option($element));
			}
			else if($element.is('optgroup'))
			{
				options.append(optgroup($element));
			}
		});
		
		return options;
	}
	
	function option( option )
	{
		var item = $('<li class="fs-dropdown-option"></li>')
					.append($('<a href="javascript:void(0);"></a>')
						.attr('data-value', option.val())
						.attr('data-text', option.text())
						.attr('data-img', option.attr('data-img'))
						.attr('data-color', option.attr('data-color'))
							.append(optionImgOrColor(option))
							.append($('<span>' + option.text() + '</span>'))
					);
		
		// append all data attribs on option object 
		$.each($(option).data(), function(key, value)
		{
			if (key != 'color' && key != 'img')
			{
				item.find('a').attr('data-' + key, value);
			}
		});

		// return
		return item;
	}
	
	function optgroup( optgroup )
	{
		var options = processChildren(optgroup.children('option'),'fs-dropdown-options-group');
		options.prepend('<li class="fs-dropdown-label">' + optgroup.attr('label') + '</li>');
		return options;
	}
	
	function optionImgOrColor( option )
	{
		var $opt  = $(option),
			img   = $opt.attr('data-img'),
			color = $opt.attr('data-color');
		
		// do we have an image
		if (img != '' && img != undefined)
		{
			return '<img class="fs-option-image" src="' + img + '" />'
		}
		
		// do we have an image
		if (color != '' && color != undefined)
		{
			return '<div class="fs-option-color" style="background-color:' + color + '"></div>'
		}
		
		return '';
	}
	
	function addEventHooks( object )
	{
		var keyValueEntered = '',
			data = $(object).data('fancyselect'),
			dropdown = $('#fs-dropdown-' + data.id);

		//open/close dropdown
		dropdown.on('click', '.fs-dropdown-selected-item', function(event) {
			event.preventDefault();
			if (!$(object).attr("readonly"))
			{
				if (dropdown.hasClass('fs-dropdown-open'))
				{
					closeDropdown( object )
				}
				else
				{
					openDowndown( object );
				}
			}
		});
		
		//selected elements
		dropdown
			.on('click', '.fs-dropdown-option a', function(event, runOnSelected){
				event.preventDefault();
				runOnSelected = (runOnSelected != null) ? runOnSelected : true;
				
				var selected = $(this),
					value = selected.attr('data-value'),
					text = selected.attr('data-text'),
					img = selected.attr('data-img');
			
				$(this).parents('.fs-dropdown-options').find('li').removeClass('fs-dropdown-option-selected fs-dropdown-option-highlighted');
				$(this).parent('li').addClass('fs-dropdown-option-selected fs-dropdown-option-highlighted');
			
				//set the text of selected item
				dropdown.find('.fs-dropdown-selected-item span').text(text);
			
				// remove any selected item colors or images
				dropdown.find('.fs-dropdown-selected-item .fs-option-image').remove();
				dropdown.find('.fs-dropdown-selected-item .fs-option-color').remove();
				
				// add new color or image
				dropdown.find('.fs-dropdown-selected-item').prepend(optionImgOrColor(selected));
			
				//find original select and set value
				dropdown.next('select').val( value ).trigger('change');
			
				//run on open function
				if (typeof data.settings.onSelected == 'function' && runOnSelected)
				{
					data.settings.onSelected.call(this, data, {'value':value,'text':text,'image':img});
				}
			
				//close dropdown
				closeDropdown( object );
			})
			.on('keyup', '.fs-dropdown-options-search input', function(event) {
				// get search term
				var searchTerm = $(this).val();
				
				//  filter options based on search terms
				methods.filterOptions.call(object, searchTerm);
			})
			.on('keydown', function(event) {

				var key = event.keyCode;

				// only do the following if we have an open fancy select
				if ($('.fs-dropdown-open').length)
				{
					var focused = $('.fs-dropdown-open li').find('.fs-dropdown-option-highlighted');

					// focus the next or prev entry
					if (key == 38 || key == 40)
					{
						event.preventDefault();
						$('.fs-dropdown-open li').removeClass('fs-dropdown-option-highlighted');

						if (key == 38)
						{
							var prev = focused.prev('li');
							if (!prev.length)
							{
								prev = $('.fs-dropdown-open .fs-dropdown-options li').first();
							}
							prev.addClass('fs-dropdown-option-highlighted');
						}
						else
						{
							var next = focused.next('li');
							if (!next.length)
							{
								next = $('.fs-dropdown-open li').last();
							}
							next.addClass('fs-dropdown-option-highlighted');
						}	
					}
					else if (key == 13)
					{
						event.preventDefault();

						var highlightedOption = $('.fs-dropdown-open').find('li.fs-dropdown-option-highlighted');
						if (highlightedOption.length)
						{
							// select selected option
							var highlightedOptionValue = highlightedOption.find('a').attr('data-value');
							methods.selectValue.call(object, highlightedOptionValue, true);
						}
					}
					else if (key == 9)
					{
						closeDropdown( object );
					}
				}

				// if not open
				// listen for down arrow key
				else if (key == 40)
				{	
					openDowndown( object );
				}

				// otherwise find first match
				else
				{
					var options = $(this).find('.fs-dropdown-options li');
					keyValueEntered += String.fromCharCode(event.keyCode);
					options.each(function(index) {
						var text = $(this).find('a span').text();
						if (keyValueEntered == text)
						{
							// select selected option
							methods.selectValue.call(object, keyValueEntered, true);
						}
					});

					// reset the value after 2 seconds
					setTimeout(function() {
						keyValueEntered = '';
					}, 2000);
				}
			});
		
		//click to close but make sure were not searching
		$('body')
			.on('click', function(event) {
				if (event.target.nodeName.toLowerCase() != 'input')
				{
					closeAllOpenDropdowns();
				}
			});
	}
	
	
	function openDowndown( object )
	{
		//get data
		var data = object.data('fancyselect');
		
		//close other dropdowns on new open
		closeAllOpenDropdowns();
		
		//get needed objects
		var dropdown = $('#fs-dropdown-' + data.id),
			dropdownOptions = dropdown.find('.fs-dropdown-options-container');
		
		//show options
		dropdownOptions.hide().css('left','0').slideDown(100, function(){
			//add open class
			dropdown.addClass('fs-dropdown-open');
		});
		
		//focus on selected option
		//dropdown.find('.fs-dropdown-options li.fs-dropdown-option-selected a').focus();
	}
	
	function closeDropdown( object )
	{
		//get data
		var data = object.data('fancyselect'),
			dropdown = $('#fs-dropdown-' + data.id),
			dropdownOptions = dropdown.find('.fs-dropdown-options-container');
		
		//hide options
		dropdownOptions.slideUp(50, function(){
			//add open class
			dropdown.removeClass('fs-dropdown-open');
			
			//css position
			$(this).css('left', '-9999px');
		
			// reset search
			methods.filterReset.call( object );
		});
	}
	
	function closeAllOpenDropdowns()
	{
		$('.fs-dropdown-open').each(function() {
			closeDropdown($(this).next('select'));
		});
	}
})( jQuery, window, document );

jQuery.extend({
    highlight: function (node, re, nodeName, className) {
        if (node.nodeType === 3) {
            var match = node.data.match(re);
            if (match) {
                var highlight = document.createElement(nodeName || 'span');
                highlight.className = className || 'highlight';
                var wordNode = node.splitText(match.index);
                wordNode.splitText(match[0].length);
                var wordClone = wordNode.cloneNode(true);
                highlight.appendChild(wordClone);
                wordNode.parentNode.replaceChild(highlight, wordNode);
                return 1; //skip added node in parent
            }
        } else if ((node.nodeType === 1 && node.childNodes) && // only element nodes that have children
                !/(script|style)/i.test(node.tagName) && // ignore script and style nodes
                !(node.tagName === nodeName.toUpperCase() && node.className === className)) { // skip if already highlighted
            for (var i = 0; i < node.childNodes.length; i++) {
                i += jQuery.highlight(node.childNodes[i], re, nodeName, className);
            }
        }
        return 0;
    }
});

jQuery.fn.unhighlight = function (options) {
    var settings = { className: 'highlight', element: 'span' };
    jQuery.extend(settings, options);

    return this.find(settings.element + "." + settings.className).each(function () {
        var parent = this.parentNode;
        parent.replaceChild(this.firstChild, this);
        parent.normalize();
    }).end();
};

jQuery.fn.highlight = function (words, options) {
    var settings = { className: 'highlight', element: 'span', caseSensitive: false, wordsOnly: false };
    jQuery.extend(settings, options);
    
    if (words.constructor === String) {
        words = [words];
    }
    words = jQuery.grep(words, function(word, i){
      return word != '';
    });
    words = jQuery.map(words, function(word, i) {
      return word.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
    });
    if (words.length == 0) { return this; };

    var flag = settings.caseSensitive ? "" : "i";
    var pattern = "(" + words.join("|") + ")";
    if (settings.wordsOnly) {
        pattern = "\\b" + pattern + "\\b";
    }
    var re = new RegExp(pattern, flag);
    
    return this.each(function () {
        jQuery.highlight(this, re, settings.element, settings.className);
    });
};
