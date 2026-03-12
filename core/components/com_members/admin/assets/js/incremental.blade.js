/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  var possibleCols = JSON.parse(
    document.getElementById('incremental-cols-data').content.textContent
  );

  function addField(container, idx) {
    var li = document.createElement('li');
    var ul = container.previousElementSibling;
    while (ul && ul.tagName !== 'UL') { ul = ul.previousElementSibling; }
    if (!ul) return;

    var sel = document.createElement('select');
    sel.className = 'select select-bordered select-sm';
    sel.name = 'group-cols-' + idx + '[]';
    var opt = document.createElement('option');
    opt.value = '';
    opt.textContent = 'Select profile field...';
    sel.appendChild(opt);
    for (var k in possibleCols) {
      if (possibleCols.hasOwnProperty(k)) {
        opt = document.createElement('option');
        opt.value = k;
        opt.textContent = possibleCols[k];
        sel.appendChild(opt);
      }
    }
    li.appendChild(sel);
    var rm = document.createElement('button');
    rm.className = 'btn btn-sm btn-ghost btn-error';
    rm.setAttribute('data-action', 'remove-parent-li');
    rm.textContent = 'Remove field';
    li.appendChild(rm);
    ul.appendChild(li);
  }

  function renumberGroups() {
    var groups = document.querySelectorAll('#reg-groups > li.reg-group');
    groups.forEach(function (li, idx) {
      li.querySelectorAll('input').forEach(function (inp) {
        inp.name = inp.name.replace(/\d+$/, idx);
      });
      li.querySelectorAll('select').forEach(function (sel) {
        sel.name = sel.name.replace(/\d+(\[\])?$/, idx + '$1');
      });
      li.querySelectorAll('.add-field').forEach(function (btn) {
        btn.setAttribute('data-group-idx', idx);
      });
    });
  }

  function addGroup(btn) {
    var ol = document.getElementById('reg-groups');
    var li = document.createElement('li');
    li.className = 'reg-group';
    var p = document.createElement('p');
    li.appendChild(p);

    p.appendChild(document.createTextNode('Beginning '));
    var hours = document.createElement('input');
    hours.name = 'group-hours-0';
    hours.size = 3;
    hours.className = 'input input-bordered input-sm w-20';
    p.appendChild(hours);

    var unit = document.createElement('select');
    unit.name = 'group-time-unit-0';
    unit.className = 'select select-bordered select-sm';
    ['hour', 'day', 'week'].forEach(function (u) {
      var opt = document.createElement('option');
      opt.value = u; opt.textContent = u + 's';
      unit.appendChild(opt);
    });
    p.appendChild(unit);
    p.appendChild(document.createTextNode(' after registration, prompt for: '));

    var fields = document.createElement('ul');
    var fieldLi = document.createElement('li');
    var fieldSel = document.createElement('select');
    fieldSel.name = 'group-cols-0[]';
    fieldSel.className = 'select select-bordered select-sm';
    var opt = document.createElement('option');
    opt.textContent = 'Select profile field...';
    fieldSel.appendChild(opt);
    for (var k in possibleCols) {
      if (possibleCols.hasOwnProperty(k)) {
        opt = document.createElement('option');
        opt.value = k; opt.textContent = possibleCols[k];
        fieldSel.appendChild(opt);
      }
    }
    fieldLi.appendChild(fieldSel);
    var fieldRm = document.createElement('button');
    fieldRm.className = 'btn btn-sm btn-ghost btn-error';
    fieldRm.setAttribute('data-action', 'remove-parent-li');
    fieldRm.textContent = 'Remove field';
    fieldLi.appendChild(fieldRm);
    fields.appendChild(fieldLi);
    p.appendChild(fields);

    var af = document.createElement('button');
    af.className = 'add-field btn btn-sm btn-ghost';
    af.setAttribute('data-action', 'add-field');
    af.setAttribute('data-group-idx', '0');
    af.textContent = 'Add field';
    p.appendChild(af);

    var rm = document.createElement('button');
    rm.className = 'btn btn-sm btn-ghost btn-error';
    rm.setAttribute('data-action', 'remove-group');
    rm.textContent = 'Remove group';
    li.appendChild(rm);
    ol.appendChild(li);
    renumberGroups();
  }

  function addRecurrence(btn) {
    var ol = document.getElementById('reg-recurrence');
    var len = ol.getElementsByTagName('li').length;
    var li = document.createElement('li');
    var inp = document.createElement('input');
    inp.size = 3; inp.name = 'recur-' + len;
    inp.className = 'input input-bordered input-sm w-20';
    li.appendChild(inp);
    var sel = document.createElement('select');
    sel.name = 'recur-type-' + len;
    sel.className = 'select select-bordered select-sm';
    ['hour', 'day', 'week'].forEach(function (u) {
      var op = document.createElement('option');
      op.value = u; op.textContent = u + 's';
      sel.appendChild(op);
    });
    li.appendChild(sel);
    var rm = document.createElement('button');
    rm.className = 'btn btn-sm btn-ghost btn-error';
    rm.setAttribute('data-action', 'remove-parent-li');
    rm.textContent = 'Remove recurrence';
    li.appendChild(rm);
    ol.appendChild(li);
  }

  // Delegated event handling
  document.addEventListener('click', function (e) {
    var target = e.target.closest('[data-action]');
    if (!target) return;
    e.preventDefault();
    var action = target.getAttribute('data-action');

    if (action === 'remove-parent-li') {
      var li = target.closest('li');
      if (li) li.remove();
    } else if (action === 'remove-group') {
      var li = target.closest('li.reg-group');
      if (li) li.remove();
      renumberGroups();
    } else if (action === 'add-field') {
      var idx = parseInt(target.getAttribute('data-group-idx'), 10) || 0;
      addField(target, idx);
    } else if (action === 'add-group') {
      addGroup(target);
    } else if (action === 'add-recurrence') {
      addRecurrence(target);
    }
  });
});
