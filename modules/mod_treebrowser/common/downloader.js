/** JavaScript functions to handle retreiving information
     for files and folders selected by checking the checkboxes
**/

function checkAll(thisform) {
  if(!thisform.CheckAll) return; // Safely return if no checkbox 'Select All'
  if (thisform.chkFiles) {
    if (thisform.chkFiles.length) {
      for (j=0; j<thisform.chkFiles.length; j++) {
        thisform.chkFiles[j].checked = thisform.CheckAll.checked;
      }
    } else {
      thisform.chkFiles.checked = thisform.CheckAll.checked;
    }
  }

  if (thisform.chkFolders) {
    if (thisform.chkFolders.length) {
      for (j=0; j<thisform.chkFolders.length; j++) {
        thisform.chkFolders[j].checked = thisform.CheckAll.checked;
      }
    } else {
      thisform.chkFolders.checked = thisform.CheckAll.checked;
    }
  }
}


function getFileNames(thisform) {

  var fileNames = "";
   var fileInfo; // array holding filename and filesize

  if (thisform.chkFiles == undefined) {
    return fileNames;
  }

  if (thisform.chkFiles.length) {
    for (j=0; j<thisform.chkFiles.length; j++) {
      if (thisform.chkFiles[j].checked) {
        fileInfo = thisform.chkFiles[j].value.split("|");
        fileNames += fileInfo[0] + '|';
      }
    }
  } else {

    if (thisform.chkFiles.checked) {
      fileNames = thisform.chkFiles.value;
      fileNames = fileNames.substring(0, fileNames.indexOf("|")+1);
    }
  }

  return fileNames;
}

function getFileSizes(thisform) {
  var fileSizes = "";
   var fileInfo; // array holding filename and filesize

  if (thisform.chkFiles == undefined) {
    return fileSizes;
  }
  if (thisform.chkFiles.length) {
    for (j=0; j<thisform.chkFiles.length; j++) {
      if (thisform.chkFiles[j].checked) {
        fileInfo = thisform.chkFiles[j].value.split("|");
          fileSizes += fileInfo[1] + '|';

      }
    }
  } else {
    if (thisform.chkFiles.checked) {
      fileSizes = thisform.chkFiles.value;
      fileSizes = fileSizes.substring(fileSizes.indexOf("|")+1 , fileSizes.length) + '|';
    }

  }

  return fileSizes;
}

function getFoldersInfo(thisform) {
  var folders = "";

  if (thisform.chkFolders == undefined) {
    return folders;
  }


  if(thisform.chkFolders.length) {
	  for (j=0; j<thisform.chkFolders.length; j++) {
	    if (thisform.chkFolders[j].checked) {
	      folders += thisform.chkFolders[j].value + '|';
	    }
	  }
  }
  else {
	  if (thisform.chkFolders.checked) {
	    folders += thisform.chkFolders.value + '|';
	  }
  }

  if (folders.length > 0)
    folders = folders.substring(0, (folders.length-1));

  return folders;
}

var newwindow;

function popit(basepath, thisform) {

  var formname = thisform.name;
  var files = getFileNames(thisform);
  var folders = getFoldersInfo(thisform);

  if (files.length == 0 && folders.length == 0) {
    alert("Please select files or folders to download by checking the box(es)!");
    return;
  }

  var sizes = getFileSizes(thisform);

  thisform.folders.value = unescape(folders);
  thisform.fileNames.value = unescape(files);
  thisform.fileSizes.value = sizes;

  //document.getElementById('folders').value = unescape(folders);
  //document.getElementById('fileNames').value = unescape(files);
  //document.getElementById('fileSizes').value = sizes;

  if (newwindow != null && !newwindow.closed) newwindow.close();
  newwindow=window.open('','download_applet_window','height=550,width=530,left=100,top=100,resizable=yes,scrollbars=no,toolbar=no,status=yes');
  if (window.focus) {newwindow.focus()}

  var a = window.setTimeout('document.' + thisform.name + '.submit();',500);
}


function anycheck(thisform)
{
  if(!thisform.CheckAll) return; // Safely return if no checkbox 'Select All'
  if(thisform.chkFiles)
  {
    if(thisform.chkFiles.length)
    {
      for (var idx = 0; idx < thisform.chkFiles.length; idx++) {
        if (thisform.chkFiles[idx].checked == false) {
          thisform.CheckAll.checked = false;
          return;
        }
      }
    }
    else {
      if (thisform.chkFiles.checked == false) {
        thisform.CheckAll.checked = false;
        return;
      }
    }
  }

  if(thisform.chkFolders)
  {
    if(thisform.chkFolders.length)
    {
      for (var idx = 0; idx < thisform.chkFolders.length; idx++) {
        if (thisform.chkFolders[idx].checked == false) {
          thisform.CheckAll.checked = false;
          return;
        }
      }
    }
    else {
      if (thisform.chkFolders.checked == false) {
        thisform.CheckAll.checked = false;
        return;
      }
    }
  }

  thisform.CheckAll.checked = true;
  return;
}



function writeDownloadApplet(cache_version, fileNames, fileSizes, gasession)
{

	fileNames = escape(fileNames).replace(/%7C/g, "|");

	document.writeln('<APPLET CODE = "org.sdsc.nees.download.BulkDownload" ARCHIVE = "/applets/BulkDownload.jar" WIDTH = "500" HEIGHT = "180">');
	document.writeln('<PARAM NAME = "code" VALUE = "org.sdsc.nees.download.BulkDownload" >');
	document.writeln('<PARAM NAME = "cache_archive" VALUE = "BulkDownload.jar" >');
	//document.writeln('<PARAM NAME = "cache_version" VALUE = "' + cache_version + '" >');
	document.writeln('<PARAM NAME = "type" VALUE="application/x-java-applet;version=1.4">');
	document.writeln('<PARAM NAME = "scriptable" VALUE="false">');
	document.writeln('<PARAM NAME = "FileNames" value="' + fileNames + '"> ');
	document.writeln('<PARAM NAME = "FileSizes" value="' + fileSizes + '"> ');
	document.writeln('<PARAM NAME = "GAsession" value="' + gasession + '"> ');
	document.writeln('<PARAM NAME = "debug" VALUE="on">');
	document.writeln('Java 1.4 or higher plugin required.');
	document.writeln('</APPLET>');

}
