var hub = hub || {};
hub.toolbox = hub.toolbox || {};


hub.alert = function( message, callback ) {
	var thisObj = this,
		overlay = new hub.toolbox.overlay(),
		dialog  = new hub.toolbox.dialog({
			class: 'dialog-alert',
			content: '<p class="title">Alert</p><p>' + message + '</p>',
			buttons: [
				{
					text: 'Ok',
					click: function() {
						thisObj.buttonClicked();
					}
				}
			]
		});
	
	//show overlay & then dialog
	overlay.show(function(){
		dialog.show();
	});
	
	//clicked button
	this.buttonClicked = function() {
		dialog.hide(function() {
			overlay.hide(function() {
				//call callback function
				if (typeof(callback) == "function") 
					callback();
				
				overlay.destroy();
				dialog.destroy();
			});
		});
	};
};

hub.confirm = function( message, callback ) {
	var thisObj = this,
		overlay = new hub.toolbox.overlay();
		dialog  = new hub.toolbox.dialog({
			class: 'dialog-confirm',
			content: '<p>' + message + '</p>',
			buttons: [
				{
					text: 'Ok',
					click: function(event) {
						thisObj.buttonClicked( true );
					}
				},
				{
					text: 'Cancel',
					click: function( event ) {
						thisObj.buttonClicked( false );
					}
				}
			]
		});
		
	//show overlay
	overlay.show(function(){
		dialog.show();
	});
	
	//clicked button
	this.buttonClicked = function( value ) {
		
		//hide dialog and overlay
		dialog.hide(function() {
			overlay.hide(function() {
				//call callback function
				if (typeof(callback) == "function")
					callback( value );
				
				//remove overlay and dialog
				overlay.destroy();
				dialog.destroy();
			});
		});
		
	};
};

hub.prompt = function( message, callback, defaultValue ) {
	var thisObj = this,
		overlay = new hub.toolbox.overlay();
		dialog  = new hub.toolbox.dialog({
			class: 'dialog-prompt',
			content: '<p>' + message + '</p><p><input type="text" value="' + defaultValue + '" /></p>',
			buttons: [
				{
					text: 'Ok',
					click: function(event) {
						thisObj.buttonClicked( true );
					}
				},
				{
					text: 'Cancel',
					click: function( event ) {
						thisObj.buttonClicked( false );
					}
				}
			]
		});
	
	//show overlay & dialog
	overlay.show(function(){
		dialog.show();
	});
	
	//clicked button
	this.buttonClicked = function( value ) {
		//get value of prompt box
		var str = null;
		if (value)
			str = dialog.dialog.find('.content input').val();
		
		//hide dialog and overlay
		dialog.hide(function() {
			overlay.hide(function() {
				//call callback function
				if (typeof(callback) == "function")
					callback( value, str );
				
				//remove overlay and dialog
				overlay.destroy();
				dialog.destroy();
			});
		});
	};
};



/*---------------------------------
  Overlay Constructor
---------------------------------*/
hub.toolbox.overlay = function( args ) {
	// overlay defaults
	this.defaults = {
		id: 'overlay' + Math.floor((Math.random()*100)+1),
		opacity: 0.5,
		color: '#000000',
		html: '<div class="hub-overlay"></div>',
		appendTo: 'body',
		animationSpeed: 200
	};
	
	//merge args with defaults
	$.extend(this.defaults, args);
	
	//create overlay & append to document
	this.construct();
};


/*---------------------------------
  Overlay Prototype
---------------------------------*/
hub.toolbox.overlay.prototype = {
	/*
	  Create Overlay Method
	*/
	construct: function()
	{
		this.overlay = $(this.defaults.html)
			.attr('id', this.defaults.id)
			.css({
				'opacity' : this.defaults.opacity,
				'background' : this.defaults.color
			})
			.appendTo( this.defaults.appendTo );
	},
	
	/*
	  Remove Overlay method
	*/
	destroy: function() 
	{
		this.overlay.remove();
	},
	
	/* 
	  Show Overlay Method 
	*/
	show: function( callback )
	{
		//get overlay
		var overlay = this.overlay;
		
		//fade in overlay
		overlay
			.fadeIn( this.defaults.animationSpeed, function() {
				if (typeof(callback) === "function")
				{
					callback();
				}
			});
	},
	
	/*
	  Hide Overlay method
	*/
	hide: function( callback ) 
	{
		//get overlay
		var overlay = this.overlay;
		
		//fade out overlay
		overlay
			.fadeOut( this.defaults.animationSpeed, function() {
				if (typeof(callback) === "function")
				{
					callback();
				}
			});
	}
};


/*---------------------------------
  Dialog Constructor
---------------------------------*/
hub.toolbox.dialog = function( args ) {
	// dialog defaults
	this.defaults = {
		id: 'dialog' + Math.floor((Math.random()*100)+1),
		class: '',
		width: '300px',
		height: 'auto',
		top: '200px',
		appendTo: 'body',
		animationSpeed: 200,
		content: '<p>This is a HUB Dialog box.</p>',
		html: '<div class="dialog-box"> \
		           <div class="content"></div> \
		           <div class="buttons"></div> \
		       </div>',
		buttons: [ 
			{
				text: "Ok", 
				click: function( event ) {
				} 
			}
		]
	};
	
	//merge args with defaults
	$.extend(this.defaults, args);
	
	//create overlay & append to document
	this.construct();
};


/*---------------------------------
  Dialog Prototype
---------------------------------*/
hub.toolbox.dialog.prototype = {
	/*
	  Create Dialog Method
	*/
	construct: function()
	{
		//get dialog box template
		var html = this.defaults.html;
		
		//create dialog
		this.dialog = $(html);
		
		//set dialog box id and css
		this.dialog
			.attr('id', this.defaults.id)
			.addClass( this.defaults.class )
			.css({
				width: this.defaults.width,
				height: this.defaults.height
			});
		
		//append content
		this._content();
		
		//create & append buttons
		this._buttons();
			
		//append dialog to document
		this.dialog.appendTo( this.defaults.appendTo );
		
		//reposition on browser resize
		this._reposition();
	},
	
	/*
	  Destroy Dialog Method
	*/
	destroy: function()
	{
		this.dialog.remove();
	},
	
	/*
	  Show Dialog Method
	*/
	show: function( callback )
	{
		//position before showing
		this._position();
		
		//posiiton in view
		this.dialog
			.animate({
				top: this.defaults.top
			}, this.defaults.animationSpeed, "easeOutQuint", function() {
				if (typeof(callback) === "function")
				{
					callback();
				}
			});
	},
	
	/*
	  Hide Dialog Method
	*/
	hide: function( callback )
	{
		//position in view
		this.dialog
			.animate({
				top: '-200px'
			}, this.defaults.animationSpeed, "easeInQuint", function() {
				if (typeof(callback) === "function")
				{
					callback();
				}
			});
	},
	
	/*
	  Reposition Dialog Method on Window Resize
	*/
	_reposition: function()
	{
		var that = this;
		$(window).resize(function(event) {
			that._position();
		});
	},
	
	/*
	  Position Dialog Method
	*/
	_position: function()
	{
		var leftPosition    = 0,
			dialogWidth     = $(this.dialog).width(),
			windowWidth     = $(window).width(),
			windowScrollLeft = $(window).scrollLeft();
		
		leftPosition = (windowWidth - dialogWidth) / 2;
		leftPosition = leftPosition + windowScrollLeft;
		
		//position left
		this.dialog.css({
			left: leftPosition + 'px'
		});
	},
	
	/*
	  Dialog Content
	*/
	_content: function()
	{
		var contentPane = this.dialog.find('.content'),
			content = this.defaults.content;
		
		//append content
		contentPane.append( content );
	},
	
	/*
	  Build Dialog Buttons Method
	*/
	_buttons: function()
	{
		var buttonPane = this.dialog.find('.buttons'),
			buttons    = this.defaults.buttons;
		
		//add each button to the dialog box
		$.each(buttons, function(index, value) {
			$('<button></button>')
				.html( value.text )
				.addClass( value.text.toLowerCase() )
				.appendTo( buttonPane )
				.on('click', function(event){
					value.click(event);
				});
		});
	}
	
};


