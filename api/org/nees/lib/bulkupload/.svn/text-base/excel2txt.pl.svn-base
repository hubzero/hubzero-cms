#! /usr/bin/perl

use strict;
use Spreadsheet::ParseExcel;

my $excel = new Spreadsheet::ParseExcel;
die "You must provide a filename to $0 to be parsed as an Excel file" unless @ARGV;

my $filename = $ARGV[0];
my $book = $excel->Parse($filename);
if (!defined $book) {
  # It's not an excel spreadsheet.  Just echo the file instead.
	open( FILE, "< $filename" ) or die "Can't open $filename : $!";

	while( <FILE> ) {
		print;
	}

	close FILE;	
	exit;
}

my($row, $column, $worksheet, $cell);

$worksheet = $book->{Worksheet}[0];

my $nrows = defined $worksheet->{MaxRow} ? $worksheet->{MaxRow} : 0;

for(my $row = $worksheet->{MinRow} ; $row <= $nrows; $row++ ) {
	 
	 my $line = "";
	
	 my $ncolumns = defined $worksheet->{MaxCol} ? $worksheet->{MaxCol} : 0;
   for(my $column = $worksheet->{MinCol}; $column <= $ncolumns;  $column++) {

     $cell = $worksheet->{Cells}[$row][$column];
		 if ($cell) {
			 $line .= $cell->Value;
		 } 
		
		 if ($column < $ncolumns) {
			 $line .= "\t";
		 }
   }
	 $line .= "\n";
	 print $line;
}
