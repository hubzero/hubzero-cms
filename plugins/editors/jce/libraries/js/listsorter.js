/**
* @version		$Id: listsorter.js 49 2009-05-28 10:02:46Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
* @author		Ryan Demmer
* @license      GNU/GPL
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
var ListSorter = new Class({
	getOptions : function(){
		return {
			onSort: Class.empty
		};
	},	
	initialize : function(sorter, type, lists, options){
		this.setOptions(this.getOptions(), options);
		this.sorter = $(sorter);
		
		this.sorter.addClass('asc').addEvent('click', function(event){
			this.sortList(type, lists);
		}.bind(this))
	},
	sortList : function(type, lists){
		s = this.sorter.hasClass('asc') ? 'desc' : 'asc';
		this.sorter.className = s;

		lists.each(function(list){
			this.sortItems($(list), s, type);		
		}.bind(this));
	},
	sortItems : function(o, s, t){
		var a = this.getSortCache(o, t);	
		a.sort(this.sortCompare);
	
		if(s == 'desc' || t == 'ext') a.reverse();
		
		// remove from doc
		o.getChildren().each(function(el){
			o.removeChild(el);   
		});		
		// insert in the new order
		a.each(function(el){
			o.appendChild(el.element);
		});
		this.destroySortCache(a);
		this.fireEvent('onSort');
	},
	sortCompare : function(n1, n2){
		if(n1.value < n2.value){
			return -1;
		}
		if(n2.value < n1.value){
			return 1;
		}
		return 0;
	},
	getSortCache : function(o, t){
		var a = [], v, x = 0;
		$ES('li', o).each(function(el){
			v = el.title.toLowerCase();
			if(t == 'ext'){
				v = string.getExt(v);
			}
			a[x] = {
				value:   v,
				element: el
			};
			x++;						   
		}.bind(this));
		return a;
	},
	destroySortCache : function(o){
		o.each(function(el){
			el.value = null;
			el.element = null;
			el = null;
		});
	}			 
});
ListSorter.implement(new Events, new Options);