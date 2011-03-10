<?PHP  //

/// Include main functions needed for action handling
require_once("action_lib.php");

/// Library of functions and constants for module feedback

//define ('feedback_ANONYMOUS_USER', 0); //wird nicht mehr verwendet
define ('feedback_ANONYMOUS_YES', 1);
define ('feedback_ANONYMOUS_NO', 2);

$feedback_item_dir = $CFG->dirroot . '/mod/feedback/item';
$feedback_names = load_feedback_items($feedback_item_dir);

//max. Breite des grafischen Balkens in der Auswertung
define ('feedback_MAX_PIX_LENGTH', '400');

function feedback_add_instance($feedback) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will create a new instance and return the id number 
/// of the new instance.

    $feedback->timemodified = time();
    $feedback->id = '';

    # May have to add extra stuff in here #
    
    return insert_record("feedback", $feedback);
}


function feedback_update_instance($feedback) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will update an existing instance with new data.

    $feedback->timemodified = time();
    $feedback->id = $feedback->instance;

    # May have to add extra stuff in here #

    return update_record("feedback", $feedback);
}


function feedback_delete_instance($id) {
/// Given an ID of an instance of this module, 
/// this function will permanently delete the instance 
/// and any data that depends on it.  

   $feedbackitems = get_records('feedback_item', 'feedback', $id);
   //loeschen der einzelnen feedback-items einschliesslich der Values
   if (is_array($feedbackitems)){
      foreach($feedbackitems as $feedbackitem){
         @delete_records("feedback_value", "item", $feedbackitem->id);
      }
      @delete_records("feedback_item", "feedback", $id);
   }
   //loeschen der tracking-daten
   @delete_records('feedback_tracking', 'feedback', $id);
   @delete_records("feedback_completed", "feedback", $id);
   return @delete_records("feedback", "id", $id);
}

function feedback_delete_course($course) {
   //delete all templates of given course
   return delete_records('feedback_template', 'course', $course->id);
}

function feedback_user_outline($course, $user, $mod, $feedback) {
/// Return a small object with summary information about what a 
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description

    return $return;
}

function feedback_user_complete($course, $user, $mod, $feedback) {
/// Print a detailed representation of what a  user has done with 
/// a given particular instance of this module, for user activity reports.

    return true;
}

function feedback_print_recent_activity($course, $isteacher, $timestart) {
/// Given a course and a time, this module should find recent activity 
/// that has occurred in feedback activities and print it out. 
/// Return true if there was output, or false is there was none.

    global $CFG;

    return false;  //  True if anything was printed, otherwise false 
}

function feedback_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such 
/// as sending out mail, toggling flags etc ... 

    return true;
}

function feedback_grades($feedbackid) {
/// Must return an array of grades for a given instance of this module, 
/// indexed by user.  It also returns a maximum allowed grade.
///
///    $return->grades = array of grades;
///    $return->maxgrade = maximum allowed grade;
///
///    return $return;

   return NULL;
}

function feedback_get_participants($feedbackid) {
//Must return an array of user records (all data) who are participants
//for a given instance of feedback. Must include every user involved
//in the instance, independient of his role (student, teacher, admin...)
//See other modules as example.

    return false;
}

function feedback_scale_used ($feedbackid,$scaleid) {
//This function returns if a scale is being used by one feedback
//it it has support for grading and scales. Commented code should be
//modified if necessary. See forum, glossary or journal modules
//as reference.
   
    $return = false;

    //$rec = get_record("feedback","id","$feedbackid","scale","-$scaleid");
    //
    //if (!empty($rec)  && !empty($scaleid)) {
    //    $return = true;
    //}
   
    return $return;
}

//////////////////////////////////////////////////////////////////////////////////////
/// Any other feedback functions go here.  Each of them must have a name that 
/// starts with feedback_

////////////////////////////////////////////////
//Funktionen zum Handling des Moduls
////////////////////////////////////////////////
///Items holen
function load_feedback_items($dir) {
   global $CFG;
   
   $names = array();
   $handle=opendir($dir); 
   while ($itemname = readdir ($handle)) { 
      if ($itemname != "." && $itemname != ".." && $itemname != "CVS" && is_dir($dir.'/'.$itemname)) { 
         $names[] = $itemname; 
      }
   }
   closedir($handle);
   
   foreach($names as $name) {
      require_once('item/'.$name.'/lib.php');
   }
   sort($names);
   return $names;
}


////////////////////////////////////////////////
//Handling der Templates
////////////////////////////////////////////////
////////////////////////////////////////////////

function create_feedback_template($courseid, $name, $public = 0) {
   $templ->id = '';
   $templ->course = $courseid;
   
   $templ->name = addslashes($name);
   
   $templ->public = $public;
   return insert_record('feedback_template', $templ);
}

function save_as_feedback_template($feedback, $name, $public = 0) {
   $feedbackitems = get_records('feedback_item', 'feedback', $feedback->id);
   if(!is_array($feedbackitems)){
      return false;
   }
   
   if(!$newtempl = create_feedback_template($feedback->course, $name, $public)) {
      return false;
   }
   //create items of this new template
   foreach($feedbackitems as $item) {
      $item->id = '';
      $item->feedback = 0;
      $item->template = $newtempl;
      $item->name = addslashes($item->name);
      $item->presentation = addslashes($item->presentation);
      insert_record('feedback_item', $item);
   }
   return true;
}

function delete_feedback_template($id) {
   @delete_records("feedback_item", "template", $id);
   @delete_records("feedback_template", "id", $id);
}

function items_from_feedback_template($feedback, $templateid) {
   //get all templateitems
   if(!$templitems = get_records('feedback_item', 'template', $templateid)) {
      return false;
   }
   //get all items
   if($feedbackitems = get_records('feedback_item', 'feedback', $feedback->id)){
      //delete all items of this feedback
      foreach($feedbackitems as $item) {
         delete_feedback_item($item->id, false);
      }
      //delete tracking-data
      @delete_records('feedback_tracking', 'feedback', $feedback->id);
      delete_records('feedback_completed', 'feedback', $feedback->id);
   }
   
   foreach($templitems as $newitem) {
      $newitem->id = '';
      $newitem->feedback = $feedback->id;
      $newitem->template = 0;
      $newitem->name = addslashes($newitem->name);
      $newitem->presentation = addslashes($newitem->presentation);
      insert_record('feedback_item', $newitem);
   }
}

function get_feedback_template_list($course, $onlyown = false) {
   if($onlyown) {
      $templates = get_records('feedback_template', 'course', $course->id);
   } else {
      $templates = get_records_select('feedback_template', 'course = ' . $course->id . ' OR public = 1');
   }
   return $templates;
}

////////////////////////////////////////////////
//Handling der Items
////////////////////////////////////////////////
////////////////////////////////////////////////
//erstellt ein item
function create_feedback_item($data)
{
   $item->id = '';
   $item->feedback = $data->feedbackid;
   //$item->template = $data->templateid;

   $item->template=0;
   if (isset($data->templateid)) {
	   	$item->template=$data->templateid;
   }   

   $itemname = trim($data->itemname);
   $item->name = addslashes($itemname?$data->itemname:'[none]');
   
   $get_present_func = 'get_presentation_'.$data->typ;
   $item->presentation = addslashes($get_present_func($data));
   $get_feedback_hasvalue_func = 'get_feedback_hasvalue_'.$data->typ;
   $item->hasvalue = $get_feedback_hasvalue_func();
   
   $item->typ = $data->typ;
   $item->position = $data->position;

   $item->required=0;
   if (isset($data->required)) {
	   	$item->required=$data->required;
   }   

   return insert_record('feedback_item', $item);
}

//aendert ein feedbackitem
function update_feedback_item($item, $data = null){
   if($data != null){
      $itemname = trim($data->itemname);
      $item->name = addslashes($itemname?$data->itemname:'[none]');
   
      $get_present_func = 'get_presentation_'.$data->typ;
      $item->presentation = addslashes($get_present_func($data));

	  $item->required=0;
	  if (isset($data->required)) {
	   	$item->required=$data->required;
	  } 
   }else {
      $item->name = addslashes($item->name);
      $item->presentation = addslashes($item->presentation);
   }

   return update_record("feedback_item", $item);
}

//loescht ein feedbackitem und die dazugehoerigen values
function delete_feedback_item($id, $renumber = true){
   $item = get_record('feedback_item', 'id', $id);
   @delete_records("feedback_value", "item", $id);
   delete_records("feedback_item", "id", $id);
   if($renumber) {
      renumber_feedback_items($item->feedback);
   }
}

function switch_feedback_item_required($item) {
   if($item->required == 1) {
      $item->required = 0;
   } else {
      $item->required = 1;
   }
   $item->name = addslashes($item->name);
   $item->presentation = addslashes($item->presentation);
   return update_record('feedback_item', $item);
}

function renumber_feedback_items($feedback_id){
   $items = get_records('feedback_item', 'feedback', $feedback_id, 'position');
   $pos = 1;
   if($items) {
      foreach($items as $item){
         $item->position = $pos;
         $pos++;
         update_feedback_item($item);
      }
   }
}

//item nach oben bewegen
function moveup_feedback_item($item){
   if($item->position == 1) return;
   $item_before = get_record_select('feedback_item', 'feedback = '.$item->feedback.' AND position = '.$item->position . ' - 1');
   $item_before->position = $item->position;
   $item->position--;
   update_feedback_item($item_before);
   update_feedback_item($item);
}
//item nach unten bewegen
function movedown_feedback_item($item){
   if(!$item_after = get_record_select('feedback_item', 'feedback = '.$item->feedback.' AND position = '.$item->position . ' + 1'))
   {
      return;
   }
   
   $item_after->position = $item->position;
   $item->position++;
   update_feedback_item($item_after);
   update_feedback_item($item);
}


//gibt ein feedbackitem aus
function print_feedback_item($item, $value = false, $readonly = false){
   if($readonly)$ro = 'readonly="readonly" disabled="disabled"';
      
   $callfunc = 'print_feedback_'.$item->typ;
   $callfunc($item, $value, $readonly);
}
////////////////////////////////////////////////
////////////////////////////////////////////////
////////////////////////////////////////////////
//Handling der Values
////////////////////////////////////////////////
function save_feedback_values($data, $usrid)
{
   $timemodified = time();
   if($usrid == 0) {
      return create_feedback_values($data, $usrid, $timemodified);
   }
   if(!$completed = get_record('feedback_completed', 'id', $data['completedid'])){
      return create_feedback_values($data, $usrid, $timemodified);
   }else{
      $completed->timemodified = $timemodified;
      return update_feedback_values($data, $usrid, $completed);
   }
}

function check_feedback_values($data)
{
   if(!$feedbackitems = get_records('feedback_item', 'feedback', $data['feedbackid'])) {
      return true;
   }

   foreach($feedbackitems as $item) {
      
      if($item->required != 1)continue;
      
      $check_feedback_value_func = 'check_feedback_value_'.$item->typ;
      
      $formvalname = $item->typ . '_' . $item->id;
      $value = $data[$formvalname];

      if(!$check_feedback_value_func($value)) {
         return false;
      }
      
      
   }
   return true;
   return $retval;
}

function create_feedback_values($data, $usrid, $timemodified){
      $completed = null;
      $completed->id = '';
      $completed->feedback = $data['feedbackid'];
      $completed->userid = $usrid;
      $completed->timemodified = $timemodified;
      if(!$completedid = insert_record('feedback_completed', $completed))
         return false;
      $completed = null;
      $completed = get_record('feedback_completed', 'id', $completedid);

   //assoziertes Array enthaelt schluessel in der Form abc_xxx
   //ueber explode wird daraus ein array mit (abc, xxx) mit abc=typ und xxx=itemnr
   $keys = array_keys($data);
   $errcount = 0;
   foreach($keys as $key){
   	  //logic not to mix feedback action -tags and item values
      if(eregi('([a-z0-9]{1,})_([0-9]{1,})',$key)){         
         $value = null;
         $itemnr = explode('_', $key);
         $value->id = '';
         $value->item = $itemnr[1];
         $value->completed = $completed->id;
         
         $createfunc = 'create_feedback_value_'.$itemnr[0];
         $value->value = $createfunc($data[$key]);
         if(!insert_record('feedback_value', $value)) {
            $errcount++;
         }
      }
   }
   return $errcount == 0?$completed->id:false;
}

function update_feedback_values($data, $usr, $completed){
   update_record('feedback_completed', $completed);
   //get the values of this completed
   $values = get_records('feedback_value','completed', $completed->id);
   
   //assoziertes Array enthaelt schluessel in der Form abc_xxx
   //ueber explode wird daraus ein array mit (abc, xxx)
   $keys = array_keys($data);
   foreach($keys as $key){
      //ist wert ein value?
   	  //logic not to mix feedback action -tags and item values
      if(eregi('([a-z0-9]{1,})_([0-9]{1,})',$key)){         
         //aktualisiertes value nachbauen([id], item, completed, value)
         $itemnr = explode('_', $key);
         $newvalue = null;
         $newvalue->id = '';
         $newvalue->item = $itemnr[1];
         $newvalue->completed = $completed->id;
         
         $createfunc = 'create_feedback_value_'.$itemnr[0];
         $newvalue->value = $createfunc($data[$key]);
         //testen, ob es existiert
         $exist = false;
         foreach($values as $value){
            if($value->item == $newvalue->item){
               $newvalue->id = $value->id;
               $exist = true;
               break;
            }
         }
         if($exist){
            update_record('feedback_value', $newvalue);
         }else {
            insert_record('feedback_value', $newvalue);
         }
         
      }
   }
   return $completed->id;
}

//liefert die values eines feedbacks in Abhaengigkeit der Gruppenid
function get_feedback_group_values($item, $groupid = false){
   global $CFG;
   if(intval($groupid) > 0) {
      $query = 'SELECT fbv .  *
                  FROM '.$CFG->prefix . 'feedback_value AS fbv, '.$CFG->prefix . 'feedback_completed AS fbc, '.$CFG->prefix . 'groups_members AS gm
                  WHERE fbv.item = '.$item->id . '
                      AND fbv.completed = fbc.id 
                      AND fbc.userid = gm.userid 
                      AND gm.groupid = '.$groupid . '
                  ORDER BY fbc.timemodified';
      $values = get_records_sql($query);
   }else {
      $values = get_records('feedback_value', 'item', $item->id);
   }   
   return $values;
}

//liefert die Liste der ausgefuellten feedbacks in Abhaengigkeit der Gruppenid
function get_completeds_group($feedback, $groupid = false) {
   global $CFG;
   if(intval($groupid) > 0){
      $query = 'SELECT fbc. *
                  FROM '.$CFG->prefix . 'feedback_completed AS fbc, '.$CFG->prefix . 'groups_members AS gm
                  WHERE  fbc.feedback = '.$feedback->id . '
                     AND gm.groupid = '.$groupid . '
                     AND fbc.userid = gm.userid';
      if($values = get_records_sql($query)) {
         return $values;
      }else {return false;}
   }else {
      if($values = get_records('feedback_completed', 'feedback', $feedback->id)){
         return $values;
      }else{return false;}
   }
}

//liefert die Anzahl der ausgefuellten feedbacks in Abhaengigkeit der Gruppenid
function get_completeds_group_count($feedback, $groupid = false) {
   if($values = get_completeds_group($feedback, $groupid)) {
      return sizeof($values);
   }else {
      return false;
   }
}

//liefert die Gruppenid (nur wenn der Gruppenmodus entsprechend eingestellt ist)
function get_feedback_groupid($course, $cm) {
   $groupmode = groupmode($course, $cm);
   
   //get groupid
   if($groupmode > 0 && !isadmin()) {
      return mygroupid($course->id);
   }else {
      return false;
   }
}

//loescht ein completed-datensatz (ein ausgefuelltes feedback)
function delete_feedback_completed($completedid) {
   if(!$completed = get_record('feedback_completed', 'id', $completedid)) {
      return false;
   }
   //erst alle verknuepften Values loeschen
   @delete_records('feedback_value', 'completed', $completed->id);
   
   //tracking eintraege dekrementieren bzw. loeschen
   if($tracking = get_record_select('feedback_tracking', " completed = ".$completed->id." AND feedback = ".$completed->feedback." ")) {
      @delete_records('feedback_tracking', 'completed', $completed->id);
   }
      
   //den completed-datensatz loeschen
   return delete_records('feedback_completed', 'id', $completed->id);
}

////////////////////////////////////////////////
////////////////////////////////////////////////
////////////////////////////////////////////////
//allgemeines
////////////////////////////////////////////////
function print_numeric_option_list($startval, $endval, $selectval = '', $interval = 1){
   for($i = $startval; $i <= $endval; $i += $interval){
      if($selectval == ($i)){
         $selected = 'selected="selected"';
      }else{
         $selected = '';
      }
      echo '<option '.$selected.'>'.$i.'</option>';
   }
}

function feedback_email_teachers($cm, $feedback, $course, $userid) {
   
   global $CFG;
   
   if ($feedback->email_notification == 0) {          // No need to do anything
      return;
   }
   
   $user = get_record('user', 'id', $userid);
   
   if (groupmode($course, $cm) == SEPARATEGROUPS) {   // Separate groups are being used
      if (!$group = user_group($course->id, $user->id)) {             // Try to find a group
         $group->id = 0;                                             // Not in a group, never mind
      }
      $teachers = get_group_teachers($course->id, $group->id);        // Works even if not in group
   } else {
      $teachers = get_course_teachers($course->id);
   }

   if ($teachers) {

      $strfeedbacks = get_string('modulenameplural', 'feedback');
      $strfeedback  = get_string('modulename', 'feedback');
      $strcompleted  = get_string('completed', 'feedback');
      
      foreach ($teachers as $teacher) {
         unset($info);
         $info->username = fullname($user);
         $info->feedback = format_string($feedback->name,true);
         $info->url = $CFG->wwwroot.'/mod/feedback/show_entries.php?id='.$cm->id.'&userid='.$userid;

         $postsubject = $strcompleted.': '.$info->username.' -> '.$feedback->name;
         $posttext = feedback_email_teachers_text($info, $course);
         $posthtml = ($teacher->mailformat == 1) ? feedback_email_teachers_html($info, $course, $cm) : '';
         
         @email_to_user($teacher, $user, $postsubject, $posttext, $posthtml);
      }
   }
}

function feedback_email_teachers_anonym($cm, $feedback, $course) {
   
   global $CFG;
   
   if ($feedback->email_notification == 0) {          // No need to do anything
      return;
   }
   
   $teachers = get_course_teachers($course->id);

   if ($teachers) {

      $strfeedbacks = get_string('modulenameplural', 'feedback');
      $strfeedback  = get_string('modulename', 'feedback');
      $strcompleted  = get_string('completed', 'feedback');
      
      foreach ($teachers as $teacher) {
         unset($info);
         $info->username = get_string('anonymous', 'feedback');
         $info->feedback = format_string($feedback->name,true);
         $info->url = $CFG->wwwroot.'/mod/feedback/show_entries_anonym.php?id='.$cm->id;

         $postsubject = $strcompleted.': '.$info->username.' -> '.$feedback->name;
         $posttext = feedback_email_teachers_text($info, $course);
         $posthtml = ($teacher->mailformat == 1) ? feedback_email_teachers_html($info, $course, $cm) : '';
         
         @email_to_user($teacher, $teacher, $postsubject, $posttext, $posthtml);
      }
   }
}

function feedback_email_teachers_text($info, $course) {
   $posttext  = $course->shortname.' -> '.get_string('modulenameplural', 'feedback').' -> '.
               $info->feedback."\n";
   $posttext .= '---------------------------------------------------------------------'."\n";
   $posttext .= get_string("emailteachermail", "feedback", $info)."\n";
   $posttext .= '---------------------------------------------------------------------'."\n";
   return $posttext;
}


function feedback_email_teachers_html($info, $course, $cm) {
   global $CFG;
   $posthtml  = '<p><font face="sans-serif">'.
            '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->shortname.'</a> ->'.
            '<a href="'.$CFG->wwwroot.'/mod/feedback/index.php?id='.$course->id.'">'.get_string('modulenameplural', 'feedback').'</a> ->'.
            '<a href="'.$CFG->wwwroot.'/mod/feedback/view.php?id='.$cm->id.'">'.$info->feedback.'</a></font></p>';
   $posthtml .= '<hr /><font face="sans-serif">';
   $posthtml .= '<p>'.get_string('emailteachermailhtml', 'feedback', $info).'</p>';
   $posthtml .= '</font><hr />';
   return $posthtml;
}


?>
