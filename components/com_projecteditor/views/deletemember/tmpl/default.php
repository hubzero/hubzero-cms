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

  $oPersonEntityRoleArray = unserialize($_REQUEST[PersonEntityRolePeer::TABLE_NAME]);
?>

<form id="frmPopout" action="/warehouse/projecteditor/removemember" method="post">
    <input type="hidden" name="personId" value="<?php echo $oPerson->getId(); ?>"/>
    <input type="hidden" name="projectId" value="<?php echo $oProject->getId(); ?>"/>
    <input type="hidden" name="format" value="ajax"/>

    
    <div><h2>Delete Team Member?</h2></div>
    <div class="information"><b>Project:</b> <?php echo $oProject->getNickname(); ?></div>

    <p align="center" class="topSpace20">Deleting a team member removes them from both the project and group space.</p>
    <p align="center" class="topSpace20"><b><?php echo ucfirst($oPerson->getFirstName())." ".ucfirst($oPerson->getLastName()); ?></b></p>
    <p align="center" class="topSpace20">
      <?php
        foreach($oPersonEntityRoleArray as $iIndex => $oPersonEntityRole){
          /* @var $oPersonEntityRole PersonEntityRole */
          echo $oPersonEntityRole->getRole()->getDisplayName();
          if($iIndex < (sizeof($oPersonEntityRoleArray)-1)){
            echo ", ";
          }
        }
      ?>
    </p>
    <p align="center" class="topSpace20">
      <input type="button" value="Delete Member" onClick="document.getElementById('frmPopout').submit();window.top.setTimeout('window.parent.document.getElementById(\'sbox-window\').close()', 700);"/>
    </p>
</form>