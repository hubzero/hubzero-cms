<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addScript($this->baseurl."/components/com_warehouse/js/warehouse.js", 'text/javascript');
?>

<?php $oProject = unserialize($_REQUEST[Search::SELECTED]); ?>

<div class="innerwrap>
  <div class="content-header">
	<h2 class="contentheading">NEES Project Warehouse</h2>
  </div>
  
  <div id="warehouseWindow" style="padding-top:20px;">
    <div id="title" style="padding-bottom:1em;">
      <span style="font-size:16px;font-weight:bold;"><?php echo $oProject->getTitle(); ?></span>
    </div>
      
    <div id="treeBrowser" style="float:left;width:20%;"></div>
    
    <div id="overview_section" class="main section" style="width:100%;float:left;">      
      <?php echo TabHtml::getSearchForm( "/warehouse/find" ); ?>
      <?php echo $this->strTabs; ?>
      
      <div class="aside">
        <p style="font-size:11px;color:#999999" align="center">
          <?php 
			$oProjectImageDataFile = unserialize($_REQUEST["oProjectImage"]);
			if($oProjectImageDataFile!=null){ 
			  $strDirLink = $oProjectImageDataFile[0]->getPath()."/".$oProjectImageDataFile[0]->getName();
              $strFileLink = str_replace("/nees/home/",  "",  $strDirLink);
              $strFileLink = str_replace(".groups",  "",  $strFileLink);
            ?>
			  <img src="/data/get/<?php echo $strFileLink; ?>"/><?php echo $oProjectImageDataFile[0]->getDescription(); ?>
			<?php }
          ?>
        </p>
      
        <div id="stats" style="margin-top:30px; border-width: 1px; border-style: dashed; border-color: #cccccc; ">
          <p style="margin-left:10px; margin-top:10px;">1000 Views</p>
          
          <p style="margin-left:10px;">100 Downloads</p>    
        </div>
        
      
        <div id="curation">
          <span class="curationTitle">Curation progress:</span>
          <?php echo $this->mod_curationprogress; ?>
        </div>
        
        <div class="whatisthis">
		  <h4>What's this?</h4>
		  <p>
		    Once the curator starts working with your submission, monitor the object's progress by reading 
		    the curation history.
		  </p>
		</div>
      </div>
      <div class="subject">
        <div id="about" style="padding-top:1em;">
          <table cellpadding="1" cellspacing="1" style="border-bottom:0px;border-top:0px;">
            <tr id="people">
              <td style="font-weight:bold;width:1px;" nowrap="">PI(s):</td>
              <td><?php echo $this->strPIandCoPIs; ?></td>
            </tr>
            <tr id="dates">
              <td style="font-weight:bold;">Dates:</td>
              <td><?php echo $this->strDates; ?></td>
            </tr>
            <tr id="facility">
              <td style="font-weight:bold;">Facility:</td>
              <td>
                <?php 
                  $oProjectFacilityOrganizationArray = unserialize($_REQUEST["oFacility"]);
                  foreach($oProjectFacilityOrganizationArray as $iFacilityIndex=>$oProjectFacilityOrganization){
                    if(isset($oProjectFacilityOrganization)){ ?>
                      <span class="nobr">
                      <a href="/sites/?view=site&id=<?php echo  $oProjectFacilityOrganization->getFacilityId(); ?>"><?php echo  $oProjectFacilityOrganization->getName(); ?></a>
                      <?php 
                        if($iFacilityIndex < sizeof($oProjectFacilityOrganizationArray)-1){
                      	  echo ",</span> "; 
                        }
                      }
                  }
                  
                ?>
              </td>
            </tr>
            <tr id="organization">
              <td style="font-weight:bold;" nowrap="">Organization(s):</td>
              <td>
                <?php 
                  $oOrganizationArray = unserialize($_REQUEST[OrganizationPeer::TABLE_NAME]);
                  foreach($oOrganizationArray as $iKey => $oOrganization){
                  ?>
                    <span class="nobr">
                    <?php
                      echo $oOrganization->getName();
                      if($iKey < sizeof($oOrganizationArray)-1){
                  	    echo ", ";
                      }
                    ?> 
                    </span>
                  <?php    
                  }                  
                ?>
              </td>
            </tr>
            <tr id="description">
              <td style="font-weight:bold;">Description:</td>
              <td>
                <?php
                  $oDescriptionClob = $oProject->getDescription();
                  echo $oDescriptionClob;
                ?>
                
              </td>
            </tr>
            <tr id="sponsor">
              <td style="font-weight:bold;">Sponsor:</td>
              <td><?php echo $this->strFundingOrg; ?></td>
            </tr>
            <tr id="websites">
              <td style="font-weight:bold;">Website(s):</td>
              <td>
                <?php 
                  $oProjectLinksArray = unserialize($_REQUEST[ProjectHomepagePeer::URL]);
                  foreach($oProjectLinksArray as $oProjectLink){
                    echo $oProjectLink->getCaption();
                ?>
                    (<a href="<?php echo $oProjectLink->getUrl(); ?>">view</a>) <br>
                <?php 
                  }
                ?>
              </td>
            </tr>
            <tr id="equipment">
              <td style="font-weight:bold;">Equipment:</td>
              <td>
                <?php
                  $oEquipmentArray = unserialize($_REQUEST[EquipmentPeer::TABLE_NAME]); 
                  if(!empty($oEquipmentArray)): 
                ?> 
                <a id="viewEquipmentLink" href="javascript:void(0);" onClick="hideElement('viewEquipmentLink');showElement('projectEquipment');showElement('hideEquipmentLink');">View Details</a>  
                <a id="hideEquipmentLink" href="javascript:void(0);" onClick="hideElement('hideEquipmentLink');hideElement('projectEquipment');showElement('viewEquipmentLink');" style="display:none;">Hide Details</a>
                <div style="border: 1px solid rgb(102, 102, 102); overflow: auto; width: 100%; padding: 0px; margin: 0px; display:none" id="projectEquipment">
				  <table cellpadding="1" cellspacing="1" style="width:100%;border-bottom:0px;border-top:0px;">
				    <tr>
				      <th style="font-weight:bold;">Equipment Class</th>
				      <th style="font-weight:bold;">Description</th>
				    </tr>
                	<?php foreach($oEquipmentArray as $iIndex=>$oEquipment){ 
				      $bgColor = "";
				      if($iIndex%2==0){
				      	$bgColor = "#EFEFEF";
				      }
				    ?>
				      <tr style="background: <?php echo $bgColor; ?>;">
				        <td><?php echo $oEquipment->getEquipmentModel()->getEquipmentClass()->getClassName(); ?></td>
				        <td><?php echo $oEquipment->getName(); ?></td>
				      </tr>
				    <?php } ?>
				  </table>
				</div>
				<?php endif; ?>
              </td>
            </tr>
            <tr id="tools">
              <td style="font-weight:bold;">Tools:</td>
              <td>
                <?php
                  $strToolList = "";
                  $strToolArray = $this->tools;
                  foreach($strToolArray as $strTool){
					$strToolList = "<span style='margin-right:7px;'>".$strTool."</span>";
                  }
                  echo $strToolList;
                ?>
              </td>
            </tr>
            <tr id="pubs">
              <td style="font-weight:bold;">Publications:</td>
              <td>
                <?php 
                  $oPublicationArray = $this->publications;
                  foreach($oPublicationArray as $iPubIndex=>$oPublication){
                     $strAuthorArray = $oPublication['authors'];
                     
                     $strAuthors = "";
                     foreach($strAuthorArray as $iAuthorIndex=>$strAuthor){
                       $strAuthors .= "<a href='/members/".$strAuthor['authorid']."'>".$strAuthor['name']."</a>";
                       if($iAuthorIndex < (sizeof($strAuthorArray)-1)){
                       	 $strAuthors .= "; ";
                       }	
                     }
                     
                  	?>
                    
                    <div id="publication<?php echo $iPubIndex; ?>>">
                      <?php echo $strAuthors .", \"". $oPublication['title'] ."\""; ?> 
                      (<a href="/resources/<?php echo $oPublication['id']; ?>">view</a>)
                    </div>	
                  <?php 
                  }
                  
                  if($this->publicationCount > 3){?>
                  	<a href="">more...</a>
                  <?php }
                ?>
              </td>
            </tr>
            <tr id="tags">
              <td style="font-weight:bold;" nowrap="">Tags (related projects):</td>
              <td><?php echo $this->mod_warehousetags; ?></td>
            </tr>
          </table>
    
        </div>
      </div>
    </div>
    <div class="clear"></div>
  </div>  
</div>



