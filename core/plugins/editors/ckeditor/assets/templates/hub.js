/**
	Copyright Info
*/

CKEDITOR.addTemplates('hubzero',{
	imagesPath: '/core/plugins/editors/ckeditor/assets/templates/template-images/',
	templates: [
		{
			title: 'Left Sidebar',
			image: 'leftsidebar.png',
			description: 'Two column Layout with left sidebar and Title',
			html: '<h2>Page Title</h2><div class="grid"><div class="col span3">Left Sidebar</div><div class="col span9 omega">Main Content</div></div>'
		},
		{
			title: 'Right Sidebar',
			image: 'rightsidebar.png',
			description: 'Two column Layout with right sidebar and Title',
			html: '<h2>Page Title</h2><div class="grid"><div class="col span9">Main Content</div><div class="col span3 omega">Right Sidebar</div></div>'
		},
		{
			title: 'Two Columns',
			image: 'twocolumns.png',
			description: 'Two column layout',
			html: '<div class="grid"><div class="col span6">Column 1</div><div class="col span6 omega">Column 2</div></div>'
		},
		{
			title: 'Three Columns',
			image: 'threecolumns.png',
			description: 'Three Column Layout',
			html: '<div class="grid"><div class="col span4">Column 1</div><div class="col span4">Column 2</div><div class="col span4 omega">Column 3</div></div>'
		}
	]
});