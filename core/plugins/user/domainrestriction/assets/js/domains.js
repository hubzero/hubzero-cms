var Domains = {
	options:{
		id:'',
		domains:[],
		strings:{}
	},
	elements:{},

	initialize: function(options){
		var self = this;

		self.options = options;

		// identify elements for this field
		self.elements['field']  = $('#' + self.options.id);
		self.elements['domain'] = $('#' + self.options.id + '-domain');
		self.elements['save']   = $('#' + self.options.id + '-save');
		self.elements['list']   = $('#' + self.options.id + '-list');

		self.elements.save.on('click',function(e){
			self.add();
			return false;
		});

		self.makelist();
	},

	add: function(){
		var self = this;
		var domain = self.elements.domain.value;
		if(!self.validdomain(self.trim(domain)) || self.trim(domain).length == 0){
			alert(self.options.strings.INVALID);
		} else {
			if(self.options.domains.contains(domain)){
				alert(self.options.strings.DUPLICATE);
			} else {
				self.options.domains[self.options.domains.length] = self.trim(domain);
				self.elements.domain.val('');
				self.options.domains.sort();
				self.store();
			}
		}
	},

	edit: function(button){
		var self = this;
		self.elements.domain.val(self.getdomain(button));
		self.remove(button);
	},

	remove: function(button){
		var self = this;
		self.options.domains = self.options.domains.erase(self.getdomain(button));
		self.store();
	},

	store: function(){
		var self = this;
		self.elements.field.val(JSON.encode(self.options.domains).toBase64());
		self.clearlist();
		self.makelist();
	},

	clearlist: function(){
		var self = this;
		self.elements.list.find('li').each(function(i, el){
			el.destroy();
		});
	},

	makelist: function(){
		var self = this;

		$(self.options.domains).each(function(i, domain){
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
				.html(domain)
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

	getdomain: function(button){
		var parent = $(button).closest('li');
		var domainspan = $(parent).find('span');
		var domain = $(domainspan[0]).html();
		return domain;
	},

	validdomain: function(domain){
		// not really sure if this is possible - more of a placeholder for the 
		// day I figure it out.
		return true;
	},

	trim: function(value){
		return value.replace(/^\s+|\s+$/g,'');
	}
}
