<div class="profilepic" id="profilepic">
        <?PHP

echo '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$USER->id.'&amp;course='.$COURSE->id.'"><img src="'.$CFG->wwwroot.'/user/pix.php?file=/'.$USER->id.'/f1.jpg" width="80px" height="80px" title="'.$USER->firstname.' '.$USER->lastname.'" alt="'.$USER->firstname.' '.$USER->lastname.'" /></a>'; 

?>
      </div>

<div class="profilename" id="profilename">
    <?PHP
	
	    function get_content () {
        global $USER, $CFG, $SESSION, $COURSE;
        $wwwroot = '';
        $signup = '';}

        if (empty($CFG->loginhttps)) {
            $wwwroot = $CFG->wwwroot;
        } else {
            $wwwroot = str_replace("http://", "https://", $CFG->wwwroot);
        }
        
	
if (!isloggedin() or isguestuser()) {
echo '<a href="'.$CFG->wwwroot.'/login/index.php">'.get_string('loggedinnot').'</a>';

} else {
echo '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$USER->id.'&amp;course='.$COURSE->id.'">'.$USER->firstname.' '.$USER->lastname.'</a>';
}		


?>
    </div>
    
    
 
      
      <div class="profileoptions" id="profileoptions">
    
    
    

 <?PHP
				
if (!isloggedin() or isguestuser()) {
echo '<ul>';
echo '<li><a href="'.$CFG->wwwroot.'/login/index.php">'.get_string('login').'</a></li>';
echo '</ul>';



} else {
echo '<ul>';
echo '<li><a href="'.$CFG->wwwroot.'/user/edit.php?id='.$USER->id.'&amp;course='.$COURSE->id.'">'.get_string('updatemyprofile').'</a></li>';
echo '<li><a href="'.$CFG->wwwroot.'/my">'.get_string('mycourses').'</a></li>';
echo '<li><a href="'.$CFG->wwwroot.'/login/logout.php?sesskey='.sesskey().'">'.get_string('logout').'</a></li>';
echo '</ul>';

}
?>


    
    </div>

