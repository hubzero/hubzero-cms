<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------
// Class for display of order receipt via fpdf
//----------------------------------------------------------
jimport('fpdf16.fpdf');

/**
 * Short description for 'PDF'
 * 
 * Long description (if any) ...
 */
class PDF extends FPDF
{

	/**
	 * Short description for 'set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	public function set( $property, $value=NULL )
	{
		$this->$property = $value;
	}

	/**
	 * Short description for 'get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @param      unknown $default Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function get( $property, $default=NULL )
	{
		if (isset($this->$property)) {
			return $this->$property;
		}
		return $default;
	}

	//-----------
	// Page header

	/**
	 * Short description for 'Header'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function Header()
	{
		$app =& JFactory::getApplication();
		$database =& JFactory::getDBO();

		// Get front-end temlate name
		$sql = "SELECT template FROM #__templates_menu WHERE client_id=0";
		$database->setQuery( $sql );
		$tmpl = $database->loadResult();

		// Logo
		$this->Image(JPATH_ROOT.DS.'templates'.DS.$tmpl.DS.'images'.DS.'hub-store-logo.png',10,10,'',15);

		$this->SetDrawColor(65,72,100);
		$this->SetLineWidth(0.4);
		$this->Line(10, 25, 200, 25);

		$this->SetFont('Helvetica','B',8);
		$this->SetTextColor(65,72,100);

		// Address
		$this->Ln(5);
		$this->Cell(190,5,$this->headertext_ln1,0,2,'R');
		if ($this->headertext_ln2) {
			$this->Cell(190,5,$this->headertext_ln2,0,2,'R');
		}
		$this->Ln(5);
		$this->SetFont('Helvetica','',8);
		for ($i=0;$i< count($this->hubaddress);$i++)
		{
			if ($this->hubaddress[$i]) {
				$this->Cell(0,5,$this->hubaddress[$i],0,1);
			}
		}

		if ($this->url) {
			$this->Ln(5);
			$this->Cell(0,5,$this->url,0,1);
		}

		$this->SetLineWidth(0.1);
		$this->Ln(5);
		$this->Line(10, $this->GetY(), 200, $this->GetY());
	}

	//-----------
	// Page footer

	/**
	 * Short description for 'Footer'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function Footer()
	{
		// Position at 1.5 cm from bottom
		$this->SetY(-15);
		$this->SetDrawColor(30);
		$this->SetFont('Helvetica','',8);
		$this->SetTextColor(65,72,100);
		$this->SetDrawColor(65,72,100);
		$this->SetLineWidth(0.4);
		$this->Line(10, $this->GetY(), 200, $this->GetY());
		$this->Cell(0,10,$this->footertext,0,0,'C');
		// Page number
		//$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
		if ($this->receipt_note) {
			$this->SetFont('Helvetica','B',14);
			$this->SetY(-30);
			$this->Cell(0,0,$this->receipt_note,0,0,'C');
		}
	}

	//-----------
	// Page title

	/**
	 * Short description for 'mainTitle'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function mainTitle()
	{
		$this->SetFont('Helvetica','B',14);
		$this->SetTextColor(65,72,100);
		$this->Ln(10);
		$this->Cell(0,0,$this->receipt_title,0,0,'L');
	}

	//-----------
	// Warning 

	/**
	 * Short description for 'warning'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $text Parameter description (if any) ...
	 * @return     void
	 */
	public function warning($text)
	{
		$this->Ln(10);
		$this->SetFont('Helvetica','',8);
		$this->SetTextColor(225,0,0);
		$this->Cell(0,0,JText::_($text),0,0);
	}

	//-----------
	// Order Details

	/**
	 * Short description for 'orderDetails'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $customer Parameter description (if any) ...
	 * @param      object $row Parameter description (if any) ...
	 * @param      array $orderitems Parameter description (if any) ...
	 * @return     void
	 */
	public function orderDetails($customer, $row, $orderitems)
	{
		//$this->SetY(40);
		$this->Ln(5);
		$this->SetFont('Helvetica','',8);
		$this->Cell(0,5,$customer->get('name').' ('.$customer->get('username').')',0,2);
		$this->Cell(0,5,$customer->get('email'),0,2);
		$this->Ln(5);
		$this->SetFont('Helvetica','B',8);
		$this->Cell(0,5,JText::_('Order ID').': ',0,0);
		$this->SetX(40);
		$this->SetFont('Helvetica','',8);
		$this->Cell(0,5,$row->id,0,1);
		$this->SetFont('Helvetica','B',8);
		$this->Cell(0,5,JText::_('Order placed').': ',0,0);
		$this->SetX(40);
		$this->SetFont('Helvetica','',8);
		$this->Cell(0,5,JHTML::_('date', $row->ordered, '%d %b, %Y'),0,1);
		$this->SetFont('Helvetica','B',8);
		$this->Cell(0,5,JText::_('Order completed').': ',0,0);
		$this->SetX(40);
		$this->SetFont('Helvetica','',8);
		$this->Cell(0,5,JHTML::_('date', date( 'Y-m-d H:i:s', time() ), '%d %b, %Y'),0,2);
		$this->SetDrawColor(65,72,100);
		$this->SetLineWidth(0.1);
		$this->Ln(5);
		$this->Line(10, $this->GetY(), 200, $this->GetY());
		$this->Ln(5);

		if ($orderitems) {
			$k=1;
			foreach ($orderitems as $o)
			{
				$html ='';
				$html  = $k.'. ['.$o->category.$o->itemid.'] ';
				$html .= $o->title. ' (x'.$o->quantity.')';
				$html .= ($o->selectedsize) ? '- size '.$o->selectedsize : '';
				$this->Cell(0,5,$html,0,0, 'L');
				$this->SetX(-100);
				$this->Cell(0,5,$o->price*$o->quantity.' '.JText::_('points'),0,2,'R');
				$this->SetX(10);
				$k++;
			}
			$this->Ln(5);
			$this->Line(10, $this->GetY(), 200, $this->GetY());
			$this->Ln(5);
		}

		$this->SetX(100);
		$this->SetFont('Helvetica','B',8);
		$this->Cell(0,5,JText::_('Total').': ',0,0);
		$this->SetX(190);
		$this->Cell(0,5,$row->total.' '.JText::_('points'),0,1, 'R');
	}
}

