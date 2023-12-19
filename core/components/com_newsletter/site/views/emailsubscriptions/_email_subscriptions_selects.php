<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$subs = $this->subs;
$userId = $this->userId;
?>

<?php
foreach($subs as $s):
	$sKey = $s['foreign_key'];
?>
	<div class="sub">
		<label><?php echo $s['label']; ?></label>

		<?php
			if ($subView = $s['view']):
				$this->view($subView)
				     ->set('userId', $userId)
				     ->display();
			endif;
		?>

		<select	name="<?php echo "subscriptions[$sKey][preference]"; ?>">
			<?php
				foreach($s['options'] as $o):
				$selected = ($o == $s['preference']);
			?>
				<option <?php if ($selected) echo 'selected'; ?>>
					<?php echo $o; ?>
				</option>
			<?php	endforeach; ?>
		</select>
		<input type="hidden" name="<?php echo "subscriptions[$sKey][foreign_key]"; ?>"
		       value="<?php echo $sKey; ?>">
	</div>
<?php endforeach; ?>
