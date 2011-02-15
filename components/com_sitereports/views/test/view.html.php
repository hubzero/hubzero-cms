<?php
/**
 * @package		NEEShub 
 * @author		David Benham (dbenha@purdue.edu)
 * @copyright           Copyright 2010 by NEES
*/
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
/**
 *
 *
 *
 *
 *
 */
 
class sitereportsViewtest extends JView
{

    function display($tpl = null)
    {


        for($i=0; $i< 10000 ; $i++)
        {

            //$x->convertStringToNumber('ABCZ');
            $convert = SiteReportsHelper::columnLetter($i);

            echo $i . ' - ' . $convert . '<br/>';

        }




        parent::display($tpl);
    }



    function spreadsheetread()
    {
        /* @var $objPHPExcel PHPExcel */
        $objPHPExcel = PHPExcel_IOFactory::load(JPATH_COMPONENT.DS. 'files/QAR.v1.xlsx');

        $objPHPExcel->setActiveSheetIndex(0);
        $r = $objPHPExcel->getActiveSheet()->getCell('C11')->getValue();

        print_r($r);


    }

}
