
var HUB = HUB || {}

TagActivityLog = HUB.TAGS.TagActivityLog
TagLogListItem = HUB.TAGS.TagLogListItem

let activityLog,
	logContainer,
	tagId,
	olderLogsExist
let rendering = false
const activityLogId = 'entry-log'
const logEntryIdAttribute = 'data-id'
const tagIdInputName = 'fields[id]'

const getLogContainer = () => {
	getActivityLog()

	if (!logContainer) {
		logContainer = activityLog.parentElement
	}
}

const getActivityLog = () => {
	if (!activityLog) {
		activityLog = document.getElementById(activityLogId)
	}
}

const logContainerScroll = (e) => {
	const totalHeight = logContainer.scrollHeight
	const scrollTop = logContainer.scrollTop
	const height = logContainer.clientHeight

	if (!rendering && scrollTop + (height * 1.5) >= totalHeight) {
		rendering = true
		renderOlderLogs()
	}
}

const renderOlderLogs = () => {
	fetchOlderLogs().then((response) => {
		let olderLogs = response.logs ? response.logs : []

		rendering = false

		appendLogs(olderLogs)
	})
}

const fetchOlderLogs = () => {
	const tagId = getTagId()
	const lastLogId = getLastLogId()

	return TagActivityLog.fetchPreviousLogs({tagId, logId: lastLogId})
}

const getTagId = () => {
	const tagIdInput = document.getElementsByName(tagIdInputName).item(0)

	return tagIdInput.getAttribute('value')
}

const getLastLogId = () => {
	const lastLogRecord = getLastLogRecord()

	return lastLogRecord.getAttribute(logEntryIdAttribute)
}

const getLastLogRecord = () => {
	const logRecords = activityLog.children
	const logRecordsCount = logRecords.length

	return logRecords[(logRecordsCount - 1)]
}

const appendLogs = (logs) => {
	let logListItem

	logs.forEach((log) => {
		logListItem = buildLogListItem(log)

		appendLogListItem(logListItem)
	})
}

const buildLogListItem = (log) => {
	const logListItem = new TagLogListItem({log})

	return logListItem.getHtml()
}

const appendLogListItem = (logListItem) => {
	activityLog.appendChild(logListItem)
}

$(document).ready(() => {
	getLogContainer()

	logContainer.addEventListener('scroll', logContainerScroll)
})
