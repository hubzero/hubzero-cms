var Emails = {
	options:{
		id:'',
		emails:[],
		strings:{}
	},
	elements:{},

	initialize:function(options){
		var self = this;

		self.options = options;

		// identify elements for this field
		self.elements['field'] = $('#' + self.options.id);
		self.elements['email'] = $('#' + self.options.id + '-email');
		self.elements['save']  = $('#' + self.options.id + '-save');
		self.elements['list']  = $('#' + self.options.id + '-list');

		self.elements.save.on('click', function(e){
			self.add();
			return false;
		});

		self.makelist();
	},

	add:function(){
		var self = this;
		var email = self.elements.email.value;

		if(!self.validemail(self.trim(email)) || self.trim(email).length == 0){
			alert(self.options.strings.INVALID);
		} else {
			if(self.options.emails.contains(email)){
				alert(self.options.strings.DUPLICATE);
			} else {
				self.options.emails[self.options.emails.length] = self.trim(email);
				self.elements.email.val('');
				self.options.emails.sort();
				self.store();
			}
		}
	},

	edit:function(button){
		var self = this;
		self.elements.email.value = self.getemail(button);
		self.remove(button);
	},

	remove:function(button){
		var self = this;
		self.options.emails = self.options.emails.erase(self.getemail(button));
		self.store();
	},

	store:function(){
		var self = this;
		self.elements.field.val(JSON.encode(self.options.emails).toBase64());
		self.clearlist();
		self.makelist();
	},

	clearlist:function(){
		var self = this;
		self.elements.list.find('li').each(function(i, el){
			el.destroy();
		});
	},

	makelist:function(){
		var self = this;

		$(self.options.emails).each(function(i, email){
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
				.html(email)
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

	getemail:function(button){
		var parent = $(button).closest('li');
		var emailspan = $(parent).find('span');
		var email = $(emailspan[0]).html();
		return email;
	},

	validemail:function(email){
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test(email);
	},

	trim:function(value){
		return value.replace(/^\s+|\s+$/g,'');
	}
}
