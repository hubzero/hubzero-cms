<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>



<?php
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addStyleSheet($this->baseurl."/plugins/tageditor/autocompleter.css",'text/css');

  $document->addScript($this->baseurl."/components/com_warehouse/js/Fx.Slide/tree.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/ajax.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/warehouse.js", 'text/javascript');
  $document->addScript($this->baseurl."/includes/js/joomla.javascript.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/textboxlist.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/observer.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/autocompleter.js", 'text/javascript');
?>

<div class="innerwrap">
  <div class="content-header">
	<h2 class="contentheading">NEES Project Warehouse</h2>
  </div>

  <div id="warehouseWindow" style="padding-top:20px;">

    <?php #tree browser section ?>
    <div id="treeBrowserMain" style="float:left;width:3%;">
      <?php echo $this->strTreeTabs; ?>
      <div id="treeSlideWrapperJs" style="display:none">
        <div id="treeSliderJs">
          <?php echo $this->mod_treebrowser; ?>
        </div>
      </div>
    </div>
    <?php #end tree browser section ?>


    <div id="overview_section" class="main section" style="width:97%;float:left;">
      <?php #main tabs for current page ?>
      <?php echo $this->strTabs; ?>


  	  <?php #right side of page ?>
  	  <div class="aside">
        <div id="popularTags">
          <p style="font-size:16px;font-weight:bold;color:#999999">Popular Tags</p>
          <ol class="tags">
            <li><a href="/warehouse/find?keywords=<?php echo urlencode('earthquake'); ?>">earthquake</a></li>
            <li><a href="/warehouse/find?keywords=<?php echo urlencode('tsunami'); ?>">tsunami</a></li>
            <li><a href="/warehouse/find?keywords=<?php echo urlencode('steel frame'); ?>">steel frame</a></li>
          </ol>
        </div>

        <?php echo $this->mod_warehousepopularsearches; ?>

      </div>

      <?php #end right side of page ?>

      <?php #search form ?>
      <form id="frmSearch" action="/warehouse/find" method="get">
        <fieldset>
          <div id="searchFormTable" style="margin-top:10px;">
		  
		 <br>
		 
		 <p>The <strong>NEES Project Warehouse </strong>is the centralized data repository for sharing and publishing earthquake engineering research data 
		 from experimental and numerical studies. <br><br>The data in the Project Warehouse are associated with research projects funded by a variety of agencies, 
		 including the National Science Foundation (NSF), and include experiments performed at NEES and non-NEES equipment sites.</p>

		 <br>
		 
		 <font size="4.5"><b>Advanced Search Project Warehouse</b></font>
		 
		 <br><br>
		  
		  
		  
		  
		  
		  
		  
      	    <table style="width:0px;border:0px;margin-top:10px;">
              <tr>
                <td><label for="strKeywords">Keywords:</label></td>
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
                <td colspan="3">
                  <div class="searchInput">
                    <input id="strMaterialType" name="materialType" type="text" value="" onClick="this.value=''" autocomplete="off"/>
                  </div>
                </td>
              </tr>
              <tr>
                <td nowrap><label for="strProjectYear">Project Year:</label></td>
                <td colspan="3"><input id="strProjectYear" name="projectYear" type="text" class="searchInput" value=""/></td>
              </tr>
	    </table>
          </div>
        </fieldset>
      </form>
      <?php #end search form ?>

    </div>
  </div>

</div>