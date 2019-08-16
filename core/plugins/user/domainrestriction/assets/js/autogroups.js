var AutoGroups = {
	options:{
		id:'',
		autogroups:[],
		strings:{}
	},
	elements:{},

	initialize: function(options){
		var self = this;

		self.options = options;

		// identify elements for this field
		self.elements['field']  = $('#' + self.options.id);
		self.elements['domain'] = $('#' + self.options.id + '-domain');
		self.elements['groups'] = $('#' + self.options.id + '-groups');
		self.elements['save']   = $('#' + self.options.id + '-save');
		self.elements['list']   = $('#' + self.options.id + '-list');

		self.elements.save.on('click', function(e){
			self.add();
			return false;
		});

		self.makelist();
	},

	add: function(){
		var self = this;
		var domain = self.elements.domain.value;
		var groups = [];

		self.elements.groups.getSelected().each(function(i, el){
			groups[groups.length] = $(el).val();
		});

		if (
			!self.validdomain(self.trim(domain)) || 
			self.trim(domain).length == 0 ||
			groups.length == 0
		) {
			alert(self.options.strings.INVALID);
		} else {
			if (self.detectdupe(domain)) {
				alert(self.options.strings.DUPLICATE);
			} else {
				self.options.autogroups[self.options.autogroups.length] = {domain:domain, groups:groups};

				self.elements.domain.val('');

				self.elements.groups.getSelected().each(function(i, el){
					el.selected = false
				});

				if (self.elements.groups.hasClass('chzn-done')) {
					$('#'+self.options.id+'-groups').trigger("liszt:updated");
				}

				self.options.autogroups.sortOn('domain', Array.CASEINSENSITIVE);
				self.store();
			}
		}
	},

	edit: function(button){
		var self = this;
		var domain = self.getdomain(button);
		var groups = [];

		self.elements.domain.value = domain;

		self.options.autogroups.each(function(i, autogroup){
			if (autogroup.domain == domain) {
				groups = autogroup.groups;
			}
		});

		self.elements.groups.getChildren().each(function(i, el){
			if (groups.contains(el.val())) {
				el.selected = true;
			}
		});

		if (self.elements.groups.hasClass('chzn-done')) {
			$('#'+self.options.id+'-groups').trigger("liszt:updated");
		}

		self.remove(button);
	},

	remove: function(button){
		var self = this;
		var domain = self.getdomain(button);

		self.options.autogroups.each(function(index, autogroup) {
			if (autogroup.domain == domain) {
				self.options.autogroups.splice(index, 1);
			}
		})

		self.store();
	},

	store: function(){
		var self = this;

		self.elements.field.val(JSON.encode(self.options.autogroups).toBase64());
		self.clearlist();
		self.makelist();
	},

	detectdupe: function(domain){
		var self = this;
		var domains = [];

		self.options.autogroups.each(function(i, autogroup){
			domains[domains.length] = autogroup.domain;
		});

		if (domains.contains(domain)){
			return true;
		} else {
			return false;
		}
	},

	clearlist: function(){
		var self = this;

		self.elements.list.find('li').each(function(i, el) {
			el.destroy();
		});
	},

	makelist: function(){
		var self = this;

		$(self.options.autogroups).each(function(i, autogroup){
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
				.html(autogroup.domain)
				.addClass('domain')
				.css('margin-left', '10px')
				.appendTo(listitem);

			var grouptext = $('<span></span>')
				.html('['+self.printgroups(autogroup.groups)+']')
				.addClass('groups')
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

	printgroups: function(groups) {
		var self = this;
		var printgroups = [];

		self.elements.groups.children().each(function(i, el){
			el = $(el);

			if (groups.contains(el.val())) {
				printgroups[printgroups.length] = el.html().replace(/^([-=\s]*)([a-zA-Z0-9])/gm,"$2");
			}
		});

		return printgroups.join(', ');
	},

	getdomain: function(button){
		var parent = $(button).closest('li');
		var domainspan = $(parent).find('span.domain');
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
};
