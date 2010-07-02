<?php

ini_set('auto_detect_line_endings', true);

class FileReader {

  private $labels=null;
  private $numRows=null;
  private $numCols=null;
  private $datafile=null;
  private $fileErrorMsg = null;
  private $startPosition = 0;
  private $delimiter = "\t";
  private $inlineComment = "#";
  private $startLineNumber = 0;

  /**
   * Constructor
   *
   * @param String $datafile
   * @return FileReader
   */
  public function __construct($datafile) {
    $this->datafile = $datafile;
  }


  /**
   * Get the file extension
   *
   * @return String extension
   */
  function getFileExtension() {
    if( !$this->datafile) return null;

    $pathinfo = pathinfo($this->datafile);
    return $pathinfo['extension'];
  }


  /**
   * Get the starting position of the file (in byte, not line number)
   *
   * @return int position
   */
  function getStartPosition() {
    return $this->startPosition;
  }


  /**
   * Get the delimeter of the file
   *
   * @return String $delim
   */
  function getDelimeter() {
    return $this->delimiter;
  }


  /**
   * Set the delimeter of the file by user
   *
   * @param String $delim
   */
  function setDelimeter($delim) {
    $this->delimiter = $delim;
  }


  /**
   * set the inline comment of the file by user
   *
   * @param String $ic
   */
  function setInlineComment($ic) {
    $this->inlineComment = $ic;
  }


  /**
   * Set start line number if user want to remove the first n lines that is not in the correct format.
   *
   * @param int $lineNumber
   */
  function setStartLineNumber($lineNumber) {
    $this->startLineNumber = $lineNumber;
  }


  /**
   * get column labels from the header
   *
   * @return array[label]
   */
  function getLabels(){
    if(!is_null($this->labels)) {
      return $this->labels;
    }

    $labels = array();
    $extension = $this->getFileExtension();

    //Excel Format, valid for Excel 95, 97, 2000, 2003 - NOT support for Excel 2007
    if(strtolower($extension) == "xls") {
      $labels = $this->getLabelsFromExcel();
    }
    else {
      $labels = $this->getLabelsFromText();
    }

    $this->labels = $labels;

    return $labels;
  }


  /**
   * get column labels from an excel file
   *
   * @return array[label]
   */
  function getLabelsFromExcel() {

    require_once "lib/excel/reader.php";
    $labels = array();

    // ExcelFile($filename, $encoding);
    $data = new Spreadsheet_Excel_Reader();

    // Set output Encoding.
    $data->setOutputEncoding('CP1251');

    $data->read($this->datafile);

    if( ! isset($data->sheets[0])) return null;

    for($col = 0; $col < $data->sheets[0]['numCols']; $col++) {
      $labels[$col] = isset($data->sheets[0]['cells'][1][$col+1]) ? trim($data->sheets[0]['cells'][1][$col+1]) : "";
    }

    return $labels;
  }


  /**
   * get column labels from a text file format (ascii file)
   *
   * @return array[label]
   */
  function getLabelsFromText() {
    $labels = array();
    $reader = fopen("$this->datafile", "r");

    $delims = array("\t", ",");

    foreach($delims as $delim) {
      rewind($reader);

      if($this->startLineNumber) {
        for($i=0; $i<$this->startLineNumber; $i++) {
          fgetcsv($reader, null, $delim);
        }
      }

      while (($data = fgetcsv($reader, null, $delim)) !== FALSE) {
        $numCols = count($data);

        if(!empty($this->inlineComment)) {
          if(isset($data[0]) && $first = $data[0]) {
            if(strpos(trim($first), $this->inlineComment) === 0) continue;
          }
        }

        if($numCols == 1) {
          $firstRow = trim($data[0]);

          // Do not count empty line
          if( empty($firstRow)) continue;

          // If the first row that is not empty line, and just have 1 column based
          //on current delimiter, then just break, this is not valid data for graph
          else break;
        }

        for ($col=0; $col < $numCols; $col++) {
          $labels[$col] = trim($data[$col]);
        }

        $this->numCols = count($labels);
        $this->delimiter = $delim;
        $this->startPosition = ftell($reader);

        fclose($reader);
        return $labels;
      }
    }

    // Not a valid data file for grap, close it and return null
    fclose($reader);
    return null;
  }


  /**
   * Get data in two-dimension array[row][column] from an XML Spreadsheet, which was saved as by MS Excel
   *
   * @return array two-dimension array[row][column]
   */
  function getDataCellFromXMLSpreadsheet(){

    $cells = array();

    $xml = simplexml_load_file($this->datafile);

    // Only work with Excel Spreadsheet format.
    if( isset($xml->Worksheet->Table->Row)) {

      for($row = 0; $row < count($xml->Worksheet->Table->Row); $row++) {
        $line = $xml->Worksheet->Table->Row[$row]->Cell;
        for($col = 0; $col < count($line); $col++) {
          $cells[$row+1][$col+1] = isset($line[$col]->Data) ? trim($line[$col]->Data) : "";
        }
      }
    }

    return $cells;
  }


  /**
   * Get the total number of rows
   *
   * @return int
   */
  function getNumRows() {
    return $this->numRows;
  }



  /**
   * Get the total number of columns
   *
   * @return int
   */
  function getNumCols() {
    return $this->numCols;
  }


  /**
   * Get the error message when detected by reader
   *
   * @return String
   */
  function getFileErrorMsg() {
    return $this->fileErrorMsg;
  }


  /**
   * printout dataURL which in specific format that required for java applet input.
   *
   * @param array $colArr
   * @param String $delimiter
   * @param boolean $isNumeric
   * @param int $deltaT
   * @param int $xdomainStart
   * @param int $xdomainEnd
   * @param int $colorIndex
   * @return file output to the server
   */
  function getDataURL($colArr, $delimiter="\t", $isNumeric=false, $deltaT=1, $xdomainStart, $xdomainEnd, $colorIndex=0) {

    if(!is_array($colArr)) return false;

    $numCols = $this->getNumCols();

    header("Pragma:");
    header("Cache-Control:");
    header("Content-Type: text/plain");

    ob_clean();

    // A trick to change color of graph by writing multiple empty Dataset label
    if(count($colArr) == 1 && $colorIndex > 0) {
      for($ind=0; $ind<$colorIndex; $ind++) {
        echo "DataSet:\n";
      }
    }

    $r = fopen($this->datafile, "r");

    foreach($colArr as $col) {

      if(!isset($this->labels[$col])) {
        continue;
      }

      echo "DataSet: " . $this->labels[$col] . "\n";

      fseek($r, $this->getStartPosition());

      if(is_null($xdomainStart) && is_null($xdomainEnd)) {
        if($isNumeric) {
          while (($data = fgetcsv($r, null, $delimiter)) !== FALSE) {
            if(count($data) == $numCols) {
              echo $data[0] . " " . $data[$col] . "\n";
            }
          }
        }
        else {
          $xvalue = 0;
          while (($data = fgetcsv($r, null, $delimiter)) !== FALSE) {
            if(count($data) == $numCols) {
              echo $xvalue . " " . $data[$col] . "\n";
              $xvalue+= $deltaT;
            }
          }
        }
      }
      else {
        $minX = min(array($xdomainStart, $xdomainEnd));
        $maxX = max(array($xdomainStart, $xdomainEnd));

        if($isNumeric) {
          while (($data = fgetcsv($r, null, $delimiter)) !== FALSE) {
            if(count($data) == $numCols) {
              if($data[0] >= $minX && $data[0] <= $maxX) {
                echo $data[0] . " " . $data[$col] . "\n";
              }
            }
          }
        }
        else {
          $xvalue = 0;
          while (($data = fgetcsv($r, null, $delimiter)) !== FALSE) {
            if(count($data) == $numCols) {
              if($xvalue >= $minX && $xvalue <= $maxX) {
                echo $xvalue . " " . $data[$col] . "\n";
              }
              $xvalue+= $deltaT;
            }
          }
        }
      }
      echo "\n";
    }
    fclose($r);
  }


  /**
   * printout data download which in easy to read for users and also valid for re-graph later.
   *
   * @param array $colArr
   * @param String $delimiter
   * @param String $attachment file name output
   * @param int $xdomainStart
   * @param int $xdomainEnd
   * @return file output to the server
   */
  function getDataDownload($colArr, $delimiter="\t", $attachment, $xdomainStart, $xdomainEnd) {

    if(!is_array($colArr)) return false;
    $colArr = array_merge(array(0), $colArr);
    $numCols = $this->getNumCols();

    if(!$attachment) $attachment = rand() . "txt";

    header("Pragma:");
    header("Cache-Control:");
    header("Content-Type: text/plain");
    header("Content-Disposition: attachment; filename=$attachment");

    ob_clean();

    $r = fopen($this->datafile, "r");

    if(is_null($xdomainStart) && is_null($xdomainEnd)) {
      while (($data = fgetcsv($r, null, $delimiter)) !== FALSE) {
        if(count($data) == $numCols) {
          $row = array();
          foreach($colArr as $col) {
            $row[] = $data[$col];
          }
          echo(implode("\t", $row) . "\n");
        }
      }
    }
    else {
      $minX = min(array($xdomainStart, $xdomainEnd));
      $maxX = max(array($xdomainStart, $xdomainEnd));

      while (($data = fgetcsv($r, null, $delimiter)) !== FALSE) {
        if(count($data) == $numCols) {

          $row = array();

          if(!is_numeric($data[0]) || ($data[0] >= $minX && $data[0] <= $maxX)) {
            foreach($colArr as $col) {
              $row[] = $data[$col];
            }
            echo(implode("\t", $row) . "\n");
          }
        }
      }
    }

    fclose($r);
  }


}
?>