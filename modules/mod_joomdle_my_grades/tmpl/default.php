<div>
<table width="100%">
<?php
foreach ($tareas as  $tarea) :
if ($tarea['itemname']) : ?>
<tr>
<td>
<?
	echo $tarea['itemname'];
?>
</td>
<td>
<?
	echo $tarea['finalgrade'];
?>
</td>
<? if ($show_averages) : ?>
<td>
(<?
	echo $tarea['average'];
?>)
</td>
<? endif; ?>
</tr>
<?php endif; ?>
<?php endforeach; ?>
</table>
</div>
