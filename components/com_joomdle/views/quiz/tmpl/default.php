<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php $category = (stripos($_SERVER['REQUEST_URI'],'/metrics') !== false)? "Metrics" : "Course" ?>
<?php 
?>
<?php
$assignment = $this->quiz_info;
$itemid = JoomdleHelperContent::getMenuItem();

$user = &JFactory::getUser();
$user_logged = $user->id;

?>

<?php 
//&tmpl=component
?>
<?php 
//function google_qr($url,$size ='150',$EC_level='L',$margin='0' ){

//$url = urlencode($url); 
//echo '<img src="http://chart.apis.google.com/chart?chs='.$size.'x'.$size.'&cht=qr&chld='.$EC_level.'|'.$margin.'&chl='.$url.'" alt="QR code" width="'.$size.'" height="'.$size.'"/>';
//}
?>
<!-- <h2 id="joomdlesectionheader" class="joomdlecourseheader"> <?php echo $assignment['fullname']; ?></h2> -->
<?php //google_qr('www.google.com',70);?>
<iframe width="100%" height="800" src="http://neesreu.org/moodle/mod/quiz/view.php?q=<?php echo $assignment['remoteid']; ?>">

</iframe>
