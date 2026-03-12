/**
 * Projects Team — Add member popup behavior
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  var saveBtn = document.getElementById('btn-save-addusers');
  if (!saveBtn) return;

  var errorMsg = saveBtn.getAttribute('data-error-msg') || 'Please fill in at least one field.';
  var redirectUrl = saveBtn.getAttribute('data-redirect-url') || '';

  function submitAddUsers() {
    var form = document.adminForm;
    if (!form) return;
    if (form.newmember.value === '' && form.newgroup.value === '') {
      alert(errorMsg);
      return;
    }
    Hubzero.submitform('addusers');
    if (redirectUrl) {
      window.top.setTimeout(function () {
        window.parent.location = redirectUrl;
      }, 700);
    }
  }

  saveBtn.addEventListener('click', submitAddUsers);

  document.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
      submitAddUsers();
    }
  });
});
