/**
 * com_modules - Module edit page behavior
 *
 * Builds ordering select from JSON template data and
 * syncs menu assignment checkboxes with the assignment dropdown.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // Build ordering select from JSON data embedded in a <template> element
  var orderData = document.getElementById('moduleorder');
  if (orderData) {
    var mod = JSON.parse(orderData.textContent || orderData.innerHTML);
    var sel = document.createElement('select');
    sel.name = mod.name;
    sel.id   = mod.id;
    sel.className = 'select select-bordered select-sm w-full mt-1';
    mod.orders.forEach(function (o) {
      if (o[0] === mod.originalPos) {
        var opt = document.createElement('option');
        opt.value = o[1];
        opt.textContent = o[2];
        if (mod.originalOrder == o[1]) { opt.selected = true; }
        sel.appendChild(opt);
      }
    });
    orderData.parentNode.insertBefore(sel, orderData.nextSibling);
  }

  // Enable/disable menu assignment checkboxes based on assignment select
  var assignSel = document.getElementById('jform_assignment');

  function syncAssignment() {
    if (!assignSel) return;
    var v = assignSel.value;
    var disabled = (v === '-' || v === '0');
    document.querySelectorAll('.chkbox').forEach(function (el) {
      el.disabled = disabled;
      if (v === '-') el.checked = false;
      if (v === '0') el.checked = true;
    });
  }

  if (assignSel) {
    assignSel.addEventListener('change', syncAssignment);
    syncAssignment();
  }
});
