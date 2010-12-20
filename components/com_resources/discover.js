/**
*
* Resource Discovery Tool
* Written by Jason Lambert
*/

//----------------------------------------------------------
// Establish the namespace if it doesn't exist
//----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

var formName = "#tagBrowserForm";
//----------------------------------------------------------
//function toggleitem(classname, fromtext)
function toggleitem(classname)
{
	var sclassname = '.' + 'ritem' + classname;
	var divclassname = '.' + 'rdivitem' + classname;
	$jQ( formName ).find( ".resourcediscoverimage" ).filter(sclassname).toggle();
	$jQ( formName  ).find( ".resourcediscovertext" ).filter(sclassname).toggle(); 

}

function leaveReview(res_id)
{
	//$jQ("#resquicksubmit").attr("href", "/resources/" + res_id + "/reviews/");
	//$jQ("#resquicksubmit").trigger('click');
	if (window!=window.top) { window.top.location = "/resources/" + res_id + "/reviews/"; }
	else
	window.location = "/resources/" + res_id + "/reviews/";
}

function fullresolution(classname)
{
	var sclassname = '#' + 'fullres' + classname;
	$jQ( sclassname ).trigger('click');
}

//----------------------------------------------------------
// Discover Browser
//----------------------------------------------------------
HUB.DiscoverBrowser = {

	baseURI: 'index.php?option=com_resources&task=discover&no_html=1',
	
	
	initialize: function() {
	
		//
		// JQuery UI for Browser
		//
		//$jQ( "#slider" ).slider();
		//$jQ("#sortablelist").sortable();
		//$jQ("#sortablelist").disableSelection();
		//$jQ(".dragitem li").draggable({
		//	helper: "clone"
		//});
		//$jQ( "#droplocation" ).droppable({
		//	activeClass: "ui-state-default",
		//	hoverClass: "ui-state-hover",
		//	accept: ":not(.ui-sortable-helper)",
		//	drop: function( event, ui ) {
		//		$jQ( this ).find( ".placeholder" ).remove();
		//		$jQ( "<li class='ui-state-default'></li>" ).text( ui.draggable.text() ).appendTo( this );
		//	}
		//}).sortable({
		//	items: "li:not(.placeholder)",
		//	sort: function() {
		//		$jQ( this ).removeClass( "ui-state-default" );
		//	}
		//});
		
		//
		// Lightboxes
		//
		$jQ("#resquicksubmit").fancybox( { 'width' : '80%', 'height' : 710, 'type' : 'iframe'  });
		$jQ(".discoverfullresoltuion").fancybox( { 'type' : 'image'  });
		
		//
		// Tag Cloud Stuff
		$jQ('#clouddroplocation').append($jQ('#tagcloudsection'));
		$jQ('ul#tagcloudblock>li').tsort({attr:"value",order:"desc"});
		$jQ('#tagcloudblock').tagcloud({type:"list",sizemin:12, colormin:"666699",colormax:"3366FF"})
		//
		
	},
	
	inlines: function() {
			$jQ(".riteminline").fancybox( {  'width' : '80%'} );
			$jQ(".previewfullresolutionimage").fancybox( {  'width' : '80%'} );
	}


}

window.addEvent('domready', HUB.DiscoverBrowser.inlines);