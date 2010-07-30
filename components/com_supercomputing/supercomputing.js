window.addEvent('domready', function()
{
	var btn = $('add-user-button');
	btn.style.visibility = 'visible';
	var next_idx = null;
	for (var idx = 1; $('person-info-' + idx); ++idx)
	{
		if (!$('first-name-' + idx).value)
		{
			$('person-info-' + idx).style.display = 'none';
		
			if (next_idx === null)
				next_idx = idx;
		}
	}
	btn.addEvent('click', function()
	{
		var next = $('person-info-' + next_idx);
		if (next)
		{
			next.style.display = 'block';
			++next_idx;
		}
		if (!$('person-info-' + next_idx))
			btn.disabled = true;
	});
});

