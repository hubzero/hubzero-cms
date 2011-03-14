<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php $category = "course" ?>
<?php 
		$titleString =  "Courses in ";
		$availableString =  "Available Courses";
?>
<?php
$index_url = JURI::base()."index.php";
$itemid = JoomdleHelperContent::getMenuItem();

$odd= 0;
if (is_array($this->categories)) :
?>
<h2 id="joomdlesectionheader"><?php echo $titleString.$this->cat_name;
		?> </h2>
<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
        <p>This page lists the <?php echo $titleString.$this->cat_name;?>.<em>You can click on each </em><strong><?php echo $category;?> title</strong><em> to view more information about that item </em></p>
        </td>
</tr>
<tr>
<td>This page lists the <?php echo $titleString.$this->cat_name;?>.<em>You can click on each </em><strong><?php echo $category;?> title</strong><em> to view more information about that item </em></td>
</tr>

<tr>
        <td width="60%" colspan="2">
        </td>
</tr>
<?php
$odd= 0;
$itemid = JoomdleHelperContent::getMenuItem();
foreach ($this->categories as  $cat) : ?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
        <td height="20">
        		<?php $urlString = "index.php?option=com_joomdle&view=coursecategory&cat_id=".$cat['id']."&Itemid=".$itemid;?>
                <?php $url = JRoute::_($urlString); ?>
                <?php  echo "<a href=\"$urlString\">".$cat['name']."</a><br>"; ?>
                <br /><span class="joomdledescription"><?php echo nl2br($cat['description']); ?></span>
        </td>
</tr>
<?php endforeach; ?>

</table>

<?php endif; // sub cats ?>

<?php if ((!is_array($this->categories)) || (is_array ($this->cursos))) : ?>
<h2 id="joomdlesectionheader"> <?php echo $titleString.$this->cat_name;
		?> </h2>
<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
        </td>
</tr>

<?php
if (is_array ($this->cursos))

echo '<div id="joomdlecatdescription">'.$this->cursos[0]['cat_description'].'</div>'."\n";

foreach ($this->cursos as  $curso) : ?>
<tr class="joomdlecoursedec sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
        <td height="20">
        <?php 
        $urlSafeString = "/index.php?option=com_joomdle&view=detail&cat_id=".$curso['cat_id']."&course_id=".$curso['remoteid']."&Itemid=".$itemid;
		$url = JRoute::_($urlSafeString); 
		
		$wrapperSafeString = "/index.php?option=com_joomdle&task=course&course_id=".$curso['remoteid']."&Itemid=".$itemid;
		$wrapper = JRoute::_($wrapperSafeString); 
		?>
		<div class="joomdlecourseheader">
		<p>
		<h4><?php echo $curso['fullname'] ?></h4>
		</p>
		
		<?php  echo "<button class=\"joomdlecourselinktitile\" onClick=\"window.location ='".$url."';\">".'Enrollment Info'."</button>"; ?>
		<?php  echo "<button class=\"joomdlecourselinktitile\" onClick=\"window.location ='".$wrapper."';\">".'Course Portal'."</button><br>"; ?>
        <span class="joomdledescription"><strong>Description:</strong><?php echo nl2br($curso['summary']); ?></span>
        </div>
        <br/>
        </td>
</tr>
<?php endforeach; ?>

<?php endif; // there are courses ?>
<?php
/*
<tr class="sectiontableentry">
        <td align="left">
		<?php $url = JRoute::_("index.php?option=com_joomdle&Itemid=$itemid"); ?>
		<P><b><?php  echo "<a href=\"$url\">".JText::_('CJ CATEGORY LISTING')."</a><br>"; ?></b>
        </td>
</tr>
*/
?>


</table>
