<?php defined('_JEXEC') or die('Restricted access'); ?>
<h2 id="joomdlesectionheader" class="joomdlenewsheader">Latest News</h2>
<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
                <?php echo JText::_('CJ COURSE NEWS').': '; ?>
                <?php echo $this->course_info['fullname']; ?>
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
$odd=0;
if (is_array ($this->news)) {
foreach ($this->news as  $news_item) : ?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
	<td align="left">
		<?php
		//	$link = $this->jump_url."/mod/forum/discuss.php?d=".$news_item['discussion'];
			$link = $this->jump_url."&mtype=news&id=".$news_item['discussion'];
			$linkstarget = $this->params->get( 'linkstarget' );
			if ($linkstarget == "new")
				 $target = " target='_blank'";
			 else $target = "";
                echo "<a $target href=\"$link\">".$news_item['subject']."</a>";

		?>
        </td>
        <td height="20">
                <?php echo date('d-m-Y', $news_item['timemodified']); ?>
        </td>
</tr>
<?php endforeach; 
} ?>

</table>

