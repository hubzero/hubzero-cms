//var myHorizontalSlide = null;

function slideIn(){
  myHorizontalSlide1 = new Fx.Slide('neesTreeWrapper', {mode: 'horizontal'});
  myHorizontalSlide1.slideIn();
}

function slideOut(){
  myHorizontalSlide2 = new Fx.Slide('neesTreeWrapper', {mode: 'horizontal'});
  myHorizontalSlide2.slideOut();
}

function toggle(){
	
	var oTreeLinkWrapper = document.getElementById('treeTabLinks');
	var oToggleLink = document.getElementById('h_toggle');
	var oTreeBrowserPanel = document.getElementById('treeBrowserMain');
	var oTreeWrapper = document.getElementById('treeSlideWrapperJs');
	var oOverviewSectionPanel = document.getElementById('overview_section');
	var bOpenStatus;
	
	if(oTreeWrapper.style.display == "none")
		bOpenStatus = false;
	else
		bOpenStatus = true;
		
	if(bOpenStatus){
	  oTreeBrowserPanel.style.width="3%";
	  oTreeWrapper.style.display="none";
	  oOverviewSectionPanel.style.width="97%";
	  oTreeLinkWrapper.style.textAlign="left";
	  oTreeLinkWrapper.style.margin="12px 0 0 0px";
	  //oToggleLink.innerHTML="&#187;";
	  oToggleLink.innerHTML="<img id='toggleArrow' src='/components/com_warehouse/images/icons/h_outArrow.png' border='0' title='Show tree browser.' onClick='toggle();'/>";
	  
	  bOpenStatus = false;
	}else{
	  oTreeBrowserPanel.style.width="29%";
	  oTreeWrapper.style.display="";
	  oOverviewSectionPanel.style.width="71%";
	  oTreeLinkWrapper.style.textAlign="right";
	  oTreeLinkWrapper.style.margin="12px 0 0 -20px";
	  //oToggleLink.innerHTML="&#171;";
	  oToggleLink.innerHTML="<img id='toggleArrow' src='/components/com_warehouse/images/icons/h_inArrow.png' border='0' title='Hide tree browser.' onClick='toggle();'/>";
	  
	  bOpenStatus = true;
	}
}

