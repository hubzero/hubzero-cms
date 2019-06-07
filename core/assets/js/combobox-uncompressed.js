// Only define the Hubzero namespace if not defined.
if (typeof(Hubzero) === 'undefined') {
	var Hubzero = {};
}

/**
* Combobox JavaScript behavior.
*
* Inspired by: Subrata Chakrabarty <http://chakrabarty.com/editable_dropdown_samplecode.html>
*/
Hubzero.combobox = {};
Hubzero.combobox.transform = function(el, options)
{
	el = $(el);
	// Add the editable option to the select.
	var o = $('<option class="custom">')
				.attr('text', Hubzero.Lang.txt('ComboBoxInitString', 'type custom...'))
				.prependTo(el);

	el.attr('changeType', 'manual');

	// Add a key press event handler.
	el.on('keypress', function(e){

		// The change in selected option was browser behavior.
		if ((this.options.selectedIndex != 0) && (this.attr('changeType') == 'auto'))
		{
			this.options.selectedIndex = 0;
			this.attr('changeType', 'manual');
		}

		// Check to see if the character is valid.
		if ((e.code > 47 && e.code < 59) || (e.code > 62 && e.code < 127) || (e.code == 32)) {
			var validChar = true;
		} else {
			var validChar = false;
		}

		// If the editable option is selected, proceed.
		if (this.options.selectedIndex == 0)
		{
			// Get the custom string for editing.
			var customString = this.options[0].value;

			// If the string is being edited for the first time, nullify it.
			if ((validChar == true) || (e.key == 'backspace'))
			{
				if (customString == Hubzero.Lang.txt('ComboBoxInitString', 'type custom...')) {
					customString = '';
				}
			}

			// If the backspace key was used, remove a character from the end of the string.
			if (e.key == 'backspace')
			{
				customString = customString.substring(0, customString.length - 1);
				if (customString == '') {
					customString = Hubzero.Lang.txt('ComboBoxInitString', 'type custom...');
				}

				// Indicate that the change event was manually initiated.
				this.attr('changeType', 'manual');
			}

			// Handle valid characters to add to the editable option.
			if (validChar == true)
			{
				// Concatenate the new character to the custom string.
				customString += String.fromCharCode(e.code);
			}

			// Set the new custom string into the editable select option.
			this.options.selectedIndex = 0;
			this.options[0].text = customString;
			this.options[0].value = customString;

			e.stop();
		}
	});

	// Add a change event handler.
	el.on('change', function(e){
		// The change in selected option was browser behavior.
		if ((this.options.selectedIndex != 0) && (this.get('changeType') == 'auto')) {
			this.options.selectedIndex = 0;
			this.attr('changeType', 'manual');
		}
	});

	// Add a keydown event handler.
	el.on('keydown', function(e){
		// Stop the backspace key from firing the back button of the browser.
		if (e.code == 8 || e.code == 127) {
			e.stop();

			// Stopping the keydown event in WebKit stops the keypress event as well.
			/*if (Browser.Engine.webkit || Browser.Engine.trident) {
				this.fireEvent('keypress', e);
			}*/
		}

		if (this.options.selectedIndex == 0) {
			/*
			 * In some browsers a feature exists to automatically jump to select options which
			 * have the same letter typed as the first letter of the option.  The following
			 * section is designed to mitigate this issue when editing the custom option.
			 *
			 * Compare the entered character with the first character of all non-editable
			 * select options.  If they match, then we assume the change happened because of
			 * the browser trying to auto-change for the given character.
			 */
			var character = String.fromCharCode(e.code).toLowerCase();
			for (var i = 1; i < this.options.length; i++)
			{
				// Get the first character from the select option.
				var FirstChar = this.options[i].value.charAt(0).toLowerCase();

				// If the first character matches the entered character, the change was automatic.
				if ((FirstChar == character)) {
					this.options.selectedIndex = 0;
					this.set('changeType', 'auto');
				}
			}
		}
	});

	// Add a keyup event handler.
	el.on('keyup', function(e){

		// If the left or right arrow keys are pressed, return to the editable option.
		if ((e.key == 'left') || (e.key == 'right')) {
			this.options.selectedIndex = 0;
		}

		// The change in selected option was browser behavior.
		if ((this.options.selectedIndex != 0) && (this.attr('changeType') == 'auto'))
		{
			this.options.selectedIndex = 0;
			this.attr('changeType', 'manual');
		}
	});

};

// Load the combobox behavior into the Hubzero namespace when the document is ready.
jQuery(document).ready(function($){
	$('select.combobox').each(function(i, el){
		Hubzero.combobox.transform(el);
	});
});
