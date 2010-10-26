/**
* @version		$Id: searchables.js 81 2009-06-05 16:31:31Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

var Searchables = new Class({
	getOptions : function(){
		return {
			onFind: Class.empty
		};
	},	
	initialize : function(input, list, items, options){
		this.setOptions(this.getOptions(), options);
		var i = $(input), x = [];
		var scroller = new Fx.Scroll($(list), {
			wait: false,
			duration: 1000
		});
		i.addEvent('keyup', function(e){
			var s = i.value;
			if(/[a-z0-9_\.-]/i.test(s)){
				$(items).getChildren().each(function(el){
					var f = string.basename(el.title).substring(0, s.length); 
					if(f.toLowerCase() == s.toLowerCase()){
						x.include(el);
					}else{	
						x.remove(el);
					}
				}.bind(this));
			}else{
				x = [];	
			}
			if(x.length){
				scroller.toElement(x[0]);
			}else{
				scroller.toTop();	
			}
			this.fireEvent('onFind', [x]);
		}.bind(this));
	}					
});
Searchables.implement(new Events, new Options);