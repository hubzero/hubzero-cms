<?PHP  // $Id: lib.php,v 1.1.2.1 2006/01/03 16:01:22 andreas Exp $
function show_edit_label($item, $usehtmleditor = false) {
	$item->presentation=isset($item->presentation)?$item->presentation:'';
?>
   <table style="display:inline">
      <tr><th><?php print_string('label', 'feedback');?></th></tr>
      <tr>
         <td>
            <?php print_textarea($usehtmleditor, 20, 60, 680, 400, "presentation", $item->presentation);?>
            <input type="hidden" id="itemname" name="itemname" value="label" />
         </td>
      </tr>
   </table>
   <div style="clear:both"></div>
<?php
   if ($usehtmleditor) {
      use_html_editor();
   }
}
function print_feedback_label($item){
?>
   <td colspan="2">
      <?php echo stripslashes_safe($item->presentation);?>
   </td>
<?php
}

function create_feedback_value_label($data) {
   return false;
}

//used by create_item and update_item functions,
//when provided $data submitted from show_edit
function get_presentation_label($data) {
   return $data->presentation;
}

function get_feedback_hasvalue_label() {
   return 0;
}

?>