<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php

/* Para cada curso, mostramos todos los eventos */
if (is_array($this->my_events))
foreach ($this->my_events as $id => $curso) :

?>

<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
                <?php echo JText::_('CJ UPCOMING COURSE EVENTS'); ?>: <?php echo $curso['info']['fullname']; ?>
        </td>
        <td width="30" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <?php echo JText::_('CJ DATE'); ?>
        </td>
</tr>

<tr>
        <td width="60%" colspan="2">
        </td>
</tr>
<?php
if (is_array($curso['events']))
$odd = 0;
foreach ($curso['events'] as  $event) : ?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
	<td align="left">
                <?php echo $event['name']; ?>

        </td>
        <td height="20">
		<?php
			$day = date('d', $event['timestart']);
			$mon = date('m', $event['timestart']);
			$year = date('Y', $event['timestart']);
			
                        $link = $this->jump_url."&mtype=event&id=".$event['courseid']."&day=$day&mon=$mon&year=$year";

			$linkstarget = $this->params->get( 'linkstarget' );
                        if ($linkstarget == "new")
                                 $target = " target='_blank'";
                         else $target = "";

			echo "<a $target href=\"$link\">".date('d-m-Y', $event['timestart'])."</a>";

		?>
        </td>
</tr>
<?php endforeach;  //events ?>

</table>
<?php endforeach;  //courses ?>
