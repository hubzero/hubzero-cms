/**
 * @package		Data view component
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright		Copyright 2012-2015 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2012-2015 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

(function($) {

	$.jqplot.DataViewFn = function() {
		this.show = false;
		this.color = '#ff0000';
		this.renderer = new $.jqplot.LineRenderer();
		this.rendererOptions = {marker:{show:false}};
		this.label = 'some line';
		this.type = 'simple';
		this.shadow = true;
		this.markerRenderer = {show:false};
		this.lineWidth = 1.5;
		this.shadowAngle = 45;
		this.shadowOffset = 1.0;
		this.shadowAlpha = 0.07;
		this.shadowDepth = 3;
		this.isDataViewFn = true;

	};

	$.jqplot.postSeriesInitHooks.push(parseDataViewFnOptions);
	$.jqplot.postDrawSeriesHooks.push(drawDataViewFn);
	$.jqplot.addLegendRowHooks.push(addDataViewFnLegend);

	function addDataViewFnLegend(series) {
		var lt = series.dataviewfn.label.toString();
		var ret = null;
		if (this.renderer.constructor != $.jqplot.PieRenderer && series.dataviewfn.show && lt) {
			ret = {label:lt, color: '#ff0000'};
		}
		return ret;
	}

	// called within scope of a series
	function parseDataViewFnOptions (target, data, seriesDefaults, options, plot) {
		if (this.renderer.constructor == $.jqplot.LineRenderer || this.renderer.constructor == $.jqplot.BarRenderer) {
			this.dataviewfn = new $.jqplot.DataViewFn();
			options = options || {};
			$.extend(true, this.dataviewfn, {color:this.color}, seriesDefaults.dataviewfn, options.dataviewfn);
			this.dataviewfn.renderer.init.call(this.dataviewfn, null);
		}
	}

	// called within scope of series object
	function drawDataViewFn(sctx, options) {
		// if we have options, merge dataviewfn options in with precedence
		options = $.extend(true, {}, this.dataviewfn, options);
		if (typeof this.dataviewfn.renderer == 'undefined') {
			this.dataviewfn.renderer = new $.jqplot.LineRenderer();
		}
		if (options.show && this.renderer.constructor != $.jqplot.PieRenderer) {
			for (var i=0; i<this.dataviewfn.func.length; i++) {
				var fit;

				var data = options.data || this.data;
				fit = fitData(data, this.dataviewfn.func[i]['fn'], this.dataviewfn.type);
				var gridData = options.gridData || this.renderer.makeGridData.call(this, fit.data);

				this.dataviewfn.renderer.draw.call(this.dataviewfn, sctx, gridData, {showLine:true, color: this.dataviewfn.func[i]['color'], shadow:this.dataviewfn.shadow});
			}
		}
	}

	function fitData(data, func, type) {
		var ret;
		var xy = [];

		if (type == 'simple') {
			for (i=0; i<data.length; i++){
				if (data[i] != null && data[i][0] != null && data[i][1] != null) {
					var x = data[i][0];
					var y = data[i][1];
					var pow = Math.pow;
					xy.push([x, eval(func)]);
				}
			}
		} else {
			eval(func);
		}

		ret = [xy[0], xy[0]];

		return {data: xy, slope: ret[0], intercept: ret[1]};
	}
})(jQuery);
