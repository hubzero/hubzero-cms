/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
		
	config.toolbar = 'NEESLearningObject';

    config.toolbar_NEESLearningObject =
    [
        ['Preview','Maximize'],
        ['Find','Replace','-','SelectAll','RemoveFormat'],
        '/',
        ['Image'],['Table','HorizontalRule','SpecialChar'],
        ['Styles','Format'],
        ['Link','Unlink'],        
         '/',
         ['Cut','Copy','Paste','PasteText'],
         ['Bold','Italic','Strike'],
         ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],		
        
    ];		config.toolbar = 'NEESLearningObjectPOWER';    config.toolbar_NEESLearningObjectPOWER =    [        ['Preview','Maximize'],        ['Find','Replace','-','SelectAll','RemoveFormat'],        '/',        ['Image'],['Table','HorizontalRule','SpecialChar'],        ['Styles','Format'],        ['Link','Unlink'],                 '/',         ['Cut','Copy','Paste','PasteText'],         ['Bold','Italic','Strike'],         ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],		            ];
	
	config.toolbar = 'NEESAbstract';

    config.toolbar_NEESAbstract =
    [		['Image'],		
        ['Cut','Copy','Paste','PasteText'],
        ['Bold','Italic','Strike'],
        ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
    ];		config.toolbar = 'NEESBasicDescription';    config.toolbar_NEESBasicDescription =    [		        ['Cut','Copy','Paste','PasteText'],    ];
};
