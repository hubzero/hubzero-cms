<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();
?>
<script type="text/javascript">
// Note:  Future self, ideally this would use AJAX. As it works now, it kicks you out of the page
// on all actions (in particular, event actions).
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'updatefmnevent') {
		if (confirm('<?php echo Lang::txt('COM_FMNS_CONFIRM_UPDATE_FMN_EVENT'); ?>')){
			submitform(pressbutton);
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'updateregevent') {
		if (confirm('<?php echo Lang::txt('COM_FMNS_CONFIRM_UPDATE_REG_EVENT'); ?>')){
			submitform(pressbutton);
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'deletefmnevent') {
		if (confirm('<?php echo Lang::txt('COM_FMNS_CONFIRM_DELETE_FMN_EVENT'); ?>')){
			submitform(pressbutton);
			return;
		} else {
			return;
		}
	}

	if (pressbutton == 'deleteregevent') {
		if (confirm('<?php echo Lang::txt('COM_FMNS_CONFIRM_DELETE_REG_EVENT'); ?>')){
			submitform(pressbutton);
			return;
		} else {
			return;
		}
	}


	if ((pressbutton == 'cancel') ||
	    (pressbutton == 'createfmnevent') ||
			(pressbutton == 'createregevent') ||
		  (pressbutton == 'editfmnevent') ||
		  (pressbutton == 'editregevent')) {
		submitform(pressbutton);
		return;
	}

	// do field validation, where we make sure required fields were not left blank
	if ($('#field-name').val() == '') {
		alert("<?php echo Lang::txt('COM_FMNS_ERROR_MISSING_FIELDS'); ?>");
	} else {
		<?php echo $this->editor()->save('text'); ?>

		submitform(pressbutton);
	}
}
</script>
