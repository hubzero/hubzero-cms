<?php defined('_JEXEC') or die('Restricted access'); ?>
<form action="<?php echo JRoute::_( 'index.php?option=com_joomdle&task=assigncourses' ); ?>" method="post">
<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr>
        <td width="60%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
		<?php echo JText::_('CJ MY COURSES'); ?>
        </td>
        <td width="10%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
		<?php echo JText::_('CJ AVAILABLE'); ?>
        </td>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
		<?php echo JText::_('CJ CHILDREN'); ?>
		<a href="<?php echo JRoute::_( 'index.php?option=com_joomdle&view=register' ); ?>">(<?php echo JText::_('CJ ADD NEW'); ?>)</a>
        </td>
</tr>

<?php
$odd = 0;
if ((is_array ($this->my_courses)) && (count ($this->my_courses)) ) :
foreach ($this->my_courses as $id => $curso) :
?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
        <td align="left">
                <?php 
			echo $curso['name'];
		?>

        </td>
	<td>
                <?php 
			echo $curso['num'];
		?>
	</td>
	<td>
	<?php
		$options = JoomdleHelperParents::childrenCheckboxes ($curso['id']);
	//	echo JHTML::_('select.genericlist', $options, 'children'.'['.$i.']', 'multiple=multiple', 'value', 'text', $value, $control_name.$name );
	?>
	</td>
</tr>
<?php endforeach; ?>
<tr>
<td>
<input type="submit" id="assign_courses" name="assign_courses" value="<?php echo JText::_('CJ ASSIGN COURSES'); ?>" />
</td>
</tr>
<?php else : ?>
<tr>
<td>
<?php echo JText::_('CJ NO COURSES PURCHASED'); ?>
</td>
</tr>
<?php endif; ?>

</table>
</form>
