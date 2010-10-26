<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: 0"); // Date in the past
?>


<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_projecteditor/css/projecteditor.css",'text/css');
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addStyleSheet($this->baseurl."/plugins/tageditor/autocompleter.css",'text/css');
  
  $document->addScript($this->baseurl."/components/com_projecteditor/js/ajax.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/tips.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/projecteditor.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/textboxlist.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/observer.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/autocompleter.js", 'text/javascript');
?>

<?php 
  $oUser = $this->oUser;
?>

<form id="frmProject" action="/warehouse/projecteditor/preview" method="post" enctype="multipart/form-data">
<input type="hidden" name="username" value="<?php echo $oUser->username; ?>" />
<input type="hidden" name="access" value="<?php $this->iAccess; ?>"/>

<div class="innerwrap">
  <div class="content-header">
	<h2 class="contentheading">NEES Project Warehouse</h2>
  </div>
  
  <div id="warehouseWindow" style="padding-top:20px;">
    <div id="title" style="padding-bottom:1em;">
      <span style="font-size:16px;font-weight:bold;">Confirm New Experiment</span>
    </div>
    
    <div id="overview_section" class="main section" style="width:100%;float:left;">
      <?php echo $this->strTabs; ?>
      
      <div class="aside">
        <p style="font-size:11px;color:#999999; border-width: 1px; border-style: solid; border-color: #cccccc;" align="center">
          <img src="/components/com_projecteditor/images/logos/NEES-logo_grayscale.png"/><br><br>
          <span style="font-size:48px;font-weight:bold;color:#999999;">NEES</span><br><br>
        </p>
        
        <input type="text" id="txtCaption" name="caption" value="Enter photo caption" style="width:210px;color:#999999;" onFocus="this.style.color='#000000'; this.value='';"/> <br><br>
        <input type="file" id="txtPhoto" name="photo"/>
      
        <div id="stats" style="margin-top:30px; border-width: 1px; border-style: dashed; border-color: #cccccc; ">
          <p style="margin-left:10px; margin-top:10px;">1000 Views</p>
          
          <p style="margin-left:10px;">100 Downloads</p>    
        </div>
        
      
        <div id="curation">
          <span class="curationTitle">Curation in progress:</span>
          <?php //echo $this->mod_curationprogress; ?>
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
          <?php 
            if(isset($_REQUEST["ERRORS"])){
              $strErrorArray = $_REQUEST["ERRORS"];
              if(!empty($strErrorArray)){?> 
                <p class="error">
                  <?  
                    foreach($strErrorArray as $strError){
                      echo $strError."<br>";
                    }
                  ?>
                </p> 
              <?php	
              }
            }
          ?>
          
          <?php echo $this->strFilmstrip; ?>

          <table cellpadding="1" cellspacing="1" style="border-bottom:0px;border-top:0px;margin-top:20px;">
            <tr id="title">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtTitle" class="editorLabel editorRequired">Title:</label>
                </p>
              </td>
              <td class="editorInputSize">
                <?php echo $this->strTitle; ?>
              </td>
            </tr>
            <tr id="description">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtDescription" class="editorLabel">Description:</label>
                </p>
              </td>
              <td class="editorInputSize">
                <?php echo $this->strDesc; ?>
              </td>
            </tr>
            <tr id="start_date">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="strStartDate" class="editorLabel editorRequired">Dates:</label>
                </p>
              </td>
              <td class="editorInputSize">
                <?php echo $this->strDates; ?>
              </td>
            </tr>
            <tr id="facility">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtFacility" class="editorLabel">Facility:</label>
                </p>
              </td>
              <td class="editorInputSize">
                <?php echo $this->strFacilities; ?>
              </td>
            </tr>
            <tr id="equipment">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="cboEquipment" class="editorLabel">Equipment:</label>
                </p>
              </td>
              <td class="editorInputSize">
                <?php echo $this->mod_warehouseequipment; ?>
              </td>
            </tr>
            <tr id="specimentType">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtSpecimenType" class="editorLabel">Specimen Type:</label>
                </p>
              </td>
              <td class="editorInputSize">
                <?php echo $this->strSpecimen; ?>
              </td>
            </tr>
            <tr id="specimenMaterial">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtSpecimenMaterial" class="editorLabel">Specimen Material:</label>
                </p>
              </td>
              <td class="editorInputSize">
                <div id="specimenMaterialList" class="editorInputFloat editorInputSize">
                  materials here...
                </div>
              </td>
            </tr>
            <tr id="tags">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="actags" class="editorLabel">Tags (keywords):</label>
                </p>
              </td>
              <td class="editorInputSize">
                <div id="tagInput">
                  <input type="hidden" value="" id="txtTags" name="tags" value="<?php echo $this->strTags; ?>"/>
                  <?php if( !empty($this->strTagArray) ): ?>
                    <ol class="tags" style="margin: 0;">
                    <?php
                      $iTagIndex = 0;
                      $strTagArray = $this->strTagArray;
                      while( $iTagIndex < sizeof($strTagArray)){
                        $strTag = $strTagArray[$iTagIndex];
                        ?>
                        <li style="margin: 0;"><a href="javascript:void(0);"><?php echo $strTag; ?></a></li>
                        <?php
                        ++$iTagIndex;
                      }
                    ?>
                    </ol>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <tr>
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtAccess" class="editorLabel editorRequired">Access Settings:</label>
                </p>
              </td>
              <td class="editorInputSize">
                <?php echo $this->strAccess; ?>
              </td>
            </tr>
            <tr id="confirm">
              <td></td>
              <td>
                <input type="submit" value="Confirm Experiment" style="margin-top:15px"/>
              </td>
            </tr>
          </table>
    
        </div>
      </div>
    </div>
    <div class="clear"></div>
  </div> 
</div>

</form>