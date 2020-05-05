/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (typeof(Joomla) == 'undefined')
{
	Joomla = {
		submitbutton: function(pressbutton)
		{
			return submitbutton(pressbutton);
		},

		submitform: function(pressbutton)
		{
			return submitform(pressbutton);
		}
	};
}