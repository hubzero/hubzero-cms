<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php $category = (stripos($_SERVER['REQUEST_URI'],'/metrics') !== false)? "Metrics" : "Course" ?>
<?php 
		$teachersString = ($category == "Metrics")? "Section Managers" : "Course Teachers";
		$topicsString = ($category == "Metrics")? "Section Information" : "Course Topics";
		$joinString = ($category == "Metrics")? "Enroll in Metric Reporting Section" : "Enroll into Course" 


?>
<?php
$course_info = $this->course_info;
$itemid = JoomdleHelperContent::getMenuItem();

$show_topics_link = $this->params->get( 'show_topìcs_link' );
$show_grading_system_link = $this->params->get( 'show_grading_system_link' );
$show_teachers_link = $this->params->get( 'show_teachers_link' );
$show_enrol_link = $this->params->get( 'show_enrol_link' );
$show_paypal_button = $this->params->get( 'show_paypal_button' );

$user = &JFactory::getUser();
$user_logged = $user->id;

if (!array_key_exists ('cost',$course_info))
	$course_info['cost'] = 0;
?>
<h2 id="joomdlesectionheader" class="joomdlecourseheader"> <?php echo $course_info['fullname']; ?></h2>
<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr><td><em>Course Details</em></td></tr>
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">       
        </td>
</tr>

<tr>
        <td width="60%" colspan="2">
        </td>
</tr>
<tr class="sectiontableentry">
	<td align="left">
		<b><?php echo JText::_('CJ CATEGORY'); ?>:&nbsp;</b><?php echo $course_info['cat_name']; ?>
        </td>
</tr>
<tr class="sectiontableentry">
	<td align="left">
		<b><?php echo JText::_('CJ SUMMARY'); ?>:&nbsp;</b><?php echo nl2br($course_info['summary']); ?>
        </td>
</tr>
<?php if ($course_info['lang']) : ?>
<tr class="sectiontableentry">
	<td align="left">
		<b><?php echo JText::_('CJ LANGUAGE'); ?>:&nbsp;</b><?php echo JoomdleHelperContent::get_language_str ($course_info['lang']); ?>
        </td>
</tr>
<?php endif; ?>
<tr class="sectiontableentry">
	<td align="left">
		<b><?php echo JText::_('CJ START DATE'); ?>:&nbsp;</b><?php echo date('d-m-Y',$course_info['startdate']); ?>
        </td>
</tr>
<?php if (array_key_exists ('enrolstartdate',$course_info)) : ?>
<tr class="sectiontableentry">
	<td align="left">
		<b><?php echo JText::_('CJ ENROLMENT START DATE'); ?>:&nbsp;</b><?php echo date('d-m-Y',$course_info['enrolstartdate']); ?>
        </td>
</tr>
<?php endif; ?>
<?php if (array_key_exists ('enrolenddate',$course_info)) : ?>
<tr class="sectiontableentry">
	<td align="left">
		<b><?php echo JText::_('CJ ENROLMENT END DATE'); ?>:&nbsp;</b><?php echo date('d-m-Y',$course_info['enrolenddate']); ?>
        </td>
</tr>
<?php endif; ?>
<?php if (array_key_exists ('enrolperiod',$course_info)) : ?>
<tr class="sectiontableentry">
	<td align="left">
		<b><?php echo JText::_('CJ ENROLMENT DURATION'); ?>:&nbsp;</b><?php
		if ($course_info['enrolperiod'] == 0)
			echo JText::_('CJ UNLIMITED');
		else
			echo ($course_info['enrolperiod'] / 86400)." ".JText::_('CJ DAYS');
		?>
        </td>
</tr>
<?php endif; ?>
<?php if ($course_info['cost']) : ?>
<tr class="sectiontableentry">
	<td align="left">
		<b><?php echo JText::_('CJ COST'); ?>:&nbsp;</b>$<?php echo $course_info['cost']." (".$course_info['currency'].")"; ?>
        </td>
</tr>
<?php endif; ?>
<?php $index_url = JURI::base()."index.php"; ?>
<tr class="sectiontableentry">
	<td align="left">
		<b><?php echo JText::_('CJ TOPICS'); ?>:&nbsp;</b><?php echo $course_info['numsections']; ?>
        </td>
</tr>

<tr class="sectiontableentry">
	<td align="left">
	<?php
		$cat_id = $course_info['cat_id'];
		$course_id = $course_info['remoteid'];
		$cat_slug = JFilterOutput::stringURLSafe ($course_info['cat_name']);
		$course_slug = JFilterOutput::stringURLSafe ($course_info['fullname']);

	if ($show_topics_link) : ?>
		<?php $url = JRoute::_("/index.php?option=com_joomdle&view=topics&cat_id=$cat_id&course_id=$course_id&Itemid=$itemid"); ?>
		<P><b><?php  echo "<a href=\"$url\">".$topicsString."</a><br>"; ?></b>
	<?php endif; ?>
	<?php
	if ($show_grading_system_link) : ?>
		<?php $url = JRoute::_("/index.php?option=com_joomdle&view=coursegradecategories&$cat_id=$cat_id&course_id=$course_id&Itemid=$itemid"); ?>
		<P><b><?php  echo "<a href=\"$url\">".JText::_('CJ COURSE GRADING SYSTEM')."</a><br>"; ?></b>
	<?php endif; ?>
	<?php
	if ($show_teachers_link) : ?>
		<?php $url = JRoute::_("/index.php?option=com_joomdle&view=teachers&cat_id=$cat_id&course_id=$course_id&Itemid=$itemid"); ?>
		<P><b><?php  echo "<a href=\"$url\">".$teachersString."</a><br>"; ?></b>
	<?php endif; ?>
	<?php 
	if (($show_enrol_link) && (!$course_info['cost'])): ?>
		<?php //$url = JRoute::_("index.php?option=com_joomdle&view=joomdle&task=enrol&course_id=$course_id"); ?>
		<?php $url = ("/index.php?option=com_joomdle&task=enrol&course_id=$course_id&Itemid=$itemid"); ?>
		<FORM>
		<INPUT TYPE="BUTTON" VALUE="<?php echo $joinString ?>" ONCLICK="window.location.href='<?php echo $url; ?>'">
		</FORM>
	<?php elseif  (JoomdleHelperShop::is_course_on_sell ($course_info['remoteid'])) : ?>
		<?php
			$url = JRoute::_(JoomdleHelperShop::get_sell_url ($course_info['remoteid']));
		?>
		<FORM>
		<INPUT TYPE="BUTTON" VALUE="<?php echo "Buy into ".$category ?>" ONCLICK="window.location.href='<?php echo $url; ?>';">
		</FORM>
	<?php endif; ?>
	<?php 
	if (($show_paypal_button) && ($course_info['cost'])): ?>
		<?php //$url = JRoute::_("index.php?option=com_joomdle&view=joomdle&task=enrol&course_id=$course_id"); ?>
		<?php $url = "/index.php?option=com_joomdle&view=buycourse&course_id=$course_id&Itemid=$itemid";
		?>
		<br><a class="joomdlepaypal" href="<?php echo  $url; ?>"><img src="https://www.paypal.com/en_US/i/logo/PayPal_mark_60x38.gif"></a>
		<span>Click to pay enrollment fee via Paypal</span>
		<p><strong>FAQ about Buying:</strong></p>
		<a id="faq1" href="#q1">What am I Buying?</a>
		<div style="display:none;"><div id="q1"><p><strong>What am I Buying?</strong></p><p>The enrollment fee will be assesed as a opportunity to enrol and does not guarantee a passing grade. Passing of the course is up to the discresion of the course teachers and administrators per the course guidelines.</p></div></div>
		<script type="text/javascript">
			$jQ("#faq1").fancybox({
				'hideOnContentClick': true,
				'width': 300,
				'height' : 150,
				'autoDimensions' : false
			});
		</script>
	<?php endif; ?>
        </td>
</tr>
<?php /*
<tr class="sectiontableentry">
	<td align="left">
		<?php $url = JRoute::_("index.php?option=com_joomdle&cat_id=$cat_id:$cat_slug&Itemid=$itemid"); ?>
		<P><b><?php  echo "<a href=\"$url\">".JText::_('CJ CATEGORY COURSES')."</a><br>"; ?></b>
        </td>
</tr>
*/
?>

</table>

