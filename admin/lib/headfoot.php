<?php
include_once(CP_LIBRARY_PATH.'lib_php/tcpdf-extra/headfoot.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF_Local extends MYPDF{
	//Page header
	public function Header() {
		$cpCfg = Zend_Registry::get('cpCfg');
		$fn    = Zend_Registry::get('fn');
		$site_id  = $fn->getSessionParam('cp_site_id');
		$this->SetFont('helvetica','B',9);
			/*$header='<table border="1" width="100%">';
			$header= $header.'
			<tr>
			<td><p style="line-height:160%;font-size:16pt"><img src="images/logo-print.gif" width="150px" height="25px"/><br/>
			<font style="font-size:10pt;">'.$cpCfg['cp.addressPdf1'].'<br/>'.$cpCfg['cp.addressPdf2'].' '.$cpCfg['cp.addressPdf3'].'<br/>'.$cpCfg['cp.addressPdf4'].' / '.$cpCfg['cp.addressPdf5'].'</font></p></td>
			</tr>
			';
			$header=$header.'</table>
			';*/

			if($site_id == 1) {
				$header='<table border="0" width="100%">';
				$header= $header.'
				<tr>
				<td width="20%"><img src="images/logo-print.png" width="100px"/></td>
				<td width="80%"><p style="font-size:24pt; line-height:0.5;"><br/>'.$cpCfg['cp.companyName'].'<br/>
					<font style="font-size:9pt; line-height:0.6;">'.$cpCfg['cp.dealersInPdf'].'</font><br/>
					<font style="font-size:8pt; line-height:1.5;">'.$cpCfg['cp.addressPdf1'].'<br/>'.$cpCfg['cp.emailAddressPdf'].'</font></p></td>
				</tr>
				';
				$header=$header.'</table>
				';
				$this->writeHTML($header, true, false, false, false, '');
				$this->SetTopMargin(50);
			} else {
				$this->SetFont('calibri','B',9);
				$header='<table border="0" width="100%">';
				$header= $header.'
				<tr>
					<td width="45%" align="left" style="border-bottom:4px solid #e5e5e5;"><p style="font-size:30pt; line-height:0.5; color:#157ca7;"><br/>'.$cpCfg['cp.companyName'].'<br/>
						<font style="font-size:9pt; line-height:0.6; color:#000000;">'.$cpCfg['cp.dealersInPdf'].'</font></p>
					</td>
					<td width="55%" align="right" style="border-bottom:4px solid #157ca7;"><p><font style="font-size:9pt; line-height:1.5;">'.$cpCfg['cp.addressPdf1'].'<br/>'.$cpCfg['cp.emailAddressPdf'].'<br/>'.$cpCfg['cp.addressPdf4'].'</font><br/></p>
					</td>
				</tr>
				';
				$header=$header.'</table>
				';
				$this->writeHTML($header, true, false, false, false, '');
				$this->SetTopMargin(35);
			}

	}

	public function Footer() {
		$this->SetFont('Courier','B',9);
      // Page number
      //$this->Cell(0, 10, '(This is computer generated document, and does not require a signature) Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'R');
      $footer='<table border="0" width="100%">';
			$footer= $footer.'
			<tr>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td width="78%">(This is computer generated document, and does not require a signature)</td>
				<td width="22%" align="right">Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages().'</td>
			</tr>';
			$footer=$footer.'</table>';
			$this->writeHTML($footer, true, false, false, false, '');
    }
}
?>