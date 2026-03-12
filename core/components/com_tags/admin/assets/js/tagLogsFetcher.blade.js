/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

var HUB = HUB || {};

(function() {
	var activityLog = null;
	var logContainer = null;
	var rendering = false;
	var activityLogId = 'entry-log';
	var logEntryIdAttribute = 'data-id';
	var tagIdInputName = 'fields[id]';

	var TagActivityLog = HUB.TAGS.TagActivityLog;
	var TagLogListItem = HUB.TAGS.TagLogListItem;

	function getLogContainer() {
		getActivityLog();

		if (!logContainer && activityLog) {
			logContainer = activityLog.parentElement;
		}
	}

	function getActivityLog() {
		if (!activityLog) {
			activityLog = document.getElementById(activityLogId);
		}
	}

	function logContainerScroll() {
		var totalHeight = logContainer.scrollHeight;
		var scrollTop = logContainer.scrollTop;
		var height = logContainer.clientHeight;

		if (!rendering && scrollTop + (height * 1.5) >= totalHeight) {
			rendering = true;
			renderOlderLogs();
		}
	}

	function renderOlderLogs() {
		fetchOlderLogs().then(function(response) {
			var olderLogs = response.logs ? response.logs : [];

			rendering = false;

			appendLogs(olderLogs);
		});
	}

	function fetchOlderLogs() {
		var tagId = getTagId();
		var lastLogId = getLastLogId();

		return TagActivityLog.fetchPreviousLogs({
			tagId: tagId,
			logId: lastLogId
		});
	}

	function getTagId() {
		var tagIdInput = document.getElementsByName(tagIdInputName).item(0);

		return tagIdInput.getAttribute('value');
	}

	function getLastLogId() {
		var lastLogRecord = getLastLogRecord();

		return lastLogRecord.getAttribute(logEntryIdAttribute);
	}

	function getLastLogRecord() {
		var logRecords = activityLog.children;
		var logRecordsCount = logRecords.length;

		return logRecords[(logRecordsCount - 1)];
	}

	function appendLogs(logs) {
		var logListItem;

		logs.forEach(function(log) {
			logListItem = buildLogListItem(log);
			appendLogListItem(logListItem);
		});
	}

	function buildLogListItem(log) {
		var logListItem = new TagLogListItem({ log: log });

		return logListItem.getHtml();
	}

	function appendLogListItem(logListItem) {
		activityLog.appendChild(logListItem);
	}

	document.addEventListener('DOMContentLoaded', function() {
		getLogContainer();

		if (logContainer) {
			logContainer.addEventListener('scroll', logContainerScroll);
		}
	});
})();
