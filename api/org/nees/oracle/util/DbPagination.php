<?php 

  class DbPagination{
  	
    public $m_nTotal;
    public $m_nDisplayCount;
    public $m_nCurrentIndex;
    public $m_nPages;
    public $m_nLowerLimit;
    public $m_nUpperLimit;
  	
    /**
     * Constructor
     *
     */
    function __construct($p_nCurrentIndex, $p_nTotal, $p_nDisplayCount, $p_nLowerLimit, $p_nUpperLimit){
      $this->m_nTotal = $p_nTotal;
      $this->m_nDisplayCount = $p_nDisplayCount;
      $this->m_nCurrentIndex = $p_nCurrentIndex;
      $this->m_nPages = 1;
      $this->m_nLowerLimit = $p_nLowerLimit;
      $this->m_nUpperLimit = ($p_nUpperLimit < $p_nTotal) ? $p_nUpperLimit : $p_nTotal;
    }
  	
    /**
     * Sets total number of rows
     * @param $p_nTotal
     */
  	public function setTotalRows($p_nTotal){
  	  $this->m_nTotal = $p_nTotal;
  	}

  	/**
  	 * Returns the total number of rows
  	 * @return $m_nTotal
  	 */
        public function getTotalRows(){
  	  $this->m_nTotal = $p_nTotal;
  	}
  	
  	/**
  	 * Sets how many rows to display.
  	 * @param $p_nDisplayCount
  	 */
  	public function setDisplayCount($p_nDisplayCount){
  	  $this->m_nDisplayCount = $p_nDisplayCount;
  	}
  	
  	/**
  	 * Returns how many rows to display.
  	 * @return $m_nDisplayCount
  	 */
  	public function getDisplayCount(){
  	  return $this->m_nDisplayCount;
  	}
  	
  	/**
  	 * Sets the selected (current) page index.
  	 * @$p_nCurrentIndex
  	 */
  	public function setCurrentIndex($p_nCurrentIndex){
  	  $this->m_nCurrentIndex = $p_nCurrentIndex;
  	}
  	
  	/**
  	 * Returns the selected (current) page index.
  	 * @$m_nCurrentIndex
  	 */
  	public function getCurrentIndex(){
  	  return $this->m_nCurrentIndex;
  	}
  	
  	/**
  	 * Computes the number of pages from the given result set.
  	 * 
  	 */
  	public function computePageCount(){
          $nDisplayCount = $this->getDisplayCount();
          if( $nDisplayCount > 0 ){
  	    $quotient = $this->m_nTotal / $nDisplayCount;	
  	    $this->m_nPages = ceil($quotient);
	  }
  	}
  	
  	/**
  	 * Returns the number of pages from the given result set.
  	 * @return $m_nPages
  	 */
  	public function getPageCount(){
  	  return $this->m_nPages;
  	}
  	
  	/**
  	 * Displays the oracle pagination footer.
  	 *
  	 */
  	public function getPaginationFooter($p_sUrl){
  	  #index counter starts with 0.  display is + 1.
  	  $nDisplayIndex = $this->m_nCurrentIndex+1;
  	  $nShowing = $this->m_nDisplayCount * $nDisplayIndex;
  	  
  	  #open the pagination div
  	  $sPagination = <<< ENDHTML
  	  					 <div style="width:100%; margin-top:20px;">
ENDHTML;
	
  	  #show the left div
  	  $sPagination = $sPagination . $this->getPaginationFooterLeft($p_sUrl, $nDisplayIndex);
  	  
  	  #show the right div
  	  $sPagination = $sPagination . $this->getPaginationFooterRight($nShowing, $nDisplayIndex);
  	  
  	  #clear and close the main div
  	  $sPagination = $sPagination . <<< ENDHTML
  	  					   
  	  					   <div class="clear"></div>
  	  					 </div>
  	  
ENDHTML;
	  return $sPagination;
  	}
  	
  public function getFooter($p_sUrl, $p_strFormId, $p_strResultId){
  	  #if the url ends with index and limit, remove the params.
  	  $p_sUrl = $this->cleanUrl($p_sUrl);
  	  
  	  #index counter starts with 0.  display is + 1.
  	  $nDisplayIndex = $this->m_nCurrentIndex+1;
  	  $nShowing = $this->m_nDisplayCount * $nDisplayIndex;
  	  
  	  #open the pagination div
  	  $sPagination = <<< ENDHTML
  	  					 <div style="width:100%; margin-top:40px;">
ENDHTML;
	
  	  #show the left div
  	  $sPagination = $sPagination . $this->getFooterLeft($p_sUrl, $nDisplayIndex);
  	  
  	  #show the right div
  	  $sPagination = $sPagination . $this->getFooterRight($nShowing, $nDisplayIndex, $p_strFormId, $p_strResultId);
  	  
  	  #clear and close the main div
  	  $sPagination = $sPagination . <<< ENDHTML
  	  					   
  	  					   <div class="clear"></div>
  	  					 </div>
  	  
ENDHTML;
	  return $sPagination;
  	}
  	
    public function getFooter24($p_sUrl, $p_strFormId, $p_strResultId){
  	  #if the url ends with index and limit, remove the params.
  	  $p_sUrl = $this->cleanUrl($p_sUrl);
  	  
  	  #index counter starts with 0.  display is + 1.
  	  $nDisplayIndex = $this->m_nCurrentIndex+1;
  	  $nShowing = $this->m_nDisplayCount * $nDisplayIndex;
  	  
  	  #open the pagination div
  	  $sPagination = <<< ENDHTML
  	  					 <div style="width:100%; margin-top:40px;">
ENDHTML;
	
  	  #show the left div
  	  $sPagination = $sPagination . $this->getFooterLeft($p_sUrl, $nDisplayIndex);
  	  
  	  #show the right div
  	  $sPagination = $sPagination . $this->getFooterRight24($nShowing, $nDisplayIndex, $p_strFormId, $p_strResultId);
  	  
  	  #clear and close the main div
  	  $sPagination = $sPagination . <<< ENDHTML
  	  					   
  	  					   <div class="clear"></div>
  	  					 </div>
  	  
ENDHTML;
	  return $sPagination;
  	}
  	
    private function cleanUrl($p_sUrl){
      //echo "old url=".$p_sUrl."<br>";
      if(preg_match("/\?index=([0-9])+&limit=([0-9])+$/", $p_sUrl)){
  	  	$strUrlArray = explode("?", $p_sUrl);
  	  	array_pop($strUrlArray);
  	  	//array_pop($strUrlArray);
  	  	$p_sUrl = implode("&", $strUrlArray);
  	  }elseif(preg_match("/&limit=([0-9])+$/", $p_sUrl)){
  	  	$strUrlArray = explode("&", $p_sUrl);
		array_pop($strUrlArray);
		$p_sUrl = implode("&", $strUrlArray);
  	  }
  	  
  	  //echo "new url=".$p_sUrl."<br>";
  	  return $p_sUrl;
  	}
  	
  	/**
  	 * Returns the left portion of the pagination html.
  	 * 
  	 * Start  Prev  #  Next  End
  	 *
  	 */
  	private function getPaginationFooterLeft($p_sUrl, $p_nDisplayIndex){
  	  #can't have a negative number for previous
  	  $nPrevIndex = $this->m_nCurrentIndex-1;
  	  if($nPrevIndex < 0){
  	  	$nPrevIndex=0;
  	  }
  	  
  	  $nNextIndex = $this->m_nCurrentIndex+1;
  	  $nEnd = $this->m_nPages-1;
  	  if($nNextIndex > $nEnd)$nNextIndex=$nEnd;
  	  
  	  if($nEnd > 1){
  	  	$strUrlStart = $p_sUrl."/projects/index/0/limit/$this->m_nDisplayCount";
  	  	$strUrlPrev = $p_sUrl."/projects/index/$nPrevIndex/limit/$this->m_nDisplayCount";
  	  	$strUrlNext = $p_sUrl."/projects/index/$nNextIndex/limit/$this->m_nDisplayCount";
  	  	$strUrlEnd = $p_sUrl."/projects/index/$nEnd/limit/$this->m_nDisplayCount";
  	  	
  	    $sPaginationLeft = <<< ENDHTML
  	    <div style="float:left">
           <span style="padding-right:7px;"><a href="$strUrlStart">Start</a></span>
           <span style="padding-right:7px;"><a href="$strUrlPrev">Prev</a></span>
           <span style="padding-right:7px;">$p_nDisplayIndex</span>
           <span style="padding-right:7px;"><a href="$strUrlNext">Next</a></span>
           <span style="padding-right:7px;"><a href="$strUrlEnd">End</a></span>
         </div>
ENDHTML;
  	  }else{
  	  	$sPaginationLeft = <<< ENDHTML
  	    <div style="float:left">
           <span style="padding-right:7px;">Start</span>
           <span style="padding-right:7px;">Prev</span>
           <span style="padding-right:7px;">$p_nDisplayIndex</span>
           <span style="padding-right:7px;">Next</span>
           <span style="padding-right:7px;">End</span>
         </div>
ENDHTML;
  	  }
	  return $sPaginationLeft;
  	}
  	
  /**
  	 * Returns the left portion of the pagination html.
  	 * 
  	 * Start  Prev  #  Next  End
  	 *
  	 */
  	private function getFooterLeft($p_sUrl, $p_nDisplayIndex){
  	  #can't have a negative number for previous
  	  $nPrevIndex = $this->m_nCurrentIndex-1;
  	  if($nPrevIndex < 0){
  	  	$nPrevIndex=0;
  	  }
  	  
  	  $nNextIndex = $this->m_nCurrentIndex+1;
  	  $nEnd = $this->m_nPages-1;
  	  if($nNextIndex > $nEnd)$nNextIndex=$nEnd;
  	  
  	  //echo "pages = ".$this->m_nPages."<br>";
  	  
  	  if($this->m_nPages > 1){
  	  	$strUrlStart = (preg_match("/\?/", $p_sUrl)) ? $p_sUrl."&index=0&limit=$this->m_nDisplayCount" : $p_sUrl."?index=0&limit=$this->m_nDisplayCount";
  	  	$strUrlPrev = (preg_match("/\?/", $p_sUrl)) ? $p_sUrl."&index=$nPrevIndex&limit=$this->m_nDisplayCount" : $p_sUrl."?index=$nPrevIndex&limit=$this->m_nDisplayCount";
  	  	$strUrlNext = (preg_match("/\?/", $p_sUrl)) ? $p_sUrl."&index=$nNextIndex&limit=$this->m_nDisplayCount" : $p_sUrl."?index=$nNextIndex&limit=$this->m_nDisplayCount";
  	  	$strUrlEnd = (preg_match("/\?/", $p_sUrl)) ? $p_sUrl."&index=$nEnd&limit=$this->m_nDisplayCount" : $p_sUrl."?index=$nEnd&limit=$this->m_nDisplayCount";
  	  	
  	  	$strNumbers = $this->getFooterLeftNumbers($p_sUrl, $p_nDisplayIndex);
  	  	
  	    $sPaginationLeft = <<< ENDHTML
  	    <div style="float:left">
           <span style="padding-right:7px;"><a href="$strUrlStart">Start</a></span>
           <span style="padding-right:7px;"><a href="$strUrlPrev">Prev</a></span>
           $strNumbers
           <!--<span style="padding-right:7px;">$p_nDisplayIndex</span>-->
           <span style="padding-right:7px;"><a href="$strUrlNext">Next</a></span>
           <span style="padding-right:7px;"><a href="$strUrlEnd">End</a></span>
         </div>
ENDHTML;
  	  }else{
  	  	$sPaginationLeft = <<< ENDHTML
  	    <div style="float:left">
           <span style="padding-right:7px;">Start</span>
           <span style="padding-right:7px;">Prev</span>
           <span style="padding-right:7px;">$p_nDisplayIndex</span>
           <span style="padding-right:7px;">Next</span>
           <span style="padding-right:7px;">End</span>
         </div>
ENDHTML;
  	  }
	  return $sPaginationLeft;
  	}
  	
  	private function getFooterLeftNumbers($p_sUrl, $p_nDisplayIndex){
  	  $strReturn = "";
  	  
  	  $iWindowWidth = 10;
  	  $iLeft = $p_nDisplayIndex - ($iWindowWidth/2);
  	  $iRight = $p_nDisplayIndex + ($iWindowWidth/2);
  	  
  	  /*
  	   * Only show 10 page numbers at a time.
  	   */
  	  if($iLeft > 0 && $iRight < $this->m_nPages){
//  	  	echo "Only show 10 page numbers at a time.<br>";
  	  	/*
  	  	 * We will display ... # # # ...
  	  	 */
  	  	$iThisIndex = $iLeft + 1;
  	  	$iStop = $iRight;
  	  	$strReturn = $this->buildNumberedList("<span style='padding-right:7px;color:#ccc'>...</span>", 
  	  								   		  $iThisIndex, $iStop, $p_nDisplayIndex, $p_sUrl);
  	  	$strReturn .= "<span style='padding-right:7px;color:#ccc;'>...</span>";
  	  }elseif($iLeft > 0 && $iRight >= $this->m_nPages){
//  	  	echo "Show ... # # #.<br>";
  	  	/*
  	  	 * Show ... # # #
  	  	 */
  	  	$iThisIndex = $this->m_nPages - $iWindowWidth;
  	  	$iStop = $this->m_nPages;
  	  	$strReturn = $this->buildNumberedList("<span style='padding-right:7px;color:#ccc'>...</span>", 
  	  								   		  $iThisIndex, $iStop, $p_nDisplayIndex, $p_sUrl);
  	  }else{
//  	  	echo "Show # # # (".$this->m_nPages.")....<br>";
  	  	/*
  	  	 * Show # # # ...
  	  	 */
  	  	$iThisIndex = 1;
  	  	$iStop = 10;
  	  	if($this->m_nPages < $iStop){
  	  	  $iStop = $this->m_nPages;
  	  	}
  	  	$strReturn = $this->buildNumberedList("", $iThisIndex, $iStop, $p_nDisplayIndex, $p_sUrl);
  	  	
  	  	if($this->m_nPages > 10){
  	  	  $strReturn .= "<span style='padding-right:7px;color:#ccc;'>...</span>";
  	  	}
  	  }
  	  
  	  return $strReturn;
  	}
  	
  	private function buildNumberedList($p_strReturn, $p_iStart, $p_iStop, $p_nDisplayIndex, $p_sUrl){
  	  $strReturn = $p_strReturn;
  	  while($p_iStart <= $p_iStop){
  	  	$strThisUrl = "";
  	  	
  	  	if($p_iStart == $p_nDisplayIndex){
  	  	  //we're on the current page.  no link required.
  	  	  $strThisUrl = $p_iStart;
  	  	}else{
  	  	  //we're not on the current page.  provide a link.
  	  	  
  	  	  //subtract one from the display # to get the index.	
  	  	  $iUrlIndex = $p_iStart - 1;
  	  	  
  	  	  //if url has query already, append &param.  otherwise, append ?param.
  	  	  $strHref = (preg_match("/\?/", $p_sUrl)) ? $p_sUrl."&index=$iUrlIndex&limit=$this->m_nDisplayCount" : $p_sUrl."?index=$iUrlIndex&limit=$this->m_nDisplayCount";
  	  	  $strThisUrl = "<a href=".$strHref.">".$p_iStart."</a>";	
  	  	}
  	  	$strReturn .= "<span style='padding-right:7px;'>" . $strThisUrl. "</span>";
  	  	
  	  	$p_iStart = $p_iStart + 1;
  	  }	
  	  return $strReturn;
  	}
  	
    private function getFooterLeftNumbersKeep($p_sUrl, $p_nDisplayIndex){
  	  $strReturn = "";
  	  $iThisIndex = 1;
  	  while($iThisIndex <= $this->m_nPages){
  	  	$strThisUrl = "";
  	  	
  	  	if($iThisIndex == $p_nDisplayIndex){
  	  	  //we're on the current page.  no link required.
  	  	  $strThisUrl = $iThisIndex;
  	  	}else{
  	  	  //we're not on the current page.  provide a link.
  	  	  
  	  	  //subtract one from the display # to get the index.	
  	  	  $iUrlIndex = $iThisIndex - 1;
  	  	  
  	  	  //if url has query already, append &param.  otherwise, append ?param.
  	  	  $strHref = (preg_match("/\?/", $p_sUrl)) ? $p_sUrl."&index=$iUrlIndex&limit=$this->m_nDisplayCount" : $p_sUrl."?index=$iUrlIndex&limit=$this->m_nDisplayCount";
  	  	  $strThisUrl = "<a href=".$strHref.">".$iThisIndex."</a>";	
  	  	}
  	  	$strReturn .= "<span style='padding-right:7px;'>" . $strThisUrl. "</span>";
  	  	
  	  	$iThisIndex = $iThisIndex + 1;
  	  }
  	  return $strReturn;
  	}
  	
  	/**
  	 * Returns the right portion of the pagination html.
  	 * 
  	 * Display Num <select tag> Results [current page] - [now showing] of [total]
  	 *
  	 */
  	private function getPaginationFooterRight($p_nShowing, $p_nDisplayIndex){
  	  #find the selected dropdown value
  	  if($this->m_nDisplayCount==5)$sSelected5 = "selected=\"selected\"";
  	  if($this->m_nDisplayCount==10)$sSelected10 = "selected=\"selected\"";
  	  if($this->m_nDisplayCount==15)$sSelected15 = "selected=\"selected\"";
  	  if($this->m_nDisplayCount==20)$sSelected20 = "selected=\"selected\"";
  	  if($this->m_nDisplayCount==25)$sSelected25 = "selected=\"selected\"";
  	  if($this->m_nDisplayCount==30)$sSelected30 = "selected=\"selected\"";
  	  if($this->m_nDisplayCount==50)$sSelected50 = "selected=\"selected\"";
  	  if($this->m_nDisplayCount==100)$sSelected100 = "selected=\"selected\"";
  	  if($this->m_nDisplayCount==0)$sSelected0 = "selected=\"selected\"";	
  	  
  	  $sPaginationRight = <<< ENDHTML
  	  <div align="right">
         <span style="padding-right:7px;">Display Num</span> 
         <select onchange="getMootoolsForm('frmProjects','listings','change');" size="1" class="inputbox" id="limit" name="limit">
           <option $sSelected5 value="5">5</option>
           <option $sSelected10 value="10">10</option>
           <option $sSelected15 value="15">15</option>
           <option $sSelected20 value="20">20</option>
           <option $sSelected25 value="25">25</option>
           <option $sSelected30 value="30">30</option>
           <option $sSelected50 value="50">50</option>
           <option $sSelected100 value="100">100</option>
           <option $sSelected0 value="0">All</option>
         </select>
         <span style="padding-left:7px;">Results $p_nDisplayIndex - $p_nShowing of $this->m_nTotal</span>
       </div>
ENDHTML;
	  return $sPaginationRight;
  	}
  	
  /**
  	 * Returns the right portion of the pagination html.
  	 * 
  	 * Display Num <select tag> Results [current page] - [now showing] of [total]
  	 *
  	 */
  	private function getFooterRight($p_nShowing, $p_nDisplayIndex, $p_strFormId, $p_strResultId){
  	  if($p_nShowing > $this->m_nTotal){
  	    $p_nShowing = $this->m_nTotal;
  	  }elseif($p_nShowing===0){
            $p_nShowing = $this->m_nTotal;
          }

          $iTotal = $this->m_nTotal;

          #find the selected dropdown value
  	  $sSelected5 = $sSelected10 = $sSelected15 = $sSelected20 = $sSelected25 = $sSelected30 = $sSelected50 = $sSelected100 = $sSelected0 = "";
  	  if($this->m_nDisplayCount==5)$sSelected5 = "selected=\"selected\"";
  	  if($this->m_nDisplayCount==10)$sSelected10 = "selected=\"selected\"";
  	  if($this->m_nDisplayCount==15)$sSelected15 = "selected=\"selected\"";
  	  if($this->m_nDisplayCount==20)$sSelected20 = "selected=\"selected\"";
  	  if($this->m_nDisplayCount==25)$sSelected25 = "selected=\"selected\"";
  	  if($this->m_nDisplayCount==30)$sSelected30 = "selected=\"selected\"";
  	  if($this->m_nDisplayCount==50)$sSelected50 = "selected=\"selected\"";
  	  if($this->m_nDisplayCount==100)$sSelected100 = "selected=\"selected\"";
  	  if($this->m_nDisplayCount==$iTotal)$sSelected0 = "selected=\"selected\"";
  	  
  	  $sPaginationRight = <<< ENDHTML
  	  <div align="right">
         <span style="padding-right:7px;">Display Num</span> 
         <!--
         <select onchange="getMootoolsForm('$p_strFormId','$p_strResultId','change');" size="1" class="inputbox" id="limit" name="limit">
         -->
         <select onchange="document.getElementById('$p_strFormId').submit();" size="1" class="inputbox" id="limit" name="limit">
           <option $sSelected5 value="5">5</option>
           <option $sSelected10 value="10">10</option>
           <option $sSelected15 value="15">15</option>
           <option $sSelected20 value="20">20</option>
           <option $sSelected25 value="25">25</option>
           <option $sSelected30 value="30">30</option>
           <option $sSelected50 value="50">50</option>
           <option $sSelected100 value="100">100</option>
           <option $sSelected0 value="$iTotal">All</option>
         </select>
         <span style="padding-left:7px;">Results $this->m_nLowerLimit - $this->m_nUpperLimit of $this->m_nTotal</span>
       </div>
ENDHTML;
	  return $sPaginationRight;
  	}
  	
    private function getFooterRight24($p_nShowing, $p_nDisplayIndex, $p_strFormId, $p_strResultId){
  	  if($p_nShowing > $this->m_nTotal){
  	  	$p_nShowing = $this->m_nTotal;
  	  }
  	  
  	  #find the selected dropdown value
  	  $sSelected5 = $sSelected10 = $sSelected15 = $sSelected20 = "";
  	  if($this->m_nDisplayCount==24)$sSelected5 = "selected=\"selected\"";
  	  if($this->m_nDisplayCount==48)$sSelected10 = "selected=\"selected\"";
  	  if($this->m_nDisplayCount==72)$sSelected15 = "selected=\"selected\"";
  	  if($this->m_nDisplayCount==96)$sSelected20 = "selected=\"selected\"";
  	  
  	  $sPaginationRight = <<< ENDHTML
  	  <div align="right">
         <span style="padding-right:7px;">Display Num</span> 
         <!--
         <select onchange="getMootoolsForm('$p_strFormId','$p_strResultId','change');" size="1" class="inputbox" id="limit" name="limit">
         -->
         <select onchange="document.getElementById('$p_strFormId').submit();" size="1" class="inputbox" id="limit" name="limit">
           <option $sSelected5 value="24">24</option>
           <option $sSelected10 value="48">48</option>
           <option $sSelected15 value="72">72</option>
           <option $sSelected20 value="96">96</option>
         </select>
         <span style="padding-left:7px;">Results $this->m_nLowerLimit - $this->m_nUpperLimit of $this->m_nTotal</span>
       </div>
ENDHTML;
	  return $sPaginationRight;
  	}

    public function getFromIndex($p_iPageIndex, $p_iDisplay) {
      if ($p_iPageIndex == 0) {
        return $p_iDisplay;
      }
      return $p_iDisplay * ($p_iPageIndex + 1);
    }
  	
  }

?>
