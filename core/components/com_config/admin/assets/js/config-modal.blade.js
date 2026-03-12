/**
 * Component Configuration Modal — CSP-safe JS
 *
 * Tab switching and button handlers for the admin config popup.
 * Loaded by the hzadmin shell when rendering com_config views.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
(function () {
  'use strict';

  // Tab switching via data-tab attribute
  document.addEventListener('click', function (e) {
    var tab = e.target.closest('[data-tab]');
    if (!tab || !tab.closest('.config-tabs')) return;

    e.preventDefault();

    var name = tab.getAttribute('data-tab');

    var panels = document.querySelectorAll('.config-tab-panel');
    for (var i = 0; i < panels.length; i++) {
      panels[i].classList.add('hidden');
    }
    var tabs = document.querySelectorAll('.config-tab');
    for (var i = 0; i < tabs.length; i++) {
      tabs[i].classList.remove('config-tab-active');
    }

    var panel = document.getElementById('tab-' + name);
    if (panel) panel.classList.remove('hidden');
    tab.classList.add('config-tab-active');
  });

  // Button handlers
  var form = document.getElementById('component-form');
  if (!form) return;

  function submitForm(task) {
    var taskField = form.querySelector('input[name="task"]');
    if (taskField) taskField.value = task;
    form.submit();
  }

  var btnApply  = document.getElementById('btn-apply');
  var btnSave   = document.getElementById('btn-save');
  var btnCancel = document.getElementById('btn-cancel');

  if (btnApply) {
    btnApply.addEventListener('click', function () {
      submitForm('component.apply');
    });
  }

  if (btnSave) {
    btnSave.addEventListener('click', function () {
      submitForm('component.save');
    });
  }

  if (btnCancel) {
    btnCancel.addEventListener('click', function () {
      if (this.getAttribute('data-refresh')) {
        window.parent.postMessage('admin-popup-close-refresh', '*');
      } else {
        window.parent.postMessage('admin-popup-close', '*');
      }
    });
  }
})();
