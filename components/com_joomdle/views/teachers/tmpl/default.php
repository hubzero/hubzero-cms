<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
$odd=0;
$itemid = JoomdleHelperContent::getMenuItem();

?>
<h2 id="joomdlesectionheader" class="joomdleteacherheader">Course Teachers</h2>
<p><?php echo $this->course_info['fullname']; ?></p>
<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
        Teachers        
        </td>
</tr>

<?php
if (is_array ($this->teachers))
foreach ($this->teachers as  $teacher) : ?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
	<td align="left">
                <?php echo $teacher['firstname']." ".$teacher['lastname']; ?>
    </td>
</tr>
<?php endforeach; ?>

<?php
/* XXX quitada el BACK
<tr class="sectiontableentry">
        <td align="left" colspan='3'>
            <?php $url = JRoute::_("index.php?option=com_joomdle&view=detail&cat_id=".$this->course_info['cat_id'].":".$this->course_info['cat_name']."&course_id=".$this->course_info['remoteid'].":".$this->course_info['fullname']."&Itemid=$itemid"); ?>
                            <P><b><?php  echo "<a href=\"$url\">".JText::_('CJ BACK TO COURSE DETAILS')."</a><br>"; ?></b>
                <P><b><?php  //echo "<a href=\"".$index_url."?option=com_joomdle&view=detail&course_id=".$this->course_info['remoteid']."&Itemid=$itemid\">".JText::_('CJ BACK TO COURSE DETAILS')."</a><br>"; ?></b>
        </td>
</tr>
*/
?>

</table>

