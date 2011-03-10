<?php

//some usefull functions

require_once("../../config.php");
require_once("lib.php");
require_once($CFG->dirroot . '/lib/excel/Workbook.php');
require_once($CFG->dirroot . '/lib/excel/Worksheet.php');

class EasyWorkbook extends Workbook
{

    function &add_worksheet($name = '')
    {
        $index     = count($this->worksheets);
        $sheetname = $this->sheetname;

        if($name == '') {
            $name = $sheetname.($index+1); 
        }
    
        // Check that sheetname is <= 31 chars (Excel limit).
        if(strlen($name) > 31) {
            die("Sheetname $name must be <= 31 chars");
        }
    
        // Check that the worksheet name doesn't already exist: a fatal Excel error.
        for($i=0; $i < count($this->worksheets); $i++)
        {
            if($name == $this->worksheets[$i]->get_name()) {
                die("Worksheet '$name' already exists");
            }
        }
    
        $worksheet = new EasyWorksheet($name,$index,$this->activesheet,
                                   $this->firstsheet,$this->url_format,
                                   $this->parser,
                                   $this);
        $this->worksheets[$index] = &$worksheet;      // Store ref for iterator
        $this->sheetnames[$index] = $name;            // Store EXTERNSHEET names
        //$this->parser->set_ext_sheet($name,$index); // Store names in Formula.php
        return($worksheet);
    }
    
    function _store_OLE_file()
    {
        $OLE  = new EasyOLEwriter($this->_filename);
        // Write Worksheet data if data <~ 7MB
        if ($OLE->set_size($this->_biffsize))
        {
            $OLE->write_header();
            $OLE->write($this->_data);
            foreach($this->worksheets as $sheet) 
            {
                while ($tmp = $sheet->get_data()) {
                    $OLE->write($tmp);
                }
            }
        }
        $OLE->close();
    }
    
}


class EasyWorksheet extends Worksheet
{
   var $m_format; //von mir hinzugefuegt
   var $m_workbook; //von mir hinzugefuegt

   function EasyWorksheet($name,$index,&$activesheet,&$firstsheet,&$url_format,&$parser,&$workbook)
   {
      parent::Worksheet($name,$index,$activesheet,$firstsheet,$url_format,$parser);
      $this->m_workbook = &$workbook;
      $this->m_format = &$this->m_workbook->add_format();
   }

   function write($row, $col, $token)
   {
      parent::write($row, $col, $token, $this->m_format);
   }

   function write_number($row, $col, $num)
   {
      parent::write_number($row, $col, $num, $this->m_format);
   }

   function write_string($row, $col, $str)
   {
      parent::write_string($row, $col, $str, $this->m_format);
   }

   function write_formula($row, $col, $formula)
   {
      parent::write_formula($row, $col, $formula, $this->m_format);
   }

   function write_url($row, $col, $url, $string = '')
   {
      parent::write_url($row, $col, $url, $string, $this->m_format);
   }

   /**
      Setz das aktuelle Format, dass zum Schreiben verwendet wird
      Der Formatstring setzt sich aus den folgenden Buchstaben mit folgender Bedeutung zusammen.
      <f> = Fett
      <k> = kursiv
      <z> = zentriert
      <l> = linksbündig
      <r> = rechtsbündig
      <vo> = vertikal oben
      <vz> = vertikal zentriert
      <vu> = vertikal unten
      <uX> = unterstrichen X=1-einfach, X=2-doppelt
      <w> = währungsformat
      <pr> = prozentformat
      <ruX> = Rahmen unten X=Stärke
      <roX> = rahmen oben X=Stärke
      <rrX> = rahmen rechts X=Stärke
      <rlX> = rahmen links X=Stärke
      <c:XXX> = Schriftfarbe, XXX kann einen der folgenden Farbwerte enthalten:
         aqua,cyan,black,blue,brown,magenta,fuchsia,gray,
         grey,green,lime,navy,orange,purple,red,silver,white,yellow
         Wichtig: alle Werte müssen klein geschrieben werden.
   */
   function setFormat($formatString,$size = 10,$textWrap = true)
   {
      $this->m_format = &$this->m_workbook->add_format();
      if($textWrap)
      {
      	$this->m_format->set_text_wrap();
      }

      if(preg_match("/<f>/i",$formatString) > 0)
      {
      	$this->m_format->set_bold();
      }

      if(preg_match("/<k>/i",$formatString) > 0)
      {
      	$this->m_format->set_italic();
      }

      if(preg_match("/<z>/i",$formatString) > 0)
      {
      	$this->m_format->set_align("center");
      }

      if(preg_match("/<l>/i",$formatString) > 0)
      {
      	$this->m_format->set_align("left");
      }

      if(preg_match("/<r>/i",$formatString) > 0)
      {
      	$this->m_format->set_align("right");
      }

      if(preg_match("/<vo>/i",$formatString) > 0)
      {
      	$this->m_format->set_align("top");
      }

      if(preg_match("/<vz>/i",$formatString) > 0)
      {
      	$this->m_format->set_align("vcenter");
      }

      if(preg_match("/<vu>/i",$formatString) > 0)
      {
      	$this->m_format->set_align("bottom");
      }

      if(preg_match("/<u\d>/i",$formatString,$treffer) > 0)
      {
      	$this->m_format->set_set_underline(substr($treffer[0],2,1));
      }

      if(preg_match("/<w>/i",$formatString) > 0)
      {
      	$this->m_format->set_num_format("#,##0.00_)€;[Red]-#,##0.00_)€");
      }

      if(preg_match("/<pr>/i",$formatString) > 0)
      {
      	$this->m_format->set_num_format("#,##0.00%");
      }

      if(preg_match("/<ru\d>/i",$formatString,$treffer) > 0)
      {
      	$this->m_format->set_bottom(substr($treffer[0],3,1));
      }

      if(preg_match("/<ro\d>/i",$formatString,$treffer) > 0)
      {
      	$this->m_format->set_top(substr($treffer[0],3,1));
      }

      if(preg_match("/<rr\d>/i",$formatString,$treffer) > 0)
      {
      	$this->m_format->set_right(substr($treffer[0],3,1));
      }

      if(preg_match("/<rl\d>/i",$formatString,$treffer) > 0)
      {
      	$this->m_format->set_left(substr($treffer[0],3,1));
      }

      if(preg_match("/<c\:[^>]+>/",$formatString,$treffer) > 0)
      {
      	$len = strlen($treffer[0]) - 4; //abzueglich der Zeichen <c:>
         $this->m_format->set_color(substr($treffer[0],3,$len));
      }

      $this->m_format->set_size($size);
   }
}

class EasyOLEwriter extends OLEwriter
{
   
    function EasyOLEwriter($OLEfilename)
    {
        $this->_OLEfilename  = $OLEfilename;
        $this->_filehandle   = "";
        $this->_tmp_filename = "";
        $this->_fileclosed   = 0;
        //$this->_size_allowed = 0;
        $this->_biffsize     = 0;
        $this->_booksize     = 0;
        $this->_big_blocks   = 0;
        $this->_list_blocks  = 0;
        $this->_root_start   = 0;
        //$this->_block_count  = 4;
        $this->_initialize();
    }
   
   function _initialize()
   {
      //tempverzeichnis erstellen bzw festlegen
      global $CFG;
      $tempDirectory = $CFG->dataroot . '/temp';
      //tempverzeichnis anlegen
      if(!is_dir($tempDirectory)) {
         mkdir($tempDirectory);
      }
      
      $OLEfile = $this->_OLEfilename;
      
      if(($OLEfile == '-') or ($OLEfile == ''))
      {
         $this->_tmp_filename = tempnam($tempDirectory, "OLEwriter");
         $fh = fopen($this->_tmp_filename,"wb");
         if ($fh == false) {
            die("Can't create temporary file.");
         }
      }
      else
      {
         // Create a new file, open for writing (in binmode)
         $fh = fopen($OLEfile,"wb");
         if ($fh == false) {
            die("Can't open $OLEfile. It may be in use or protected.");
         }
      }
   
      // Store filehandle
      $this->_filehandle = $fh;
   }
}
?>
