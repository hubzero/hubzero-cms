/**
 * mod_supportactivity — Blade view auto-refresh
 *
 * Polls the feed endpoint every 60 seconds and prepends new activity items.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
document.addEventListener('DOMContentLoaded', function () {
	var containers = document.querySelectorAll('[data-feed-url]');

	containers.forEach(function (container) {
		var feedUrl = container.getAttribute('data-feed-url');
		if (!feedUrl) {
			return;
		}

		var categoryIcons = {
			ticket:  '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
			comment: '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
			change:  '<polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>'
		};

		var categoryColors = {
			ticket:  'text-info',
			comment: 'text-primary',
			change:  'text-muted-foreground'
		};

		function getLatestTimestamp() {
			var first = container.querySelector('time[datetime]');
			return first ? first.getAttribute('datetime') : '';
		}

		function parseItems(html) {
			var parser = new DOMParser();
			var doc = parser.parseFromString('<ul>' + html + '</ul>', 'text/html');
			var items = [];

			doc.querySelectorAll('li').forEach(function (li) {
				var link = li.querySelector('a');
				var time = li.querySelector('time');
				if (!link || !time) {
					return;
				}

				items.push({
					href:     link.getAttribute('href'),
					category: li.className || 'change',
					created:  time.getAttribute('datetime') || '',
					text:     (li.querySelector('.activity-event') || {}).textContent || '',
					time:     (li.querySelector('.activity-time time') || {}).textContent || '',
					date:     (li.querySelector('.activity-date time') || {}).textContent || ''
				});
			});

			return items;
		}

		function buildItemHtml(item) {
			var cat = item.category;
			var icon = categoryIcons[cat] || categoryIcons.change;
			var colorCls = categoryColors[cat] || 'text-muted-foreground';

			return '<a href="' + escapeHtml(item.href) + '"'
				+ ' class="flex items-start gap-2 px-2 py-1.5 rounded hover:bg-base-200 transition-colors group">'
				+ '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"'
				+ ' fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"'
				+ ' stroke-linejoin="round" class="shrink-0 mt-0.5 ' + colorCls + '">'
				+ icon + '</svg>'
				+ '<div class="flex-1 min-w-0">'
				+ '<div class="text-xs truncate">' + escapeHtml(item.text) + '</div>'
				+ '<div class="text-[0.65rem] opacity-50">'
				+ '<time datetime="' + escapeHtml(item.created) + '">'
				+ escapeHtml(item.time) + ' &middot; ' + escapeHtml(item.date)
				+ '</time></div></div></a>';
		}

		function escapeHtml(str) {
			var el = document.createElement('span');
			el.textContent = str;
			return el.innerHTML;
		}

		function poll() {
			var start = getLatestTimestamp();
			if (!start) {
				return;
			}

			fetch(feedUrl + encodeURIComponent(start), {
				credentials: 'same-origin'
			})
			.then(function (res) { return res.text(); })
			.then(function (html) {
				if (!html || !html.trim()) {
					return;
				}

				var items = parseItems(html);
				if (!items.length) {
					return;
				}

				var fragment = document.createDocumentFragment();
				items.forEach(function (item) {
					var wrapper = document.createElement('div');
					wrapper.innerHTML = buildItemHtml(item);
					var el = wrapper.firstElementChild;
					if (el) {
						el.style.opacity = '0';
						fragment.appendChild(el);
					}
				});

				container.prepend(fragment);

				// Fade in new items
				requestAnimationFrame(function () {
					container.querySelectorAll('[style*="opacity: 0"]').forEach(function (el) {
						el.style.transition = 'opacity 0.3s ease';
						el.style.opacity = '1';
					});
				});
			})
			.catch(function () {
				// Silently ignore fetch errors
			});
		}

		setInterval(poll, 60 * 1000);
	});
});
