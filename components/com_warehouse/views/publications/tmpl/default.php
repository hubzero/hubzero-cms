<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addScript($this->baseurl."/components/com_warehouse/js/ajax.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/warehouse.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/resources.js", 'text/javascript');
?>

<?php $oProject = unserialize($_REQUEST[Search::SELECTED]); ?>

<div class="innerwrap">
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
          <p><?php //echo $oExperiment->getExperimentThumbnailHTML(); ?></p>

          <div id="stats" style="margin-top:30px; border-width: 1px; border-style: dashed; border-color: #cccccc; ">
            <p style="margin-left:10px; margin-top:10px;"><?php echo $this->iEntityActivityLogViews; ?> Views</p>

            <p style="margin-left:10px;"><?php echo $this->iEntityActivityLogDownloads; ?> Downloads</p>
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
            <div id="experimentInfo">

                <p>Publication List</p>

                <p><a href="/warehouse/project/<?php echo $oProject->getId(); ?>">Return</a></p>

                <table cellpadding="1" cellspacing="1">
                  <thead>
                    <th>Publication</th>
                  </thead>
                  <?php

                  $oPublicationArray = $this->pubArray;
                  foreach($oPublicationArray as $iPubIndex=>$oPublication){
                     $strAuthorArray = $oPublication['authors'];

                     $strAuthors = "";
                     foreach($strAuthorArray as $iAuthorIndex=>$strAuthor){
                       $strAuthors .= "<a href='/members/".$strAuthor['authorid']."'>".$strAuthor['name']."</a>";
                       if($iAuthorIndex < (sizeof($strAuthorArray)-1)){
                         $strAuthors .= "; ";
                       }
                     }

                     $strBgColor = "odd";
                     if($iPubIndex%2 === 0){
                       $strBgColor = "even";
                     }

                  ?>

                  <tr class="<?php echo $strBgColor; ?>">
                    <td id="publication<?php echo $iPubIndex; ?>>">
                      <?php echo $strAuthors .", \"". $oPublication['title'] ."\""; ?>
                      (<a href="/resources/<?php echo $oPublication['id']; ?>">view</a>)
                    </td>
                  </tr>

                  <?php
                  }
                  ?>
                </table>

            </div>
          </div>
        </div>
    </div>
	<!-- close overview_section -->

    <div class="clear"></div>
  </div>

</div>


