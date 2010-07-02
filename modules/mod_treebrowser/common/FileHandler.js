// ----------------------------------------------------------------------
// JavaScript routines to handle UI for uploading and editing DataFile
// metadata.
// ----------------------------------------------------------------------

var n = 1000;

function addRow(table,protoRow) {
    newRow = protoRow.cloneNode(true);
    newRow.id=n++;
    newRow.style.display="";
    table.getElementsByTagName("TBODY")[0].appendChild(newRow);
    return newRow;
}

function removeRow(row) {
    row.parentNode.removeChild(row);
}

function addFileInput(tableID, rowID, namePrefix) {
	table = document.getElementById(tableID);
	row = document.getElementById(rowID);
	newRow = addRow(table,row);
	newRow.getElementsByTagName("input")[0].name=namePrefix+"_"+newRow.id;
}


