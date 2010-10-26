/**
 * @version		$Id: tree.js 81 2009-06-05 16:31:31Z happynoodleboy $
 * @package      JCE
 * @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
 * @author		Ryan Demmer
 * @license      GNU/GPL
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
/*
 Class: Tree
 Note:
 The Sortables require an XHTML doctype.
 Arguments:
 container 	- the container element
 options 	- an Object, see options below.
 Options:
 rootName		- the root name. Defaults to 'Root'
 rootClass		- the root node class. Defaults to 'root'
 collapseTree	- collapse all other tree nodes on the same level when one is opened. Defaults to false.
 useLink			- Use the link href
 Events:
 onNodeToggle 	- function executed when a node is toggled
 onNodeClick 	- function executed when a node is clicked
 onNodeLoad 		- function executed when a node is loaded
 onNodeCreate 	- function executed when a node is created
 */
var Tree = new Class({
    getOptions: function() {
        return {
            rootName: 'Root',
            rootClass: 'root',
            loaderClass: 'load',
            collapseTree: false,
            charLength: false,
            onInit: Class.empty,
            onNodeClick: Class.empty,
            onNodeLoad: Class.empty,
            onNodeCreate: Class.empty
        };
    },
    initialize: function(container, options) {
        this.setOptions(this.getOptions(), options);
        this.container = $(container);
        
        this.fireEvent('onInit', function() {
            this.nodeEvents();
        }.bind(this));
        
        return this;
    },
    nodeEvents: function(parent) {
        if (!parent) {
            parent = this.container;
        }
        
        $ES('div.tree-row', parent).each(function(el) {
            el.addEvent('mouseover', function() {
                el.addClass('hover');
            })
            el.addEvent('mouseout', function() {
                el.removeClass('hover');
            })
        });
        
        $ES('div.tree-image', parent).each(function(el) {
            var p = this.findParent(el);
            el.addEvent('click', function(e) {
                this.toggleNode(e, p);
            }.bind(this))
            if (this.getNode(p)) {
                el.addClass('open');
            }
        }.bind(this));
        
        $ES('span', this.container).each(function(el) {
            if (window.ie) {
                el.onselectstart = function() {
                    return false;
                }
            }
            if (window.gecko) {
                el.setStyle('moz-user-select', 'none');
            }
            if (this.getNode(el.getParent())) {
                el.addClass('open');
            }
        }.bind(this));
        
        $ES('a', this.container).each(function(el) {
            var p = this.findParent(el);
            el.addEvent('click', function(e) {
                this.fireEvent('onNodeClick', [e, p]);
            }.bind(this))
        }.bind(this));
    },
    /**
     * Does a parent (ul) have childnodes
     * @param {String} The parent
     * @return {Boolean}.
     */
    hasNodes: function(parent) {
        if ($type(parent) == 'string') {
            parent = this.findParent(parent);
        }
        var c = parent.childNodes;
        return c.length > 1 || (c.length == 1 && c[0].className != 'spacer');
    },
    /**
     * Does the node exist?
     * @param {String} The node title
     * @param {String or Element} The parent node
     * @return {Boolean}.
     */
    isNode: function(id, parent) {
        return this.findNode(id, parent) ? true : false;
    },
    /**
     * Does a parent have subnodes?
     * @param {String or Element} The parent node
     * @return {Boolean}.
     */
    getNode: function(parent) {
        if ($type(parent) == 'string') {
            parent = this.findParent(parent);
        }
        return $E('ul.tree-node', parent);
    },
    /**
     * Reset all nodes. Set to closed
     */
    resetNodes: function() {
        $ES('span', this.container).each(function(el) {
            el.removeClass('open');
        });
        $ES('div.tree-image', this.container).each(function(el) {
            el.removeClass('open');
        });
    },
    /**
     * Rename a node
     * @param {String} The node title
     * @param {String} The new title
     */
    renameNode: function(id, name) {
        var parent = string.dirname(id);
        var node = this.findNode(id, parent);
        // Rename the node
        node.setProperty('id', name);
        // Rename the span
        $E('a', node).setHTML(string.basename(name));
        // Rename each of the child nodes
        $ES('li[id^=' + this._escape(encodeURI(id)) + ']', node).each(function(n) {
            var nt = n.getProperty('id');
            n.setProperty('id', nt.replace(id, name));
        });
    },
    /**
     * Remove a node
     * @param {String} The node title
     */
    removeNode: function(id) {
        var parent = string.dirname(id);
        var node = this.findNode(id, parent);
        var ul = node.getParent();
        // Remove the node
        node.remove();
        // Remove it if it is now empty
        if (ul && !this.hasNodes(ul)) {
            ul.remove();
        }
    },
    /**
     * Create a node <ul></ul>
     * @param {String or Element} The parent node
     * @return {Array} An array of nodes to create.
     */
    createNode: function(nodes, parent) {
        var e, p, h, l, np, i;
        // If parent is not an element, find the parent element
        if (!parent) {
            parent = string.dirname(nodes[0].id);
        }
        if ($type(parent) == 'string') {
            parent = this.findParent(parent);
        }
        /* Create the nodes from the array
         * <li><div class="tree-row"><div class="tree-image"></div><span><a>node</a></span><div></li>
         */
        if (nodes && nodes.length) {
            // Get parent ul
            var ul = $E('ul.tree-node', parent);
            // Create it if it doesn't exist
            if (!ul) {
                ul = new Element('ul').addClass('tree-node').adopt(new Element('li').addClass('spacer')).injectInside(parent);
            }
            
            // Iterate through nodes array
            nodes.each(function(node) {				
				if (!this.isNode(node.id, parent)) {
					// Set default
                    if (!node['class']) {
                        node['class'] = 'folder';
                    }
                    // title and link html
                    t = node.name || node.id;
                    // decode
                    t = string.decode(t);
                    h = t;
                    l = this.options.charLength;
                    // shorten
                    if (l) {
                        if (h.length > l) {
                            h = h.substring(0, l) + '...';
                        }
                    }
                    ul.adopt(new Element('li', {
                        'id': this._escape(encodeURI(node.id))
                    }).adopt(new Element('div', {
                        'class': 'tree-row',
                        events: {
                            mouseover: function() {
                                this.addClass('hover');
                            },
                            mouseout: function() {
                                this.removeClass('hover');
                            }
                        }
                    }).adopt(new Element('div', {
                        'class': node['class'].contains('folder') ? 'tree-image' : 'tree-noimage',
                        events: {
                            'click': function(e) {
                                e = new Event(e);
                                p = this.findParent(e.target);
                                this.toggleNode(e, p);
                            }.bind(this)
                        }
                    })).adopt(new Element('span', {
                        'class': node['class']
                    }).adopt(new Element('a', {
                        'href': !node.url ? 'javascript:;' : node.url,
                        'tabindex': '1',
                        'title': t,
                        events: {
                            'click': function(e) {
                                e = new Event(e);
                                p = this.findParent(e.target);
                                this.fireEvent('onNodeClick', [e, p]);
                                e.stop();
                            }.bind(this)
                        }
                    }).setHTML(h)))))
                    this.toggleNodeState(parent, 1);
                    this.fireEvent('onNodeCreate');
                } else {
                    // Node exists, set as open
                    this.toggleNodeState(parent, 1);
                }
            }.bind(this))
        } else {
            // No new nodes, set as open
            this.toggleNodeState(parent, 1);
        }
    },
    /**
     * Find the parent node
     * @param {String} The child node id
     * @return {Element} The parent node.
     */
    findParent: function(el) {
        if ($type(el) == 'element') {
            n = el.parentNode;
            while (n) {
                if (n.nodeName == 'LI') {
                    return n;
                }
                n = n.parentNode;
            }
        } else {
            
			return $E('li[id=' + this._encode(el) + ']', this.container);
        }
    },
    /**
     * Find a node by id
     * @param {String} The child node title
     * @param {String / Element} The parent node
     * @return {Element} The node.
     */
    findNode: function(id, parent) {
        if (!parent || parent == '/') {
            parent = this.container;
        }
        if ($type(parent) == 'string') {
            parent = this.findParent(parent);
        }
		
        return $E('li[id=' + this._escape(this._encode(id)) + ']', parent) || false;
    },
    /**
     * Toggle the loader class on the node span element
     * @param {Element} The target node
     */
    toggleLoader: function(node) {
        var span = $E('span', node), cls = this.options.loaderClass;
        
        if (!span.hasClass(cls)) {
            span.$tmp = span.className || '';
            span.className = cls;
        } else {
            span.removeClass(cls).addClass(span.$tmp);
            span.$tmp = null;
        }
    },
    /**
     * Collapse all tree nodes except one excluded
     * @param {Element} The excluded node
     */
    collapseNodes: function(ex) {
        if (!ex) 
            this.resetNodes();
        var parent = ex.getParent();
        
        $ES('li', parent).each(function(el) {
            if (el != ex) {
                if (el.getParent() == parent) {
                    this.toggleNodeState(el, 0);
                    var child = this.getNode(el);
                    if (child) {
                        child.addClass('hide');
                    }
                }
            }
        }.bind(this));
    },
    /**
     * Toggle a node's state, open or closed
     * @param {Element} The node
     */
    toggleNodeState: function(node, state) {
        var span = $E('span', node);
        var div = $E('div.tree-image', node);
        
        [span, div].each(function(el) {
            if (el) {
                if (state == 1) {
                    el.addClass('open');
                } else 
                    if (state == 0) {
                        el.removeClass('open');
                    } else {
                        el.toggleClass('open');
                    }
            }
        });
        if (state == 1) {
            if (node.id == '/') {
                return;
            }
            var c = $E('ul.tree-node', node);
            if (c) {
                if (span.hasClass('open')) {
                    c.removeClass('hide');
                } else {
                    c.addClass('hide');
                }
            }
        }
    },
    /**
     * Toggle a node
     * @param {Element} The node
     */
    toggleNode: function(e, node) {
        e = new Event(e);
        // Force reload
        if (e.shift) {
            return this.fireEvent('onNodeLoad', node);
        }
        var child = this.getNode(node);
        // No children load or close
        if (!child) {
            if ($E('div.tree-image', node).hasClass('open')) {
                this.toggleNodeState(node);
            } else {
                this.fireEvent('onNodeLoad', node);
            }
            // Hide children, toggle node
        } else {
            child.toggleClass('hide');
            this.toggleNodeState(node);
        }
        // Collpase the all other tree nodes
        if (this.options.collapseTree) {
            this.collapseNodes(node);
        }
    },
	_encode : function(s) {
		// decode first in case already encoded
		s = decodeURIComponent(s);
		// encode but decode backspace
		return encodeURIComponent(s).replace(/%2F/gi, '\/');
	},
    /**
     * Private function
     * Escape a string
     * @param {String} The string
     * @return {String} The escaped string
     */
    _escape: function(s) {
        return s.replace(/'/, '%27');
    }
});
Tree.implement(new Events, new Options);
