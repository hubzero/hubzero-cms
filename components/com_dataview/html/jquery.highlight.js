/*
* Modified (for dataview) version of jquery.highlight by 
* Johann Burkard [http://johannburkard.de, jb@eaio.com]
* (original @ : http://johannburkard.de/resources/Johann/jquery.highlight-3.js)
* MIT license.
*/

jQuery.fn.highlight = function(pat, style_class) {
	function innerHighlight(node, pat, h_type) {
		var skip = 0;
		if (node.nodeType == 3) {
			var pos = node.data.toUpperCase().indexOf(pat);
			if (pos >= 0) {
				var spannode = document.createElement('span');
				spannode.className = style_class;
				var middlebit = node.splitText(pos);
				var endbit = middlebit.splitText(pat.length);
				var middleclone = middlebit.cloneNode(true);
				spannode.appendChild(middleclone);
				middlebit.parentNode.replaceChild(spannode, middlebit);
				skip = 1;
			}
		} else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
			for (var i = 0; i < node.childNodes.length; ++i) {
				i += innerHighlight(node.childNodes[i], pat);
			}
		}
	return skip;
	}
	
	return this.each(function() {
		innerHighlight(this, pat.toUpperCase());
	});
};

jQuery.fn.removeHighlight = function(style_class) {
	return this.find('span.' + style_class).each(function() {
		this.parentNode.firstChild.nodeName;
		with (this.parentNode) {
			replaceChild(this.firstChild, this);
			
			normalize();
		}
	}).end();
};

(function($){
  $.fn.highlightRegex = function(regex, style) {
    if(regex == undefined || regex.source == '') {
      $(this).find('span.' + style).each(function(){
        $(this).replaceWith($(this).text());
        $(this).parent().each(function(){
          node = $(this).get(0);
          if(node.normalize) node.normalize();
        });
      });
    } else {
      $(this).each(function(){
        elt = $(this).get(0)
        elt.normalize();
        $.each($.makeArray(elt.childNodes), function(i, node){
          if(node.nodeType == 3) {
            var searchnode = node
            while((pos = searchnode.data.search(regex)) >= 0) {
              match = searchnode.data.slice(pos).match(regex)[0];
              if(match.length == 0) break;
              var spannode = document.createElement('span');
              spannode.className = style;
              var middlebit = searchnode.splitText(pos);
              var searchnode = middlebit.splitText(match.length);
              var middleclone = middlebit.cloneNode(true);
              spannode.appendChild(middleclone);
              searchnode.parentNode.replaceChild(spannode, middlebit);
            }
          } else {
            $(node).highlightRegex(regex);
          }
        })
      })
    }
    return $(this);
  }
})(jQuery);
