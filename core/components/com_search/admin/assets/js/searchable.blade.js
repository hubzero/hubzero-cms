/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

var activeProcesses = 0;

document.addEventListener('DOMContentLoaded', function () {
	document.addEventListener('click', function (e) {
		var link = e.target.closest('.unpublishtask');
		if (!link) {
			return;
		}
		var td = link.closest('td');
		if (!td) {
			return;
		}

		e.preventDefault();

		if (link.getAttribute('disabled') !== 'disabled') {
			activeProcesses++;
			var url = link.getAttribute('href');
			link.setAttribute('disabled', 'disabled');
			link.classList.remove('unpublished');
			link.classList.remove('state');
			link.textContent = 'Indexing, please wait...';
			link.setAttribute('data-current', 0);
			indexResults(url, link);
		}
	});

	window.addEventListener('beforeunload', function (e) {
		if (activeProcesses > 0) {
			e.preventDefault();
			e.returnValue = 'Please let the current process finish before leaving the page';
			return e.returnValue;
		}
	});
});

function indexResults(url, link, limit, offset, numprocess) {
	var params = new URLSearchParams();
	if (offset !== undefined) {
		params.append('offset', offset);
	}
	if (limit !== undefined) {
		params.append('limit', limit);
	}
	if (numprocess !== undefined) {
		params.append('numprocess', numprocess);
	}

	var fetchUrl = url;
	var paramStr = params.toString();
	if (paramStr) {
		fetchUrl += (url.indexOf('?') === -1 ? '?' : '&') + paramStr;
	}

	fetch(fetchUrl, {
		headers: {
			'X-Requested-With': 'XMLHttpRequest'
		}
	})
	.then(function (response) {
		return response.json();
	})
	.then(function (response) {
		if (response.state != 1 && response.error === undefined) {
			var currentProcess = parseInt(link.getAttribute('data-current'), 10);
			currentProcess++;
			link.setAttribute('data-current', currentProcess);
			console.log('Indexed ' + currentProcess + ' of ' + response.numprocess);
			link.textContent = 'Indexed ' + currentProcess + ' of ' + response.numprocess;
			indexResults(url, link, response.limit, response.offset, response.numprocess);
		} else if (response.error) {
			activeProcesses--;
			location.reload();
		} else {
			var td = link.parentNode;
			if (link.getAttribute('data-linktext')) {
				var buttonText = link.getAttribute('data-linktext');
				link.textContent = buttonText;
			} else {
				var rebuildLink = link.cloneNode(true);
				rebuildLink.textContent = 'Rebuild Index';
				rebuildLink.classList.add('button');
				rebuildLink.removeAttribute('disabled');
				rebuildLink.setAttribute('data-linktext', 'Rebuild Index');

				var tasksCell = td.parentNode.querySelector('.tasks');
				if (tasksCell) {
					tasksCell.appendChild(rebuildLink);
				}

				link.textContent = '';
				link.classList.add('state');
				link.classList.add('published');
				link.classList.remove('unpublishtask');
				link.setAttribute('href', response.link);
			}

			var totalCell = td.parentNode.querySelector('.total');
			if (totalCell) {
				totalCell.innerHTML = response.total;
			}

			link.removeAttribute('disabled');
			activeProcesses--;
		}
	})
	.catch(function (error) {
		console.error('Index request failed:', error);
		activeProcesses--;
	});
}
