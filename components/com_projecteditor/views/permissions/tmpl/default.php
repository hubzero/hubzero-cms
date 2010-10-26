<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addStyleSheet($this->baseurl."/components/com_projecteditor/css/projecteditor.css",'text/css');
?>

<?php
 $strAuthorizationArray = $this->strAuthorizationArray;
?>

<input type="checkbox" name="canView" value="1" checked disabled> view &nbsp;
<input type="checkbox" name="canCreate" value="1" <?php if(array_search("create",$strAuthorizationArray)){echo "checked";} ?>> create  &nbsp;
<input type="checkbox" name="canEdit" value="1" <?php if(array_search("edit",$strAuthorizationArray)){echo "checked";} ?>> edit &nbsp;
<input type="checkbox" name="canDelete" value="1" <?php if(array_search("delete",$strAuthorizationArray)){echo "checked";} ?>> delete  &nbsp;
<input type="checkbox" name="canGrant" value="1" <?php if(array_search("grant",$strAuthorizationArray)){echo "checked";} ?>> grant


