window.addEvent('domready', function(){

	/* Tips 1 */
	var Tips1 = new Tips($$('.Tips1'));
	 
	/* Tips 2 */
	var Tips2 = new Tips($$('.Tips2'), {
		initialize:function(){
			this.fx = new Fx.Style(this.toolTip, 'opacity', {duration: 500, wait: false}).set(0);
		},
		onShow: function(toolTip) {
			this.fx.start(1);
		},
		onHide: function(toolTip) {
			this.fx.start(0);
		}
	});
	 
	/* Tips 3 */
	var Tips3 = new Tips($$('.Tips3'), {
		showDelay: 400,
		hideDelay: 400,
		fixed: true
	});
	 
	/* Tips 4 */
	var Tips4 = new Tips($$('.Tips4'), {
		className: 'custom'
	});

});