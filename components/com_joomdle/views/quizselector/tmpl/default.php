<?php defined('_JEXEC') or die('Restricted access'); ?>
<script type="text/javascript">
/* Initialize important CKEditor variables from opening editor. */
var CKEDITOR   = window.parent.CKEDITOR;
var oEditor   = CKEDITOR.instances.editorName;


var okListener = function(ev) {
   //alert("OK!");
    var text = '<p id="primary-document"><a class="joomdlequizinsert" href="/index.php?option=com_joomdle&view=quiz&quiz_id='+document.getElementById('quiz').value+'&tmpl=component">View Quiz</a></p>';
  this._.editor.insertHtml(text);
   
    // remove the listeners to avoid any JS exceptions
   CKEDITOR.dialog.getCurrent().removeListener("ok", okListener);
   CKEDITOR.dialog.getCurrent().removeListener("cancel", cancelListener);
   
};

var cancelListener = function(ev) {
   //alert("CANCEL!");
       
   
    // remove the listeners to avoid any JS exceptions
   CKEDITOR.dialog.getCurrent().removeListener("ok", okListener);
   CKEDITOR.dialog.getCurrent().removeListener("cancel", cancelListener);
};

CKEDITOR.event.implementOn(CKEDITOR.dialog.getCurrent());
CKEDITOR.dialog.getCurrent().on("ok", okListener);
CKEDITOR.dialog.getCurrent().on("cancel", cancelListener);
</script>


<?php 
?>
<?php
$assignment = $this->quiz_info;
$itemid = JoomdleHelperContent::getMenuItem();

$user = &JFactory::getUser();
$user_logged = $user->id;

?>
<FORM NAME="qform">
<h3 id="" class=""> Courses </h3>
<select name="course">
<?php 
//&tmpl=component
$courses = $this->course_info; //list of courses

foreach ($courses as $course) {
	
	echo '<option value='.$course['remoteid'].'>'.$course['fullname'].'</option>';
}


?>
</select>



<h3 id="" class=""> Quizes </h3>
<select name="quiz" id = "quiz">
<?php 
//&tmpl=component


$quizes = $this->quiz_info; //list of quizes

foreach ($quizes as $quiz) {
	
	echo '<option value='.$quiz['remoteid'].'>'.$quiz['name'].'</option>';
	echo ($quiz['remoteid']);
}


?>
</select>
</FORM>
