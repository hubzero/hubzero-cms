/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function() {
	var counter = document.querySelectorAll('.rule').length;

	var newRuleBtn = document.querySelector('.new-rule');
	if (newRuleBtn) {
		newRuleBtn.addEventListener('click', function(e) {
			e.preventDefault();

			var sample = document.querySelector('.rule-sample');
			if (!sample) {
				return;
			}
			var rule = sample.cloneNode(true);
			rule.style.display = 'none';
			rule.classList.remove('rule-sample');

			var rulesContainer = document.querySelector('.rules');
			if (rulesContainer) {
				rulesContainer.appendChild(rule);
			}

			counter++;

			var extField = rule.querySelector('#field-extension-new');
			if (extField) {
				extField.setAttribute(
					'name', 'rules[' + counter + '][extension]'
				);
				extField.id = 'field-extension-' + counter;
			}
			var qtyField = rule.querySelector('#field-quantity-new');
			if (qtyField) {
				qtyField.setAttribute(
					'name', 'rules[' + counter + '][quantity]'
				);
				qtyField.id = 'field-quantity-' + counter;
			}

			// Fade in
			rule.style.display = '';
			rule.style.opacity = '0';
			rule.style.transition = 'opacity 0.4s';
			setTimeout(function() {
				rule.style.opacity = '1';
			}, 10);
		});
	}

	var rulesContainer = document.querySelector('.rules');
	if (rulesContainer) {
		rulesContainer.addEventListener('click', function(e) {
			var deleteBtn = e.target.closest('.delete-rule');
			if (!deleteBtn) {
				return;
			}
			e.preventDefault();

			var ruleEl = deleteBtn.closest('.rule');
			if (ruleEl) {
				ruleEl.style.transition = 'opacity 0.4s';
				ruleEl.style.opacity = '0';
				setTimeout(function() {
					ruleEl.remove();
				}, 400);
			}
		});
	}
});
