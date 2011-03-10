<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
$itemid = JoomdleHelperContent::getMenuItem();
?>
<h2 id="joomdlesectionheader"><?php echo JText::_('CJ COURSES'); ?></h2>
<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr>
        <td>
           <em>This page shows all the current courses/content available through NEEShub assesment</em>     
        </td>
</tr>
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
                
        </td>
</tr>

<?php
$odd= 0;
if (is_array ($this->cursos))
{

//print_r ($this->cursos);
foreach ($this->cursos as  $curso) : ?>
<?php
$cat_id = $curso['cat_id'];
$course_id = $curso['remoteid'];
$cat_slug = JFilterOutput::stringURLSafe ($curso['cat_name']);
$course_slug = JFilterOutput::stringURLSafe ($curso['fullname']);
?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
        <td height="20">
		<?php $url = JRoute::_("index.php?option=com_joomdle&view=detail&cat_id=$cat_id&course_id=$course_id&Itemid=$itemid"); ?>

		<?php  echo "<a href=\"$url\">".$curso['fullname']."</a><br>"; ?>
                <br /><span class="description"><?php echo nl2br($curso['summary']); ?></span>
        </td>
</tr>
<tr><td></td>
</tr>
<?php endforeach; 
} ?>

</table>
