window.addEvent('domready', function(){	
	
	var bOpenStatus = true;		//show tree by default
	
	//--horizontal
	
	var myHorizontalSlide = new Fx.Slide('neesTreeWrapper', {mode: 'horizontal'});
	
	$('h_slidein').addEvent('click', function(e){
		e = new Event(e);
		myHorizontalSlide.slideIn();
		e.stop();
	});
	
	$('h_slideout').addEvent('click', function(e){
		e = new Event(e);
		myHorizontalSlide.slideOut();
		e.stop();
	});
	
//	$('h_toggle').addEvent('click', function(e){
//		e = new Event(e);
//		myHorizontalSlide.toggle();
//		alert('toggle');
//		var oTreeLinkWrapper = document.getElementById('treeTabLinks');
//		var oToggleLink = document.getElementById('h_toggle');
//		var oTreeBrowserPanel = document.getElementById('neesTreeWrapper');
//		var oTreeWrapper = document.getElementById('treeSlideWrapperJs');
//		var oOverviewSectionPanel = document.getElementById('overview_section');
//		
//		if(bOpenStatus){
//		  oTreeBrowserPanel.style.width="3%";
//		  oTreeWrapper.style.display="none";
//		  oOverviewSectionPanel.style.width="97%";
//		  oTreeLinkWrapper.style.textAlign="left";
//		  oTreeLinkWrapper.style.margin="12px 0 0 0px";
//		  //oToggleLink.innerHTML="&#187;";
//		  oToggleLink.innerHTML="<img id='toggleArrow' src='/components/com_warehouse/images/icons/h_outArrow.png' border='0' title='Show tree browser.'/>";
//		  
//		  bOpenStatus = false;
//		}else{
//		  oTreeBrowserPanel.style.width="29%";
//		  oTreeWrapper.style.display="";
//		  oOverviewSectionPanel.style.width="71%";
//		  oTreeLinkWrapper.style.textAlign="right";
//		  oTreeLinkWrapper.style.margin="12px 0 0 -20px";
//		  //oToggleLink.innerHTML="&#171;";
//		  oToggleLink.innerHTML="<img id='toggleArrow' src='/components/com_warehouse/images/icons/h_inArrow.png' border='0' title='Hide tree browser.'/>";
//		  
//		  bOpenStatus = true;
//		}
//		e.stop();
//	});
	
	alert('dom ready');
}); 
