<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/templates/newpulse/css/main.css",'text/css');
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  //$document->addScript($this->baseurl."/components/com_curate/js/ajax.js", 'text/javascript');
?>

<div class="innerwrap>
  <div class="content-header">
	<h2 class="contentheading">NEES Project Warehouse</h2>
  </div>
  <?php echo $this->strTabs; ?>
  <div id="overview_section" class="main section">
    <div class="aside">Photo, Stats, Curation</div>
    <div class="subject">
      <?php print_r($this->strResultsArray); ?>
    </div>
  </div>
</div>



