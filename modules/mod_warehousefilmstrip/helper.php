<?php
/**
* @version		$Id: helper.php 11668 2009-03-08 20:33:38Z willebil $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/DataFile.php';
require_once 'api/org/nees/static/Files.php';

class modWarehouseFilmStripHelper{

  /**
   * Find the list of DataFiles.
   * @param int $p_iProjectId
   * @param int $p_iExperimentId
   * @return array
   */
  public function getFilmStripByProjectExperiment($p_iProjectId, $p_iExperimentId){
    $oReturnArray = array();

    $oDataFileArray = DataFilePeer::findDataFileByEntityType("Film Strip", $p_iProjectId, $p_iExperimentId);

    /* @var $oDataFile DataFile */
    foreach($oDataFileArray as $oDataFile){
      //temporarily store the datafile as a request for the plugin
      $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDataFile);

      //scale if needed
      JPluginHelper::importPlugin( 'project', 'upload' );
      $oDispatcher =& JDispatcher::getInstance();
      $strParamArray = array(0,0);
      $bResultsArray = $oDispatcher->trigger('onScaleImageDataFile',$strParamArray);

      //store DataFiles that were accurately scaled.
      $bImageScaled = $bResultsArray[0];
      if($bImageScaled){
        array_push($oReturnArray, $oDataFile);
      }
    }

    return $oReturnArray;
  }

  public function getFilmStripByProjectExperimentHTML($p_oDataFileArray){
    $strHTML = <<< ENDHTML
          <div class="sscontainer">
            <div id="showcase">
              <div id="showcase-prev" class=""></div>
              <div id="showcase-window">
                <div class="showcase-pane" style="left: 0px;">
ENDHTML;

    /* @var $oDataFile DataFile */
    $count = 0;
    foreach($p_oDataFileArray as $iFilmStripIndex=>$oDataFile){
      $iFilmStripAlt = $iFilmStripIndex+1;
      $strDescription = $oDataFile->getDescription();
      
      //temporarily set path to Generated_Pics (DON'T SAVE!!!!)
      $strPath = $oDataFile->getPath()."/".Files::GENERATED_PICS;
      $oDataFile->setPath($strPath);

      //original name
      $strName = $oDataFile->getName();

      $strThumbName = "thumb_".$oDataFile->getId()."_".$strName;
      $oDataFile->setName($strThumbName);
      $strThumbUrl = $oDataFile->get_url();

      $strDisplayName = "display_".$oDataFile->getId()."_".$strName;
      $oDataFile->setName($strDisplayName);
      $strDisplayUrl = $oDataFile->get_url();

      $strHTML .= <<< ENDHTML
                  <a title="$strDescription" href="$strDisplayUrl" rel="lightbox[filmstrip]">
                    <img class="thumbima" alt="thumbnail$iFilmStripAlt" src="$strThumbUrl">
                  </a>
ENDHTML;
    	$count = $count + 1;  
    }

    $strHTML .= <<< ENDHTML
                </div>
              </div>
              <div id="showcase-next" class=""></div>
            </div>
          </div>
ENDHTML;
//echo $count;
	if ( $count == 0 ){
		//echo "Inside the count == 0 thing: ".$count;
    	$strHTML = "";
	}
    return $strHTML;
  }
  
  public function getExperiment28(){
  	$strHTML = <<< ENDHTML
              <div class="sscontainer">
		        <div id="showcase">
		          <div id="showcase-prev" class=""></div>
		          <div id="showcase-window">
		          <div class="showcase-pane" style="left: 0px;">
		            <a title="RWN, drift levels of 0.2%" href="/components/com_warehouse/images/prototype/film_strip/RWN-Original/5-RWN.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail1" src="/components/com_warehouse/images/prototype/film_strip/RWN-h75/5-RWN.jpg">
		            </a>
	                <a title="RWN, drift levels of 0.3%" href="/components/com_warehouse/images/prototype/film_strip/RWN-Original/6-RWN.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail2" src="/components/com_warehouse/images/prototype/film_strip/RWN-h75/6-RWN.jpg">
		            </a>
	                <a title="RWN, drift levels of 0.5%." href="/components/com_warehouse/images/prototype/film_strip/RWN-Original/7-RWN.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail3" src="/components/com_warehouse/images/prototype/film_strip/RWN-h75/7-RWN.jpg">
		            </a>
	                <a title="RWN, drift levels of 0.75%." href="/components/com_warehouse/images/prototype/film_strip/RWN-Original/8-RWN.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail4" src="/components/com_warehouse/images/prototype/film_strip/RWN-h75/8-RWN.jpg">
		            </a> 
		            <a title="RWN, drift levels of 1.0%." href="/components/com_warehouse/images/prototype/film_strip/RWN-Original/9-RWN.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail5" src="/components/com_warehouse/images/prototype/film_strip/RWN-h75/9-RWN.jpg">
		            </a> 
		            <a title="RWN, drift levels of 1.5% in the No. 5 & 6 BE in tension direction w/ 1.0% in the No. 9 BE in tension direction." href="/components/com_warehouse/images/prototype/film_strip/RWN-Original/10-RWN.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail6" src="/components/com_warehouse/images/prototype/film_strip/RWN-h75/10-RWN.jpg">
		            </a> 
		            <a title="RWN, drift levels of 2.0% in the No. 5 & 6 BE in tension direction w/ 1.0% in the No. 9 BE in tension direction." href="/components/com_warehouse/images/prototype/film_strip/RWN-Original/11-RWN.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail7" src="/components/com_warehouse/images/prototype/film_strip/RWN-h75/11-RWN.jpg">
		            </a> 
		            <a title="RWN, drift levels of 2.5% in the No. 5 & 6 BE in tension, loading ramp maximum prior to buckling." href="/components/com_warehouse/images/prototype/film_strip/RWN-Original/12-RWN.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail8" src="/components/com_warehouse/images/prototype/film_strip/RWN-h75/12-RWN.jpg">
		            </a> 
		            <a title="RWN, buckling of the No. 5 & 6 boundary element following 2.5% drift in the No. 5 & 6 BE in tensino direction, 2% dirft in the No. 9 BE in tension direction." href="/components/com_warehouse/images/prototype/film_strip/RWN-Original/13-RWN.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail9" src="/components/com_warehouse/images/prototype/film_strip/RWN-h75/13-RWN.jpg">
		            </a> 
		            <a title="RWN following all loading applied to the test structure." href="/components/com_warehouse/images/prototype/film_strip/RWN-Original/14-RWN.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail10" src="/components/com_warehouse/images/prototype/film_strip/RWN-h75/14-RWN.jpg">
		            </a> 
		          </div>
		          </div>
		          <div id="showcase-next" class=""></div>
		        </div>
		      </div>
ENDHTML;
      return $strHTML;
  }
  
  public function getExperiment29(){
  	$strHTML = <<< ENDHTML
              <div class="sscontainer">
		        <div id="showcase">
		          <div id="showcase-prev" class=""></div>
		          <div id="showcase-window">
		          <div class="showcase-pane" style="left: 0px;">
		            <a title="RWC, drift levels of 0.2%." href="/components/com_warehouse/images/prototype/film_strip/RWC-Original/3-RWC.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail1" src="/components/com_warehouse/images/prototype/film_strip/RWC-h75/3-RWC.jpg">
		            </a>
	                <a title="RWC, drift levels of 0.3%" href="/components/com_warehouse/images/prototype/film_strip/RWC-Original/4-RWC.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail2" src="/components/com_warehouse/images/prototype/film_strip/RWC-h75/4-RWC.jpg">
		            </a>
	                <a title="RWC, drift levels of 0.5%." href="/components/com_warehouse/images/prototype/film_strip/RWC-Original/5-RWC.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail3" src="/components/com_warehouse/images/prototype/film_strip/RWC-h75/5-RWC.jpg">
		            </a>
	                <a title="RWC, drift levels of 0.75%." href="/components/com_warehouse/images/prototype/film_strip/RWC-Original/6-RWC.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail4" src="/components/com_warehouse/images/prototype/film_strip/RWC-h75/6-RWC.jpg">
		            </a> 
		            <a title="RWC, drift levels of 1.0%." href="/components/com_warehouse/images/prototype/film_strip/RWC-Original/7-RWC.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail5" src="/components/com_warehouse/images/prototype/film_strip/RWC-h75/7-RWC.jpg">
		            </a> 
		            <a title="RWC, drift levels of 1.5% in the No. 5 & 6 BE in tension direction w/ 1.0% in the No. 9 BE in tension direction." href="/components/com_warehouse/images/prototype/film_strip/RWC-Original/8-RWC.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail6" src="/components/com_warehouse/images/prototype/film_strip/RWC-h75/8-RWC.jpg">
		            </a> 
		            <a title="RWC, drift levels of 2.0% in the No. 5 & 6 BE in tension direction w/ 1.0% in the No. 9 BE in tension direction." href="/components/com_warehouse/images/prototype/film_strip/RWC-Original/9-RWC.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail7" src="/components/com_warehouse/images/prototype/film_strip/RWC-h75/9-RWC.jpg">
		            </a> 
		            <a title="RWC, drift levels of 2.5% in the No. 5 & 6 BE in tension, loading ramp maximum prior to buckling." href="/components/com_warehouse/images/prototype/film_strip/RWC-Original/10-RWC.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail8" src="/components/com_warehouse/images/prototype/film_strip/RWC-h75/10-RWC.jpg">
		            </a> 
		            <a title="RWC, buckling of the No. 5 & 6 boundary element followign 2.5% drift in the No. 5 & 6 BE in tension direction, 2% drift in the No. 9 BE in tension direction." href="/components/com_warehouse/images/prototype/film_strip/RWC-Original/11-RWC.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail9" src="/components/com_warehouse/images/prototype/film_strip/RWC-h75/11-RWC.jpg">
		            </a> 
		            <a title="RWC following all loading applied to the test structure." href="/components/com_warehouse/images/prototype/film_strip/RWC-Original/12-RWC.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail10" src="/components/com_warehouse/images/prototype/film_strip/RWC-h75/12-RWC.jpg">
		            </a> 
		          </div>
		          </div>
		          <div id="showcase-next" class=""></div>
		        </div>
		      </div>
ENDHTML;
      return $strHTML;
  }
  
  public function getExperiment30(){
  	$strHTML = <<< ENDHTML
              <div class="sscontainer">
		        <div id="showcase">
		          <div id="showcase-prev" class=""></div>
		          <div id="showcase-window">
		          <div class="showcase-pane" style="left: 0px;">
		            <a title="RWS, drift levels of 0.2%." href="/components/com_warehouse/images/prototype/film_strip/RWS-Original/3-RWS.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail1" src="/components/com_warehouse/images/prototype/film_strip/RWS-h75/3-RWS.jpg">
		            </a>
	                <a title="RWS, drift levels of 0.3%" href="/components/com_warehouse/images/prototype/film_strip/RWS-Original/4-RWS.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail2" src="/components/com_warehouse/images/prototype/film_strip/RWS-h75/4-RWS.jpg">
		            </a>
	                <a title="RWS, drift levels of 0.5%." href="/components/com_warehouse/images/prototype/film_strip/RWS-Original/5-RWS.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail3" src="/components/com_warehouse/images/prototype/film_strip/RWS-h75/5-RWS.jpg">
		            </a>
	                <a title="RWS, drift levels of 0.75%." href="/components/com_warehouse/images/prototype/film_strip/RWS-Original/6-RWS.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail4" src="/components/com_warehouse/images/prototype/film_strip/RWS-h75/6-RWS.jpg">
		            </a> 
		            <a title="RWS, drift levels of 1.0%." href="/components/com_warehouse/images/prototype/film_strip/RWS-Original/7-RWS.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail5" src="/components/com_warehouse/images/prototype/film_strip/RWS-h75/7-RWS.jpg">
		            </a> 
		            <a title="RWS, drift levels of 1.5% in the No. 5 & 6 BE in tension direction w/ 1.0% in the No. 9 BE in tension direction." href="/components/com_warehouse/images/prototype/film_strip/RWS-Original/8-RWS.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail6" src="/components/com_warehouse/images/prototype/film_strip/RWS-h75/8-RWS.jpg">
		            </a> 
		            <a title="RWS, drift levels of 2.0% in the No. 5 & 6 BE in tension direction w/ 1.0% in the No. 9 BE in tension direction." href="/components/com_warehouse/images/prototype/film_strip/RWS-Original/9-RWS.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail7" src="/components/com_warehouse/images/prototype/film_strip/RWS-h75/9-RWS.jpg">
		            </a> 
		            <a title="RWS, drift levels of 2.5% in the No. 5 & 6 BE in tension, loading ramp maximum prior to buckling." href="/components/com_warehouse/images/prototype/film_strip/RWS-Original/10-RWS.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail8" src="/components/com_warehouse/images/prototype/film_strip/RWS-h75/10-RWS.jpg">
		            </a> 
		            <a title="RWS, buckling of the No. 5 & 6 boundary element followign 2.5% drift in the No. 5 & 6 BE in tension direction, 2% drift in the No. 9 BE in tension direction." href="/components/com_warehouse/images/prototype/film_strip/RWS-Original/11-RWS.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail9" src="/components/com_warehouse/images/prototype/film_strip/RWS-h75/11-RWS.jpg">
		            </a> 
		            <a title="RWS following all loading applied to the test structure." href="/components/com_warehouse/images/prototype/film_strip/RWS-Original/12-RWS.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail10" src="/components/com_warehouse/images/prototype/film_strip/RWS-h75/12-RWS.jpg">
		            </a> 
		          </div>
		          </div>
		          <div id="showcase-next" class=""></div>
		        </div>
		      </div>
ENDHTML;
      return $strHTML;
  }
  
  public function getExperiment835(){
  	$strHTML = <<< ENDHTML
              <div class="sscontainer">
		        <div id="showcase">
		          <div id="showcase-prev" class=""></div>
		          <div id="showcase-window">
		          <div class="showcase-pane" style="left: 0px;">
		            <a title="Fourth story web of NTW2 after web failure." href="/components/com_warehouse/images/prototype/film_strip/NTW2-Original/4-NTW2.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail1" src="/components/com_warehouse/images/prototype/film_strip/NTW2-w90/4-NTW2.jpg">
		            </a>
	                <a title="First story flange of NTW2 after flange failure." href="/components/com_warehouse/images/prototype/film_strip/NTW2-Original/5-NTW2.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail2" src="/components/com_warehouse/images/prototype/film_strip/NTW2-w90/5-NTW2.jpg">
		            </a>
	                <a title="Second story flange of NTW2 after flange failure." href="/components/com_warehouse/images/prototype/film_strip/NTW2-Original/6-NTW2.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail3" src="/components/com_warehouse/images/prototype/film_strip/NTW2-w90/6-NTW2.jpg">
		            </a>
	                <a title="Third story flange of NTW2 after flange failure." href="/components/com_warehouse/images/prototype/film_strip/NTW2-Original/7-NTW2.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail4" src="/components/com_warehouse/images/prototype/film_strip/NTW2-w90/7-NTW2.jpg">
		            </a> 
		            <a title="Fourth story flange of NTW2 after flange failure." href="/components/com_warehouse/images/prototype/film_strip/NTW2-Original/8-NTW2.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail5" src="/components/com_warehouse/images/prototype/film_strip/NTW2-w90/8-NTW2.jpg">
		            </a> 
		            <a title="First story web of NTW2 after flange failure." href="/components/com_warehouse/images/prototype/film_strip/NTW2-Original/9-NTW2.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail6" src="/components/com_warehouse/images/prototype/film_strip/NTW2-w90/9-NTW2.jpg">
		            </a> 
		            <a title="Second story web of NTW2 after flange failure." href="/components/com_warehouse/images/prototype/film_strip/NTW2-Original/10-NTW2.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail7" src="/components/com_warehouse/images/prototype/film_strip/NTW2-w90/10-NTW2.jpg">
		            </a> 
		            <a title="Third story web of NTW2 after flange failure." href="/components/com_warehouse/images/prototype/film_strip/NTW2-Original/11-NTW2.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail8" src="/components/com_warehouse/images/prototype/film_strip/NTW2-w90/11-NTW2.jpg">
		            </a> 
		            <a title="Fourth story web of NTW2 after flange failure." href="/components/com_warehouse/images/prototype/film_strip/NTW2-Original/12-NTW2.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail9" src="/components/com_warehouse/images/prototype/film_strip/NTW2-w90/12-NTW2.jpg">
		            </a> 
		            <a title="Web tip after removal of loose concrete." href="/components/com_warehouse/images/prototype/film_strip/NTW2-Original/13-NTW2.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail10" src="/components/com_warehouse/images/prototype/film_strip/NTW2-w90/13-NTW2.jpg">
		            </a> 
		          </div>
		          </div>
		          <div id="showcase-next" class=""></div>
		        </div>
		      </div>
ENDHTML;
      return $strHTML;
  }
  
  public function getExperiment874(){
  	$strHTML = <<< ENDHTML
              <div class="sscontainer">
		        <div id="showcase">
		          <div id="showcase-prev" class=""></div>
		          <div id="showcase-window">
		          <div class="showcase-pane" style="left: 0px;">
		            <a title="Fourth story web of NTW1 after web failure." href="/components/com_warehouse/images/prototype/film_strip/NTW1-Original/16-NTW1.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail1" src="/components/com_warehouse/images/prototype/film_strip/NTW1-w90/16-NTW1.jpg">
		            </a>
	                <a title="First story flange of NTW1 after flange failure." href="/components/com_warehouse/images/prototype/film_strip/NTW1-Original/17-NTW1.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail2" src="/components/com_warehouse/images/prototype/film_strip/NTW1-w90/17-NTW1.jpg">
		            </a>
	                <a title="Second story flange of NTW1 after flange failure." href="/components/com_warehouse/images/prototype/film_strip/NTW1-Original/18-NTW1.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail3" src="/components/com_warehouse/images/prototype/film_strip/NTW1-w90/18-NTW1.jpg">
		            </a>
	                <a title="Third story flange of NTW1 after flange failure." href="/components/com_warehouse/images/prototype/film_strip/NTW1-Original/19-NTW1.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail4" src="/components/com_warehouse/images/prototype/film_strip/NTW1-w90/19-NTW1.jpg">
		            </a> 
		            <a title="Fourth story flange of NTW1 after flange failure." href="/components/com_warehouse/images/prototype/film_strip/NTW1-Original/20-NTW1.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail5" src="/components/com_warehouse/images/prototype/film_strip/NTW1-w90/20-NTW1.jpg">
		            </a> 
		            <a title="First story web of NTW1 after flange failure." href="/components/com_warehouse/images/prototype/film_strip/NTW1-Original/21-NTW1.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail6" src="/components/com_warehouse/images/prototype/film_strip/NTW1-w90/21-NTW1.jpg">
		            </a> 
		            <a title="Second story web of NTW1 after flange failure." href="/components/com_warehouse/images/prototype/film_strip/NTW1-Original/22-NTW1.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail7" src="/components/com_warehouse/images/prototype/film_strip/NTW1-w90/22-NTW1.jpg">
		            </a> 
		            <a title="Third story web of NTW1 after flange failure." href="/components/com_warehouse/images/prototype/film_strip/NTW1-Original/23-NTW1.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail8" src="/components/com_warehouse/images/prototype/film_strip/NTW1-w90/23-NTW1.jpg">
		            </a> 
		            <a title="Fourth story web of NTW1 after flange failure." href="/components/com_warehouse/images/prototype/film_strip/NTW1-Original/24-NTW1.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail9" src="/components/com_warehouse/images/prototype/film_strip/NTW1-w90/24-NTW1.jpg">
		            </a> 
		            <a title="Web tip after removal of loose concrete." href="/components/com_warehouse/images/prototype/film_strip/NTW1-Original/26-NTW1.jpg" rel="lightbox[filmstrip]">
		              <img class="thumbima" alt="thumbnail10" src="/components/com_warehouse/images/prototype/film_strip/NTW1-w90/26-NTW1.jpg">
		            </a> 
		          </div>
		          </div>
		          <div id="showcase-next" class=""></div>
		        </div>
		      </div>
ENDHTML;
      return $strHTML;
  }
	
}
