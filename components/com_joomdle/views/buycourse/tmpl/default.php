<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
$course_info = $this->course_info;
$itemid = JoomdleHelperContent::getMenuItem();

$user = &JFactory::getUser();
$user_logged = $user->id;

$cat_id = $course_info['cat_id'];
$course_id = $course_info['remoteid'];
$cat_slug = JFilterOutput::stringURLSafe ($course_info['cat_name']);
$course_slug = JFilterOutput::stringURLSafe ($course_info['fullname']);

?>
<div align="center">

<?php
echo "<P>";
echo JText::_('CJ YOU ARE BEING TRANSFERED TO PAYPAL');
echo "</P>";
//print_r ($course_info);
    $paypal_config = JoomdleHelperContent::call_method('get_paypal_config');
    $paypalurl = $paypal_config['paypalurl'];
    $paypalbusiness = $paypal_config['paypalbusiness'];
    $coursefullname = $course_info['fullname'];
    $courseshortname = $course_info['shortname'];
    $userfullname = $user->name;
    $user_id = JoomdleHelperContent::call_method('user_id', $user->username);
    $cost = $course_info['cost'];
    $userfirstname = 'as';
    $userlastname = 'sa';
    $useraddress = 'as';
    $usercity = 'sa';
    $useremail = $user->email;
    $usercountry = 'US';

    $joomla_root = JURI::base();
?>
<form action="<?php echo $paypalurl ?>" method="post" id="foo">

<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="charset" value="utf-8" />
<input type="hidden" name="business" value="<?php echo $paypalbusiness; ?>" />
<input type="hidden" name="item_name" value="<?php echo $coursefullname; ?>" />
<input type="hidden" name="item_number" value="<?php echo $courseshortname; ?>" />
<input type="hidden" name="quantity" value="1" />
<input type="hidden" name="on0" value="User" />
<input type="hidden" name="os0" value="<?php echo $userfullname; ?>" />
<input type="hidden" name="custom" value="<?php echo $user_id.'-'.$course_id; ?>" />

<input type="hidden" name="currency_code" value="USD" />
<input type="hidden" name="amount" value="<?php echo $cost; ?>" />

<input type="hidden" name="for_auction" value="false" />
<input type="hidden" name="no_note" value="1" />
<input type="hidden" name="no_shipping" value="1" />
<input type="hidden" name="notify_url" value="<?php echo $this->moodle_url.'/enrol/paypal/ipn.php'?>" />
<input type="hidden" name="return" value="<?php echo $this->moodle_url.'/enrol/paypal/return.php?id='.$course_id ?>" />
<input type="hidden" name="cancel_return" value="<?php echo $joomla_root; ?>" />
<input type="hidden" name="rm" value="2" />
<input type="hidden" name="cbt" value="Click here to enter your course" />

<input type="hidden" name="first_name" value="<?php echo ($userfirstname); ?>" />
<input type="hidden" name="last_name" value="<?php echo ($userlastname); ?>" />
<input type="hidden" name="address" value="<?php echo ($useraddress); ?>" />
<input type="hidden" name="city" value="<?php echo ($usercity); ?>" />
<input type="hidden" name="email" value="<?php echo ($useremail) ?>" />
<input type="hidden" name="country" value="<?php echo ($usercountry) ?>" />

<!-- <input type="submit" value="Send payment" /> -->

</form>

</div>

<script type="text/javascript">
function myfunc () {
var frm = document.getElementById("foo");
frm.submit();
}
window.onload = myfunc;
</script>

