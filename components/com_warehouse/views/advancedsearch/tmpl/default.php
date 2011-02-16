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

<table style="width:0px;border:0px;margin-top:10px;">
  <tr>
    <td nowrap><label for="strKeywords">Keywords:</label></td>
    <td><input id="strKeywords" type="text" class="searchInput" name="keywords" value=""/></td>
    <td><input type="submit" value="GO"/></td>
    <td nowrap>&nbsp;&nbsp;<a href="/warehouse/search">Search</a></td>
  </tr>
  <tr>
    <td nowrap><label for="strFunding">Funding:</label></td>
    <td colspan="3">
      <select id="strFunding" name="funding" class="searchInput">
          <option value="">All Projects</option>
          <option value="Caltrans">Caltrans</option>
          <option value="Connecticut Cooperative Highway Research Program">Connecticut Cooperative Highway Research Program</option>
          <option value="DARPA">DARPA</option>
          <option value="EERI">EERI</option>
          <option value="FEMA">FEMA</option>
          <option value="FHWA">FHWA</option>
          <option value="KOCED">KOCED</option>
          <option value="MAE Center">MAE Center</option>
          <option value="MCEER Center">MCEER Center</option>
          <option value="NCHRP">NCHRP</option>
          <option value="NCREE">NCREE</option>
          <option value="NIED">NIED (E-defense)</option>
          <option value="NIST">NIST</option>
          <option value="NSF">NSF</option>
          <option value="NSF NEES Program">NSF NEES Program</option>
          <option value="NIH">NIH</option>
          <option value="PCI">PCI</option>
          <option value="PEER Center">PEER Center</option>
          <option value="PITA">PITA</option>
          <option value="USGS">USGS</option>
        </select>
    </td>
  </tr>
  <tr>
    <td nowrap><label for="strMember">Member:</label></td>
    <td colspan="3"><input id="strMember" name="member" type="text" class="searchInput" value="Last Name, First Name" onClick="this.value=''"/></td>
  </tr>
  <tr>
    <td nowrap><label for="strSite">NEES Site:</label></td>
    <td colspan="3">
      <select id="strSite" name="neesSite" class="searchInput">
        <option value="0">All Sites</option>
        <?php
          $oFacilityArray = unserialize($_REQUEST[FacilityPeer::TABLE_NAME]);
          foreach($oFacilityArray as $oFacility){
        ?>
            <option value="<?php echo $oFacility->getId(); ?>"><?php echo $oFacility->getName(); ?></option>
        <?php
          }
        ?>
      </select>
    </td>
  </tr>
  <tr>
    <td nowrap><label for="strProjectType">Project Type:</label></td>
    <td colspan="2">
      <select id="strProjectType" name="projectType" class="searchInput">
        <option value="">All Projects</option>
        <option value="1">Unstructured Project</option>
        <option value="2">Structured Project</option>
        <option value="3">Project Group</option>
        <option value="4">Hybrid Project</option>
      </select>
    </td>
  </tr>
  <tr>
    <td nowrap><label for="strProjectNumber">Project #:</label></td>
    <td colspan="3"><input id="strProjectNumber" name="projid" type="text" class="searchInput" value="(Separate by commas)" onClick="this.value=''"/></td>
  </tr>
  <tr>
    <td nowrap><label for="strAwardNumber">Award #:</label></td>
    <td colspan="3"><input id="strAwardNumber" name="award" type="text" class="searchInput" value="(Separate by commas)" onClick="this.value=''"/></td>
  </tr>
  <tr>
    <td nowrap><label for="strMaterialType">Material Type:</label></td>
    <td colspan="3"><input id="strMaterialType" name="materialType" type="text" class="searchInput" value="(Separate by commas)" onClick="this.value=''"/></td>
  </tr>
  <tr>
    <td nowrap><label for="strProjectYear">Project Year:</label></td>
    <td colspan="3"><input id="strProjectYear" name="projectYear" type="text" class="searchInput" value=""/></td>
  </tr>
</table>