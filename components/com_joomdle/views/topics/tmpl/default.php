<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php 
$index_url = JURI::base()."index.php"; 
$itemid = JoomdleHelperContent::getMenuItem();
?>
<h2 id="joomdlesectionheader" class="joomdlecourseheader"><?php echo $this->course_info['fullname']; ?></h2>
<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr>
        <td width="30" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <?php echo JText::_('CJ TOPICS'); ?>
        </td>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
                Name
        </td>
</tr>

<tr>
        <td width="60%" colspan="2">
        </td>
</tr>
<?php
$odd = 0;
if (is_array ($this->temas)) {
foreach ($this->temas as  $tema) : ?>
<?php if ($tema['summary']) : ?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
	<td align="right">
                <?php 
		if ($tema['section'])
			echo $tema['section'] ; 
		else
			echo  JText::_('CJ INTRO');
		?>
        </td>
        <td height="20">
                <?php echo nl2br($tema['summary']); ?>
        </td>
</tr>
<?php endif; ?>
<?php endforeach;
}
$cat_id = $this->course_info['cat_id'];
$course_id = $this->course_info['remoteid'];
$cat_slug = JFilterOutput::stringURLSafe ($this->course_info['cat_name']);
$course_slug = JFilterOutput::stringURLSafe ($this->course_info['fullname']);
?>

<?php
/*
<tr class="sectiontableentry">
        <td align="left" colspan='3'>
		<?php $url = JRoute::_("index.php?option=com_joomdle&cat_id=$cat_id:$cat_slug&course_id=$course_id-$course_slug&Itemid=$itemid"); ?>
		<P><b><?php  echo "<a href=\"$url\">".JText::_('CJ BACK TO COURSE DETAILS')."</a><br>"; ?></b>
        </td>
</tr>
*/
?>

</table>

