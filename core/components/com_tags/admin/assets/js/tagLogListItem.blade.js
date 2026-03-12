/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

var HUB = HUB || {};

HUB.TAGS = HUB.TAGS || {};

function TagLogListItem(opts) {
	this.log = opts.log;
}

TagLogListItem.prototype.getHtml = function() {
	var logListItem = document.createElement('li');
	var itemSpan = document.createElement('span');

	itemSpan.className = 'entry-log-data';
	itemSpan.textContent = this.log.parsedDescription;
	logListItem.className = this.log.htmlClass;
	logListItem.setAttribute('data-id', this.log.id);
	logListItem.appendChild(itemSpan);

	return logListItem;
};

HUB.TAGS.TagLogListItem = TagLogListItem;
