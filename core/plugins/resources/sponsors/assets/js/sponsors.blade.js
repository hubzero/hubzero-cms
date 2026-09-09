/**
 * Sponsors plugin — Blade/daisyUI JavaScript.
 *
 * Replaces sponsors.js when using Blade templates. Vanilla JS, CSP-safe.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

window.Hubzero = window.Hubzero || {};

Hubzero.submitbutton = function (task) {
  document.dispatchEvent(new Event('editorSave'));

  var frm = document.getElementById('item-form');
  if (!frm) return;

  if (task === 'cancel' || (document.formvalidator && document.formvalidator.isValid(frm))) {
    Hubzero.submitform(task, frm);
  } else {
    alert(frm.getAttribute('data-invalid-msg'));
  }
};
