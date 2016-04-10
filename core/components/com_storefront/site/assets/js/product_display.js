$(document).ready(function() {

	SF.PRODUCT.skus = (SF.OPTIONS.skus);
	SF.PRODUCT.ops = (SF.OPTIONS.ops);
	SF.PRODUCT.pId = (SF.OPTIONS.pId);
	
	SF.PRODUCT.deselectAll();

	$('#productOptions input:radio').change(function() {
		var me = $(this);
		var optionGroupName = me.attr('name');

		// If selected option is unavailable reset other selections	
		if(me.isUnavailable()) {
			// reset all selections
			SF.PRODUCT.resetAllSelectedOptions();
			// reset selections
			SF.PRODUCT.deselectAll();			
		}
		else {	
			// get the old value
			var optionGroup = $('input[name="' + optionGroupName + '"]');
			var oldValue = optionGroup.data('selectedValue');
			// remove old value from the list of selections
			SF.PRODUCT.removeSelectedOption(oldValue);
			
			// deselect all options in in the current group
			optionGroup.each(function(index) {
				SF.PRODUCT._deselectOption(this);				
			});
		}
		
		// set the new value
		$('input[name="' + optionGroupName + '"]').data('selectedValue', me.val());
		// add a new value to the list of selections
		SF.PRODUCT.addSelectedOption(me.val());
		
		// select current
		SF.PRODUCT.selectOption(this);
		
		SF.PRODUCT.updateOptions();
		SF.PRODUCT.updatePriceQty();

		// update url
		//console.log(SF.PRODUCT.selectedOptions);
		var urlHash = '/storefront/product/' + SF.PRODUCT.pId + '/';
		var hashInit = urlHash;
		$.each(SF.PRODUCT.selectedOptions, function(key, val) {
			if(urlHash !== hashInit) {
				urlHash += ',';
			}
			urlHash += val;
		});
		history.replaceState(null, null, urlHash);
    });

	$('.product-options li').click(function() {
		$(this).find('input').prop("checked", true).trigger("change");
	});

	// Check if specific product options are requested in the URL
	var pathname = (window.location.pathname).replace('/storefront/product/' + SF.PRODUCT.pId + '/', '');
	var pathname = pathname.replace('/', '');

	//console.log(pathname);

	if(pathname !== '') {
		var productOptions = pathname.split(',');

		$('#productOptions .product-options input').each(function(index) {
			var input = $(this);
			if(productOptions.indexOf($(input).val()) > -1) {
				console.log(input);
				input.prop("checked", true).trigger("change");
			}

		});
	}
	else {
		console.log('q');
		// Auto-select single product options (https://freedcamp.com/wl_14B/Shopping_Cart_Tk2/todos/2197226/)
		$('#productOptions .product-options').each(function (index) {
			var inputs = $(this).find('input');
			if (inputs.length == 1) {
				inputs.prop("checked", true).trigger("change");
			}
		});
	}

	SF.PRODUCT.updateOptions();
	SF.PRODUCT.updatePriceQty();

});

/* ---------------------------------------------------------------------------------------------------------------------------*/
/* ----------------------------- *************************************************************** -----------------------------*/
/* ---------------------------------------------------------------------------------------------------------------------------*/

(function($) {
    // declare var in global scope
    window.SF = {};

    SF.PRODUCT = {
		
		selectedOptions: [],
		skus: null,
		ops: null,
		pId: null,
		
		resetAllSelectedOptions: function(opt) {
			SF.PRODUCT.selectedOptions.length = 0;
			//console.log(SF.PRODUCT.selectedOptions);
		},
		
		removeSelectedOption: function(opt) {
			if((indexof = SF.PRODUCT.selectedOptions.indexOf(opt)) != -1) {
				SF.PRODUCT.selectedOptions.splice(indexof, 1);	
			}
		},
		
		addSelectedOption: function(opt) {
			SF.PRODUCT.selectedOptions.push(opt);
		},
		
		deselectAll: function(opt) {
			$('#productOptions input:radio').each(function(index) {
				SF.PRODUCT._deselectOption(this);				
			});			
		},
		
		_deselectOption: function(opt) {
			$(opt).prop('checked', false).closest('li').removeClass('selected');	
		},
		
		selectOption: function(opt) {
			$(opt).prop('checked', true).closest('li').addClass('selected');	
		},
		
		disableOption: function(opt) {
			$(opt).closest('li').removeClass('available').addClass('unavailable');
		},
		
		enableOption: function(opt) {
			$(opt).closest('li').removeClass('unavailable').addClass('available');
		},
		
		updateOptions: function() {
			var availableOptions = SF.PRODUCT._getAvailableOptions();
						
			// update options availability
			$('#productOptions input:radio').each(function(index) {
				var val = ($(this).val());
				if(($.inArray(val, availableOptions)) == -1) {
					SF.PRODUCT.disableOption(this);
				}
				else {
					SF.PRODUCT.enableOption(this);
				}
			});
			
			$.each(availableOptions, function(key, val) {
				
			});
		},
		
		/*
			Update price/price range based on the current selection
		*/
		updatePriceQty: function() {
			var highestPrice = 0;
			var lowestPrice = null;
			var skuMatch = false;
			var matchKey = null;
			
			// Find how many selected options required for a SKU (just look for the length of any SKU options)
			optionsNeeded = SF.PRODUCT.skus[0].length;			
						
			// go through each sku and see if there is a match
			$.each(SF.PRODUCT.skus, function(key, skuOptions) {
				// find those SKUs that have a given combination of selected options
				var matchFound = SF.PRODUCT._subtractArrays(skuOptions, SF.PRODUCT.selectedOptions, true);
				
				if(matchFound) {
					// get price of the matched SKU and update highest and lowest prices
					if(highestPrice < parseInt(SF.OPTIONS.skuPrices[key])) {
						highestPrice = parseInt(SF.OPTIONS.skuPrices[key]);
					}
					
					if(lowestPrice == null || lowestPrice > parseInt(SF.OPTIONS.skuPrices[key])) {
						lowestPrice = parseInt(SF.OPTIONS.skuPrices[key]);
					}
					
					skuMatch = true;
					matchKey = key;
				}
			});
			
			var priceRange = '$';
			
			if(lowestPrice == highestPrice) {
				lowestPrice = lowestPrice / 100;
				priceRange += lowestPrice.formatMoney(2, '.', ',');
			}
			else {
				lowestPrice = lowestPrice / 100;
				highestPrice = highestPrice / 100;
				
				priceRange += lowestPrice.formatMoney(2, '.', ',');
				priceRange += ' &ndash; $' + highestPrice.formatMoney(2, '.', ',');	
			}
			
			$('#price').html(priceRange);
			
			// update qty gropdown either update the number or remove the dropdown
			// If not enough options selected -- no match
			if(optionsNeeded != SF.PRODUCT.selectedOptions.length) {
				skuMatch = false;
			}

			//console.log(skuMatch + ' -- ' +  matchKey);
			SF.PRODUCT._updateQty(skuMatch, matchKey);
		},
		
		/*
			Update available SKU qty based on the current selection
		*/
		_updateQty: function(skuMatch, key) {
			
			// find out if the current selection identifies the SKU
			if(skuMatch) {
				if(SF.OPTIONS.skuInventory[key] > 1) {
					// check if dropdown exist
					if ($("#qty").length == 0) {
						// create a new drop-down
						var dropDown = $('<select />', {
							id: 'qty',
							name: 'qty'
						});

						var inner = $('<div class="inner" />');

						inner.append('<label>Quantity </label>');
						inner.append(dropDown);

						$('#qtyWrap').append(inner);
					}
					// populate dropdown
					var dropDown = $('#qty');
					dropDown.html('');

					for (var i = 1; i <= SF.OPTIONS.skuInventory[key]; i++) {
						dropDown.append('<option value="' + i + '">' + i + '</option>')
					}
				}
				else {
					$('#qtyWrap').html('');
				}

				// enable 'add to cart' button
				$('#addToCart').removeClass('disabled').prop('disabled', false).addClass('enabled');
				
			}
			else {
				$('#qtyWrap').html('');
				//$("#qty").remove();
				$('#addToCart').prop('disabled', true).removeClass('enabled').addClass('disabled');
			}
			
		},
		
		_getAvailableOptions: function() {
			var availableOptions = [];
			
			// go through each line of options
			$.each(SF.PRODUCT.ops, function(key, val) {
				// remove any of elements in current options line from selected options
				var remainingRowOptions = SF.PRODUCT._subtractArrays(SF.PRODUCT.selectedOptions, val, false);
				
				// If there are other options left, find what is available for these remaining options for this options line
				if(remainingRowOptions.length > 0) {
					$.merge(availableOptions, SF.PRODUCT._getAvailableOptionsForLine(remainingRowOptions, val));
				}
				else {
					// all options available for this line
					$.merge(availableOptions, val);
				}				
			});
			
			return(availableOptions);
		},
		
		/*
		 * Looks at all SKUs and finds available options given the current selection of options
		 * Pool of options (array) defins a scope of values to return (all other discarded)
		 */
		_getAvailableOptionsForLine: function(selectedOps, poolOfOptions) {
			var availableOptions = [];
			
			// go through each line of skus
			$.each(SF.PRODUCT.skus, function(key, val) {
				// find those SKUs that have a given combination of selected options and return available (remaining) options
				var lineAvailableOptions = SF.PRODUCT._subtractArrays(val, selectedOps, true);
				
				if(lineAvailableOptions) {
					// push all available options to a global function availableOptions array
					$.merge(availableOptions, lineAvailableOptions);
				}
			});
			
			// filter out options that are not in the pool of options 
			// (Remove all duplicate elements from an array)
			// Remove poolOfOptions from available elements to get the unwanted items...
			// ..and then remove unwanted items from available elements
			availableOptions = SF.PRODUCT._uniqueArray(availableOptions);			
			var uselessOptions = SF.PRODUCT._subtractArrays(availableOptions, poolOfOptions);
			availableOptions = SF.PRODUCT._subtractArrays(availableOptions, uselessOptions);
			
			return(availableOptions);
			
		},
		
		/*
		 * Subtracts all the elements of the arr2 from arr1 elements. 
		 * If strict, then returns false if arr1 doesn't contain at least one element from arr2
		 */
		_subtractArrays: function(arr1, arr2, strict) {
			// make a copy of arr1, since arrays are passed by reference
			var returnArray = arr1.slice(0); // slice returns a copy of the array, not the reference	
			var matchFound = true;
			
			$.each(arr2, function(k, v) {
				if((indexof = $.inArray(v, returnArray)) != -1) {
					while((indexof = $.inArray(v, returnArray)) != -1) {
						returnArray.splice(indexof, 1);	
					}
				}
				else if(strict) {
					matchFound = false;
					return false;	
				}
			});
			
			if(!matchFound) {
				return false;
			}
			return returnArray;
		},
		
		/*
		 * Remove duplicate values from array
		 */
		_uniqueArray: function(arr) {
			var returnArray = [];
			
			$.each(arr, function(key, val) {
				if((indexof = $.inArray(val, returnArray)) == -1) {
					returnArray.push(val);
				}
			});
			
			return(returnArray);
		}
		
    }
 
})(jQuery);

jQuery.fn.isUnavailable = function(obj) {
	var obj = $(this[0]);
	if(obj.closest('li').hasClass('unavailable')) {
		return true;
	}
};

Number.prototype.formatMoney = function(c, d, t){
var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };