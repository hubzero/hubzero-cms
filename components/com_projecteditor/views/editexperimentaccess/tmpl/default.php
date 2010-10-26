<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_projecteditor/css/projecteditor.css",'text/css');
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addStyleSheet($this->baseurl."/templates/fresh/html/com_groups/groups.css",'text/css');
  $document->addStyleSheet($this->baseurl."/plugins/tageditor/autocompleter.css",'text/css');

  $document->addScript($this->baseurl."/components/com_projecteditor/js/ajax.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/tips.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/projecteditor.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/resources.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/textboxlist.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/observer.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/autocompleter.js", 'text/javascript');
?>

<?php
  /* @var $oPerson Person */
  $oPerson = unserialize($_REQUEST[PersonPeer::TABLE_NAME]);

  /* @var $oProject Project */
  $oProject = unserialize($_REQUEST[ProjectPeer::TABLE_NAME]);
?>

<form id="frmPopout" name="frmPopout" action="/warehouse/projecteditor/removefile" method="post">
    <input type="hidden" name="personId" value="<?php echo $oPerson->getId(); ?>"/>
    <input type="hidden" name="projectId" value="<?php echo $oProject->getId(); ?>"/>
    <input type="hidden" name="format" value="ajax"/>

    
    <div><h2>Edit Access to Experiments</h2></div>
    <div class="information"><b>Project:</b> <?php echo $oProject->getNickname(); ?></div>

    <p align="center" class="topSpace20"><b><?php echo ucfirst($oPerson->getFirstName())." ".ucfirst($oPerson->getLastName()); ?></b></p>

    <table>
      <thead>
        <th width="1"><input id="checkAll" type="checkbox" name="checkAll" onClick="setAllCheckBoxes('frmPopup', 'experimentId[]', this.checked, <?php echo $oProject->getId(); ?>);"/></th>
        <th>Name</th>
        <th>Title</th>
      </thead>

      <?php
        $oExperimentArray = $oProject->getExperiments();
        foreach($oExperimentArray as $iIndex => $oExperiment){
          /* @var $oExperiment Experiment */
          ?>
          <tr>
            <td width="1"><input id="<?php echo $oProject->getId(); ?>" type="checkbox" name="experimentId" value="<?php echo $oExperiment->getId(); ?>"/></td>
            <td><?php echo $oExperiment->getName(); ?></td>
            <td width="100%"><?php echo $oExperiment->getTitle(); ?></td>
          </tr>
          <input id="<?php echo $oProject->getId(); ?>" type="hidden" name="experimentId" value="<?php echo $oExperiment->getId(); ?>"/>
          <?php
        }
      ?>
    </table>

    <p align="center" class="topSpace20">
      <input type="button" value="Save Experiment Access" onClick="saveExperimentAccess('/warehouse/projecteditor/setexperimentaccess', <?php echo $oPerson->getId(); ?>, 'expAccess-<?php echo $oPerson->getId(); ?>');window.parent.SqueezeBox.close();"/>
    </p>
</form>