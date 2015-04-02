jQuery(document).ready(function($)
{
	var inps = document.getElementById('hubForm').getElementsByTagName('input');
	var wallet = document.getElementById('wallet').getElementsByTagName('span')[0];
	var wallet_par = document.getElementById('wallet');
	var showing = 0;
	for (var idx = 0; idx < inps.length; ++idx)
		if (window.bonus_eligible_fields[inps[idx].getAttribute('name')])
			(function(inp)
			{
				var timer = null;
				var progress = 0;
				var dir = null;
				$(inp).on('keyup', function()
				{
					if (inp.value == '' && dir != 'down' && progress)
					{
						dir = 'down';
						++showing;
						if (timer)
							clearInterval(timer);
						wallet_par.style.display = 'block';
						wallet.style.color = '#900';
						timer = setInterval(function()
						{
							wallet.innerHTML = wallet.innerHTML*1 - 1;
							if (--progress == 0)
							{
								clearInterval(timer)
								timer = null;
								setTimeout(function() { if (--showing == 0) wallet_par.style.display = 'none'; }, 2500);
							}
						}, 20);
					}
					else if (inp.value != '' && dir != 'up')
					{
						dir = 'up'
						++showing;
						if (timer)
							clearInterval(timer);

						wallet_par.style.display = 'block';
						wallet.style.color = '#090';
						timer = setInterval(function()
						{
							wallet.innerHTML = wallet.innerHTML*1 + 1;
							if (++progress == window.bonus_amount)
							{
								clearInterval(timer);
								timer = null;
								setTimeout(function() { if (--showing == 0) wallet_par.style.display = 'none'; }, 2500);
							}
						}, 20);
					}
				});
			})(inps[idx]);
});
