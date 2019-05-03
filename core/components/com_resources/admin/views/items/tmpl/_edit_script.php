<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$path = trim($this->rconfig->get('uploadpath'), DS);
$root = substr(PATH_APP, strlen(PATH_ROOT));
if (substr($path, 0, strlen($root)) != $root)
{
	$path = $root . DS . $path;
}
$path .= DS;
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	if (pressbutton == 'resethits') {
		if (confirm('<?php echo Lang::txt('COM_RESOURCES_CONFIRM_HITS_RESET'); ?>')){
			submitform(pressbutton);
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'resetrating') {
		if (confirm('<?php echo Lang::txt('COM_RESOURCES_CONFIRM_RATINGS_RESET'); ?>')){
			submitform(pressbutton);
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// do field validation
	if (document.getElementById('field-title').value == ''){
		alert('<?php echo Lang::txt('COM_RESOURCES_ERROR_MISSING_TITLE'); ?>');
	} else if (document.getElementById('type').value == "-1"){
		alert('<?php echo Lang::txt('COM_RESOURCES_ERROR_MISSING_TYPE'); ?>');
	} else {
		<?php echo $this->editor()->save('text'); ?>

		submitform(pressbutton);
	}
}

function doFileoptions()
{
	var fwindow = window.filer.window.imgManager;

	if (fwindow) {
		if (fwindow.document) {
			var fform = fwindow.document.forms['filelist'];

			if (fform) {
				//var filepath = fform.elements['listdir'];
				var slctdfiles = fform.slctdfile;
				if (slctdfiles.length > 1) {
					for (var i = 0; i < slctdfiles.length; i++)
					{
						if (slctdfiles[i].checked) {
							var filepath = slctdfiles[i].value;
						}
					}
				} else {
					var filepath = slctdfiles.value;
				}

				box = document.adminForm.fileoptions;
				act = box.options[box.selectedIndex].value;

				//var selection = window.filer.document.forms[0].dirPath;
				//var dir = selection.options[selection.selectedIndex].value;

				if (act == '1') {
					document.forms['adminForm'].elements['params[series_banner]'].value = '<?php echo $this->rconfig->get('uploadpath') . '/'; ?>' + filepath;
				} else if (act == '2') {
					//if (filepath) {
					//document.forms['adminForm'].elements['path'].value = '<?php echo $path; ?>' + filepath;
					document.forms['adminForm'].elements['fields[path]'].value = filepath;
					//}
				} else if (act == '3') {
					var content = <?php echo $this->editor()->getContent('field-fulltxt'); ?>
					content = content + '<p><img class="contentimg" src="<?php echo $path; ?>' + filepath + '" alt="image" /></p>';
					<?php //echo $this->editor()->setContent('field-fulltxt', 'content'); ?>
					setEditorContent('field-fulltxt', content);
				} else if (act == '4') {
					var content = <?php echo $this->editor()->getContent('field-fulltxt'); ?>
					content = content + '<p><a href="<?php echo $path; ?>' + filepath + '">' + filepath + '</a></p>';
					<?php //echo $this->editor()->setContent('field-fulltxt', 'content'); ?>
					setEditorContent('field-fulltxt', content);
				}
			}
		}
	}
}
</script>
