<?PHP  // $Id: lib.php,v 1.1.2.1 2006/01/03 16:01:23 andreas Exp $
function show_edit_textarea($item, $usehtmleditor = false) {

	$item->presentation=empty($item->presentation)?'':$item->presentation;

?>
   <table>
      <tr>
         <th colspan="2"><?php print_string('textarea', 'feedback');?>
            &nbsp;(<input type="checkbox" name="required" value="1" <?php 
			$item->required=isset($item->required)?$item->required:0;
			echo ($item->required == 1?'checked="checked"':'');
			?> />&nbsp;<?php print_string('required', 'feedback');?>)
         </th>
      </tr>
      <tr>
         <td><?php print_string('item_name', 'feedback');?></td>
         <td><input type="text" id="itemname" name="itemname" size="40" maxlength="255" value="<?php echo isset($item->name)?stripslashes_safe($item->name):'';?>" /></td>
      </tr>
      <tr>
         <td><?php print_string('textarea_width', 'feedback');?></td>
         <td>
            <select name="itemwidth">
<?php
               //Dropdown-Items fuer die Textareabreite
               $widthAndHeight = explode('|',$item->presentation);
               print_numeric_option_list(5, 80, $widthAndHeight[0]?$widthAndHeight[0]:30, 5);
?>
            </select>
         </td>
      </tr>
      <tr>
         <td><?php print_string('textarea_height', 'feedback');?></td>
         <td>
            <select name="itemheight">
<?php
               //Dropdown-Items fuer die Textareahoehe
               print_numeric_option_list(5, 40, $widthAndHeight[1], 5);
?>
            </select>
         </td>
      </tr>
   </table>
<?php
}

//liefert eine Struktur ->name, ->data = array(mit Antworten)
function get_analysed_textarea($item, $groupid) {
   $aVal = null;
   $aVal->name = $item->name;
   //$values = get_records('feedback_value', 'item', $item->id);
   $values = get_feedback_group_values($item, $groupid);
   if($values) {
      $data = array();
      foreach($values as $value) {
         $data[] = str_replace("\n", '<br />', $value->value);
      }
      $aVal->data = $data;
   }
   return $aVal;
}

function get_feedback_printval_textarea($item, $value) {
   $printval = '';
   $printval = $value->value;
   return $printval;
}

function print_analysed_textarea($item, $itemnr = 0, $groupid = false) {
   $values = get_feedback_group_values($item, $groupid);
   if($values) {
      //echo '<table>';2
      $itemnr++;
      echo '<tr><th colspan="2">'. $itemnr . '.)&nbsp;' . stripslashes_safe($item->name) .'</th></tr>';
      foreach($values as $value) {
         echo '<tr><td valign="top" align="right">-</td>';
         echo '<td>' . str_replace("\n", '<br />', $value->value) . '</td></tr>';
      }
      //echo '</table>';
   }
   return $itemnr;
}

function excelprint_item_textarea(&$worksheet, $rowOffset, $item, $groupid) {
   $analysed_item = get_analysed_textarea($item, $groupid);

   $worksheet->setFormat("<l><f><ro2><vo><c:green>");
   $worksheet->write_string($rowOffset, 0, stripslashes_safe($item->name));
   $data = $analysed_item->data;
   if(is_array($data)) {
      $worksheet->setFormat("<l><ro2><vo>");
      $worksheet->write_string($rowOffset, 1, $data[0]);
      $rowOffset++;
      for($i = 1; $i < sizeof($data); $i++) {
         $worksheet->setFormat("<l><vo>");
         $worksheet->write_string($rowOffset, 1, $data[$i]);
         $rowOffset++;
      }
   }
   $rowOffset++;
   return $rowOffset;
}

function print_feedback_textarea($item, $value = false, $readonly = false){
   $presentation = explode ("|", $item->presentation);
   $requiredmark =  ($item->required == 1)?'<font color="red">*</font>':'';
?>
   <td valign="top" align="left"><?php echo text_to_html(stripslashes_safe($item->name) . $requiredmark, true, false, false);?></td>
   <td valign="top" align="left">
<?php
   if($readonly){
      print_simple_box_start('left');
      echo $value?str_replace("\n",'<br />',$value):'&nbsp;';
      print_simple_box_end();
   }else {
?>
      <textarea name="<?php echo $item->typ . '_' . $item->id;?>"
               cols="<?php echo $presentation[0];?>"
               rows="<?php echo $presentation[1];?>"><?php echo $value?$value:'';?></textarea>
<?php
   }
?>
   </td>
<?php
}

function check_feedback_value_textarea($value) {
   if($value == "")return false;
   return true;
}

function create_feedback_value_textarea($data) {
   return $data;
}

function get_presentation_textarea($data) {
   return $data->itemwidth . '|'. $data->itemheight;
}

function get_feedback_hasvalue_textarea() {
   return 1;
}

?>