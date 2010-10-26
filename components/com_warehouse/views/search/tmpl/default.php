<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>



<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
//  $document->addScript($this->baseurl."/components/com_warehouse/js/Fx.Slide/demo.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/Fx.Slide/tree.js", 'text/javascript');
  $document->addScript($this->baseurl."/includes/js/joomla.javascript.js", 'text/javascript');

  


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
      	  <table style="width:0px;border-bottom:0px;border-top:0px;margin-top:10px;">
	        <tr>
	          <td><label for="strKeywords">Keywords:</label></td>
	          <td><input id="strKeywords" type="text" class="searchInput" name="keywords" value=""  onClick="this.value=''"/></td>
	          <td><input type="submit" value="GO"/></td>
	        </tr>
                <!--
	        <tr>
	          <td><label for="strType">Type:</label></td>
	          <td colspan="2">
	          	<select id="strType" name="type" class="searchInput" disabled>
	          	  <option value="">All Projects</option>
	          	  <option value="">My Projects</option>
	          	  <option value="">All Publicly Assessible Projects</option>
	          	  <option value="">Curated Projects</option>
	          	</select>
	          </td>
	        </tr>
                -->
	        <tr>
	          <td><label for="strFunding">Funding:</label></td>
	          <td colspan="2">
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
	          <td><label for="strMember">Member:</label></td>
	          <td colspan="2"><input id="strMember" name="member" type="text" class="searchInput" value="Last Name, First Name" onClick="this.value=''"/></td>
	        </tr>
	        
	        <?php JHTML::_('behavior.calendar'); ?>
	        <tr>
	          <td nowrap><label for="strStartDate">Start Date:</label></td>
	          <td><input id="strStartDate" type="text" name="startdate" class="searchInput" value="mm/dd/yyyy" onClick="this.value=''"/></td>
	          <td><img class="calendar" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="calendar" onclick="return showCalendar('strStartDate', '%m/%d/%Y');" /></td>
	        </tr>
	        <tr>
	          <td nowrap><label for="strEndDate">End Date:</label></td>
	          <td><input id="strEndDate" type="text" name="enddate" class="searchInput" value="mm/dd/yyyy" onClick="this.value=''"/></td>
	          <td><img class="calendar" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="calendar" onclick="return showCalendar('strEndDate', '%m/%d/%Y');" /></td>
	        </tr>
	      </table>
	    </fieldset>
	  </form>
	  <?php #end search form ?>

    </div>
  </div>  
  
</div>