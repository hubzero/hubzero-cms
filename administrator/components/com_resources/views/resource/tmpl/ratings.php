<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

?>
<table class="adminform">
 <thead>
  <tr>
   <th colspan="3">User ratings and comments</th>
  </tr>
 </thead>
 <tbody>
<?php foreach($rows as $row) { 
if (intval( $row->created ) <> 0) {
	$thedate = JHTML::_('date', $row->created );
}
$juser =& JUser::getInstance($row->user_id);
?>
  <tr>
   <th>User:</th>
   <td><?php echo $juser->get('name'); ?></td>
  </tr>
  <tr>
   <th>Rating:</th>
   <td><?php echo ResourcesHtml::writeRating( $row->rating );?></td>
  </tr>
  <tr>
   <th>Rated:</th>
   <td><?php echo $thedate; ?></td>
  </tr>
  <tr>
   <th style="border-bottom: 2px solid #999;vertical-align:top;">Comment:</th>
   <td style="border-bottom: 2px solid #999;" class="aLeft"><?php 
  if($row->comment) {
   echo stripslashes($row->comment); 
  } else {
  	echo '[ no comment ]';
  }
   ?></td>
  </tr>
<?php } ?>
 </tbody>
</table>