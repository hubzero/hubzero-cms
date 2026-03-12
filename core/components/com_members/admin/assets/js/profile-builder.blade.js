/**
 * Members — Profile field builder initialization
 *
 * Reads bootstrap data and access levels from <template> elements,
 * initializes the Formbuilder widget, and overrides submitbutton
 * to save the form state before submission.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
(function () {
  'use strict';

  var fb = null;

  // Read bootstrap data from template elements
  var dataEl = document.getElementById('profile-builder-data');
  var accessEl = document.getElementById('profile-builder-accesses');
  if (!dataEl) return;

  var bootstrapData = JSON.parse(dataEl.content.textContent);
  window.accesses = JSON.parse(accessEl.content.textContent);

  jQuery(document).ready(function ($) {
    fb = new Formbuilder({
      selector: '.fb-main',
      bootstrapData: bootstrapData
    });

    fb.on('save', function (payload) {
      $('#profile-schema').val(payload);
    });
  });

  // Override submitbutton to save form state before submission
  window.submitbutton = function (pressbutton) {
    if (pressbutton === 'cancel') {
      Hubzero.submitform(pressbutton);
      return;
    }

    if (fb) {
      fb.mainView.saveForm();
    }
    Hubzero.submitform(pressbutton);
  };

  Hubzero.submitbutton = window.submitbutton;
})();
