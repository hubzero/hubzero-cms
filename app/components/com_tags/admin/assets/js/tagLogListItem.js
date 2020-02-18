
var HUB = HUB || {}

HUB.TAGS = HUB.TAGS || {}

class TagLogListItem {

	constructor({log}) {
		this.log = log
	}

	getHtml() {
		const logListItem = document.createElement('li')
		const itemSpan = document.createElement('span')

		itemSpan.className = 'entry-log-data'
		itemSpan.textContent = this.log.parsedDescription
		logListItem.className = this.log.htmlClass
		logListItem.setAttribute('data-id', this.log.id)
		logListItem.appendChild(itemSpan)

		return logListItem
	}

}

HUB.TAGS.TagLogListItem = TagLogListItem

