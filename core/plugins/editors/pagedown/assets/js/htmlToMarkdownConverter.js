/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

var HUB = HUB || {}

HUB.PageDown = HUB.PageDown || {}

class HtmlConverter {

	constructor() {
		this.markdownConverter = new TurndownService()
	}

	toMarkdown(html) {
		const markdown = this.markdownConverter.turndown(html)

		return markdown
	}

}

HUB.PageDown.HtmlConverter = HtmlConverter
