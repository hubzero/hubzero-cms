<?php 

// Resource Highlight Template
// Jason Lambert NEESHub 2010

// no direct access
defined('_JEXEC') or die('Restricted access');

$database =& JFactory::getDBO();

$sortbys = array();
if ($this->config->get('show_ranking')) {
	$sortbys['ranking'] = JText::_('COM_RESOURCES_RANKING');
}
$sortbys['date'] = JText::_('COM_RESOURCES_DATE_PUBLISHED');
$sortbys['date_modified'] = JText::_('COM_RESOURCES_DATE_MODIFIED');
$sortbys['title'] = JText::_('COM_RESOURCES_TITLE');
?>
<?php if ($this->no_html == 1) {?>

<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->baseurl ?>/templates/fresh/html/com_resources/resources.css"/>
<?php }?>

<?php if (!$this->minimalist) { ?>
<div class="resource-highlight">
<img id="eot-img-header" src="templates/fresh/images/logos/neesacademysmall.jpg"/><h1><?php echo $this->type ?></h1>
<p> Here are some highlighted resources, you can always browse the complete <a href="/resources/<?php echo $this->type ?>">list of <?php echo $this->type ?> here &rsaquo;</a> </p>
<?php } else { ?>
<h2>In The News</h2>
<div ALIGN="RIGHT">

<div class="resource-action active">
<a href="/contribute/?step=1&type=10">Contribute</a>

</div>
<p>The most recent 25 entries appear in this list, click here for <a href="/resources/notes">Archived Entries &rsaquo;</a></p>
</div>
<?php }?>
<div id="content-header" class="full">
<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="section">
	
	<?php
	if ($this->results) {
		$show_date = 3;
		//echo ResourcesHtml::writeResultsThumbs( $database, $this->results, $this->authorized, $show_date,  );
		echo ResourcesHtml::writeResultsThumbs( $database, $this->results, false, $show_date, $this->columns == 2, $this->minimalist );
		echo '<div class="clear"></div>';
	} else { ?>
				<p class="warning"><?php echo JText::_('COM_RESOURCES_NO_RESULTS'); ?></p>
	<?php } ?>

</div><!-- / .section -->
<?php if (!$this->minimalist) { ?>
</div><!-- /resource highlight -->
<?php } else { ?>
<p>The most recent 25 entries appear in this list, click here for <a href="/resources/notes">Archived Entries &rsaquo;</a></p>
<?php }?>
<script>
</script>