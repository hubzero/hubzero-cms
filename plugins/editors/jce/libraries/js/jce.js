
/**
 * @version		$Id: jce.js 49 2009-05-28 10:02:46Z happynoodleboy $
 * @package      JCE
 * @copyright    Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
 * @author		Ryan Demmer
 * @license      GNU/GPL
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
if (!tinymce) 
    document.location.href = 'index.php';

var Editor = {
    xhr: {
        _queue: [],
        /**
         * XHR request. Requires Mootools json.js
         * @param {string} The target function to call
         * @param {array} An array of arguments
         * @param {function} The callback function on success
         * @param {Object} Optional scope for callback
         */
        request: function(fn, args, cb, s) {
            var t = this;
            
            this.timer = false;
			
			function _encode(s) {
				s = decodeURIComponent(s);
				return encodeURIComponent(s).replace(/%2F/gi, '\/');
			}
			
			if (typeof(args) == 'string') {
				if (/\//.test(args)) {
					args = _encode(args);
				}
			} else {
				var o = {};
				if (args.hasOwnProperty && args instanceof Array) {
					o = [];
				}
				
				tinymce.each(args, function(v, k) {
					if (/\//.test(v)) {
						v = _encode(v);
					}
					o[k] = v;
				});
				args = o;
			}
			
            // Add to queue
            this._queue.include({
                fn: fn,
                args: args,
                cb: cb
            });
            
            // Process queue
            if (!this.timer) {
                this.processQueue.delay(500, this, s);
            }
        },
        /**
         * Process XHR queue
         * @param {Object} s Optional scope for callback
         */
        processQueue: function(s) {
            var u = document.location.href;
            
            this._queue.each(function(n, i) {
                var t = this, fn = n.fn, args = n.args, cb = n.cb;
                
                var x = (i > 0) ? 1000 : 0;
                
                window.setTimeout(function() {
                    new Json.Remote(u, {
                        autoCancel: true,
                        onComplete: function(o) {
                            if (o.error) {
                                alert(o.error);
                            }
                            r = o.result ||
                            {
                                error: false
                            };
                            if (cb) {
                                cb.pass(r, s || t)();
                            } else {
                                return r;
                            }
                        },
                        onFailure: function(x) {
                            alert('ERROR STATUS CODE: ' + x.status);
                        }
                    }).send({
                        'fn': fn,
                        'args': args
                    });
                }, x);
                
                this._queue.splice(i, 1);
                // Queue finished, clear timer.
                
                if (this._queue.length == 0) {
                    this.timer = false;
                }
            }.bind(this));
        }
    },
    dom: {
        doc: document,
        /**
         * Shortcut for document.getElementById
         * @param {string/element} The element id or element
         * @return {Element} the target element
         */
        get: function(o) {
            if (typeof o == 'string') {
                o = this.doc.getElementById(o);
            }
            return o;
        },
        /**
         * Attribute getter/setter
         * @param {string/element} The element id or element
         * @param {string} The attribute name
         * @param {string} The attribute value
         * @return {string} Attribute value
         */
        attr: function(o, a, v) {
            if (typeof v != 'undefined') {
                return this.get(o).setAttribute(a, v);
            }
            return this.get(o).getAttribute(a);
        },
        value: function(o, v) {
            var n = this.get(o);
            if (!n) {
                return;
            }
            if (typeof v != 'undefined') {
                if (n.nodeName == 'SELECT') {
                    return this.setSelect(o, v);
                }
				n.value = v;
                return v;
            }
            if (n.nodeName == 'SELECT') {
                return this.getSelect(o);
            }
            return n.value;
        },
        style: function(o, s, v) {
            if (typeof v != 'undefined') {
                this.get(o).style.s = v;
				return v;
            }
            return this.get(o).style.s;
        },
        html: function(o, v) {
            if (typeof v != 'undefined') {
                this.get(o).innerHTML = v;
				return v;
            }
            return this.get(o).innerHTML;
        },
        ischecked: function(o) {
            return this.get(o).checked;
        },
        check: function(o, b) {
            this.get(o).checked = b;
			
			return b;
        },
        disabled: function(o) {
            return this.get(o).disabled ? true : false;
        },
        disable: function(o, b) {
            this.get(o).disabled = b;
			return b;
        },
        hasClass: function(o, c) {
            return tinyMCEPopup.dom.hasClass(o, c);
        },
        setClass: function(o, c) {
            this.get(o).className = c;
			return c;
        },
        addClass: function(o, c) {
            return tinyMCEPopup.dom.addClass(o, c);
        },
        removeClass: function(o, c) {
            return tinyMCEPopup.dom.removeClass(o, c);
        },
        show: function(o) {
            this.get(o).style.display = 'block';
        },
        hide: function(o) {
            this.get(o).style.display = 'none';
        },
        getSelect: function(fn, v) {
            var s = this.get(fn);
            if (!s) {
                return;
            }
            return s.value;
        },
        /** 
         * From TinyMCE form_utils.js function, slightly modified.
         * @author Moxiecode
         * @copyright Copyright � 2004-2008, Moxiecode Systems AB, All rights reserved.
         */
        setSelect: function(fn, v, ac, ic) {
            var s = this.get(fn);
            if (!s) {
                return;
            }
            var found = false;
            for (var i = 0; i < s.options.length; i++) {
                var o = s.options[i];
                
                if (o.value == v || (ic && o.value.toLowerCase() == v.toLowerCase())) {
                    o.selected = true;
                    found = true;
                } else {
                    o.selected = false;
                }
            }
            if (!found && ac && v != '') {
                this.addSelect(fn, v, v, true);
            }
            return found;
        },
        /**
         *  From TinyMCE form_utils.js function, slightly modified.
         * @author Moxiecode
         * @copyright Copyright � 2004-2008, Moxiecode Systems AB, All rights reserved.
         */
        addSelect: function(fn, n, v, s) {
            var o = this.get(fn);
            o.options[o.options.length] = new Option(n, v);
            if (s) {
                o.selectedIndex = o.options.length - 1;
            }
        }
    },
    string: {
        trim: function(s) {
            return tinymce.trim(s);
        },
        basename: function(s) {
            s = s.replace(/\\/g, '/');
            return s.substring(s.length, s.lastIndexOf('/') + 1);
        },
        dirname: function(s) {
            return s.substring(0, s.lastIndexOf('/'));
        },
        filename: function(s) {
            return this.stripExt(this.basename(s));
        },
        getExt: function(s) {
            return s.substring(s.length, s.lastIndexOf('.') + 1).toLowerCase();
        },
        stripExt: function(s) {
            return s.replace(/\.[^.]+$/i, '');
        },
        pathinfo: function(s) {
            var info = {
                'basename': this.basename(s),
                'dirname': this.dirname(s),
                'extension': this.getExt(s),
                'filename': this.filename(s)
            }
            return info;
        },
        path: function(a, b) {
            a = this.clean(a);
            b = this.clean(b);
            
            if (a.substring(a.length - 1) != '/') 
                a += '/';
            
            if (b.charAt(0) == '/') 
                b = b.substring(1);
            
            return a + b;
        },
        clean: function(s) {
            if (!/:\/\//.test(s)) {
                return s.replace(/\/+/g, '/');
            }
            return s;
        },
        safe: function(s) {
            s = s.replace(/(\.){2,}/g, '').replace(/[^a-z0-9\.\_\-\s~]/gi, '').replace(/\s/gi, '_');
            return this.basename(s);
        },
        query: function(s) {
            var p = {};
            if (s) {
                var n = s.split(/[;&?]/);
                for (var i = 0; i < n.length; i++) {
                    var kv = n[i].split('=');
                    if (!kv || kv.length != 2) {
                        continue;
                    }
                    var k = unescape(kv[0]);
                    var v = unescape(kv[1]);
                    v = v.replace(/\+/g, ' ');
                    p[k] = v;
                }
            }
            return p;
        },
        encode: function(s) {
            return tinyMCEPopup.editor.dom.encode(s);
        },
        decode: function(s) {
            return tinyMCEPopup.editor.dom.decode(s).replace(/&apos;/, "'").replace(/&quot;/, '"');
        },
        escape: function(s) {
            return encodeURI(s);
        },
        unescape: function(s) {
            return decodeURI(s);
        },
        /* From TinyMCE form_utils.js function, slightly modified.
         * @author Moxiecode
         * @copyright Copyright � 2004-2008, Moxiecode Systems AB, All rights reserved.
         */
        toHex: function(color) {
            var re = new RegExp("rgb\\s*\\(\\s*([0-9]+).*,\\s*([0-9]+).*,\\s*([0-9]+).*\\)", "gi");
            
            var rgb = color.replace(re, "$1,$2,$3").split(',');
            if (rgb.length == 3) {
                r = parseInt(rgb[0]).toString(16);
                g = parseInt(rgb[1]).toString(16);
                b = parseInt(rgb[2]).toString(16);
                
                r = r.length == 1 ? '0' + r : r;
                g = g.length == 1 ? '0' + g : g;
                b = b.length == 1 ? '0' + b : b;
                
                return "#" + r + g + b;
            }
            return color;
        },
        /* From TinyMCE form_utils.js function, slightly modified.
         * @author Moxiecode
         * @copyright Copyright � 2004-2008, Moxiecode Systems AB, All rights reserved.
         */
        toRGB: function(color) {
            if (color.indexOf('#') != -1) {
                color = color.replace(new RegExp('[^0-9A-F]', 'gi'), '');
                
                r = parseInt(color.substring(0, 2), 16);
                g = parseInt(color.substring(2, 4), 16);
                b = parseInt(color.substring(4, 6), 16);
                
                return "rgb(" + r + "," + g + "," + b + ")";
            }
            return color;
        }
    },
    utilities: {
        setDimensions: function(wo, ho) {
            var w = Editor.dom.value(wo);
            var h = Editor.dom.value(ho);
            
            if (!w || !h) 
                return;
            // Get tmp values	
            var th = Editor.dom.value('tmp_' + ho);
            var tw = Editor.dom.value('tmp_' + wo);
            // tmp values must be set
            if (th && tw) {
                if (Editor.dom.ischecked('constrain')) {
                    var temp = (w / Editor.dom.value('tmp_' + wo)) * Editor.dom.value('tmp_' + ho);
                    h = temp.toFixed(0);
                    Editor.dom.value(ho, h);
                }
            }
            // set tmp values
            Editor.dom.value('tmp_' + ho, h);
            Editor.dom.value('tmp_' + wo, w);
        },
        setDefaults: function(d) {
            for (n in d) {
                if (n == 'border') {
                    Editor.dom.check('border', parseInt(d[n]));
                } else 
                    if (d[n] == 'default') {
                        Editor.dom.value(n, '');
                    } else {
                        Editor.dom.value(n, d[n]);
                    }
            }
        },
        setClasses: function(v) {
            var c = Editor.dom.value('classes').split(' ');
            if (tinymce.inArray(c, v) == -1) {
                c.push(v);
            }
            Editor.dom.value('classes', tinymce.trim(c.join(' ')));
        }
    }
};
// Global shortcuts
var dom = Editor.dom, string = Editor.string;
