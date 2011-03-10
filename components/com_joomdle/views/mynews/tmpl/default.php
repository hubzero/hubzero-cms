<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php $category = (stripos($_SERVER['REQUEST_URI'],'/metrics') !== false)? "Metrics" : "Course" ?>
<?php 
		$titleString = ($category == "Metrics")? "My Assesment News" : "My Assesment News";
		$newsString = ($category == "Metrics")? "Section" : "Course";
?>
<?php

/* Para cada curso, mostramos todos las noticias */
if (is_array ($this->my_news)  && !empty($this->my_news)) { ?>

<h2 id="joomdlesectionheader" class="joomdlenewsheader"><?php echo $titleString;?></h2>
<link type="text/css" href="/media/system/css/overcast/jquery-ui-1.8.5.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="/media/system/js/jquery-ui-1.8.5.custom.min.js"></script>
<?php 
foreach ($this->my_news as $id => $curso) :
?>

<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
		<em><?php echo $curso['info']['cat_name']?></em>: <?php echo $curso['info']['fullname']; ?>
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
$itemID = JoomdleHelperContent::getMenuItem();
$odd = 0;
if (is_array ($curso['news']) && !empty($curso['news'])) {
foreach ($curso['news'] as  $news_item) : ?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
        <td align="left">
                <?php 
			$link = $this->jump_url."&mtype=news&id=".$news_item['discussion']."&Itemid=$itemID";
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
<?php endforeach; } else {?>
<tr><td>No News</td></tr>


<?php } endforeach; } else {?>

<h2 id="joomdlesectionheader" class="joomdlenewsheader">NEEShub Assesment</h2>
<p><em>You have not joined a course or assesment module yet.</em> If you had joined a course, you would see relevant course news on this page.</p>


<?php }?>
</table>