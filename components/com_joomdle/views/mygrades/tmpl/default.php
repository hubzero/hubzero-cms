<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
require_once(JPATH_SITE.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'content.php');


$user = & JFactory::getUser();
$id = $user->get('id');
$username = $user->get('username');

/* Obtenemos los cursos del alumno */
$cursos = JoomdleHelperContent::call_method ("my_courses", $username);


?>
<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<?php
/* Para cada curso, mostramos sus tareas */
foreach ($cursos as  $curso) :
$id_curso = $curso['id'];

/* Obtenemos el titulo del curso */
$course_info = JoomdleHelperContent::call_method ("get_course_info", $id_curso);

?>
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
		<?php echo JText::_('CJ COURSE TASKS'); ?>:
                <?php echo $course_info['fullname']; ?>
        </td>
        <td width="30" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <?php echo JText::_('CJ GRADE'); ?>
        </td>

<?php

$tareas = JoomdleHelperContent::call_method ("get_user_grades", $username, $id_curso);

$odd = 0;
if (!$tareas)
	echo "<tr><td><br></td></tr>";
foreach ($tareas as  $tarea) :
if ($tarea['itemname']) : 
?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
        <td height="20">
                <?php  echo $tarea['itemname']; ?>
        </td>
        <td height="20">
                <?php //printf ("%.2f", $tarea['finalgrade']); 
		echo $tarea['finalgrade'];
		?>

        </td>
</tr>
<?php endif; ?>
<?php endforeach; ?>
<?php endforeach; ?>

</table>

