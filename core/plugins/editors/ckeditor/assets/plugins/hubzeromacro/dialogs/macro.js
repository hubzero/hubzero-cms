/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

CKEDITOR.dialog.add( 'hubzeroMacroDialog', function( editor ) {
	return {
		title: 'Macros List',
		width: 800,
		resizable: 0,
		contents: [
			{
				id: 'basic',
				label: 'Basic Settings',
				elements: [
					{
						type: 'iframe',
						src : '/help/content/formathtml/macros',
						width : '100%',
						height : 500,
						onContentLoad : function() {}
					}
				]
			}
		],
		onLoad: function(event) {},
		onShow: function(event) {},
		onOk: function(event) {}
	}
});
