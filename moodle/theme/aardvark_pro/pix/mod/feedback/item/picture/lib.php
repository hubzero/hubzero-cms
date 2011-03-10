<?php // $Id: lib.php,v 1.1.2.1 2006/01/03 16:01:22 andreas Exp $ 
/**
 * constant points to the directory where the picrure file library is located
 */
define ('feedback_PICTURE_FILES', '/mod/feedback/item/picture/library');

/**
 * outputs HTML for defining a new picture item or updating an existing one
 * 
 * Outputs HTML code to browser window, allows user to define
 * a new picture item  or to update an existing one
 * 
 * @param object $item contains the item data (a record from prefix_feedback_item table)
 * @param boolean $usehtmleditor should HTML editor be used or not? (not needed in show_edit_picture)
 */
function show_edit_picture($item, $usehtmleditor = false)
{
    global $CFG;
    $picdir = $CFG->dirroot . feedback_PICTURE_FILES;

	$item->presentation=empty($item->presentation)?'':$item->presentation;
    $itemvalues=explode('|', $item->presentation);
    
	// Let's compose the HTML select for picture filenames
    $fselect = "";
    $picfiles = read_feedback_picture_list($picdir, "jpg;png;gif");

    if ($itemvalues == "") {
        // we do not have anything selected yet
        foreach ($picfiles as $picfile) {
            $fselect = '<option value="' . $picfile . '">' . $picfile . '</option>' . $fselect;
        } 
    } else {
        // we are updating an item, some picture file names are already selected
        foreach ($picfiles as $picfile) {
            if (in_array($picfile, $itemvalues)) {
                $fselect = $fselect . '<option value="' . $picfile . '" selected="selected">' . $picfile . '</option>' ;
            } else {
                $fselect = $fselect . '<option value="' . $picfile . '" >' . $picfile . '</option>';
            } 
        } 
    } 
    // The rest of this function will produce a HTML table and will fill in the elements we need
    ?>
   <table>
      <tr>
         <th colspan="2"><?php print_string('picture', 'feedback');

    ?> 
            &nbsp;(<input type="checkbox" name="required" value="1" <?php 
				$item->required=isset($item->required)?$item->required:0;
				echo ($item->required == 1?'checked="checked"':'');

    ?> />&nbsp;<?php print_string('required', 'feedback');

    ?>)
         </th>
      </tr>
	  <tr>
         <td><?php print_string('item_name', 'feedback'); 

    ?></td>
         <td><input type="text" id="itemname" name="itemname" size="40" maxlength="255" value="<?php echo isset($item->name)?stripslashes_safe($item->name):'';?>" /></td>
      </tr>

      <tr><td colspan="2">&nbsp;</td></tr>
      <tr><td></td><td> <?php print_string('picture_file_list', 'feedback')?></td></tr>
	  <tr>
         <td valign="top">
            <?php print_string('picture_values', 'feedback');

    ?>
         </td>
         <td>
			<?php $itemvalues = str_replace('|', "\n", $item->presentation);

    ?>
            <select name="itemvalues[]" size="10" multiple="multiple"><?php echo $fselect;

    ?></select>
         </td>
      </tr>
   </table>
   
   <table align="center"><tr><td>
   
   </td></tr></table>
   
<?php
} 
// liefert ein eindimensionales Array mit drei Werten(typ, name, XXX)
// XXX ist ein eindimensionales Array (anzahl der Antworten bei Typ Radio) Jedes Element ist eine Struktur (answertext, answercount)
/**
 * counts the answers to a given picture item
 * 
 * Goes through all submitted answers to a 
 * given feedback item, counts the occurrances of each answer
 * and calculates a quotient showing 
 * (received answers per pic)/(all received answers)
 * 
 * @param object $item contains the item data (a record from prefix_feedback_item table)
 * @param boolean $groupid 
 * @return array returned array will contain something like this <pre>
 * Array
 * (
 *           [0] => picture
 *           [1] => What is the flag of Andorra?
 *           [2] => Array
 *               (
 *                   [0] => stdClass Object
 *                       (
 *                           [answertext] => angola.png
 *                           [answercount] => 1
 *                           [quotient] => 0.5
 *                       )
 *                   [1] => stdClass Object
 *                       (
 *                           [answertext] => antiguabarbuda.png
 *                           [answercount] => 0
 *                           [quotient] => 0
 *                       )
 *                   [2] => stdClass Object
 *                       (
 *                           [answertext] => andorra.png
 *                           [answercount] => 1
 *                           [quotient] => 0.5
 *                       )
 *               )
 * )</pre>
 */
function get_analysed_picture($item, $groupid = false)
{ 
    // for the beginning first only the radiobadges
    $analysedItem = array();
    $analysedItem[] = $item->typ;
    $analysedItem[] = $item->name; 
    // the possible answers extract
    $answers = null;
    $answers = explode ("|", $item->presentation);
    if (!is_array($answers)) return null; 
    // the values get
    $values = get_feedback_group_values($item, $groupid);
    if (!$values) return null; 
    // trail about the values and about the answer possibilities
    $analysedAnswer = array();

    for($i = 1; $i <= sizeof($answers); $i++) {
        $ans = null;
        $ans->answertext = $answers[$i-1];
        $ans->answercount = 0;
        foreach($values as $value) {
            // if the answer is immediately index of the answers + 1?
            if ($value->value == $i) {
                $ans->answercount++;
            } 
        } 
        $ans->quotient = $ans->answercount / sizeof($values);
        $analysedAnswer[] = $ans;
    } 
    $analysedItem[] = $analysedAnswer;
    return $analysedItem;
} 

/**
 * outputs HTML presenting the distribution of answers
 * 
 * Outputs HTML code to browser window, which shows
 * the distribution of answers to feedback item $item
 * 
 * @param object $item contains the item data (a record from prefix_feedback_item table)
 * @param integer $itemnr used for ordering items list for viewing
 * @param boolean $groupid 
 * @return integer 
 */
function print_analysed_picture($item, $itemnr = 0, $groupid = false)
{
    global $CFG;
    $analysedItem = get_analysed_picture($item, $groupid); //compute the distribution of received answers        
    // do we have anlyzed items to show?
    if ($analysedItem) {
        $itemnr++; 
        // outputs running index of item together with the question associated with the item
        echo '<tr><th colspan="2">' . $itemnr . '.)&nbsp;' . $analysedItem[1] . '</th></tr>';
        $analysedVals = $analysedItem[2];
        $pixnr = 0; 
        // create suitably wide picture to present a horizontal bar proportional to the number of answers received
        foreach($analysedVals as $val) {
            if (function_exists("bcmod")) {
                $pix = 'pics/' . bcmod($pixnr, 10) . '.gif'; // define the colour of the bar
            } else {
                $pix = 'pics/0.gif';
            } 
            $pixnr++;
            $pixwidth = intval($val->quotient * feedback_MAX_PIX_LENGTH);
            $quotient = number_format(($val->quotient * 100), 2, ',', '.');
            list($picname) = explode('.', trim($val->answertext)); //removing file name extension        
            // create HTML for a horizontal graph showing distribution of answers
            echo '<tr><td align="right" valign="bottom"><b>' . $picname . '<img style="padding-right: 20px;padding-left: 20px;" src="' . $CFG->wwwroot . feedback_PICTURE_FILES . '/' . trim($val->answertext) . '" />' . ':</b></td><td align="left"><img style=" vertical-align: baseline;" src="' . $pix . '" height="5" width="' . $pixwidth . '" />&nbsp;'
             . $val->answercount . (($val->quotient > 0)?'&nbsp;(' . $quotient . '&nbsp;%)':'') . '</td></tr>';
        } 
    } 
    return $itemnr;
} 

/**
 * returns the value of an item to be used in an Excel report 
 * 
 * used by funtion excelprint_detailed_items in analysis_to_excel.php
 * 
 * @param object $item contains the item data (a record from prefix_feedback_item table)
 * @param integer $value value of the item as submitted (integer index of the selected radio button)
 * @return string string presentation of item value
 */
function get_feedback_printval_picture($item, $value)
{
    $printval = '';
    $presentation = explode ("|", $item->presentation);
    $index = 1;
    foreach($presentation as $pres) {
        if ($value->value == $index) {
            $printval = $pres;
            break;
        } 
        $index++;
    } 
    list($tmp) = explode(".", $printval); //just removing the file extensiom
    return $tmp;
} 

/**
 * outputs analyzed data into an Excel worksheet
 * 
 * used by analysis_to_excel.php
 * 
 * @param  $ &EasyWorkbook reference to the Excel workbook into which data is written
 * @param integer $rowOffset printing will take place to row number $rowoffset
 * @param object $item contains the item data (a record from prefix_feedback_item table)
 * @param boolean $groupid 
 * @return integer retuns value of $rowOffset
 */
function excelprint_item_picture(&$worksheet, $rowOffset, $item, $groupid)
{
    $analysed_item = get_analysed_picture($item, $groupid);
    $data = $analysed_item[2];

    $worksheet->setFormat("<l><f><ro2><vo><c:green>"); 
    // write question
    $worksheet->write_string($rowOffset, 0, $analysed_item[1]);
    if (is_array($data)) {
        for($i = 0; $i < sizeof($data); $i++) {
            $aData = $data[$i]; 
            // $i is index to the column
            $worksheet->setFormat("<l><f><ro2><vo><c:blue>");
            $worksheet->write_string($rowOffset, $i + 1, trim($aData->answertext));

            $worksheet->setFormat("<l><vo>");
            $worksheet->write_number($rowOffset + 1, $i + 1, $aData->answercount);
            $worksheet->setFormat("<l><f><vo><pr>");
            $worksheet->write_number($rowOffset + 2, $i + 1, $aData->quotient);
        } 
    } 
    $rowOffset += 3 ;
    return $rowOffset;
} 

/**
 * outputs HTML for picture item
 * 
 * Outputs HTML code to browser window showing the picture item,
 * item may have already a $value (a sumitted form has been received), and
 * it is possible to show only the selected picture ($readonly=true)
 * 
 * Radio button values are numbered starting from 1 ($index)
 * 
 * @param object $item contains the item data (a record from prefix_feedback_item table)
 * @param integer $value gives the index to the selected picture (if any)
 * @param boolean $readonly if true, only the selected picture is shown
 */
function print_feedback_picture($item, $value = false, $readonly = false)
{
    global $CFG;
    global $SESSION;

    $presentation = explode ("|", $item->presentation);
    $requiredmark = ($item->required == 1)?'<font color="red">*</font>':'';

    ?>
   <td valign="top" align="left" style="padding-right: 40px;"><?php echo text_to_html(stripslashes_safe($item->name) . $requiredmark, true, false, false);

    ?></td>
   <td valign="top" align="left">
<?php
    $index = 1;
    $checked = '';
    if ($readonly) {
        // here we want to show the selected picture only, $value must be provided
        // this is used by feedback/show_entries.php, for example
        foreach($presentation as $pic) {
            if ($value == $index) {
                print_simple_box_start('left');
                echo '<img style="padding-left: 20px;" src="' . $CFG->wwwroot . feedback_PICTURE_FILES . '/' . $pic . '" />';
                print_simple_box_end();
                break;
            } 
            $index++;
        } 
    } else {
        // this is what we want most of the time, to show the picture item so that answering is possible
        // item may have already a value, after a failed saving attempt, say)
        $currentpic = 0;
        $piccount = count($presentation);
        $course_module = get_record('course_modules', 'id', $SESSION->feedback->coursemoduleid);

        foreach($presentation as $pic) {
            // do we have somehting already selected?
            if ($value == $index) {
                $checked = 'checked="checked"';
            } else {
                $checked = '';
            } 
            // generate the HTML for the item
            ?>
         <table><tr>
         <td valign="top"><input type="radio"
               name="<?php echo $item->typ . '_' . $item->id?>"
               value="<?php echo $index;

            ?>" <?php echo $checked;

            ?> />
         </td><td><?php echo '<img style="padding-left: 20px;" src="' . $CFG->wwwroot . feedback_PICTURE_FILES . '/' . $pic . '" />';

            ?>&nbsp;
		 <?php
            $currentpic++;
            if (isadmin() || isteacher($course_module->course)) {
                if ($currentpic != 1) {
                    echo '</td><td width="20"> ' . feedback_create_action_form('moveup_picture', array($item, $currentpic), 'up.gif');
                } else {
                    echo '</td><td width="20"> &nbsp;';
                } 

                if ($currentpic < $piccount) {
                    echo '</td><td width="50"> ' . feedback_create_action_form('movedown_picture', array($item, $currentpic), 'down.gif');
                } else {
                    echo '</td><td width="50"> &nbsp;';
                } 
            } 

            ?>
         </td></tr></table>
<?php
            $index++;
        } 
    } 

    ?>
   </td>
<?php
} 

/**
 * validity check for picture item value
 * 
 * @param string $value data to be checked
 * @return boolean true if data is acceptable to be stored in picture item
 */
function check_feedback_value_picture($value = "")
{
    if (intval($value) > 0)return true;
    return false;
} 

/**
 * creates proper data format for picture item value
 * 
 * For picture item this function is trivial, because the submitted data
 * from radio button group is directly the integer value we want
 * 
 * NOTE: it is this integer that is stored in the database table prefix_feedback_value,
 * not the picture filename
 * 
 * @param string $data data to be modified
 * @return string picture item value
 */
function create_feedback_value_picture($data)
{
    return $data;
} 

/**
 * creates a string presentation of a picture item
 * 
 * Data comes in as an array, but we want to make
 * the presentation to be a string, which allos easy 
 * writing to a database.
 * 
 * @param array $data data from submitted from form edit_item.php
 * @return string presentation of picture item
 */
function get_presentation_picture($data)
{
    $present = isset($data->itemvalues)?feedback_picture_array2string($data->itemvalues):'';
    return $present;
} 

/**
 * returns always 1 indicating that picture item can have a value
 * 
 * Note that this is in contrast with the label item that cannot have a value.
 * (get_feedback_hasvalue_label() returns always false)
 * 
 * @return 1
 */
function get_feedback_hasvalue_picture()
{
    return 1;
} 

/**
 * reads the file list of a given directory
 * 
 * @param string $dir directory from which we want to list files having a given extension
 * @param string $ext gives the file name extensions that will included in the returned array, for example "jpg;png;gif"
 * @return array string array of file names
 */
function read_feedback_picture_list ($dir, $extensions = "")
{
    $picfiles = array();
    if (is_dir($dir)) {
        $d = dir($dir);
    } else {
        return $picfiles;
    } 
	while (false !== ($entry = $d->read())) {
        if ($extensions == "") {
            $picfiles[] = $entry;
        } else {
			if (substr_count($entry, '.') > 0) {
			  list($name, $fext) = explode('.', $entry);   
			} else{
		      $name=$entry;
			  $fext='';
			}
            $exts = explode(';', $extensions);
            foreach ($exts as $ext) {
                if ($ext == $fext) {
                    $picfiles[] = $entry;
                } 
            } 
        } 
    } 
    $d->close();

    return $picfiles;
} 

/**
 * formats an array into a string, were array values are separated by '|' 
 * 
 * This is useful for converting received form variable arrays into a string,
 * which can be easily stored in a database.
 * 
 * @param array $arr array to be converted into a string
 * @return string string where $arr values are separated by '|'
 */
function feedback_picture_array2string($arr)
{
    if (!is_array($arr)) {
        return '';
    } 
    $retval = '';
    $arrvals = array_values($arr);
    $retval = $arrvals[0];
    for($i = 1; $i < sizeof($arrvals); $i++) {
        $retval = $retval . '|' . $arrvals[$i];
    } 
    return $retval;
} 


// action handlers
/**
 * rearrange pictures: moves picture up within one picture item
 */
function feedback_handler_moveup_picture($item, $index)
{
    global $SESSION;

    $presentation = explode("|", stripslashes_safe($item->presentation));
    if ($index <= 1) {
        return false;
    } 
    $a = $presentation[$index-1];
    $presentation[$index-1] = $presentation[$index-2];
    $presentation[$index-2] = $a;
    $item->name = addslashes($item->name);
    $item->presentation = addslashes(feedback_picture_array2string($presentation));
    $retval = update_record('feedback_item', $item);
    return $retval;
} 

/**
 * rearrange pictures: moves picture down within one picture item
 */
function feedback_handler_movedown_picture($item, $index) {
    global $SESSION;
    $presentation = explode("|", stripslashes_safe($item->presentation));
    if ($index >= count($presentation)) {
        return false;
    } 
    $a = $presentation[$index-1];
    $presentation[$index-1] = $presentation[$index];
    $presentation[$index] = $a;
    $item->name = addslashes($item->name);
    $item->presentation = addslashes(feedback_picture_array2string($presentation));
    $retval = update_record('feedback_item', $item);
    return $retval;
} 

/**
 * just testing, used on testpage_1 amd testpage_2
 */
function feedback_handler_print_picture($text) {
    global $SESSION;
    $SESSION->feedback->testmessage = $text;
    return true;
} 

/**
 * just testing, used on testpage_1 amd testpage_2
 */
function feedback_handler_submitbutton_picture($text) {
    global $SESSION;
    $SESSION->feedback->testmessage = $text;
    return true;
} 




?>