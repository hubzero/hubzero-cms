/**
 * http://github.com/valums/file-uploader
 * 
 * Multiple file upload component with progress-bar, drag-and-drop. 
 * © 2010 Andrew Valums ( andrew(at)valums.com ) 
 * 
 * Licensed under GNU GPL 2 or later and GNU LGPL 2 or later, see license.txt.
 */    

//
// Helper functions
//
qq.ButtonFileUploader = function(o) {
	// call parent constructor
    qq.FileUploader.apply(this, arguments);
    
	// additional options
    qq.extend(this._options, { 
		listElement: null,
    	onChange: function(input)	{
	
		},		
		classes: {
            // used to get elements from templates
            button: 'qq-upload-button',
            drop: 'qq-upload-drop-area',
            dropActive: 'qq-upload-drop-area-active',
            list: 'qq-upload-list',
                        
            file: 'qq-upload-file',
			name: 'qq-upload-name',
            spinner: 'qq-upload-spinner',
            size: 'qq-upload-size',
            cancel: 'qq-upload-cancel',
			ext: 'qq-upload-ext',
			icon: 'qq-upload-icon',
			status: 'qq-upload-status',
			error: 'qq-upload-error',

            // added to list item when upload completes
            // used in css to hide progress spinner
            success: 'qq-upload-success',
            fail: 'qq-upload-fail'
        },
		showMessage: function(message) {
            // nothing
        }
    });
    // overwrite options with user supplied    
    qq.extend(this._options, o);
    
    this._que 		= []; // upload queue
	this._sFiles 	= []; // filenames in upload queue
	this._delay		= 0;  // delay for uploads to make them truely sequential
	
	this._preprocessed = 0;
	
	if (!jq) {
		var jq = $;
	}
	
	this.jQuery = jq;	
};

//inherit from FileUploader
qq.extend(qq.ButtonFileUploader.prototype, qq.FileUploader.prototype);

qq.extend(qq.ButtonFileUploader.prototype, {
	
	checkQueue: function()
	{
		return this._que;
	},
	checkFiles: function()
	{
		return this._sFiles;
	},
	_getCss: function(ext)
	{
		var type = 'file';
		
		// Compressed file extensions
		var tar = {
		  'gz'  	: 1,
		  '7z'      : 1,
		  'zip' 	: 1,
		  'zipx' 	: 1,
		  'sit' 	: 1,
		  'sitx' 	: 1,
		  'rar' 	: 1
		};
		
		// Video file extensions
		var video = {
		  'avi'  	: 1,
		  'mpeg' 	: 1,
		  'mov' 	: 1,
		  'mp4'  	: 1,
		  'mpg'  	: 1,
		  'rm'  	: 1,
		  'ogg'  	: 1,
		  'wmv'  	: 1
		};
		
		// Image file extensions
		var image = {
		  'bmp'  : 1,
		  'jpeg' : 1,
		  'jpg'  : 1,
		  'jpe'  : 1,
		  'gif'  : 1,
		  'png'  : 1,
		  'tif'  : 1,
		  'tiff' : 1
		};
		
		// Text file extensions
		var text = {
		  'tex'  : 1,
		  'txt'  : 1,
		  'rtf'  : 1,
		  'php'  : 1,
		  'css'  : 1,
		  'html' : 1,
		  'csv'  : 1
		};
		
		// Office file extensions
		var office = {
		  'xls'   : 1,
		  'xlsx'  : 1,
		  'doc'   : 1,
		  'docx'  : 1,
		  'ppt'   : 1,
		  'pptx'  : 1
		};
		
		if (ext == 'pdf')
		{
			type = 'pdf';
		}
		else if (office[ext]) {
			type = 'office';
		}
		else if (tar[ext]) {
			type = 'archive';
		}
		else if (video[ext]) {
			type = 'video';
		}
		else if (image[ext]) {
			type = 'image';
		}
		else if (text[ext]) {
			type = 'text';
		}
				
		var cssClass = 'type-' + type;
		return cssClass;
	},
	_checkButton: function()
	{
		var $ = this.jQuery;
		if ($('#f-upload').length == 0)
		{
			return false;
		}
		
		if (this._que.length == 0)
		{
			qq.addClass($('#f-upload'), 'disabled');
		}
		else if ($('#f-upload').hasClass('disabled'))
		{
			$('#f-upload').removeClass('disabled');
		}
	},
	_checkArchive: function()
	{
		var isArchive = 0;
		
		if (this._sFiles.length == 0)
		{
			return 0;
		}
		
		for (var i=0; i< this._sFiles.length; i++)
		{
			fileName = this._sFiles[i];
			var ext = fileName.split('.').pop().toLowerCase();
			if (ext == 'zip' || ext == 'tar' || ext =='gz')
			{
				isArchive = 1;
			}
		}

		return isArchive;
	},
	reportChange: function()
	{
		var $ = this.jQuery;
		if ($('#queue').length)
		{
			$('#queue').val(this._sFiles);
		}
				
		// Check total size against quota
		var cSize = this._addUpSize();
		
		// Show total size
		this._showTotals(cSize);	
		
		// Enable submit vutton?
		this._checkButton();	
	},
	
	checkMaxSize: function(size)
	{
		var $ = this.jQuery;
		var over = 0;
		
		if ($('#maxsize').length)
		{
			if (size >= $('#maxsize').val())
			{
				return 1;
			}
		}
		
		return over;					
	},
	
	_getAvailSpace: function(cSize)
	{
		var $ = this.jQuery;
		var avail = 0;
		if ($('#avail').length)
		{
			avail = $('#avail').val();
		}
		
		avail = avail - cSize;
		avail = avail < 0 ? 0 : avail;
		return avail;
	},
	
	_showTotals: function(cSize)
	{
		var $ = this.jQuery;
		if (cSize)
		{
			if ($('#upload-csize').length)
			{
				//$('#upload-csize').remove();
				$('#upload-csize').empty();
				$('#upload-csize').css('display', 'none');	
			}
			
			var avail = this._getAvailSpace(cSize);
			
			var availClass = avail < 100 ? 'urgency' : 'green';
			avail = avail ? this._formatSize(avail) : 0;
			
			/*
			var item = qq.toElement('<li id="upload-csize">' +	
					'<span id="csize-avail">Available: <span class="prominent ' + availClass + '">' + avail + '</span></span>' +					
	                '<span id="csize">Total: <span class="prominent">' + this._formatSize(cSize) + '</span></span>' +
	            '</li>');
			//this._listElement.appendChild(item);
			*/
			$('#upload-csize').css('display', 'block');	
			$('#upload-csize').append('<ul><li>' +	
					'<span id="csize-avail">Available: <span class="prominent ' + availClass + '">' + avail + '</span></span>' +					
	                '<span id="csize">Total: <span class="prominent">' + this._formatSize(cSize) + '</span></span>' +
	            '</li></ul>');				
		}
		else if ($('#upload-csize').length)
		{
			//$('#upload-csize').remove();
			$('#upload-csize').empty();
			$('#upload-csize').css('display', 'none');	
		}
	},
	
	// method to add file to que
	_addFileToQue: function(fileContainer) {
		
		var fileName = fileContainer.fileName != null ? fileContainer.fileName : fileContainer.name; 
		var size	 = fileContainer.fileSize != null ? fileContainer.fileSize : fileContainer.size; 
	    
		var i = this.getArrayIndex(fileName, this._sFiles);

		if (i == -1)
		{				
			this._que.push(fileContainer);
			this._sFiles.push(fileName);
			var id = this._que.length - 1;
			this._addToList(id, fileName, size);							
			this.reportChange();				
		}
	},
	// overrule method to disable autoUpload
	_onInputChange: function(input) {
		// reset que to 'forget' previously selected files
		//this._que = [];
		if (this._handler instanceof qq.UploadHandlerXhr) {                
			for (var i=0; i<input.files.length; i++) {
	            if ( this._validateFile(input.files[i]) && this._options.onChange(input.files[i].fileName) !== false) {
	        		this._addFileToQue(input.files[i]);        
				}
	        }                   
	    } else {
	        if (this._validateFile(input) && this._options.onChange(input.value) !== false) {
        		this._addFileToQue(input);                                
	        }                      
	    }
	},
	
	// method to call when clicking a button
	startUploads: function(expand) 
	{
		var $ = this.jQuery;
		if ($('#f-upload').length > 0)
		{
			if ($('#f-upload').hasClass('started'))
			{
				return false;
			}
			
			$('#f-upload').val('Upload started. Please wait');
			$('#f-upload').addClass('started');
			
			if ($('#cancel-action'))
			{
				$('#cancel-action').remove();
			}
			
			$('#ajax-uploader div div').css('display', 'none');	
			$('#upload-instruct').html('Uploading selected file(s). Please do not close this window.');   
		}
		for (var i=0; i< this._que.length; i++)
		{
			fileContainer = this._que[i];
			this._iniUpload(fileContainer, expand);
		}
	},
	
	_iniUpload: function(fileContainer, expand)
	{
		var self = this;
		if (!this._delay)
		{
			// Upload file
			this._uploadFile(fileContainer, expand);
			
			// Mark as waiting turn
			var fileName = fileContainer.fileName != null ? fileContainer.fileName : fileContainer.name;
			var item = this._getItemByFileName(fileName);
			var elExt  = this._find(item, 'ext');
			elExt.innerHTML = '';
			
			var can = this._find(item, 'cancel');
			can.innerHTML = '';
			
			var status = this._find(item, 'status'); 
			status.innerHTML = 'starting';	
		}
		else
		{
			// Mark as waiting turn
			var fileName = fileContainer.fileName != null ? fileContainer.fileName : fileContainer.name;
			var item = this._getItemByFileName(fileName);
			var elExt  = this._find(item, 'ext');
			elExt.innerHTML = '';
			
			var can = this._find(item, 'cancel');
			can.innerHTML = '';
			
			var status = this._find(item, 'status'); 
			status.innerHTML = 'waiting';
			
			// Make sure previous upload has cleared
			setTimeout((function() {  
				self._iniUpload(fileContainer, expand);	
			}), 1000);
		}
	},
	
	_uploadFile: function(fileContainer, expand)
	{      
		// Must wait for task completion
		this._delay = 1;        
		
		var id = this._handler.add(fileContainer);
		var fileName = fileContainer.fileName != null ? fileContainer.fileName : fileContainer.name;
		
		var params = {expand_zip:expand};
  		
        if (this._options.onSubmit(id, fileName) !== false)
		{
            this._onSubmit(id, fileName);
            this._handler.upload(id, params);
        }
    },

	_onSubmit: function(id, fileName){
        qq.FileUploaderBasic.prototype._onSubmit.apply(this, arguments);
        //this._addToList(id, fileName);  
    },
 	_onProgress: function(id, fileName, loaded, total){
        qq.FileUploaderBasic.prototype._onProgress.apply(this, arguments);

        var item = this._getItemByFileName(fileName);

		var can = this._find(item, 'cancel');
		can.innerHTML = '';

		var elExt  = this._find(item, 'ext');
		elExt.innerHTML = ''; 

        var size = this._find(item, 'size');
		size.style.display = 'inline';
		
		var status = this._find(item, 'status'); 
		status.innerHTML = this._preprocessed ? 'uploading' : 'getting data';
		qq.addClass(status, 'qq-upload-processing');
		 
        var text; 
		var perc = Math.round(loaded / total * 100);
		text = total > 0 ? perc + '% from ' + this._formatSize(total) : 'loaded';
		
        if (perc >= 99) {   
	        text = this._formatSize(total);
			status.innerHTML = 'processing'; 
			this._preprocessed = 1;
        }          

        qq.setText(size, text);         
    },
	_getItemByFileName: function(fileName)
	{
        var item = this._listElement.firstChild;        
        
        // there can't be txt nodes in dynamically created list
        // and we can  use nextSibling
        while (item) {    
	        
			var fBlock  = this._find(item, 'name');
			var fName 	= fBlock ? fBlock.innerHTML : null;
						
            if (fName == fileName) return item;            
            item = item.nextSibling;
        }          
    },

	_onComplete: function(id, fileName, result)
	{
        qq.FileUploaderBasic.prototype._onComplete.apply(this, arguments);

        // mark completed
        var item = this._getItemByFileName(fileName);   
        
		qq.remove(this._find(item, 'cancel'));        
        qq.remove(this._find(item, 'spinner'));

		var status = this._find(item, 'status');  
		var elExt  = this._find(item, 'ext'); 
		var elError = this._find(item, 'error');
		
		this._preprocessed = 0;
				
        if (result.success){
			status.innerHTML = 'uploaded';
            qq.addClass(status, this._classes.success); 
        } else {
			status.innerHTML = 'failed';
            qq.addClass(status, this._classes.fail);
			
			if (result.error)
			{				
				elError.innerHTML = result.error; 
			}
        } 
		elExt.innerHTML = '';
        this._delay = 0; // allow next upload
    },
	
	_uploadFileList: function(files) 
	{
        for (var i=0; i<files.length; i++)
		{
            if ( this._validateFile(files[i])){
                this._addFileToQue(files[i]);
            }            
        }       
    },

	getArrayIndex: function (obj, arr)
	{
		if (!Array.indexOf)
		{
			// Fix for indexOf in IE browsers
			for (var i = 0, j = arr.length; i < j; i++) 
			{
			   if (arr[i] === obj) { return i; }
			}
			return -1;
		}
		else
		{
			return arr.indexOf(obj);
		}
	},
	
	_addUpSize: function() 
	{	
		var cSize = 0;
		if (this._que.length > 0)
		{
			for (id in this._que) {
			    fileContainer = this._que[id];
				
				var fSize	 = fileContainer.fileSize != null ? fileContainer.fileSize : fileContainer.size;
				
				if (fSize)
				{
					cSize = cSize + fSize;
				}		
			}
		}
		return cSize;
	},
		
	_addToList: function(id, fileName, size) 
	{
        var item = qq.toElement(this._options.fileTemplate);                
        item.qqFileId = id;

        var fileElement = this._find(item, 'file');        
        qq.setText(fileElement, this._formatFileName(fileName));
		fSize = this._formatSize(size);
		
		ext = fileName.split('.').pop().toLowerCase();
		if (ext.length > 4) { ext = ''; }
		
        var elSize  = this._find(item, 'size'); 
		var elExt   = this._find(item, 'ext'); 
		var elIcon  = this._find(item, 'icon');
		var elError = this._find(item, 'error');
		   
		elSize.innerHTML = this._formatSize(size);
		elExt.innerHTML  = ext;
		
		var over = this.checkMaxSize(size);
		if (over && elError)
		{
			elError.innerHTML = 'File exceeds upload size limit';
		}
		
		var elName  = this._find(item, 'name'); 
		elName.innerHTML = fileName;
		
		css = this._getCss(ext);
		
		if (elIcon)
		{
			qq.addClass(elIcon, css);
		}
   		
        this._listElement.appendChild(item);
    },

	/**
     * delegate click event for cancel link 
     **/
    _bindCancelEvent: function()
	{
        var self = this,
            list = this._listElement;            
        
        qq.attach(list, 'click', function(e){            
            e = e || window.event;
            var target = e.target || e.srcElement;
            
            if (qq.hasClass(target, self._classes.cancel)){                
                qq.preventDefault(e);
               
                var item = target.parentNode;
				var id   = item.qqFileId;
				var file  = self._find(item, 'file');
				var fileName = file ? file.innerHTML : null;

				qq.remove(item);
								
				for (var i=0; i< self._que.length; i++)
				{
					fileContainer = self._que[i];
					var fName = fileContainer.fileName != null ? fileContainer.fileName : fileContainer.name;
					
					if (fName == fileName)
					{
						self._que.splice(i, 1);
					}
				}
				
				var k = self.getArrayIndex(fileName, self._sFiles);
				if (k != -1)
				{
					self._sFiles.splice(k, 1);
				}
			
				self.reportChange();
            }
        });
    }
});