<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
$index_url = JURI::base()."index.php";
$itemid = JoomdleHelperContent::getMenuItem();

?>
<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">

<tr>
        <td width="60%" colspan="2">
        </td>
</tr>
<?php
if (is_array($this->categories))
foreach ($this->categories as  $cat) : 
$odd= 0;

$cursos = JoomdleHelperContent::getCourseCategory ($cat['id']);
$cat_id = $cat['id'];

if (!is_array ($cursos))
	continue;
?>
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
                <?php echo $cat['name']; ?>
        </td>
</tr>
<?php
foreach ($cursos as  $curso) : ?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
        <td height="20">
		<?php $url = JRoute::_("index.php?option=com_joomdle&&view=detail&cat_id=$cat_id:".JFilterOutput::stringURLSafe($cat['name'])."&course_id=".$curso['remoteid'].":".JFilterOutput::stringURLSafe($curso['fullname'])."&Itemid=$itemid"); ?>
		<?php  echo "<a href=\"$url\">".$curso['fullname']."</a><br>"; ?>
                <br /><span class="description"><?php echo nl2br($curso['summary']); ?></span>
        </td>
</tr>
<?php endforeach; //courses ?>
<?php
$parent_name = $cat['name'];
$categories = JoomdleHelperContent::getCourseCategories ($cat_id);
$odd= 0;
if (is_array($categories)) :
foreach ($categories as  $cat) : 
$odd= 0;

$cursos = JoomdleHelperContent::getCourseCategory ($cat['id']);
$cat_id = $cat['id'];

if (!is_array ($cursos))
	continue;
?>
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
                <?php echo $parent_name.'>'.$cat['name']; ?>
        </td>
</tr>
<?php
foreach ($cursos as  $curso) : ?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
        <td height="20">
		<?php $url = JRoute::_("index.php?option=com_joomdle&&view=detail&cat_id=$cat_id"."&course_id=".$curso['remoteid']."&Itemid=$itemid"); ?>
		<?php  echo "<a href=\"$url\">".$curso['fullname']."</a><br>"; ?>
                <br /><span class="description"><?php echo nl2br($curso['summary']); ?></span>
        </td>
</tr>

<?php endforeach; //cursos ?>
<?php endforeach; //cats ?>

<?php endif; ?>
<?php endforeach; //cats ?>

</table>
