
(function($) {

	$.fn.passwordstrength = function(target, options) {

		var pluginName = 'passwordstrength',
			defaults = {
				threshold: 66,
				primer: '',
				height: 5,
				opacity: 1,
				bgcolor: 'transparent',
				onUpdate: null
			},
			_DEBUG = false;

		var options = $.extend({}, defaults, {target:target}, options);

		return this.each(function() {
			// save this to self because this changes when scope changes
			var self = $(this);

			if (options.primer) {
				options.threshold = strength(options.primer);
			}

			var coord = $(this).position();

			var bar = $('<div class="passwordstrength-bar"></div>').css({
				'position': 'absolute',
				'top': coord.top + coord.height,
				'left': coord.left,
				'width': coord.width,
				'height': options.height,
				'opacity': options.opacity,
				'background-color': options.bgcolor
			});
			$(this).after(bar);

			var meter = $('<div class="passwordstrength-meter"></div>')
				.css('width', 0)
				.css('height', '100%');
			bar.append(meter);

			$(this).on('keyup change', animate(meter));

			if ($(this).val()) {
				animate(meter);
			}

			function animate(meter) {
				var value = self.val();
				var color,
					strngth = strength(value),
					ratio = Math.round(strngth / options.threshold, 2); //.limit(0, 1);

				if (ratio < 0.5) {
					color = 'rgb(255, ' + Math.round(255 * ratio * 2) + ', 0)';
				} else {
					color = 'rgb(' + (255 * Math.round(1 - ratio) * 2) + ', 255, 0)';
				}
				meter.css({
					'width': 100 * ratio,
					'background-color': color
				});
			}

			function strength(str){
				var n = 0;
				if (str.match(/\d/)) n += 10;
				if (str.match(/[a-z]+/)) n += 26;
				if (str.match(/[A-Z]+/)) n += 26;
				if (str.match(/[^\da-zA-Z]/)) n += 33;
				return (n == 0) ? 0 : (str.length * n.log() / (2).log()).round();
			}
		});
	};

})(jQuery);