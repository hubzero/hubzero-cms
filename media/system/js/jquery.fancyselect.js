;(function( $ ) {
	
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
			onSelected: function() {}
		};
		
	methods.init = function( options ) {
		var settings = $.extend({}, defaults, options);
		return this.each(function(){
			var $this = $(this),
				dropdown = '',
				options = $("option", $this),
				selected = $this.find('option[selected="selected"]');
			
			//make sure we have a selected index
			if ($(selected).text() == undefined || $(selected).text() == '')
			{
				selected = options.first();
			}
			
			//set random #
			$this.data('fancyselect', {
				id: Math.floor((Math.random()*1000)+1),
				settings: settings
			});
			
			var img = ($(selected).attr('data-img') != undefined && $(selected).attr('data-img') != '') ? $(selected).attr('data-img') : '';
			
			//create dropdown
			dropdown = '<div class="fs-dropdown" id="fs-dropdown-' + $this.data('fancyselect').id + '">';
			dropdown += '<ul>'
			dropdown += '<li class="fs-dropdown-selected">';
			dropdown += '<a class="fs-dropdown-selected-item" href="javascript:void(0);">';
			dropdown += '<span class="fs-dropdown-selected-image">';
			if (img != '')
			{
				dropdown += '<img src="' + img + '" />';
			}
			dropdown += '</span>';
			dropdown += '<span>' + $(selected).text() + '</span>';
			dropdown += '</a>';
			dropdown += '<ul class="fs-dropdown-options">';
			
			//add options
			options.each(function() {
				var cls = ($(this).val() == $(selected).val()) ? 'fs-dropdown-option-selected' : '',
					img = ($(this).attr('data-img') != '' && $(this).attr('data-img') != undefined) ? $(this).attr('data-img') : '';
				
				dropdown += '<li class="fs-dropdown-option ' + cls + '">';
				dropdown += '<a href="javascript:void(0);" data-value="' + $(this).val() + '" data-text="' + $(this).text() + '" data-img="' + img + '">';
				if (img != '')
				{
					dropdown += '<span class="fs-dropdown-option-image">';
					dropdown += '<img src="' + img + '" />';
					dropdown += '</span>'
				}
				dropdown += '<span>' + $(this).text() + '</span>';
				dropdown += '</a>';
				dropdown += '</li>';
			});
			
			dropdown += '</ul>';
			dropdown += '</li>';
			dropdown += '</ul>'
			dropdown += '</div>'
			
			
			//hide select box
			if(settings.hideSelect)
			{
				$this.hide();
			}
			
			//append drop down
			$this.before( dropdown );
			
			//add event triggers
			addEventHooks( $this );
		});
	};
	
	function addEventHooks( object )
	{
		var data = $(object).data('fancyselect'),
			dropdown = $('#fs-dropdown-' + data.id);
		
		//open/close dropdown
		dropdown.on('click', '.fs-dropdown-selected-item', function(event) {
			event.preventDefault();
			if (dropdown.hasClass('fs-dropdown-open'))
			{
				closeDropdown( object )
			}
			else
			{
				openDowndown( object );
			}
		});
		
		//selected elements
		dropdown.on('click', '.fs-dropdown-option a', function(event){
			event.preventDefault();
			
			var selected = $(this),
				value = selected.attr('data-value'),
				text = selected.attr('data-text'),
				img = selected.attr('data-img');
			
			$(this).parents('.fs-dropdown-options').find('li').removeClass('fs-dropdown-option-selected');
			$(this).parent('li').addClass('fs-dropdown-option-selected');
			
			//set the text of selected item
			dropdown.find('.fs-dropdown-selected-item span').text(text);
			
			//set the text of selected item
			dropdown.find('.fs-dropdown-selected-image').html('<img src="'+img+'" />');
			
			//find original select and set value
			dropdown.next('select').val( value );
			
			//run on open function
			if (typeof data.settings.onSelected == 'function')
			{
				data.settings.onSelected.call(this, data, {'value':value,'text':text,'image':img});
			}
			
			//close dropdown
			closeDropdown( object );
		})
		
		//click to close
		$('body').on('click', function(event) {
			closeAllOpenDropdowns();
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
			dropdownOptions = dropdown.find('.fs-dropdown-options');
		
		//show options
		dropdownOptions.hide().css('left','0').slideDown(200, function(){
			//add open class
			dropdown.addClass('fs-dropdown-open');
		});
	}
	
	function closeDropdown( object )
	{
		//get data
		var data = object.data('fancyselect'),
			dropdown = $('#fs-dropdown-' + data.id),
			dropdownOptions = dropdown.find('.fs-dropdown-options');
		
		//hide options
		dropdownOptions.slideUp(350, function(){
			//add open class
			dropdown.removeClass('fs-dropdown-open');
			
			//css position
			$(this).css('left', '-9999px');
		});
	}
	
	function closeAllOpenDropdowns()
	{
		$('.fs-dropdown-open').each(function() {
			$(this).removeClass('fs-dropdown-open');
			$(this).find('.fs-dropdown-options').slideUp(100, function(){
				$(this).css('left', '-9999px');
			});
		});
	}
})( jQuery );