<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

?>

<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
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
          <?php echo $this->mod_treebrowser; ?>
        </div>
      </div>
    </div>
    <?php #end tree browser section ?>
    
    <div id="overview_section" class="main section" style="width:71%;float:left;">
    <?php echo TabHtml::getSearchForm( "/warehouse/find" ); ?>
    <?php echo $this->strTabs; ?>
    <form id="frmResults" style="margin:0px;padding:0px;">
	  <input type="hidden" name="task" value="find"/>
        <div id="project-list" class="subject-full">
          <?php 
            $oProjectArray = unserialize($_SESSION[Search::RESULTS]);       
            if(empty($oProjectArray)){
                $strUsername = $this->oHubUser;
                if(empty($strUsername)){
                  ?>
                    <p class="warning">Please login or register.</p>
                  <?php
                }else{
                  ?>
                    <p class="warning">No projects found.</p>
                  <?php
                }
            }

            $strProjectIconArray = $_SESSION[Search::THUMBNAILS];
            foreach($oProjectArray as $iProjectIndex=>$oProject){
          	  $iProjectId = $oProject->getId();
        	  $strTitle = $oProject->getTitle();
        	  $strStartDate = strftime("%B %d, %Y", strtotime($oProject->getStartDate()));
        	  $oDescriptionClob = StringHelper::neat_trim($oProject->getDescription(), 250);
                  if($oDescriptionClob=="...")$oDescriptionClob="";

                  $strThumbnail =  $strProjectIconArray[$iProjectIndex];

                  
          ?>
            <div id="Project" style="width:100%;">
              <div id="ProjectInfo" style="float:left;width:85%;"
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


