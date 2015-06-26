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