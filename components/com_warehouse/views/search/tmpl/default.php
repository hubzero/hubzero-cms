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
      	    <table style="width:0px;border:0px;margin-top:10px;">
              <tr>
                <td><label for="strKeywords">Keywords:</label></td>
                <td><input id="strKeywords" type="text" class="searchInput" name="keywords" value=""/></td>
                <td><input type="submit" value="GO"/></td>
                <!--<td nowrap>&nbsp;&nbsp;<a href="javascript:void(0);" onClick="getMootools('/warehouse/advancedsearch?format=ajax','searchFormTable');">Advanced Search</a></td>-->
                <td nowrap>&nbsp;&nbsp;<a href="/warehouse/advancedsearch">Advanced Search</a></td>
              </tr>
	    </table>
          </div>
        </fieldset>
      </form>
      <?php #end search form ?>

    </div>
  </div>

</div>