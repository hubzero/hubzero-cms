<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

?>

<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addScript($this->baseurl."/components/com_warehouse/js/ajax.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/warehouse.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/Fx.Slide/tree.js", 'text/javascript');
  $document->addScript($this->baseurl."/includes/js/joomla.javascript.js", 'text/javascript');
?>


<div class="innerwrap">
  <div class="content-header">
	<h2 class="contentheading">NEES Project Warehouse</h2>
  </div>
  
  <div id="warehouseWindow" style="padding-top:20px;">
    <?php #tree browser section ?>
    <div id="treeBrowserMain" style="float:left;width:29%;">
      <?php echo $this->strTreeTabs; ?>
      <div id="treeSlideWrapperJs">
        <div id="treeSliderJs">
          <?php //echo $this->mod_treebrowser; ?>
          <?php echo $this->mod_warehousefilter; ?>
        </div>
      </div>
    </div>
    <?php #end tree browser section ?>
    
    <div id="overview_section" class="main section" style="width:71%;float:left;">
    <?php echo TabHtml::getSearchForm( "/warehouse/find" ); ?>
    <?php echo $this->strTabs; ?>
    <form id="frmResults" style="margin:0px;padding:0px;">
	<input type="hidden" name="task" value="find"/>
	<input type="hidden" name="keywords" value="<?php echo $_SESSION[Search::KEYWORDS]; ?>"/>
	<input type="hidden" name="type" value="<?php echo $_SESSION[Search::SEARCH_TYPE]; ?>"/>
	<input type="hidden" name="funding" value="<?php echo $_SESSION[Search::FUNDING_TYPE]; ?>"/>
	<input type="hidden" name="member" value="<?php echo $_SESSION[Search::MEMBER]; ?>"/>
	<input type="hidden" name="startdate" value="<?php echo $_SESSION[Search::START_DATE]; ?>"/>
	<input type="hidden" name="enddate" value="<?php echo $_SESSION[Search::END_DATE]; ?>"/>
        <div id="project-list" class="subject-full">
          <div id="project-sort">
            <?php
              $strOrderBy = $_REQUEST[Search::ORDER_BY];
              $iResultCount = $_REQUEST[Search::COUNT];
              $dTimer = $_REQUEST[Search::TIMER];
              $strKeywords = StringHelper::EMPTY_STRING;
              if(isset($_SESSION[Search::KEYWORDS])){
                $strKeywords = trim($_SESSION[Search::KEYWORDS]);
                if(StringHelper::hasText($strKeywords)){
              ?>
                <p class="information"><b>Keywords:</b> &nbsp;<?php echo $strKeywords; ?></p>
              <?php
                }
              }
            ?>

            <p id="project-count" style="margin-bottom:30px;">
              <?php
                $strOrderBy = $_REQUEST[Search::ORDER_BY];
                $iResultCount = $_REQUEST[Search::COUNT];
                $dTimer = $_REQUEST[Search::TIMER];
              ?>  
              <b>Results:</b> &nbsp;<?php echo $iResultCount; ?>  
            </p>
          </div>

          <?php 
            $oProjectArray = unserialize($_SESSION[Search::RESULTS]);       
            if(empty($oProjectArray)){
              ?> 
                <p class="warning">No projects found.  Go to <a href="/warehouse/advancedsearch">Advanced Search</a></p>
              <?php
            }

            $strProjectIconArray = $_SESSION[Search::THUMBNAILS];
            foreach($oProjectArray as $iProjectIndex=>$oProject){
          	  $iProjectId = $oProject->getId();
        	  $strTitle = $oProject->getTitle();
        	  $strStartDate = strftime("%B %d, %Y", strtotime($oProject->getStartDate()));
                  $strStartDate = ($strStartDate=="December 31, 1969") ? "" : $strStartDate;
        	  $oDescriptionClob = StringHelper::neat_trim($oProject->getDescription(), 250);
                  if($oDescriptionClob=="...")$oDescriptionClob="";

                  //focus in on keywords
                  if(StringHelper::hasText($strKeywords)){
                    //original keywords
                    $strKeywordArray = split(" ", $strKeywords);

                    //a little keyword cleanup
                    $strKeywordTempArray = array();
                    foreach($strKeywordArray as $iKeywordIndex=>$strThisKeyword){
                      //convert all keyword terms to lower case
                      $strKeywordArray[$iKeywordIndex] = trim(strtolower($strThisKeyword));

                      //remove articles and prepositions
                      if(!SearchHelper::isArticle($strKeywordArray[$iKeywordIndex]) &&
                         !SearchHelper::isPreposition($strKeywordArray[$iKeywordIndex])){
                        array_push($strKeywordTempArray, $strKeywordArray[$iKeywordIndex]);
                      }
                    }

                    //keep the good keywords
                    $strKeywordArray = $strKeywordTempArray;
                    
                    //original description terms
                    $strDescTemp = str_replace("-", " ", $oDescriptionClob);  //replace hyphen
                    $strDescTemp = str_replace("_", " ", $strDescTemp);       //replace underscore
                    $strDescriptionArray = explode(" ", $strDescTemp);

                    //convert all description terms to lower case
                    $strDescriptionLowerArray = array();
                    foreach($strDescriptionArray as $iDescIndex=>$strThisDesc){
                      $strDescriptionLowerArray[$iDescIndex] = trim(strtolower($strThisDesc));
                    }
                    
                    //original title terms
                    $strTitleTemp = str_replace("-", " ", $strTitle);       //replace hyphen
                    $strTitleTemp = str_replace("_", " ", $strTitleTemp);   //replace underscore
                    $strTitleArray = explode(" ", $strTitleTemp);
                    
                    //convert all title terms to lower case
                    $strTitleLowerArray = array();
                    foreach($strTitleArray as $iTitleIndex=>$strThisTitle){
                      $strTitleLowerArray[$iTitleIndex] = trim(strtolower($strThisTitle));
                    }
                    
                    foreach($strKeywordArray as $strKeywordLowerCase){
                      //get the keys for all of the matched (lowercase) terms
                      $iDescriptionKeywordIndexArray = array_keys($strDescriptionLowerArray, $strKeywordLowerCase);

                      //use original description array to find replace term
                      foreach($iDescriptionKeywordIndexArray as $iDescIndex){
                        $strReplace = "<span class='resultsKeyword'>$strDescriptionArray[$iDescIndex]</span>";
                        $oDescriptionClob = str_ireplace($strKeywordLowerCase, $strReplace, $oDescriptionClob);
                      }

                      //get the keys for all of the matched (lowercase) terms
                      $iTitleKeywordIndexArray = array_keys($strTitleLowerArray, $strKeywordLowerCase);

                      //use original title array to find replace term
                      foreach($iTitleKeywordIndexArray as $iTitleIndex){
                        $strReplace = "<span class='resultsKeyword'>$strTitleArray[$iTitleIndex]</span>";
                        $strTitle = str_ireplace($strKeywordLowerCase, $strReplace, $strTitle);
                      }
                    }
                  }


                  //$strThumbnail =  $oProject->getProjectThumbnailHTML("icon");
                  $strThumbnail =  $strProjectIconArray[$iProjectIndex];
                  $strWidth="100%";
                  if( strlen($strThumbnail) > 0 ){
                    $strWidth="85%";  
                  }

          ?>
            <div id="Project" style="width:100%;">
              <div id="ProjectInfo" style="float:left;width:<?php echo $strWidth; ?>;">
                <a href="/warehouse/project/<?php echo $iProjectId; ?>" style="font-size: 15px;"><?php echo $strTitle; ?></a>
                <p style="color: #666666"><?php echo $strStartDate; ?></p>
                <p><?php echo $oDescriptionClob; ?></p>
                <p><a href="/warehouse/project/<?php echo $iProjectId; ?>" style="color: green;"><?php echo $_SERVER['SERVER_NAME']; ?>/warehouse/project/<?php echo $iProjectId; ?></a></p>
              </div>
              <div align="right" id="thumbnail">
                <?php
                  if( strlen($strThumbnail) > 0 ){
                    echo $strThumbnail;
                  } 
                ?>
              </div>
              <div class="clear"></div>
            </div>
            <?php if($iProjectIndex < (sizeof($oProjectArray)-1)): ?>
              <hr class="dashes"/>
              <br>
            <?php endif; ?>
            
          <?php 
            }
          ?>
      
          <?php 
            echo $this->pagination;
          ?>
        </div>
      </form>
    </div>
    <div class="clear"></div>
  </div>
  
</div>


