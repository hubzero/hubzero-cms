/**
 * com_resources — Resource edit page behavior
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
(function () {
  'use strict';

  var configEl = document.getElementById('resource-edit-config');
  if (!configEl) return;

  var config = JSON.parse(configEl.textContent);

  // Override global submitbutton for resource edit validation
  window.submitbutton = function (pressbutton) {
    if (pressbutton === 'resethits') {
      if (confirm(config.confirmHitsReset)) {
        Hubzero.submitform(pressbutton);
      }
      return;
    }

    if (pressbutton === 'resetrating') {
      if (confirm(config.confirmRatingReset)) {
        Hubzero.submitform(pressbutton);
      }
      return;
    }

    if (pressbutton === 'cancel') {
      Hubzero.submitform(pressbutton);
      return;
    }

    var titleEl = document.getElementById('field-title');
    var typeEl = document.getElementById('type');

    if (titleEl && titleEl.value === '') {
      alert(config.errorMissingTitle);
    } else if (typeEl && typeEl.value === '-1') {
      alert(config.errorMissingType);
    } else {
      Hubzero.submitform(pressbutton);
    }
  };

  // Also set as Hubzero.submitbutton override
  Hubzero.submitbutton = window.submitbutton;

  // File options handler
  window.doFileoptions = function () {
    var fwindow = window.filer && window.filer.window && window.filer.window.imgManager;
    if (!fwindow || !fwindow.document) return;

    var fform = fwindow.document.forms['filelist'];
    if (!fform) return;

    var slctdfiles = fform.slctdfile;
    var filepath;
    if (slctdfiles.length > 1) {
      for (var i = 0; i < slctdfiles.length; i++) {
        if (slctdfiles[i].checked) {
          filepath = slctdfiles[i].value;
        }
      }
    } else {
      filepath = slctdfiles.value;
    }

    var box = document.adminForm.fileoptions;
    var act = box.options[box.selectedIndex].value;
    var adminForm = document.forms['adminForm'];

    if (act === '1') {
      adminForm.elements['params[series_banner]'].value = config.uploadPath + filepath;
    } else if (act === '2') {
      adminForm.elements['fields[path]'].value = filepath;
    } else if (act === '3' || act === '4') {
      // Get current editor content
      var editorEl = document.getElementById('field-fulltxt');
      var content = '';
      if (window.CKEDITOR && CKEDITOR.instances['field-fulltxt']) {
        content = CKEDITOR.instances['field-fulltxt'].getData();
      } else if (editorEl) {
        content = editorEl.value;
      }

      if (act === '3') {
        content += '<p><img class="contentimg" src="' + config.filePath + filepath + '" alt="image" /></p>';
      } else {
        content += '<p><a href="' + config.filePath + filepath + '">' + filepath + '</a></p>';
      }

      if (window.CKEDITOR && CKEDITOR.instances['field-fulltxt']) {
        CKEDITOR.instances['field-fulltxt'].setData(content);
      } else if (editorEl) {
        editorEl.value = content;
      }
    }
  };
})();
