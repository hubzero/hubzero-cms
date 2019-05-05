/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

var page = require('webpage').create(),
	system = require('system'),
	address, output, size;
	
if (system.args.length < 3 || system.args.length > 5)
{
	console.log('Usage: rasterize.js URL filename [paperwidth*paperheight|paperformat] [zoom]');
	console.log('  paper (pdf output) examples: "5in*7.5in", "10cm*20cm", "A4", "Letter"');
	console.log(system.args);
	phantom.exit(1);
}
else
{
	address = system.args[1];
	output = system.args[2];
	page.viewportSize = { width: 800, height: 768 };
	
	if (system.args.length > 3 && system.args[2].substr(-4) === ".pdf")
	{
		size = system.args[3].split('*');
		page.paperSize = size.length === 2 ? { width: size[0], height: size[1], margin: '0in' } : { format: system.args[3], orientation: 'portrait', margin: '0.3in' };
	}
	
	if (system.args.length > 4) 
	{
		page.zoomFactor = system.args[4];
	}
	
	page.open(address, function (status) {
		if (status !== 'success') 
		{
			console.log('Unable to load the address!');
			phantom.exit();
		}
		else
		{
			window.setTimeout(function () {
				//page.clipRect = { left: 0, top: 0, width: 800, height: 708 };
				page.render(output);
				phantom.exit();
			}, 200);
		}
	});
}