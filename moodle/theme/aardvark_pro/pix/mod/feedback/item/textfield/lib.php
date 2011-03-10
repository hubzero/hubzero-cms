<?PHP  // $Id: lib.php,v 1.1.2.1 2006/01/03 16:01:23 andreas Exp $
function show_edit_textfield($item, $usehtmleditor = false) {

	$item->presentation=empty($item->presentation)?'':$item->presentation;

?>
   <table>
      <tr>
         <th colspan="2"><?php print_string('textfield', 'feedback');?>
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
         <td><?php print_string('textfield_size', 'feedback');?></td>
         <td>
            <select name="itemsize">
<?php
               //Dropdown-Items fuer die Textfeldbreite
               $sizeAndLength = explode('|',$item->presentation);
               $selected = '';
               print_numeric_option_list(5, 50, ($sizeAndLength[0])?$sizeAndLength[0]:40, 5);
?>
            </select>
         </td>
      </tr>
      <tr>
         <td><?php print_string('textfield_maxlength', 'feedback');?></td>
         <td>
            <select name="itemmaxlength">
<?php
               //Dropdown-Items fuer die Textlaenge
               print_numeric_option_list(5, 50, ($sizeAndLength[1])?$sizeAndLength[1]:40, 5);
?>
            </select>
         </td>
      </tr>
   </table>
<?php
}

//liefert eine Struktur ->name, ->data = array(mit Antworten)
function get_analysed_textfield($item, $groupid = false) {
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

function get_feedback_printval_textfield($item, $value) {
   $printval = '';
   $printval = $value->value;
   return $printval;
}

function print_analysed_textfield($item, $itemnr = 0, $groupid = false) {
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

function excelprint_item_textfield(&$worksheet, $rowOffset, $item, $groupid) {
   $analysed_item = get_analysed_textfield($item, $groupid);

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

function print_feedback_textfield($item, $value = false, $readonly = false){
   $presentation = explode ("|", $item->presentation);
   $requiredmark =  ($item->required == 1)?'<font color="red">*</font>':'';
?>
   <td valign="top" align="left"><?php echo text_to_html(stripslashes_safe($item->name) . $requiredmark, true, false, false);?></td>
   <td valign="top" align="left">
<?php
   if($readonly){
      print_simple_box_start('left');
      echo $value?$value:'&nbsp;';
      print_simple_box_end();
   }else {
?>
      <input type="text" name="<?php echo $item->typ . '_' . $item->id;?>"
                        size="<?php echo $presentation[0];?>"
                        maxlength="<?php echo $presentation[1];?>"
                        value="<?php echo $value?$value:'';?>" />
<?php
   }
?>
   </td>
<?php
}

function check_feedback_value_textfield($value) {
   if($value == "")return false;
   return true;
}

function create_feedback_value_textfield($data) {
   return $data;
}

function get_presentation_textfield($data) {
   return $data->itemsize . '|'. $data->itemmaxlength;
}

function get_feedback_hasvalue_textfield() {
   return 1;
}

?>