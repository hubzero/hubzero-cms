<?php
/**
 * Primary controller file for the sitereports component
 * 
 * @package		NEEShub 
 * @author		David Benham (dbenham@purdue.edu)
 * @copyright           Copyright 2010 by NEESCommIT
 */
 
// No direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport('joomla.application.component.controller');

/**
 *Facility Component Controller
 *
 * @package    NEEShub
 * @subpackage Components
 */
class SiteReportsController extends JController
{

    function __construct()
    {
        parent::__construct();

        $this->registerTask( 'injest' , 'injest' );
        $this->registerTask( 'deletesitemembership' , 'deleteSiteReportingSiteMembership' );
        $this->registerTask( 'addsitemembership' , 'addsitemembership' );
    }
	
	
    function display()
    {
    	parent::display();
    }


    function runreport()
    {

        $report =  JRequest::getVar('report', '');
        $filename = '/tmp/' . uniqid() . '.xlsx';
        $quarter = JRequest::getVar('period', '1');
        $year = JRequest::getVar('year', '2010');

        switch($report)
        {
            case 'test1':
                SiteReportsController::generateTestReport($filename);
                SiteReportsController::writeSpreadsheetFileToUser($filename);
            break;
            case 'generateQFRSummary':
                SiteReportsController::QFRSummaryReport($filename, $quarter, $year);
                SiteReportsController::writeSpreadsheetFileToUser($filename);
            break;



        }



        //echo 'done';
    }

    /*
     * Given a file, write it to the browser
     */
    function writeSpreadsheetFileToUser($fullPath)
    {
        if ($fd = fopen ($fullPath, "r")) {
            $fsize = filesize($fullPath);
            $path_parts = pathinfo($fullPath);
            $ext = strtolower($path_parts["extension"]);
            switch ($ext) {
                case "pdf":
                header("Content-type: application/pdf"); // add here more headers for diff. extensions
                header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
                break;
                default;
                header("Content-type: application/octet-stream");
                header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
            }
            header("Content-length: $fsize");
            header("Cache-control: private"); //use this to open files directly
            while(!feof($fd)) {
                $buffer = fread($fd, 2048);
                echo $buffer;
            }
        }
        fclose ($fd);
        exit;

        
    }

    /*
     * generate a report and store it at the location specified
     */
    function generateTestReport($filename)
    {
        
        /** Error reporting */
        error_reporting(E_ALL);
        ini_set('display_errors',TRUE);

        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        // Set properties
        $objPHPExcel->getProperties()->setCreator("NEEScomm IT")
            ->setLastModifiedBy("NEEScomm IT")
            ->setTitle("NEEScomm IT test")
            ->setCategory("Test result file");

        // Add some data
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Hello')
            ->setCellValue('B2', 'world!')
            ->setCellValue('C1', 'Hello')
            ->setCellValue('D2', 'world!');

        // Miscellaneous glyphs, UTF-8
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A4', 'Miscellaneous glyphs')
                    ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Simple Sheet 1');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Save Excel 2007 file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($filename);

    }


    function QFRSummaryReport($filename, $quarter, $year)
    {
        $i = 0; // spreadsheettab index
        $objPHPExcel = new PHPExcel();

        // Set properties
        $objPHPExcel->getProperties()->setCreator("NEEScomm IT")
            ->setLastModifiedBy("NEEScomm IT")
            ->setTitle("NEEScomm IT")
            ->setCategory("QFR Summary File");

        // Loop through all the reporting organziations
        $repOrgs = SiteReportsHelper::getReportingOrgs();

        foreach($repOrgs as $site)
        {
            /* @var $site SiteReportsSite */

            $objPHPExcel->createSheet(intval($i));
            $objPHPExcel->setActiveSheetIndex(intval($i));
            SiteReportsController::styleQFRSummaryTab($objPHPExcel, substr($site->getOrganization()->getShortName(),0,25));
            SiteReportsController::fillQFRSummaryTab($objPHPExcel, $site, $quarter, $year);
            $i++;
        }


        // Write the SiteSum summary tab
        $i++;
        $objPHPExcel->createSheet(intval($i));
        $objPHPExcel->setActiveSheetIndex(intval($i));
        $objPHPExcel->getActiveSheet()->setTitle('SiteSum');
        SiteReportsController::generateSiteSumTab($objPHPExcel);



        // Save Excel 2007 file to the pre specified filename
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($filename);


    }



    function generateSiteSumTab($objPHPExcel)
    {
        /* @var $objPHPExcel PHPExcel */
        /* @var $as PHPExcel_Worksheet */
        $as = $objPHPExcel->getActiveSheet();
        $i; // iterator used for allour foreach loops
        $siteCount = 0;

        //**** Set column/row widths and heights
        $as->getColumnDimension('A')->setWidth(35);

        
        // Loop through all the reporting organziations ($repOrgs will be used a lot)
        $repOrgs = SiteReportsHelper::getReportingOrgs();



        // Reported Budget Distribution section
        $as->setCellValue('A1', 'Reported Budget Distribution');
        $as->getStyle('A1')->getFont()->setSize(20);

        // Planned Budget Distribution Section
        $as->setCellValue('A27', 'Planned Budget Distribution');
        $as->getStyle('A27')->getFont()->setSize(20);


        $i=1; // Start column (B) 
        foreach($repOrgs as $site)
        {
            /* @var $site SiteReportsSite */
            $as->setCellValueByColumnAndRow($i++, 3, $site->getOrganization()->getShortName());
        }
        $as->setCellValueByColumnAndRow($i+1, 3, 'Total');


        // We'll need this to set formulas later
        $siteCount = $i;

        // Row headers
        $as->setCellValue('A4', 'Shared-Use Research Support');
        $as->setCellValue('A5', 'Site Readiness');
        $as->setCellValue('A6', 'Network Requirements');
        $as->setCellValue('A7', 'IT Community Activities');
        $as->setCellValue('A8', 'Facility Enhancement Activities');
        $as->setCellValue('A9', 'Network EOT');
        $as->setCellValue('A10', 'Annualized Equipment Maintenance');
        $as->setCellValue('A11', 'Network Resource Sharing');
        $as->setCellValue('A12', 'Total Estimate');
        $as->setCellValue('A14', 'Total Budget');

        $as->setCellValue('A16', '% of Budget Expensed');
        $as->setCellValue('A17', 'Shared-Use Research Support');
        $as->setCellValue('A18', 'Site Readiness');
        $as->setCellValue('A19', 'Network Requirements');
        $as->setCellValue('A20', 'IT Community Activities');
        $as->setCellValue('A21', 'Facility Enhancement Activities');
        $as->setCellValue('A22', 'Network EOT');
        $as->setCellValue('A23', 'Annualized Equipment Maintenance');
        $as->setCellValue('A24', 'Network Resource Sharing');



        // Reported Budget Distribution Figures for each Org
        $i=1; // $i will be used for column index (Start with Column B)
        $tabIndex = 0;
        foreach($repOrgs as $site)
        {
            // Lets calculate this just once
            $col = SiteReportsHelper::columnLetter($i);

            // Calculate the sums from each of the Grand Total Summary Rows on each tab
            $as->setCellValueByColumnAndRow($i, '4', '=SUM('.$site->getOrganization()->getShortName().'!D35:I35)');
            $as->setCellValueByColumnAndRow($i, '5', '=SUM('.$site->getOrganization()->getShortName().'!D36:I36)');
            $as->setCellValueByColumnAndRow($i, '6', '=SUM('.$site->getOrganization()->getShortName().'!D37:I37)');
            $as->setCellValueByColumnAndRow($i, '7', '=SUM('.$site->getOrganization()->getShortName().'!D38:I38)');
            $as->setCellValueByColumnAndRow($i, '8', '=SUM('.$site->getOrganization()->getShortName().'!D39:I39)');
            $as->setCellValueByColumnAndRow($i, '9', '=SUM('.$site->getOrganization()->getShortName().'!D40:I40)');
            $as->setCellValueByColumnAndRow($i, '10', '=SUM('.$site->getOrganization()->getShortName().'!D41:I41)');
            $as->setCellValueByColumnAndRow($i, '11', '=SUM('.$site->getOrganization()->getShortName().'!D42:I42)');

            // Get the column sums
            $as->setCellValueByColumnAndRow($i, '12', '=SUM('.$site->getOrganization()->getShortName().'!D43:I43)');

            // Retrieve the actual budget amount
            $as->setCellValueByColumnAndRow($i, '14', '='.$site->getOrganization()->getShortName().'!C43');

            // Calculate percentages of each figure as a percentage of the Total budget
            $as->setCellValueByColumnAndRow($i, '16', '='. $col .'12:' . $col . '14)');
            $as->setCellValueByColumnAndRow($i, '17', '='. $col .'4:' . $col . '14)');
            $as->setCellValueByColumnAndRow($i, '18', '='. $col .'5:' . $col . '14)');
            $as->setCellValueByColumnAndRow($i, '19', '='. $col .'6:' . $col . '14)');
            $as->setCellValueByColumnAndRow($i, '20', '='. $col .'7:' . $col . '14)');
            $as->setCellValueByColumnAndRow($i, '21', '='. $col .'8:' . $col . '14)');
            $as->setCellValueByColumnAndRow($i, '22', '='. $col .'9:' . $col . '14)');
            $as->setCellValueByColumnAndRow($i, '23', '='. $col .'10:' . $col . '14)');
            $as->setCellValueByColumnAndRow($i, '24', '='. $col .'11:' . $col . '14)');

            $i++;
        }

        // Calculate the network wide figures, remember to account for an arbitrary number of sites

        // Total column (starting offset, site count, 2 spots after than
        $totalCol = SiteReportsHelper::columnLetter(1+$siteCount+2);
        $lastSiteCol = $totalCol-2;

        $as->setCellValue($totalCol . '4', '=SUM(B4:' . $lastSiteCol . '4)');
        $as->setCellValue($totalCol . '5', '=SUM(B5:' . $lastSiteCol . '5)');
        $as->setCellValue($totalCol . '6', '=SUM(B6:' . $lastSiteCol . '6)');
        $as->setCellValue($totalCol . '7', '=SUM(B7:' . $lastSiteCol . '7)');
        $as->setCellValue($totalCol . '8', '=SUM(B8:' . $lastSiteCol . '8)');
        $as->setCellValue($totalCol . '9', '=SUM(B9:' . $lastSiteCol . '9)');
        $as->setCellValue($totalCol . '10', '=SUM(B10:' . $lastSiteCol . '10)');
        $as->setCellValue($totalCol . '11', '=SUM(B11:' . $lastSiteCol . '11)');
        $as->setCellValue($totalCol . '12', '=SUM(B12:' . $lastSiteCol . '12)');

        // Setup our money cell formats
        $as->getStyle('B4:'.$lastSiteCol.'12')->getNumberFormat()->setFormatCode('_($* #,##0_);_($* (#,##0);_($* "-"??_);_(@_)');
        $as->getStyle('B14:'.$lastSiteCol.'14')->getNumberFormat()->setFormatCode('_($* #,##0_);_($* (#,##0);_($* "-"??_);_(@_)');
        $as->getStyle($lastSiteCol.'4:'.$lastSiteCol.'12')->getNumberFormat()->setFormatCode('_($* #,##0_);_($* (#,##0);_($* "-"??_);_(@_)');

        // Our percentage based cells
        $as->getStyle('B16:'.$lastSiteCol.'24')->getNumberFormat()->setFormatCode('0.00%');
        $as->getStyle($lastSiteCol.'16:'.$lastSiteCol.'24')->getNumberFormat()->setFormatCode('0.00%');



    }

    function fillQFRSummaryTab($objPHPExcel, $site, $q, $y)
    {
        /* @var $site SiteReportsSite */

        $as = $objPHPExcel->getActiveSheet();

        $as->setCellValue('B5', $site->getOrganization()->getName());

        //Look up the QFR submission
        $c = new Criteria();
        $c->add(SiteReportsQFRPeer::QUARTER, $q);
        $c->add(SiteReportsQFRPeer::YEAR, $y);
        $c->add(SiteReportsQFRPeer::FACILITY_ID, $site->getOrganization()->getId());

        /* @var $QFR SiteReportsQFR */
        $QFR = SiteReportsQFRPeer::doSelectOne($c);

        if(!empty($QFR))
        {
            $as->setCellValue('A1', 'Found');

            //print_r($QFR);

            $as->setCellValue('B26', $QFR->getQFR_SR_P_COST());
            $as->setCellValue('B27', $QFR->getQFR_SR_E_COST());
            $as->setCellValue('B28', $QFR->getQFR_SR_PSC_COST());
            $as->setCellValue('B29', $QFR->getQFR_SR_ODC_COST());
            $as->setCellValue('B31', $QFR->getQFR_SR_IC_COST());

            $as->setCellValue('C26', $QFR->getQFR_NR_P_COST());
            $as->setCellValue('C27', $QFR->getQFR_NR_E_COST());
            $as->setCellValue('C28', $QFR->getQFR_NR_PSC_COST());
            $as->setCellValue('C29', $QFR->getQFR_NR_ODC_COST());
            $as->setCellValue('C31', $QFR->getQFR_NR_IC_COST());

            $as->setCellValue('D26', $QFR->getQFR_ITCA_P_COST());
            $as->setCellValue('D27', $QFR->getQFR_ITCA_E_COST());
            $as->setCellValue('D28', $QFR->getQFR_ITCA_PSC_COST());
            $as->setCellValue('D29', $QFR->getQFR_ITCA_ODC_COST());
            $as->setCellValue('D31', $QFR->getQFR_ITCA_IC_COST());

            $as->setCellValue('E26', $QFR->getQFR_FEA_P_COST());
            $as->setCellValue('E27', $QFR->getQFR_FEA_E_COST());
            $as->setCellValue('E28', $QFR->getQFR_FEA_PSC_COST());
            $as->setCellValue('E29', $QFR->getQFR_FEA_ODC_COST());
            $as->setCellValue('E31', $QFR->getQFR_FEA_IC_COST());

            $as->setCellValue('F26', $QFR->getQFR_NEOT_P_COST());
            $as->setCellValue('F27', $QFR->getQFR_NEOT_E_COST());
            $as->setCellValue('F28', $QFR->getQFR_NEOT_PSC_COST());
            $as->setCellValue('F29', $QFR->getQFR_NEOT_ODC_COST());
            $as->setCellValue('F31', $QFR->getQFR_NEOT_IC_COST());

            $as->setCellValue('G26', $QFR->getQFR_AEM_P_COST());
            $as->setCellValue('G27', $QFR->getQFR_AEM_E_COST());
            $as->setCellValue('G28', $QFR->getQFR_AEM_PSC_COST());
            $as->setCellValue('G29', $QFR->getQFR_AEM_ODC_COST());
            $as->setCellValue('G31', $QFR->getQFR_AEM_IC_COST());

            $as->setCellValue('H26', $QFR->getQFR_NRS_P_COST());
            $as->setCellValue('H27', $QFR->getQFR_NRS_E_COST());
            $as->setCellValue('H28', $QFR->getQFR_NRS_PSC_COST());
            $as->setCellValue('H29', $QFR->getQFR_NRS_ODC_COST());
            $as->setCellValue('H31', $QFR->getQFR_NRS_IC_COST());


            $as->setCellValue('C35', $QFR->getFY_BUDGET_SURS());
            $as->setCellValue('C36', $QFR->getFY_BUDGET_SR());
            $as->setCellValue('C37', $QFR->getFY_BUDGET_NR());
            $as->setCellValue('C38', $QFR->getFY_BUDGET_ITCA());
            $as->setCellValue('C39', $QFR->getFY_BUDGET_FEA());
            $as->setCellValue('C40', $QFR->getFY_BUDGET_NEOT());
            $as->setCellValue('C41', $QFR->getFY_BUDGET_AEM());
            $as->setCellValue('C42', $QFR->getFY_BUDGET_NRS());

            $as->setCellValue('D35', $QFR->getQ1RE_SURS());
            $as->setCellValue('D36', $QFR->getQ1RE_SR());
            $as->setCellValue('D37', $QFR->getQ1RE_NR());
            $as->setCellValue('D38', $QFR->getQ1RE_ITCA());
            $as->setCellValue('D39', $QFR->getQ1RE_FEA());
            $as->setCellValue('D40', $QFR->getQ1RE_NEOT());
            $as->setCellValue('D41', $QFR->getQ1RE_AEM());
            $as->setCellValue('D42', $QFR->getQ1RE_NRS());

            $as->setCellValue('E35', $QFR->getQ2RE_SURS());
            $as->setCellValue('E36', $QFR->getQ2RE_SR());
            $as->setCellValue('E37', $QFR->getQ2RE_NR());
            $as->setCellValue('E38', $QFR->getQ2RE_ITCA());
            $as->setCellValue('E39', $QFR->getQ2RE_FEA());
            $as->setCellValue('E40', $QFR->getQ2RE_NEOT());
            $as->setCellValue('E41', $QFR->getQ2RE_AEM());
            $as->setCellValue('E42', $QFR->getQ2RE_NRS());

            $as->setCellValue('F35', $QFR->getQ3RE_SURS());
            $as->setCellValue('F36', $QFR->getQ3RE_SR());
            $as->setCellValue('F37', $QFR->getQ3RE_NR());
            $as->setCellValue('F38', $QFR->getQ3RE_ITCA());
            $as->setCellValue('F39', $QFR->getQ3RE_FEA());
            $as->setCellValue('F40', $QFR->getQ3RE_NEOT());
            $as->setCellValue('F41', $QFR->getQ3RE_AEM());
            $as->setCellValue('F42', $QFR->getQ3RE_NRS());

            $as->setCellValue('G35', $QFR->getQ4RE_SURS());
            $as->setCellValue('G36', $QFR->getQ4RE_SR());
            $as->setCellValue('G37', $QFR->getQ4RE_NR());
            $as->setCellValue('G38', $QFR->getQ4RE_ITCA());
            $as->setCellValue('G39', $QFR->getQ4RE_FEA());
            $as->setCellValue('G40', $QFR->getQ4RE_NEOT());
            $as->setCellValue('G41', $QFR->getQ4RE_AEM());
            $as->setCellValue('G42', $QFR->getQ4RE_NRS());

            $as->setCellValue('H35', $QFR->getPQA_SURS());
            $as->setCellValue('H36', $QFR->getPQA_SR());
            $as->setCellValue('H37', $QFR->getPQA_NR());
            $as->setCellValue('H38', $QFR->getPQA_ITCA());
            $as->setCellValue('H39', $QFR->getPQA_FEA());
            $as->setCellValue('H40', $QFR->getPQA_NEOT());
            $as->setCellValue('H41', $QFR->getPQA_AEM());
            $as->setCellValue('H42', $QFR->getPQA_NRS());

            $as->setCellValue('I35', $QFR->getCQE_SURS());
            $as->setCellValue('I36', $QFR->getCQE_SR());
            $as->setCellValue('I37', $QFR->getCQE_NR());
            $as->setCellValue('I38', $QFR->getCQE_ITCA());
            $as->setCellValue('I39', $QFR->getCQE_FEA());
            $as->setCellValue('I40', $QFR->getCQE_NEOT());
            $as->setCellValue('I41', $QFR->getCQE_AEM());
            $as->setCellValue('I42', $QFR->getCQE_NRS());

            //Note to self: save this, the network was being flakey


        }
        else
        {
            $as->setCellValue('A1', 'Not Found');
        }

    }




    /*
     * Responsiblility of the caller to create the tab BEFORE calling this function
     * This funciton styles the tab, it doesn't populate the tab with any info
     */
    function styleQFRSummaryTab($objPHPExcel, $sheetName)
    {

        // These will be passed in later
        $projectCount = 5;

        // 14 is the standard display for 14 or less, more than that? We go to what we need
        $projectColumnCount = $projectCount > 14 ? $projectCount : 14;

        /** Error reporting */

        /* @var $objPHPExcel PHPExcel */
        $as = $objPHPExcel->getActiveSheet();

        // Turn off gridlines
        $as->setShowGridlines(false);

        // Rename sheet
        $as->setTitle($sheetName);


        //**** Merge Cells
        $as->mergeCells('B5:C5');
        $as->mergeCells('B6:C6');
        $as->mergeCells('B7:C7');
        $as->mergeCells('B8:C8');
        $as->mergeCells('B10:' . SiteReportsHelper::columnLetter($projectColumnCount+1) . '10');
        $as->mergeCells('A10:A14');
        $as->mergeCells('A23:A25');
        $as->mergeCells('I23:K25');
        $as->mergeCells('A34:B34');
        $as->mergeCells('A35:B35');
        $as->mergeCells('A36:B36');
        $as->mergeCells('A37:B37');
        $as->mergeCells('A38:B38');
        $as->mergeCells('A39:B39');
        $as->mergeCells('A40:B40');
        $as->mergeCells('A41:B41');
        $as->mergeCells('A42:B42');
        $as->mergeCells('A43:B43');
        $as->mergeCells('I26:J26');
        $as->mergeCells('I27:J27');
        $as->mergeCells('I28:J28');
        $as->mergeCells('I29:J29');
        $as->mergeCells('I30:J30');
        $as->mergeCells('I31:J31');
        $as->mergeCells('I32:J32');
        $as->mergeCells('B23:B24');
        $as->mergeCells('C23:C24');
        $as->mergeCells('D23:D24');
        $as->mergeCells('E23:E24');
        $as->mergeCells('F23:F24');
        $as->mergeCells('G23:G24');
        $as->mergeCells('H23:H24');
        $as->mergeCells('E8:G8');
        $as->mergeCells('H8:J8');



        //**** Add labels
        $as
            ->setCellValue('A5', 'Institution')
            ->setCellValue('A6', 'Date Prepared')
            ->setCellValue('A7', 'Report Period')
            ->setCellValue('A8', 'Subaward Funded Amount')
            ->setCellValue('A10', 'Category')
            ->setCellValue('A15', '   Personnel')
            ->setCellValue('A16', '   Equipment')
            ->setCellValue('A17', '   Particpant Support Costs')
            ->setCellValue('A18', '   Other Direct Costs')
            ->setCellValue('A19', 'Total Direct Costs')
            ->setCellValue('A20', '   Indirect Costs')
            ->setCellValue('A21', 'Total')
            ->setCellValue('A23', 'Category')
            ->setCellValue('A26', '   Personnel')
            ->setCellValue('A27', '   Equipment')
            ->setCellValue('A28', '   Particpant Support Costs')
            ->setCellValue('A29', '   Other Direct Costs')
            ->setCellValue('A30', 'Total Direct Costs')
            ->setCellValue('A31', '   Indirect Costs')
            ->setCellValue('A32', 'Total')
            ->setCellValue('A34', 'Grand Total Summary')
            ->setCellValue('A35', 'Shared-Use Research Support')
            ->setCellValue('A36', 'Site Readiness')
            ->setCellValue('A37', 'Network Requirements')
            ->setCellValue('A38', 'IT Community Activities')
            ->setCellValue('A39', 'Facility Enhancement Activities')
            ->setCellValue('A40', 'Network EOT')
            ->setCellValue('A41', 'Annualized Equipment Maintenance')
            ->setCellValue('A42', 'Network Resource Sharing')
            ->setCellValue('A43', 'Total')
            ->setCellValue('B10', 'Shared-Use Research Support')
            ->setCellValue('B24', 'Site Readiness')
            ->setCellValue('C24', 'Network Requirements')
            ->setCellValue('D24', 'IT Community Activities')
            ->setCellValue('E24', 'Facility Enhancement Activities')
            ->setCellValue('F24', 'Network EOT')
            ->setCellValue('G24', 'Annualized Equipment Maintenance')
            ->setCellValue('H24', 'Network Resource Sharing')
            ->setCellValue('I23', 'Grand Total')
            ->setCellValue('I32', 'Total')
            ->setCellValue('I26', '   Personnel')
            ->setCellValue('I27', '   Equipment')
            ->setCellValue('I28', '   Particpant Support Costs')
            ->setCellValue('I29', '   Other Direct Costs')
            ->setCellValue('I30', 'Total Indirect Costs')
            ->setCellValue('I31', '   Indirect Costs')
            ->setCellValue('C34', 'FY__ Budget')
            ->setCellValue('D34', 'Q1 Reported Estimate')
            ->setCellValue('E34', 'Q2 Reported Estimate')
            ->setCellValue('F34', 'Q3 Reported Estimate')
            ->setCellValue('G34', 'Q4 Reported Estimate')
            ->setCellValue('H34', 'Previous Quarter Estimate')
            ->setCellValue('I34', 'Current Quarter Estimate')
            ->setCellValue('B23', 'Site Readiness')
            ->setCellValue('C23', 'Network Requirements')
            ->setCellValue('D23', 'IT Community Activities')
            ->setCellValue('E23', 'Facility Enhancement Activities')
            ->setCellValue('F23', 'Network EOT')
            ->setCellValue('G23', 'Annualized Equipment Maintenance')
            ->setCellValue('H23', 'Network Resource Sharing')
            ->setCellValue('J34', 'Estimated Balance')
            ->setCellValue('B25', 'Estimated')
            ->setCellValue('C25', 'Estimated')
            ->setCellValue('D25', 'Estimated')
            ->setCellValue('E25', 'Estimated')
            ->setCellValue('F25', 'Estimated')
            ->setCellValue('G25', 'Estimated')
            ->setCellValue('H25', 'Estimated')
            ->setCellValue('E8', 'NEES Site Operations Master:');

        // After all the projects
        $as->setCellValue(SiteReportsHelper::columnLetter($projectColumnCount+2) . '11' ,'Total');

        // Project Specific cell values
        for($i=1; $i<=$projectColumnCount; $i++)
        {
            $col = SiteReportsHelper::columnLetter($i+1);

            $as->setCellValue($col . '11' ,'Project ' . $i);
            $as->setCellValue($col . '12' ,'(Project Identifier)');
            $as->setCellValue($col . '13' ,'PI Name(s)');
            $as->setCellValue($col . '14' ,'Estimated');

            // Formulas
            $as->setCellValue($col . '19' ,'=SUM('.$col.'15:'.$col.'18)');
            $as->setCellValue($col . '21' ,'=SUM('.$col.'19:'.$col.'20)');
        }



        //**** Set cell alignments
        SiteReportsController::centerCell($objPHPExcel, 'A10', 2);
        SiteReportsController::centerCell($objPHPExcel, 'A23', 2);
        SiteReportsController::centerCell($objPHPExcel, 'B10', 0);
        SiteReportsController::centerCell($objPHPExcel, 'I23', 2);
        SiteReportsController::centerCell($objPHPExcel, 'I23', 2);
        SiteReportsController::centerCell($objPHPExcel, SiteReportsHelper::columnLetter($projectColumnCount+2) . '12', 2);
        SiteReportsController::centerCell($objPHPExcel, 'B11:' . SiteReportsHelper::columnLetter($projectColumnCount+2) . '14', 2);
        SiteReportsController::centerCell($objPHPExcel, 'A34:J34', 2);
        SiteReportsController::centerCell($objPHPExcel, 'B24:H25', 2);
        SiteReportsController::centerCell($objPHPExcel, 'B23:H23', 2);



        //**** Set column/row widths and heights
        $as->getColumnDimension('A')->setWidth(32);

        // Saw no neat function call for doing this on a range of columns
        for($i=2; $i<=$projectColumnCount+2; $i++)
        {
            $as->getColumnDimension(SiteReportsHelper::columnLetter($i))->setWidth(21);
        }

        $as->getRowDimension('12')->setRowHeight('35');
        $as->getRowDimension('24')->setRowHeight('55');
        $as->getRowDimension('34')->setRowHeight('50');



        //**** Set background colors

        $as->getStyle('B10:' . SiteReportsHelper::columnLetter($projectColumnCount+2) . '14')->getFill()->applyFromArray(
            array(  'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('rgb' => 'CCFFCC'))); // green

        $as->getStyle('B15:' . SiteReportsHelper::columnLetter($projectColumnCount+2) . '20')->getFill()->applyFromArray(
            array(  'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array('rgb' => 'FFFFCC'))); //yellow

        $as->getStyle('A10:A14')->getFill()->applyFromArray(array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E9E9E9'))); //gray
        $as->getStyle('A21')->getFill()->applyFromArray(array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E9E9E9'))); // gray
        $as->getStyle('A23:A25')->getFill()->applyFromArray( array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E9E9E9'))); //gray
        $as->getStyle('A23:A25')->getFill()->applyFromArray( array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E9E9E9'))); //gray
        $as->getStyle('A34:J34')->getFill()->applyFromArray( array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E9E9E9'))); //gray
        $as->getStyle('A43')->getFill()->applyFromArray( array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E9E9E9'))); //gray
        $as->getStyle('A32')->getFill()->applyFromArray( array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E9E9E9'))); //gray
        $as->getStyle('I23:K25')->getFill()->applyFromArray( array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E9E9E9'))); //gray
        $as->getStyle('A32')->getFill()->applyFromArray( array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'E9E9E9'))); //gray
        $as->getStyle('B23:H25')->getFill()->applyFromArray( array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'CCFFCC'))); // green
        $as->getStyle('B26:H31')->getFill()->applyFromArray( array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'FFFFCC'))); // yellow
        $as->getStyle('I35:I42')->getFill()->applyFromArray( array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'FFFFCC'))); // yellow
        $as->getStyle('H35:H42')->getFill()->applyFromArray( array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'FFCCCC'))); // yellow


        //**** Set border styles
        $borderStyleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $as->getStyle('A34:J43')->applyFromArray($borderStyleArray);
        $as->getStyle('A23:K32')->applyFromArray($borderStyleArray);
        $as->getStyle('B5:C8')->applyFromArray($borderStyleArray);
        $as->getStyle('A10:' . SiteReportsHelper::columnLetter($projectColumnCount+2) . '10')->applyFromArray($borderStyleArray);
        $as->getStyle('B14:' . SiteReportsHelper::columnLetter($projectColumnCount+2) . '14')->applyFromArray($borderStyleArray);
        $as->getStyle('B11:' . SiteReportsHelper::columnLetter($projectColumnCount+2) . '21')->applyFromArray($borderStyleArray);



        //**** Set Wraps
        $as->getStyle('B24:H24')->getAlignment()->setWrapText(true);
        $as->getStyle('C34:I34')->getAlignment()->setWrapText(true);
        $as->getStyle('B23:H23')->getAlignment()->setWrapText(true);



        //**** Set cell content format - money format
        $as->getStyle('B15:' . SiteReportsHelper::columnLetter($projectColumnCount+2) . '21')->getNumberFormat()->setFormatCode('_($* #,##0_);_($* (#,##0);_($* "-"??_);_(@_)');
        $as->getStyle('B26:H31')->getNumberFormat()->setFormatCode('_($* #,##0_);_($* (#,##0);_($* "-"??_);_(@_)');
        $as->getStyle('C35:J43')->getNumberFormat()->setFormatCode('_($* #,##0_);_($* (#,##0);_($* "-"??_);_(@_)');

        

        //**** Set formulas
        $as->setCellValue(SiteReportsHelper::columnLetter($projectColumnCount+2).'15' ,'=SUM(B15:'. SiteReportsHelper::columnLetter($projectColumnCount+1) .'15)');
        $as->setCellValue(SiteReportsHelper::columnLetter($projectColumnCount+2).'16' ,'=SUM(B16:'. SiteReportsHelper::columnLetter($projectColumnCount+1) .'16)');
        $as->setCellValue(SiteReportsHelper::columnLetter($projectColumnCount+2).'17' ,'=SUM(B17:'. SiteReportsHelper::columnLetter($projectColumnCount+1) .'17)');
        $as->setCellValue(SiteReportsHelper::columnLetter($projectColumnCount+2).'18' ,'=SUM(B18:'. SiteReportsHelper::columnLetter($projectColumnCount+1) .'18)');
        $as->setCellValue(SiteReportsHelper::columnLetter($projectColumnCount+2).'19' ,'=SUM(B19:'. SiteReportsHelper::columnLetter($projectColumnCount+1) .'19)');
        $as->setCellValue(SiteReportsHelper::columnLetter($projectColumnCount+2).'20' ,'=SUM(B20:'. SiteReportsHelper::columnLetter($projectColumnCount+1) .'20)');
        $as->setCellValue(SiteReportsHelper::columnLetter($projectColumnCount+2).'21' ,'=SUM(B21:'. SiteReportsHelper::columnLetter($projectColumnCount+1) .'21)');

        $as->setCellValue('B30','=SUM(B26:B29)');
        $as->setCellValue('B32','=SUM(B30:B31)');
        $as->setCellValue('C30','=SUM(C26:C29)');
        $as->setCellValue('C32','=SUM(C30:C31)');
        $as->setCellValue('D30','=SUM(D26:D29)');
        $as->setCellValue('D32','=SUM(D30:D31)');
        $as->setCellValue('E30','=SUM(E26:E29)');
        $as->setCellValue('E32','=SUM(E30:E31)');
        $as->setCellValue('F30','=SUM(F26:F29)');
        $as->setCellValue('F32','=SUM(F30:F31)');
        $as->setCellValue('G30','=SUM(G26:G29)');
        $as->setCellValue('G32','=SUM(G30:G31)');
        $as->setCellValue('H30','=SUM(H26:H29)');
        $as->setCellValue('H32','=SUM(H30:H31)');
        $as->setCellValue('K26','=SUM(B26:H26)');
        $as->setCellValue('K27','=SUM(B27:H27)');
        $as->setCellValue('K28','=SUM(B28:H28)');
        $as->setCellValue('K29','=SUM(B29:H29)');
        $as->setCellValue('K30','=SUM(B30:H30)');
        $as->setCellValue('K31','=SUM(B30:H30)');
        $as->setCellValue('K32','=SUM(B30:H30)');

        $as->setCellValue('C43','=SUM(C35:C42)');
        $as->setCellValue('D43','=SUM(D35:D42)');
        $as->setCellValue('E43','=SUM(E35:E42)');
        $as->setCellValue('F43','=SUM(F35:F42)');
        $as->setCellValue('G43','=SUM(G35:G42)');
        $as->setCellValue('H43','=SUM(H35:H42)');
        $as->setCellValue('I43','=SUM(I35:I42)');

        $as->setCellValue('J35','=SUM(C35:I35)');
        $as->setCellValue('J36','=SUM(C36:I36)');
        $as->setCellValue('J37','=SUM(C37:I37)');
        $as->setCellValue('J38','=SUM(C38:I38)');
        $as->setCellValue('J39','=SUM(C39:I39)');
        $as->setCellValue('J40','=SUM(C40:I40)');
        $as->setCellValue('J41','=SUM(C41:I41)');
        $as->setCellValue('J42','=SUM(C42:I42)');
        $as->setCellValue('J43','=SUM(C43:I43)');


        // Just for the header
        $as->mergeCells('B1:H1');
        $as->setCellValue('B1' ,'Quarterly Financial Report - Summary');
        $as->getStyle('B1')->getFont()->setSize(20);


        // Protect the Formula cells from accidental editing, have to set entire sheet to protected, then unprotect cells
        $as->getProtection()->setSheet(true);
        $as->getStyle('C35:I42')->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
        $as->getStyle('B26:H29')->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
        $as->getStyle('B31:H31')->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
        $as->getStyle('B15:'.SiteReportsHelper::columnLetter($projectColumnCount+2).'18')->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
        $as->getStyle('B20:'.SiteReportsHelper::columnLetter($projectColumnCount+2).'20')->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
        $as->getStyle('B5:B8')->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
        $as->getStyle('E9:H9')->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);


    }

    /*
     * $horizontalVertical - 0: horiz, 1:vert, 2: both
     */
    function centerCell($objPHPExcel, $cellLocation, $horizontalVertical)
    {
        if($horizontalVertical == 2)
        {
            $objPHPExcel->getActiveSheet()->getStyle($cellLocation)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle($cellLocation)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        }
        else if($horizontalVertical == 1)
            $objPHPExcel->getActiveSheet()->getStyle($cellLocation)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        else if($horizontalVertical == 0)
            $objPHPExcel->getActiveSheet()->getStyle($cellLocation)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);





    }


    function addsitemembership()
    {
        $editpersonid = JRequest::getVar('editpersonid', '-1');
        $facilityID = JRequest::getVar('facilityid', '1002');
        $siteReportsSite = SiteReportsHelper::getSiteReportsSite($facilityID);


        // Todo, check that current logged in user can actually modify membership
        /*if(!$canGrant)
        {
            return JError::raiseError( 500, "You do not have access to grant or edit permissions" );
        }
        */

        $editPerson = PersonPeer::find($editpersonid);

        if(!$editPerson)
            return JError::raiseError( 500, "Cannot locate PersonPeer record with ID of: " . $editperonsid );

        if(!$siteReportsSite)
            return JError::raiseError( 500, "Cannot locate SiteReportsSite record with facilityID of: " . $facilityID );

        $editPersonId = $editPerson->getId();

        $siteReportsSite = SiteReportsHelper::getSiteReportsSite($facilityID);
        $editPerson->removeFromEntity($siteReportsSite);

        $perms = new Permissions(Permissions::PERMISSION_ALL);
        $auth = new Authorization($editpersonid, $siteReportsSite->getID(), DomainEntityType::ENTITY_TYPE_SITEREPORTSSITE, $perms);
        $auth->save();

        $this->_redirect =  JRoute::_('index.php?option=com_sitereports&view=security&facilityid=' . $facilityID );
        $this->redirect();
    }


    function deleteSiteReportingSiteMembership()
    {
        $facilityID = Jrequest::getVar('facilityid','1002');
        $editpersonid = JRequest::getVar('editpersonid', '-1');

        $canGrant = true; //SiteReportsHelper::canGrant($siteReportsSite);

        if($editpersonid == -1)
            return JError::raiseError( 500, "No personid available");

        if($facilityID == -1)
            return JError::raiseError( 500, "No facilityid available");

        if(!$canGrant)
            return JError::raiseError( 500, "You do not have access to grant or edit facility members for facilityID:" . $facilityID);

        $siteReportsSite = SiteReportsHelper::getSiteReportsSite($facilityID);

        /* @var $editPerson Person */
        $editPerson = PersonPeer::find($editpersonid);
        $editPerson->removeFromEntity($siteReportsSite);

        $this->_redirect =  JRoute::_('index.php?option=com_sitereports&view=security&facilityid=' . $facilityID );
        $this->redirect();
    }


    /*
     * Primary inject task, calls several sub injestion tasks as indicated by the input provided
     * via a webform
     */
    function injest()
    {
        $injestEntry = '';
        $year = '';
        $period = '';
        $reportType = '';
        $summaryMsg = '';
        $temp = '';

        //Grab all the reportingSites
        $reportingSites = SiteReportsSitePeer::doSelectJoinOrganization(new Criteria());

        // Each site *might* have something to do
        /* @var $reportingSite SiteReportsSite */
        foreach($reportingSites as $reportingSite)
        {

            // Each site potentially has a select list with a name set to the ID of organization (site)
            // associated with it, the selected item in each select list has a pipe delimited string
            // that indicates what to do for the site.
            // Below is the format of the delimited string:
            //
            // year||period||reporttype||reportFile
            // ---------------------------------------
            // Year - to process
            // Period - quarter for the report (or 0 for annual)
            // reporttype - QAR, QFR, Annual
            // reportFile - the path to the file for ingestion
            $siteID = $reportingSite->getEntityID();

            // Like to do just siteID, but Joomla craps out wheh the name of a form
            // element is just a number, so the prepended 's'
            $siteSelectListSelectedItem = JRequest::getVar('s'.$siteID, '');


            if(!empty ($siteSelectListSelectedItem))
            {
                $injestEntry = explode('|', $siteSelectListSelectedItem);
                $year = $injestEntry[0];
                $period = $injestEntry[1];
                $reportType = $injestEntry[2];
                $reportFile = $injestEntry[3];
            }
            else
            {
                $year = '';
                $period = '';
                $reportType = '';
                $reportFile = '';
            }
            
            switch(strtolower($reportType))
            {
                case "qfr":
                    $temp = $this->injestQFR($reportFile, $siteID, $period, $year);
                break;

                case "qar":

                break;

            }

            if(!empty ($siteSelectListSelectedItem))
            {
                if(empty($temp))
                    $summaryMsg .= $reportType . ' Import for ' . $reportingSite->getOrganization()->getName() . ' sucessful';
                else
                    $summaryMsg .= $reportType . ' Import for ' . $reportingSite->getOrganization()->getName() . ' failed. Input file: ' . $reportFile . ' Msg:' . $temp;
            }
            
            $temp = '';

        }

        echo $summaryMsg;
        //$this->_redirect =  JRoute::_('index.php?option=com_sitereports&view=sitesubmissions');
    }

    /*
     * Save a little typing wiht this check, this function is used hundreds of times.
     * Throw exception if value isn't a float. The Propel framework sets non-numberic fields to 0 when saved to database
     */
    function checkFloat($ss, $cell)
    {
        /* @var $ss PHPExcel */

        $rv = null;
        $v = trim($ss->getActiveSheet()->getCell($cell)->getValue());

        if(!empty($v))
        {
            if(is_numeric($v))
                $rv = $v;
            else
                throw new Exception('Failed call to checkFloat, non-numeric data: (' . $v . ') in cell: ' . $cell . ' on sheetIndex: ' .  $ss->getActiveSheetIndex());
        }
        
        return $rv;



    }

    /*
     * Simple wrapper for extracting strings, return null for blank
     */
    function checkString($ss, $cell)
    {
        $rv = null;
        $v = $ss->getActiveSheet()->getCell($cell)->getValue();

        if(!empty($v))
            $rv = $v;

        return $rv;
    }

    
    function injestQFR($file, $facilityID, $period, $year)
    {
        $rv = '';

        try{
            // Start transaction
            $oConnection = Propel::getConnection();
            $oConnection->begin();

            // Create the PHPExcel spreadsheet object from the file
            /* @var $objPHPExcel PHPExcel */
            $objPHPExcel = PHPExcel_IOFactory::load($file);
            $objPHPExcel->setActiveSheetIndex(0);

            $QFR = new SiteReportsQFR();
            $QFR->setYear($year);
            $QFR->setQUARTER($period);
            $QFR->setFACILITY_ID($facilityID);

            $QFR->setPREPARED_BY($this->checkString($objPHPExcel, 'E9'));
            $QFR->setPREPARERS_TITLE($this->checkString($objPHPExcel, 'H9'));
            $QFR->setSUBAWARDED_FUNDED_AMT($this->checkString($objPHPExcel, 'B8'));

            $QFR->setQFR_SR_P_COST($this->checkFloat($objPHPExcel, 'B26'));
            $QFR->setQFR_SR_E_COST($this->checkFloat($objPHPExcel, 'B27'));
            $QFR->setQFR_SR_PSC_COST($this->checkFloat($objPHPExcel, 'B28'));
            $QFR->setQFR_SR_ODC_COST($this->checkFloat($objPHPExcel, 'B29'));
            $QFR->setQFR_SR_IC_COST($this->checkFloat($objPHPExcel, 'B31'));

            $QFR->setQFR_NR_P_COST($this->checkFloat($objPHPExcel, 'C26'));
            $QFR->setQFR_NR_E_COST($this->checkFloat($objPHPExcel, 'C27'));
            $QFR->setQFR_NR_PSC_COST($this->checkFloat($objPHPExcel, 'C28'));
            $QFR->setQFR_NR_ODC_COST($this->checkFloat($objPHPExcel, 'C29'));
            $QFR->setQFR_NR_IC_COST($this->checkFloat($objPHPExcel, 'C31'));

            $QFR->setQFR_ITCA_P_COST($this->checkFloat($objPHPExcel, 'D26'));
            $QFR->setQFR_ITCA_E_COST($this->checkFloat($objPHPExcel, 'D27'));
            $QFR->setQFR_ITCA_PSC_COST($this->checkFloat($objPHPExcel, 'D28'));
            $QFR->setQFR_ITCA_ODC_COST($this->checkFloat($objPHPExcel, 'D29'));
            $QFR->setQFR_ITCA_IC_COST($this->checkFloat($objPHPExcel, 'D31'));

            $QFR->setQFR_FEA_P_COST($this->checkFloat($objPHPExcel, 'E26'));
            $QFR->setQFR_FEA_E_COST($this->checkFloat($objPHPExcel, 'E27'));
            $QFR->setQFR_FEA_PSC_COST($this->checkFloat($objPHPExcel, 'E28'));
            $QFR->setQFR_FEA_ODC_COST($this->checkFloat($objPHPExcel, 'E29'));
            $QFR->setQFR_FEA_IC_COST($this->checkFloat($objPHPExcel, 'E31'));

            $QFR->setQFR_NEOT_P_COST($this->checkFloat($objPHPExcel, 'F26'));
            $QFR->setQFR_NEOT_E_COST($this->checkFloat($objPHPExcel, 'F27'));
            $QFR->setQFR_NEOT_PSC_COST($this->checkFloat($objPHPExcel, 'F28'));
            $QFR->setQFR_NEOT_ODC_COST($this->checkFloat($objPHPExcel, 'F29'));
            $QFR->setQFR_NEOT_IC_COST($this->checkFloat($objPHPExcel, 'F31'));

            $QFR->setQFR_AEM_P_COST($this->checkFloat($objPHPExcel, 'G26'));
            $QFR->setQFR_AEM_E_COST($this->checkFloat($objPHPExcel, 'G27'));
            $QFR->setQFR_AEM_PSC_COST($this->checkFloat($objPHPExcel, 'G28'));
            $QFR->setQFR_AEM_ODC_COST($this->checkFloat($objPHPExcel, 'G29'));
            $QFR->setQFR_AEM_IC_COST($this->checkFloat($objPHPExcel, 'G31'));

            $QFR->setQFR_AEM_P_COST($this->checkFloat($objPHPExcel, 'H26'));
            $QFR->setQFR_AEM_E_COST($this->checkFloat($objPHPExcel, 'H27'));
            $QFR->setQFR_AEM_PSC_COST($this->checkFloat($objPHPExcel, 'H28'));
            $QFR->setQFR_AEM_ODC_COST($this->checkFloat($objPHPExcel, 'H29'));
            $QFR->setQFR_AEM_IC_COST($this->checkFloat($objPHPExcel, 'H31'));

            $QFR->setFY_BUDGET_SURS($this->checkFloat($objPHPExcel, 'C35'));
            $QFR->setFY_BUDGET_SR($this->checkFloat($objPHPExcel, 'C36'));
            $QFR->setFY_BUDGET_NR($this->checkFloat($objPHPExcel, 'C37'));
            $QFR->setFY_BUDGET_ITCA($this->checkFloat($objPHPExcel, 'C38'));
            $QFR->setFY_BUDGET_FEA($this->checkFloat($objPHPExcel, 'C39'));
            $QFR->setFY_BUDGET_NEOT($this->checkFloat($objPHPExcel, 'C40'));
            $QFR->setFY_BUDGET_AEM($this->checkFloat($objPHPExcel, 'C41'));
            $QFR->setFY_BUDGET_NRS($this->checkFloat($objPHPExcel, 'C42'));

            $QFR->setQ1RE_SURS($this->checkFloat($objPHPExcel, 'D35'));
            $QFR->setQ1RE_SR($this->checkFloat($objPHPExcel, 'D36'));
            $QFR->setQ1RE_NR($this->checkFloat($objPHPExcel, 'D37'));
            $QFR->setQ1RE_ITCA($this->checkFloat($objPHPExcel, 'D38'));
            $QFR->setQ1RE_FEA($this->checkFloat($objPHPExcel, 'D39'));
            $QFR->setQ1RE_NEOT($this->checkFloat($objPHPExcel, 'D40'));
            $QFR->setQ1RE_AEM($this->checkFloat($objPHPExcel, 'D41'));
            $QFR->setQ1RE_NRS($this->checkFloat($objPHPExcel, 'D42'));

            $QFR->setQ2RE_SURS($this->checkFloat($objPHPExcel, 'E35'));
            $QFR->setQ2RE_SR($this->checkFloat($objPHPExcel, 'E36'));
            $QFR->setQ2RE_NR($this->checkFloat($objPHPExcel, 'E37'));
            $QFR->setQ2RE_ITCA($this->checkFloat($objPHPExcel, 'E38'));
            $QFR->setQ2RE_FEA($this->checkFloat($objPHPExcel, 'E39'));
            $QFR->setQ2RE_NEOT($this->checkFloat($objPHPExcel, 'E40'));
            $QFR->setQ2RE_AEM($this->checkFloat($objPHPExcel, 'E41'));
            $QFR->setQ2RE_NRS($this->checkFloat($objPHPExcel, 'E42'));

            $QFR->setQ3RE_SURS($this->checkFloat($objPHPExcel, 'F35'));
            $QFR->setQ3RE_SR($this->checkFloat($objPHPExcel, 'F36'));
            $QFR->setQ3RE_NR($this->checkFloat($objPHPExcel, 'F37'));
            $QFR->setQ3RE_ITCA($this->checkFloat($objPHPExcel, 'F38'));
            $QFR->setQ3RE_FEA($this->checkFloat($objPHPExcel, 'F39'));
            $QFR->setQ3RE_NEOT($this->checkFloat($objPHPExcel, 'F40'));
            $QFR->setQ3RE_AEM($this->checkFloat($objPHPExcel, 'F41'));
            $QFR->setQ3RE_NRS($this->checkFloat($objPHPExcel, 'F42'));

            $QFR->setQ4RE_SURS($this->checkFloat($objPHPExcel, 'G35'));
            $QFR->setQ4RE_SR($this->checkFloat($objPHPExcel, 'G36'));
            $QFR->setQ4RE_NR($this->checkFloat($objPHPExcel, 'G37'));
            $QFR->setQ4RE_ITCA($this->checkFloat($objPHPExcel, 'G38'));
            $QFR->setQ4RE_FEA($this->checkFloat($objPHPExcel, 'G39'));
            $QFR->setQ4RE_NEOT($this->checkFloat($objPHPExcel, 'G40'));
            $QFR->setQ4RE_AEM($this->checkFloat($objPHPExcel, 'G41'));
            $QFR->setQ4RE_NRS($this->checkFloat($objPHPExcel, 'G42'));

            $QFR->setPQA_SURS($this->checkFloat($objPHPExcel, 'H35'));
            $QFR->setPQA_SR($this->checkFloat($objPHPExcel, 'H36'));
            $QFR->setPQA_NR($this->checkFloat($objPHPExcel, 'H37'));
            $QFR->setPQA_ITCA($this->checkFloat($objPHPExcel, 'H38'));
            $QFR->setPQA_FEA($this->checkFloat($objPHPExcel, 'H39'));
            $QFR->setPQA_NEOT($this->checkFloat($objPHPExcel, 'H40'));
            $QFR->setPQA_AEM($this->checkFloat($objPHPExcel, 'H41'));
            $QFR->setPQA_NRS($this->checkFloat($objPHPExcel, 'H42'));

            $QFR->setCQE_SURS($this->checkFloat($objPHPExcel, 'I35'));
            $QFR->setCQE_SR($this->checkFloat($objPHPExcel, 'I36'));
            $QFR->setCQE_NR($this->checkFloat($objPHPExcel, 'I37'));
            $QFR->setCQE_ITCA($this->checkFloat($objPHPExcel, 'I38'));
            $QFR->setCQE_FEA($this->checkFloat($objPHPExcel, 'I39'));
            $QFR->setCQE_NEOT($this->checkFloat($objPHPExcel, 'I40'));
            $QFR->setCQE_AEM($this->checkFloat($objPHPExcel, 'I41'));
            $QFR->setCQE_NRS($this->checkFloat($objPHPExcel, 'I42'));

            // Supplemental Budget entries 1-4 are on the 2nd tab (zero indexed)
            $objPHPExcel->setActiveSheetIndex(1);

            $QFR->setSUPBUD_SUP1_P($this->checkFloat($objPHPExcel, 'B3'));
            $QFR->setSUPBUD_SUP1_E($this->checkFloat($objPHPExcel, 'B4'));
            $QFR->setSUPBUD_SUP1_PSC($this->checkFloat($objPHPExcel, 'B5'));
            $QFR->setSUPBUD_SUP1_ODC($this->checkFloat($objPHPExcel, 'B6'));
            $QFR->setSUPBUD_SUP1_IC($this->checkFloat($objPHPExcel, 'B8'));
            $QFR->setSUPBUD_SUP1_SA($this->checkFloat($objPHPExcel, 'B10'));

            $QFR->setSUPBUD_SUP2_P($this->checkFloat($objPHPExcel, 'C3'));
            $QFR->setSUPBUD_SUP2_E($this->checkFloat($objPHPExcel, 'C4'));
            $QFR->setSUPBUD_SUP2_PSC($this->checkFloat($objPHPExcel, 'C5'));
            $QFR->setSUPBUD_SUP2_ODC($this->checkFloat($objPHPExcel, 'C6'));
            $QFR->setSUPBUD_SUP2_IC($this->checkFloat($objPHPExcel, 'C8'));
            $QFR->setSUPBUD_SUP2_SA($this->checkFloat($objPHPExcel, 'C10'));

            $QFR->setSUPBUD_SUP3_P($this->checkFloat($objPHPExcel, 'D3'));
            $QFR->setSUPBUD_SUP3_E($this->checkFloat($objPHPExcel, 'D4'));
            $QFR->setSUPBUD_SUP3_PSC($this->checkFloat($objPHPExcel, 'D5'));
            $QFR->setSUPBUD_SUP3_ODC($this->checkFloat($objPHPExcel, 'D6'));
            $QFR->setSUPBUD_SUP3_IC($this->checkFloat($objPHPExcel, 'D8'));
            $QFR->setSUPBUD_SUP3_SA($this->checkFloat($objPHPExcel, 'D10'));

            $QFR->setSUPBUD_SUP4_P($this->checkFloat($objPHPExcel, 'E3'));
            $QFR->setSUPBUD_SUP4_E($this->checkFloat($objPHPExcel, 'E4'));
            $QFR->setSUPBUD_SUP4_PSC($this->checkFloat($objPHPExcel, 'E5'));
            $QFR->setSUPBUD_SUP4_ODC($this->checkFloat($objPHPExcel, 'E6'));
            $QFR->setSUPBUD_SUP4_IC($this->checkFloat($objPHPExcel, 'E8'));
            $QFR->setSUPBUD_SUP4_SA($this->checkFloat($objPHPExcel, 'E10'));

            $user =& JFactory::getUser();
            $username = $user->get('username');
            if (empty($username)) $username = '<unknown>';

            $QFR->setCREATED_BY($username);
            $QFR->setUPDATED_BY($username);
            $QFR->setUPDATED_ON(Date('m/d/Y h:m'));
            $QFR->setCREATED_ON(Date('m/d/Y h:m'));

            $QFR->save();

            // Create the projects
            $this->injestQFR_Projects($QAR->getID(), $objPHPExcel);

            // Create the EPCD records
            $this->injestQFR_EPCD($QAR->getID(), $objPHPExcel);

            $this->injestQFR_ProgramIncome($QAR->getID(), $objPHPExcel);

            // Commit transaction
            $oConnection->commit();
        }
        catch(Exception $e)
        {
            // Any exception should cause rollback of transaction
            $oConnection->rollback();

            // Todo - handle this better, array of errors returned to caller
            $rv = $e;
        }

        return $rv;
        
    }

    function injestQFR_ProgramIncome($QFRID, $objPHPExcel)
    {
        /* @var $objPHPExcel PHPExcel */

        $objPHPExcel->setActiveSheetIndex(3);
        
        $pi = new SiteReportsQFRPrgInc();
        
        $pi->setQFR_ID($QFRID);
        $pi->setBEG_BAL($this->checkFloat($objPHPExcel, 'B8'));
        $pi->setPRG_INC_REC($this->checkFloat($objPHPExcel, 'B10'));
        $pi->setPRG_INC_EXP($this->checkFloat($objPHPExcel, 'B12'));
        //$pi->setEND_BAL($this->checkFloat($objPHPExcel, 'B14'));
        $pi->setNAR($this->checkFloat($objPHPExcel, 'B18'));

        $user =& JFactory::getUser();
        $username = $user->get('username');
        if (empty($username)) $username = '<unknown>';

        $pi->setCREATED_BY($username);
        $pi->setUPDATED_BY($username);
        $pi->setUPDATED_ON(Date('m/d/Y h:m'));
        $pi->setCREATED_ON(Date('m/d/Y h:m'));

        $pi->save();
        
    }


    function injestQFR_Projects($QFRID, $objPHPExcel)
    {
        /* @var $objPHPExcel PHPExcel */

        $objPHPExcel->setActiveSheetIndex(0);

        // Find out how many projects there are
        $startingColumn = 2; //Column B
        $i = $startingColumn;
        $projectCount = 0;
        $temp = '';
        $loop = true;

        while($loop)
        {
            $temp = $objPHPExcel->getActiveSheet()->getCell(SiteReportsHelper::columnLetter($i).'12')->getValue();

            // The NEES_XXXX_XXXX project identifier is required, just look for a 4 digit number anywhere in this cell
            // and assume that means a file is there.
            $matchCount = preg_match('/[0-9][0-9][0-9][0-9]*/', $temp);
            if($matchCount==0)
                $loop=false;
            else
                $i++;
        }

       $projectCount = $i-2;


        for($i=0; $i<$projectCount; $i++)
        {

            $excelCol = SiteReportsHelper::columnLetter($startingColumn + $i);

            $p = new SiteReportsQFRProject();
            $p->setQFR_ID($QFRID);
            $p->setPROJECT_ID($this->checkString($objPHPExcel, $excelCol . '12'));
            $p->setPI($this->checkString($objPHPExcel, $excelCol . '13'));
            $p->setP_COST($this->checkFloat($objPHPExcel, $excelCol . '15'));
            $p->setE_COST($this->checkFloat($objPHPExcel, $excelCol . '16'));
            $p->setPSC_COST($this->checkFloat($objPHPExcel, $excelCol . '17'));
            $p->setODC_COST($this->checkFloat($objPHPExcel, $excelCol . '18'));
            $p->setIC_COST($this->checkFloat($objPHPExcel, $excelCol . '20'));

            $user =& JFactory::getUser();
            $username = $user->get('username');
            if (empty($username)) $username = '<unknown>';

            $p->setCREATED_BY($username);
            $p->setUPDATED_BY($username);
            $p->setUPDATED_ON(Date('m/d/Y h:m'));
            $p->setCREATED_ON(Date('m/d/Y h:m'));

            $p->save();

        }

    }


    function injestQFR_EPCD($QFRID, $objPHPExcel)
    {
        $objPHPExcel->setActiveSheetIndex(2);

        // Grab the user name for the import
        $user =& JFactory::getUser();
        $username = $user->get('username');
        if (empty($username)) $username = '<unknown>';


        // Search for first non blank entry in the Equipment section
        $startingRow = 6;
        $i = $startingRow;
        $equipmentCount = 0;
        $PSCCount = 0;
        $temp = '';
        $loop = true;

        // Get the Equipment count (entries start at A6)
        while($loop)
        {
            $temp = $objPHPExcel->getActiveSheet()->getCell('A'. $i)->getValue();

            $matchCount = preg_match('/TOTAL EQUIPMENT*/', strtoupper($temp));
            if($matchCount==1 || empty($temp))
                $loop=false;
            else
            {
                $equipmentCount++;
                $i++;
            }
        }

        // Get the start row of PSC section by scanning for 'FY10 Participant' text
        $i = 14;
        while($loop)
        {
            $temp = $objPHPExcel->getActiveSheet()->getCell('A'. $i)->getValue();

            $matchCount = preg_match('/FY10 PART*/', strotupper($temp));
            if($matchCount==1)
                $loop=false;
            else
                $i++;
        }

        // PSC entries start 2 later than the header we just found
        $i+=2;
        $PSCEntryStartRow = $i;

       
        // Get the PSC entry count
        $PSCCount = 0;
        $loop = true;
        while($loop)
        {
            $temp = $objPHPExcel->getActiveSheet()->getCell('A'. $i)->getValue();

            // Last entry might be followed by a blank line or by the 'TOTAL...' summary line if
            // the list is completely full
            $matchCount = preg_match('/TOTAL PARTICIPANT SUPPORT COSTS*/', strtoupper($temp));
            if($matchCount==1 || empty($temp))
                $loop=false;
            else
            {
                $PSCCount++;
                $i++;
            }
        }


        // Scan and create the Equipment records
        for($i=0; $i<$equipmentCount; $i++)
        {
            // The offset starts 6 rows down from the top of the sheet
            $row = $i + 6;

            $e = new SiteReportsQFREPcd();

            $e->setQFR_ID($QFRID);
            $e->setEQ_OR_PSC_TYPE(0); // 0 for the equipment records
            $e->setDESCRIPTION($this->checkString($objPHPExcel, 'A' . $row));
            $e->setDETAILS($this->checkString($objPHPExcel, 'B' . $row));
            $e->setEST_AMT($this->checkString($objPHPExcel, 'C' . $row));

            $e->setCREATED_BY($username);
            $e->setUPDATED_BY($username);
            $e->setUPDATED_ON(Date('m/d/Y h:m'));
            $e->setCREATED_ON(Date('m/d/Y h:m'));

            $e->save();
        }


        // Scan and create the PSC Entries
        for($i=0; $i<$PSCCount; $i++)
        {
            // The offset starts a set number of rows below the header, depending on the
            // number of rows of Equipment
            $row = $i + $PSCEntryStartRow;

            $e = new SiteReportsQFREPcd();

            $e->setQFR_ID($QFRID);
            $e->setEQ_OR_PSC_TYPE(1); // 1 for the PSC records
            $e->setDESCRIPTION($this->checkString($objPHPExcel, 'A' . $row));
            $e->setDETAILS($this->checkString($objPHPExcel, 'B' . $row));
            $e->setEST_AMT($this->checkString($objPHPExcel, 'C' . $row));

            $e->setCREATED_BY($username);
            $e->setUPDATED_BY($username);
            $e->setUPDATED_ON(Date('m/d/Y h:m'));
            $e->setCREATED_ON(Date('m/d/Y h:m'));

            $e->save();
        }

    }


    function injestQAR($file, $facilityID, $period, $year)
    {

        $rv = '';

        try{
            // Start transaction
            $oConnection = Propel::getConnection();
            $oConnection->begin();

            // Create the PHPExcel spreadsheet object from the file
            /* @var $objPHPExcel PHPExcel */
            $objPHPExcel = PHPExcel_IOFactory::load($file);

            $QAR = new SiteReportsQAR();
            $QAR->setYear($year);
            $QAR->setQUARTER($period);
            $QAR->setFACILITY_ID($facilityID);

            // Required Base Services Tab
            $objPHPExcel->setActiveSheetIndex(1);

            // Date format checking function needed
            //$this->checkFloat($objPHPExcel, 'F35')
            //$QAR->setRBS_SS_LAST_REV_DATE_Q1();
            //$QAR->setRBS_SS_LAST_REV_DATE_Q2();
            //$QAR->setRBS_SS_LAST_REV_DATE_Q3();
            //$QAR->setRBS_SS_LAST_REV_DATE_Q4();
            $QAR->setRBS_SS_RI_Q1($this->checkFloat($objPHPExcel, 'C8'));
            $QAR->setRBS_SS_RI_Q2($this->checkFloat($objPHPExcel, 'D8'));
            $QAR->setRBS_SS_RI_Q3($this->checkFloat($objPHPExcel, 'E8'));
            $QAR->setRBS_SS_RI_Q4($this->checkFloat($objPHPExcel, 'F8'));
            $QAR->setRBS_SS_INJURY_NAR($this->checkString($objPHPExcel, 'C9'));
            $QAR->setRBS_SS_PSA_NAR($this->checkString($objPHPExcel, 'C10'));

            $QAR->setRBS_PMCR_PPM_PRG_Q1($this->checkFloat($objPHPExcel, 'C13'));
            $QAR->setRBS_PMCR_PPM_PRG_Q2($this->checkFloat($objPHPExcel, 'D13'));
            $QAR->setRBS_PMCR_PPM_PRG_Q3($this->checkFloat($objPHPExcel, 'E13'));
            $QAR->setRBS_PMCR_PPM_PRG_Q4($this->checkFloat($objPHPExcel, 'F13'));
            $QAR->setRBS_PMCR_PPM_NAR($this->checkString($objPHPExcel, 'C14'));

            $QAR->setRBS_PMCR_PC_PRG_Q1($this->checkFloat($objPHPExcel, 'C16'));
            $QAR->setRBS_PMCR_PC_PRG_Q2($this->checkFloat($objPHPExcel, 'D16'));
            $QAR->setRBS_PMCR_PC_PRG_Q3($this->checkFloat($objPHPExcel, 'E16'));
            $QAR->setRBS_PMCR_PC_PRG_Q4($this->checkFloat($objPHPExcel, 'F16'));
            $QAR->setRBS_PMCR_PC_NAR($this->checkString($objPHPExcel, 'C17'));

            $QAR->setRBS_PMCR_PR_PRG_Q1($this->checkFloat($objPHPExcel, 'C19'));
            $QAR->setRBS_PMCR_PR_PRG_Q2($this->checkFloat($objPHPExcel, 'D19'));
            $QAR->setRBS_PMCR_PR_PRG_Q3($this->checkFloat($objPHPExcel, 'E19'));
            $QAR->setRBS_PMCR_PR_PRG_Q4($this->checkFloat($objPHPExcel, 'F19'));
            $QAR->setRBS_PMCR_PR_NAR($this->checkString($objPHPExcel, 'C20'));

            // Capacity Building Initiatives tab
            $objPHPExcel->setActiveSheetIndex(2);
            $QAR->setCB_FE_PRG_Q1($this->checkFloat($objPHPExcel, 'C7'));
            $QAR->setCB_FE_PRG_Q2($this->checkFloat($objPHPExcel, 'D7'));
            $QAR->setCB_FE_PRG_Q3($this->checkFloat($objPHPExcel, 'E7'));
            $QAR->setCB_FE_PRG_Q4($this->checkFloat($objPHPExcel, 'F7'));
            $QAR->setCB_FE_NAR($this->checkString($objPHPExcel, 'C8'));

            // Network Initiatives Tab
            $objPHPExcel->setActiveSheetIndex(3);
            $QAR->setNI_ITCA_PRG_Q1($this->checkFloat($objPHPExcel, 'C7'));
            $QAR->setNI_ITCA_PRG_Q2($this->checkFloat($objPHPExcel, 'D7'));
            $QAR->setNI_ITCA_PRG_Q3($this->checkFloat($objPHPExcel, 'E7'));
            $QAR->setNI_ITCA_PRG_Q4($this->checkFloat($objPHPExcel, 'F7'));
            $QAR->setNI_ITCA_NAR($this->checkString($objPHPExcel, 'C8'));

            $QAR->setNI_NEOT_PRG_Q1($this->checkFloat($objPHPExcel, 'C11'));
            $QAR->setNI_NEOT_PRG_Q2($this->checkFloat($objPHPExcel, 'D11'));
            $QAR->setNI_NEOT_PRG_Q3($this->checkFloat($objPHPExcel, 'E11'));
            $QAR->setNI_NEOT_PRG_Q4($this->checkFloat($objPHPExcel, 'F11'));
            $QAR->setNI_NEOT_NAR($this->checkString($objPHPExcel, 'C12'));

            $QAR->setNI_NRS_PRG_Q1($this->checkFloat($objPHPExcel, 'C15'));
            $QAR->setNI_NRS_PRG_Q2($this->checkFloat($objPHPExcel, 'D15'));
            $QAR->setNI_NRS_PRG_Q3($this->checkFloat($objPHPExcel, 'E15'));
            $QAR->setNI_NRS_PRG_Q4($this->checkFloat($objPHPExcel, 'F15'));
            $QAR->setNI_NRS_NAR($this->checkString($objPHPExcel, 'C16'));

            // Facility Highlights
            $objPHPExcel->setActiveSheetIndex(4);
            $QAR->setFH($this->checkString($objPHPExcel, 'C6'));

            // Annualized Equipment Maintenance (AEM) tab
            $objPHPExcel->setActiveSheetIndex(5);
            $QAR->setAEM_NAR($this->checkString($objPHPExcel, 'C6'));

            // Supplement Awards
            $objPHPExcel->setActiveSheetIndex(6);
            $QAR->setSA1_PRG_Q1($this->checkFloat($objPHPExcel, 'C7'));
            $QAR->setSA1_PRG_Q2($this->checkFloat($objPHPExcel, 'D7'));
            $QAR->setSA1_PRG_Q3($this->checkFloat($objPHPExcel, 'E7'));
            $QAR->setSA1_PRG_Q4($this->checkFloat($objPHPExcel, 'F7'));
            $QAR->setSA1_NAR($this->checkString($objPHPExcel, 'C8'));
            
            $QAR->setSA2_PRG_Q1($this->checkFloat($objPHPExcel, 'C10'));
            $QAR->setSA2_PRG_Q2($this->checkFloat($objPHPExcel, 'D10'));
            $QAR->setSA2_PRG_Q3($this->checkFloat($objPHPExcel, 'E10'));
            $QAR->setSA2_PRG_Q4($this->checkFloat($objPHPExcel, 'F10'));
            $QAR->setSA2_NAR($this->checkString($objPHPExcel, 'C11'));

            $QAR->setSA3_PRG_Q1($this->checkFloat($objPHPExcel, 'C13'));
            $QAR->setSA3_PRG_Q2($this->checkFloat($objPHPExcel, 'D13'));
            $QAR->setSA3_PRG_Q3($this->checkFloat($objPHPExcel, 'E13'));
            $QAR->setSA3_PRG_Q4($this->checkFloat($objPHPExcel, 'F13'));
            $QAR->setSA3_NAR($this->checkString($objPHPExcel, 'C14'));



            $user =& JFactory::getUser();
            $username = $user->get('username');
            if (empty($username)) $username = '<unknown>';

            $QAR->setCREATED_BY($username);
            $QAR->setUPDATED_BY($username);
            $QAR->setUPDATED_ON(Date('m/d/Y h:m'));
            $QAR->setCREATED_ON(Date('m/d/Y h:m'));

            $QFR->save();


            // Commit transaction
            $oConnection->commit();
        }
        catch(Exception $e)
        {
            // Any exception should cause rollback of transaction
            $oConnection->rollback();

            // Todo - handle this better, array of errors returned to caller
            $rv = $e;
        }

        return $rv;


    }

    function injestQAR_EOT($QARID, $objPHPExcel)
    {
        $eventRowHeight = 4;
        $eventStartRow = 7;
        $loop = true;
        $temp = '';
        $i = 0;
        $eventCount = 0;

        // EOT Event Report tab
        $objPHPExcel->setActiveSheetIndex(7);

        // Grab the user name for the import
        $user =& JFactory::getUser();
        $username = $user->get('username');
        if (empty($username)) $username = '<unknown>';

        // Get the number of events
        while($loop)
        {
            $temp = $objPHPExcel->getActiveSheet()->getCell('A'. ($eventStartRow + ($i * 4)))->getValue();

            if(empty($temp))
                $loop=false;
            else
            {
                $equipmentCount++;
                $i++;
            }
           
        }

        $eventCount = $i;

        for($i=0 ; i<$eventCount; $i++)
        {
            $e = new SiteReportsQAREotEvt();

            $e->setEVENT_TYPE($this->checkString($objPHPExcel, 'A' . ($eventStartRow + ($i * 4)) ) );
            $e->setACTIVITY($this->checkString($objPHPExcel, 'B' . ($eventStartRow + ($i * 4)) ) );
            $e->setACTIVITY_OBJECTIVES($this->checkString($objPHPExcel, 'C' . ($eventStartRow + ($i * 4)) ) );
            $e->setOBJECTIVE_MET($this->checkString($objPHPExcel, 'D' . ($eventStartRow + ($i * 4)) ) );

            $e->setPARTICIPANT_CAT1($this->checkString($objPHPExcel, 'F' . ($eventStartRow + ($i * 4)) ) );
            $e->setNUM_OF_PARTICIPANTS1($this->checkString($objPHPExcel, 'F' . ($eventStartRow + ($i * 4)) ) );
            $e->setPARTICIPANT_DETAILS1($this->checkString($objPHPExcel, 'F' . ($eventStartRow + ($i * 4)) ) );

            $e->setPARTICIPANT_CAT2($this->checkString($objPHPExcel, 'F' . ($eventStartRow + ($i * 4))+1 ) );
            $e->setNUM_OF_PARTICIPANTS2($this->checkString($objPHPExcel, 'F' . ($eventStartRow + ($i * 4))+1 ) );
            $e->setPARTICIPANT_DETAILS2($this->checkString($objPHPExcel, 'F' . ($eventStartRow + ($i * 4))+1 ) );

            $e->setPARTICIPANT_CAT3($this->checkString($objPHPExcel, 'F' . ($eventStartRow + ($i * 4))+2 ) );
            $e->setNUM_OF_PARTICIPANTS3($this->checkString($objPHPExcel, 'F' . ($eventStartRow + ($i * 4))+2 ) );
            $e->setPARTICIPANT_DETAILS3($this->checkString($objPHPExcel, 'F' . ($eventStartRow + ($i * 4))+2 ) );

            $e->setPARTICIPANT_CAT4($this->checkString($objPHPExcel, 'F' . ($eventStartRow + ($i * 4))+3 ) );
            $e->setNUM_OF_PARTICIPANTS4($this->checkString($objPHPExcel, 'F' . ($eventStartRow + ($i * 4))+3 ) );
            $e->setPARTICIPANT_DETAILS4($this->checkString($objPHPExcel, 'F' . ($eventStartRow + ($i * 4))+3 ) );

            $e->setEVENT_NAR($this->checkString($objPHPExcel, 'A' . ($eventStartRow + ($i * 4)) ) );

            $e->setCREATED_BY($username);
            $e->setUPDATED_BY($username);
            $e->setUPDATED_ON(Date('m/d/Y h:m'));
            $e->setCREATED_ON(Date('m/d/Y h:m'));

            $e->save();
        }

    }

    function injestQAR_RPS($QARID, $objPHPExcel)
    {
        $projectRowHeight = 9;
        $projectStartRow = 5;
        $loop = true;
        $i = 0;
        $projectCount = 0;
        $temp = '';

        // Research Project Support tab
        $objPHPExcel->setActiveSheetIndex(0);

        // Grab the user name for the import for later
        $user =& JFactory::getUser();
        $username = $user->get('username');
        if (empty($username)) $username = '<unknown>';

        // Get the number of projects
        while($loop)
        {
            $temp = $objPHPExcel->getActiveSheet()->getCell('A'. ($projectStartRow + ($i * projectRowHeight)))->getValue();

            $matchCount = preg_match('/Project [0-9]{?}*/', $temp);

            if(empty($temp))
                $loop=false;
            else
            {
                $projectCount++;
                $i++;
            }

        }

        $projectCount = $i;

        for($i=0; i<$projectCount; $i++)
        {
            $projectUpperLeftRow = $projectStartRow + ($i * $projectRowHeight);

            $p = new SiteReportsQARRPS();

            $p->setPI_NAME($this->checkString($objPHPExcel, 'A' .  $projectUpperLeftRow + 3));
            $p->setINSTITUTION($this->checkString($objPHPExcel, 'B' . $projectUpperLeftRow + 3));
            $p->setPPP_FY_START_PRG($this->checkFloat($objPHPExcel, 'C' . $projectUpperLeftRow + 3));
            $p->setPPP_FY_END_PRG($this->checkFloat($objPHPExcel, 'D' . $projectUpperLeftRow + 3));
            $p->setAPP_Q1($this->checkFloat($objPHPExcel, 'E' . $projectUpperLeftRow + 3));
            $p->setAPP_Q2($this->checkFloat($objPHPExcel, 'F' . $projectUpperLeftRow + 3));
            $p->setAPP_Q3($this->checkFloat($objPHPExcel, 'G' . $projectUpperLeftRow + 3));
            $p->setAPP_Q4($this->checkFloat($objPHPExcel, 'H' . $projectUpperLeftRow + 3));
            $p->setPROJECT_WEIGHT($this->checkFloat($objPHPExcel, 'I' . $projectUpperLeftRow + 3));
            $p->set($this->checkFloat($objPHPExcel, 'J' . $projectUpperLeftRow + 3));

            $p->setQ1_NAR($this->checkString($objPHPExcel, 'B' .  $projectUpperLeftRow + 5));
            $p->setQ2_NAR($this->checkString($objPHPExcel, 'B' .  $projectUpperLeftRow + 6));
            $p->setQ3_NAR($this->checkString($objPHPExcel, 'B' .  $projectUpperLeftRow + 7));
            $p->setQ4_NAR($this->checkString($objPHPExcel, 'B' .  $projectUpperLeftRow + 8));

            $p->setCREATED_BY($username);
            $p->setUPDATED_BY($username);
            $p->setUPDATED_ON(Date('m/d/Y h:m'));
            $p->setCREATED_ON(Date('m/d/Y h:m'));

            $p->save();
        }

    }



    public function redirect()
    {
        if ($this->_redirect != NULL)
        {
            $app =& JFactory::getApplication();
            $app->redirect( $this->_redirect, $this->_message, $this->_messageType );
        }
    }



}
