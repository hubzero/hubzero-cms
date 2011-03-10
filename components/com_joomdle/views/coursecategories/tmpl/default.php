<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php

$index_url = JURI::base()."index.php";

?>
<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
                <?php echo JText::_('CJ COURSE CATEGORIES'); ?>
        </td>
</tr>

<tr>
        <td width="60%" colspan="2">
        </td>
</tr>
<?php
$odd= 0;
if (is_array($this->categories))
$itemid = JoomdleHelperContent::getMenuItem();
foreach ($this->categories as  $cat) : ?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
        <td height="20">
		<?php $url = JRoute::_("index.php?option=com_joomdle&view=coursecategory&cat_id=".$cat['id'].':'.JFilterOutput::stringURLSafe($cat['name'])."&Itemid=$itemid"); ?>
		<?php  echo "<a href=\"$url\">".$cat['name']."</a><br>"; ?>
                <br /><span class="description"><?php echo nl2br($cat['description']); ?></span>
        </td>
</tr>
<?php endforeach; ?>

</table>
