/**
 * [a11y] Remove role="presentation" from reCAPTCHA iframes.
 *
 * Google's reCAPTCHA JS injects iframes with role="presentation",
 * which hides interactive content from assistive technologies.
 * This observer removes that attribute as soon as the iframe appears.
 */
(function () {
	var containers = document.querySelectorAll(".g-recaptcha");
	for (var i = 0; i < containers.length; i++) {
		(function (container) {
			var observer = new MutationObserver(function () {
				var frames = container.querySelectorAll('iframe[role="presentation"]');
				for (var j = 0; j < frames.length; j++) {
					frames[j].removeAttribute("role");
				}
				if (frames.length) {
					observer.disconnect();
				}
			});
			observer.observe(container, { childList: true, subtree: true });
		})(containers[i]);
	}
})();
