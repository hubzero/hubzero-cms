<?php 

  class TabHtml{
  	
  	/**
  	 * 
  	 * @see com_groups/groups.html.php
  	 */
    public static function getTabsByAction( $p_strOption, $p_iId, $p_oTabArray, $p_strActive='default' ) {
	  $strHtml  = '<div id="sub-menu">';
	  $strHtml .= '<ul>';
	  $i = 1;
	  foreach ($p_oTabArray as $strTabArray){
	    //$strName = key($strTabArray);
	    $strName = $strTabArray;
	    if ($strName != '') {
		  $strHtml .= '<li id="sm-'.$i.'"';
		  $strHtml .= (strtolower($strName) == $p_strActive) ? ' class="active"' : '';
		  $strHtml .= '><a class="tab" rel="'.$strName.'" href="'.JRoute::_('index.php?option='.$p_strOption.'&'.'id='.$p_iId.'&'.'active='.$strName).'"><span>'.$strName.'</span></a></li>';
		  $i++;
	    }
	  }
	  $strHtml .= '</ul>';
	  $strHtml .= '<div class="clear"></div>';
	  $strHtml .= '</div><!-- / #sub-menu -->'; 
	
	  return $strHtml;
    }
    
    public static function getTabs( $p_strOption, $p_iId, $p_oTabArray, $p_oTabViewArray, $p_strActive='default' ) {
	  $strHtml  = '<div id="sub-menu" style="position:relative;z-index:3">';
	  $strHtml .= '<ul>';
	  $i = 1;
	  foreach ($p_oTabArray as $iTabIndex=>$strName){
	    $strView = $p_oTabViewArray[$iTabIndex];
            if ($strName != '') {
		  $strHtml .= '<li id="sm-'.$i.'"';
		  $strHtml .= (strtolower($strView) == $p_strActive) ? ' class="active"' : '';
		  $strHtml .= '><a class="tab" rel="'.$strName.'" href="'.$p_strOption;
                  if($strView != ""){
                    $strHtml .="/".strtolower($strView);
                  }
                  if($p_iId != ""){
                    $strHtml .="/".$p_iId;
                  }
                  $strHtml .= '"><span>'.$strName.'</span></a></li>';
		  $i++;
	    }
	  }
	  $strHtml .= '</ul>';
	  $strHtml .= '<div class="clear"></div>';
	  $strHtml .= '</div><!-- / #sub-menu -->';
	
	  return $strHtml;
    }

    public static function getOnClickTabs( $p_oTabArray, $p_oTabViewArray, $p_strActive='default' ) {
	  $strHtml  = '<div id="sub-menu" style="position:relative;z-index:3">';
	  $strHtml .= '<ul>';
	  $i = 1;
	  foreach ($p_oTabArray as $iTabIndex=>$strName){
	    $strView = $p_oTabViewArray[$iTabIndex];
	    if ($strName != '') {
                  $onClick = (strtolower($strView) == $p_strActive) ? "" : $strView;
		  $strHtml .= '<li id="sm-'.$i.'"';
		  $strHtml .= (strtolower($strView) == $p_strActive) ? ' class="active"' : '';
		  $strHtml .= '><a class="tab" rel="'.$strName.'" href="javascript:void(0);" onClick="'.$onClick.'"><span>'.$strName.'</span></a></li>';
		  $i++;
	    }
	  }
	  $strHtml .= '</ul>';
	  $strHtml .= '<div class="clear"></div>';
	  $strHtml .= '</div><!-- / #sub-menu -->';

	  return $strHtml;
    }
    
    public static function getSubTabs( $p_strOption, $p_iId, $p_oTabArray, $p_oTabViewArray, $p_strActive='default' ) {
	  $strHtml  = '<div id="sub-sub-menu" style="position:relative;z-index:3; border-bottom: 1px solid #cccccc; padding:0; height: 1.77em;">';
	  $strHtml .= '<ul>';
	  $i = 1;
	  foreach ($p_oTabArray as $iTabIndex=>$strTabArray){
	    $strName = $strTabArray;
	    $strView = $p_oTabViewArray[$iTabIndex];

	    if ($strName != '') {
              $strHtml .= '<li';
              $strHtml .= (strtolower($strName) == $p_strActive) ? ' class="active"' : '';
              if($p_iId){
                $strHtml .= '><a class="tab" rel="'.$strName.'" href="'.$p_strOption."/".$p_iId."/".strtolower($strView).'"><span>'.$strName.'</span></a></li>';
              }else{
                $strHtml .= '><a class="tab" rel="'.$strName.'" href="'.$p_strOption."/".strtolower($strView).'"><span>'.$strName.'</span></a></li>';
              }
              $i++;
	    }
	  }
	  $strHtml .= '</ul>';
	  $strHtml .= '<div class="clear"></div>';
	  $strHtml .= '</div><!-- / #sub-sub-menu -->';
	
	  return $strHtml;
    }

    public static function getOnClickSubTabs( $p_strAlert, $p_oTabArray, $p_strActive='default' ) {
	  $strHtml  = '<div id="sub-sub-menu" style="position:relative;z-index:3; border-bottom: 1px solid #cccccc; padding:0; height: 1.77em;">';
	  $strHtml .= '<ul>';
	  $i = 1;
	  foreach ($p_oTabArray as $strTabArray){
	    $strName = $strTabArray;
	    $strView = $strName;
	    $strNameArray = explode(" ", $strName);
	    if(sizeof($strNameArray) == 2){
	      $strView = $strNameArray[1];
	    }
	    if ($strName != '') {
                  $strOnClick = (strtolower($strName) == $p_strActive) ? "" : $p_strAlert;
		  $strHtml .= '<li';
		  $strHtml .= (strtolower($strName) == $p_strActive) ? ' class="active"' : '';
		  $strHtml .= '><a class="tab" rel="'.$strName.'" href="javascript:void(0);" onClick="'.$strOnClick.'"><span>'.$strName.'</span></a></li>';
		  $i++;
	    }
	  }
	  $strHtml .= '</ul>';
	  $strHtml .= '<div class="clear"></div>';
	  $strHtml .= '</div><!-- / #sub-sub-menu -->';

	  return $strHtml;
    }
    
    /*
    public static function getTreeTab( $p_strOption, $p_iId, $p_oTabArray, $p_strActive='default' ) {
	  $strHtml = '<div id="treeTabLinks" style="text-align:left; margin:12px 0 0 0px">';
	  //$strHtml .= '  <a id="h_toggle" href="#">&#171;</a>';
	  $strHtml .= '  <a id="h_toggle" href="javascript:void(0);" style="border-bottom: 0px"><img id="toggleArrow" src="/components/com_warehouse/images/icons/h_outArrow.png" border="0" title="Show tree browser." onClick="toggle();"/></a>';
	  $strHtml .= '</div>';
      $strHtml .= '<div id="sub-menu-treebrowser">';
	  $strHtml .= '<ul>';
	  $i = 0;
//	  foreach ($p_oTabArray as $strTabArray){
//	    //$strName = key($strTabArray);
//	    $strName = $strTabArray;
//	    if ($strName != '') {
//		  $strHtml .= '<li id="tb-'.$i.'"';
//		  $strHtml .= (strtolower($strName) == $p_strActive) ? ' class="active"' : '';
//		  $strHtml .= '><a class="tab" rel="'.$strName.'" href="javascript:void(0);"><span>'.$strName.'</span></a></li>';
//		  $i++;
//	    }
//	  }
	  $strHtml .= '</ul>';
	  $strHtml .= '<div class="clear"></div>';
	  $strHtml .= '</div><!-- / #sub-menu-treebrowser -->';
	  return $strHtml;
    }
*/
    
    public static function getTreeTab( $p_strOption, $p_iId, $p_oTabArray, $p_strActive='default', $minimized) {
	  
    	
      if($minimized == true)
      {
	  		$strHtml = '<div id="treeTabLinks" style="text-align:left; margin:12px 0 0 0px">';
	  		$strHtml .= '  <a id="h_toggle" href="javascript:void(0);" style="border-bottom: 0px"><img id="toggleArrow" src="/components/com_warehouse/images/icons/h_outArrow.png" border="0" title="Show tree browser." onClick="toggle();"/></a>';
      }
	  else
	  {
	  		$strHtml = '<div id="treeTabLinks" style="text-align:right;">';
	  		$strHtml .= '  <a id="h_toggle" href="javascript:void(0);" style="border-bottom: 0px"><img id="toggleArrow" src="/components/com_warehouse/images/icons/h_inArrow.png" border="0" title="Show tree browser." onClick="toggle();"/></a>';
	  }
	  	  
	  //$strHtml .= '  <a id="h_toggle" href="#">&#171;</a>';
	  $strHtml .= '</div>';
      $strHtml .= '<div id="sub-menu-treebrowser">';
	  $strHtml .= '<ul>';
	  $i = 0;
//	  foreach ($p_oTabArray as $strTabArray){
//	    //$strName = key($strTabArray);
//	    $strName = $strTabArray;
//	    if ($strName != '') {
//		  $strHtml .= '<li id="tb-'.$i.'"';
//		  $strHtml .= (strtolower($strName) == $p_strActive) ? ' class="active"' : '';
//		  $strHtml .= '><a class="tab" rel="'.$strName.'" href="javascript:void(0);"><span>'.$strName.'</span></a></li>';
//		  $i++;
//	    }
//	  }
	  $strHtml .= '</ul>';
	  $strHtml .= '<div class="clear"></div>';
	  $strHtml .= '</div><!-- / #sub-menu-treebrowser -->';
	  return $strHtml;
    }
    
    
    
    public static function getSearch( $p_strFormId, $p_strAction ){
      $strHTML = <<< ENDHTML
              <div class="tabKeywordSearch" id="warehouseSearch">
		        <input type="text" name="keywords" value="warehouse search" style="color:#999999" onClick="this.value='';">
		        <input type="button" value="GO" onClick="document.getElementById('$p_strFormId').action='$p_strAction';document.getElementById('$p_strFormId').submit();">
		      </div>
ENDHTML;
      return $strHTML;
    }
    
    public static function getSearchForm( $p_strAction ){
      $strHTML = <<< ENDHTML
              <div class="tabKeywordSearch" id="warehouseSearch">
		        <form action=$p_strAction method="get">
                  <input type="text" name="keywords" value="warehouse search" style="color:#999999" onClick="this.value='';">
		          <input type="submit" value="GO">
		        </form>
		      </div>
ENDHTML;
      return $strHTML;
    }

    public static function getSearchFormWithAction( $p_strFormId, $p_strAction ){
      $strHTML = <<< ENDHTML
              <div class="tabKeywordSearch" id="warehouseSearch">
                  <input type="text" name="keywords" value="warehouse search" style="color:#999999" onClick="this.value='';">
                  <input type="button" value="GO" onClick="document.getElementById('$p_strFormId').action='$p_strAction';document.getElementById('$p_strFormId').submit();">
              </div>
ENDHTML;
      return $strHTML;
    }
  	
  }

?>