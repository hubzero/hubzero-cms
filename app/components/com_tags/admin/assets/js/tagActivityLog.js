
var HUB = HUB || {}

HUB.TAGS = HUB.TAGS || {}

class TagActivityLog {

	static get apiEndpoint() {
		return 'v2.0/tags/tagactivitylogs'
	}

	static get api() {
		return HUB.TAGS.Api
	}

	static fetchPreviousLogs({tagId, logId, limit = 50}) {
		const endpoint = `${this.apiEndpoint}/previouslogs`

		return this.api.get(endpoint, {tagId, logId, limit})
	}

}

HUB.TAGS.TagActivityLog = TagActivityLog
