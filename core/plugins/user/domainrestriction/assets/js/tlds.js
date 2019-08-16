var TLDs = {
	options:{
		id:'',
		tlds:[],
		strings:{}
	},
	elements:{},

	initialize: function(options){
		var self = this;

		self.options = options;

		// identify elements for this field
		self.elements['field'] = $('#' + self.options.id);
		self.elements['tld']   = $('#' + self.options.id + '-tld');
		self.elements['save']  = $('#' + self.options.id + '-save');
		self.elements['list']  = $('#' + self.options.id + '-list');

		self.elements.save.on('click', function(e){
			e.preventDefault();

			self.add();

			return false;
		});

		self.makelist();
	},

	add: function(){
		var self = this;
		var tld = self.elements.tld.val();

		if (!self.validtld(self.trim(tld)) || self.trim(tld).length == 0){
			alert(self.options.strings.INVALID);
		} else {
			if (self.options.tlds.contains(tld)){
				alert(self.options.strings.DUPLICATE);
			} else {
				self.options.tlds[self.options.tlds.length] = self.trim(tld);
				self.elements.tld.value = '';
				self.options.tlds.sort();
				self.store();
			}
		}
	},

	edit: function(button){
		var self = this;
		self.elements.tld.value = self.gettld(button);
		self.remove(button);
	},

	remove: function(button){
		var self = this;
		self.options.tlds = self.options.tlds.erase(self.gettld(button));
		self.store();
	},

	store: function(){
		var self = this;
		self.elements.field.val(JSON.encode(self.options.tlds).toBase64());
		self.clearlist();
		self.makelist();
	},

	clearlist: function(){
		var self = this;
		self.elements.list.find('li').each(function(i, el){
			$(el).destroy();
		});
	},

	makelist: function(){
		var self = this;
		$(self.options.tlds).each(function(i, tld){
			var listitem = $('<li></li>')
				.css('display', 'block')
				.css('clear', 'both')
				.appendTo(self.elements.list);

			var edit = $('<button></button>')
				.html(self.options.strings.EDIT)
				.appendTo(listitem);

			var remove = $('<button></button>')
				.html(self.options.strings.REMOVE)
				.appendTo(listitem);

			var text = $('<span></span>')
				.html(tld)
				.css('margin-left', '10px')
				.appendTo(listitem);

			edit.on('click',function(e){
				self.edit(this);
				return false;
			});

			remove.on('click',function(e){
				self.remove(this);
				return false;
			});
		});
	},

	gettld: function(button){
		var parent = button.getParent('li');
		var tldspan = parent.getChildren('span');
		var tld = tldspan.get('html')[0]; 
		return tld;
	},

	validtld: function(tld){
		// not really sure if this is possible - more of a placeholder for the 
		// day I figure it out.
		return true;
	},

	trim: function(value){
		return value.replace(/^\s+|\s+$/g,'');
	}
}
