/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

var HUB = HUB || {};

HUB.TAGS = HUB.TAGS || {};

var TagActivityLog = {
	apiEndpoint: 'v2.0/tags/tagactivitylogs',

	api: null,

	getApi: function() {
		if (!this.api) {
			this.api = HUB.TAGS.Api;
		}
		return this.api;
	},

	fetchPreviousLogs: function(opts) {
		var tagId = opts.tagId;
		var logId = opts.logId;
		var limit = opts.limit || 50;
		var endpoint = this.apiEndpoint + '/previouslogs';

		return this.getApi().get(
			endpoint,
			{ tagId: tagId, logId: logId, limit: limit }
		);
	}
};

HUB.TAGS.TagActivityLog = TagActivityLog;
