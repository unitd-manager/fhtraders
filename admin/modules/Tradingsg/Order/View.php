<?
class CPL_Admin_Modules_Tradingsg_Order_View extends CP_Admin_Modules_Tradingsg_Order_View
{
    /**
     *
     */
    function getList($dataArray){
        $listObj = Zend_Registry::get('listObj');
        $db = Zend_Registry::get('db');
        $tv = Zend_Registry::get('tv');
        $fn = Zend_Registry::get('fn');
        $cpCfg = Zend_Registry::get('cpCfg');

        $rows  = "";
        $rowCounter = 0;

        //--------------------------------------------------------------------------//
        foreach ($dataArray as $row){
            $creation_date = $fn->getCPDate($row['order_date'], 'd-m-Y');
            $currency = strtoupper($row['currency']);
            $order_amount = $row['order_amount'];

            if($cpCfg['m.tradingsg.order.addGstAmountToOrderTotal']){
                $gsttaxperc   = $cpCfg['amtForGSTCalc'] ;
                $order_amount = $row['order_amount'] + ($row['order_amount'] * $gsttaxperc/100);
            }

            $rows .= "
            {$listObj->getListRowHeader($row, $rowCounter)}
            {$listObj->getGoToDetailText($rowCounter, $row['order_id'])}
            {$listObj->getListDataCell($row['companyName'])}
            {$listObj->getListDataCell($creation_date)}
            {$listObj->getListDataCell($currency.'&nbsp;'.number_format(round($order_amount), 2))}
            {$listObj->getListDataCell($row['order_status'])}
            {$listObj->getListPublishedImage($row['published'], $row['order_id'])}
            {$listObj->getListRowEnd($row['order_id'])}
            ";
            $rowCounter++ ;
        }

        $text = "
        {$listObj->getListHeader()}
        {$listObj->getListHeaderCell('Order Id', 'o.order_id')}
        {$listObj->getListHeaderCell('Company Name', 'c.companyName')}
        {$listObj->getListHeaderCell('Order Date', 'o.order_date')}
        {$listObj->getListHeaderCell('Amount', '')}
        {$listObj->getListHeaderCell('Status', 'o.order_status')}
        {$listObj->getListHeaderCell('Published', 'o.published', 'headerCenter')}
        {$listObj->getListHeaderEnd()}
        {$rows}
        {$listObj->getListFooter()}
        ";

        return $text;
    }

    /**
     *
     */
    function getEdit($row) {
        $tv = Zend_Registry::get('tv');
        $cpCfg = Zend_Registry::get('cpCfg');
        $formObj = Zend_Registry::get('formObj');
        $dateUtil = Zend_Registry::get('dateUtil');
        $ln = Zend_Registry::get('ln');

        $formObj->mode = $tv['action'];

        $expStatus = array('sqlType' => 'OneField');
        $expNoEdit = array('isEditable' => 0);

        $sqlCountry = getCPModelObj('common_geoCountry')->getCountryDDSQL();
        $expCountry = array('detailValue' => $row['shipping_address_country']);

        $creation_date = $dateUtil->formatDate($row['creation_date'], 'DD MM YYYY');

        $currency = strtoupper($row['currency']);

        $order_amount = $row['order_amount'];

        if($cpCfg['m.tradingsg.order.addGstAmountToOrderTotal']){
            $gsttaxperc   = $cpCfg['amtForGSTCalc'] ;
            $order_amount = $row['order_amount'] + ($row['order_amount'] * $gsttaxperc/100);
        }

        $discount = '';
        if ($cpCfg['m.ecommerce.order.hasDiscount']){
            $discount = $formObj->getTBRow('Discount', 'discount', $row['discount']);
        }

        $quote = "<a href='index.php?_topRm=order&module=tradingsg_quote&record_id={$row['quote_id']}&_action=edit'>{$row['quote_code']}</a>";

        $fielset1 = "
        {$formObj->getTBRow('Order Id', 'order_id', $row['order_id'], $expNoEdit)}
        {$formObj->getTBRow('Quote Code', 'quote_id', $quote, $expNoEdit)}
        {$formObj->getDateRow('Order Date', 'order_date', $row['order_date'])}
        {$formObj->getTBRow('Amount', 'amount', $currency.'&nbsp;'. number_format(round($order_amount), 2), $expNoEdit)}
        {$discount}
        {$formObj->getDDRowByArr('Status', 'order_status', $cpCfg['m.ecommerce.order.statusArr'], $row['order_status'], $expStatus)}
        {$formObj->getTARow('Terms', 'invoice_terms', $row['invoice_terms'])}
        {$formObj->getTARow('Notes', 'notes', $row['notes'])}
        {$formObj->getYesNoRRow('IGST', 'igst_show', $row['igst_show'])}
        ";

        //{$formObj->getTBRow('Country', 'company_country_name', $row['company_country_name'], $expNoEdit)}

        $fielset2 = "
        {$formObj->getTBRow('Company Name', 'shipping_first_name', $row['shipping_first_name'])}
        {$formObj->getTBRow('Address 1', 'shipping_address1', $row['shipping_address1'])}
        {$formObj->getTBRow('Address 2', 'shipping_address2', $row['shipping_address2'])}
        {$formObj->getTBRow('District/ Town', 'shipping_address_city', $row['shipping_address_city'])}
        {$formObj->getTBRow('State/ Zip', 'shipping_address_state', $row['shipping_address_state'])}
        {$formObj->getDDRowBySQL('Country', 'shipping_address_country', $sqlCountry, $row['shipping_address_country'], $expCountry)}
        ";

        $fielset3 = "
        {$formObj->getTBRow('Company Name', 'companyName', $row['companyName'], $expNoEdit)}
        {$formObj->getTBRow('Website', 'company_website', $row['company_website'], $expNoEdit)}
        {$formObj->getTBRow('Fax', 'company_fax', $row['company_fax'], $expNoEdit)}
        {$formObj->getTBRow('Phone', 'company_phone', $row['company_phone'], $expNoEdit)}
        {$formObj->getTBRow('Office Address', 'company_address_flat', $row['company_address_flat'], $expNoEdit)}
        {$formObj->getTBRow('Street Address', 'company_address_street', $row['company_address_street'], $expNoEdit)}
        {$formObj->getTBRow('District / Town', 'company_address_town', $row['company_address_town'], $expNoEdit)}
        {$formObj->getTBRow('State / Zip', 'company_address_state', $row['company_address_state'], $expNoEdit)}
        ";

        $text = "
        {$formObj->getFieldSetWrapped('Main Details', $fielset1)}
        {$formObj->getFieldSetWrapped('Delivery Address', $fielset2)}
        {$formObj->getFieldSetWrapped('Customer Details', $fielset3)}
        {$formObj->getCreationModificationText($row)}
        ";

        return $text;
    }
    
    /**
     *
     */
    function getPrintInvoiceRecord1() {
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $tv = Zend_Registry::get('tv');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $searchVar = Zend_Registry::get('searchVar');
        $media = Zend_Registry::get('media');
        $cpPaths = Zend_Registry::get('cpPaths');
        $dbUtil = Zend_Registry::get('dbUtil');

        ini_set('memory_limit', '512M');

        set_time_limit(50000);

        include_once(CP_LIBRARY_PATH.'lib_php/fpdf/fpdf.php');
        include_once(CP_LIBRARY_PATH.'lib_php/fpdf-extra/html2pdf.php');
        include_once(CP_LIBRARY_PATH.'lib_php/fpdf-extra/html_table1.php');
        include_once(CP_LIBRARY_PATH.'lib_php/fpdf-extra/mc_table.php');

        //$pdf = new MYPDF();
        $pdf = new PDF_MC_Table();
		$pdf->AddPage();
		$pdf->SetFont('Arial','',11);

        $invoiceHeading = '';

        $invoice_code = $fn->getReqParam('invoice_code');
        $invoice_type = $fn->getReqParam('invoice_type');
        if($invoice_type == 'normal'){
            $invoiceHeading = '';
        }
        else if($invoice_type == 'transporter'){
            $invoiceHeading = 'TRANSPORTER - ';
        }
        else if($invoice_type == 'proforma'){
            $invoiceHeading = 'PROFORMA - ';
        }
        else if($invoice_type == 'extra'){
            $invoiceHeading = 'EXTRA - ';
        }


        $SQL = "
        SELECT ini.*
              ,ini.item_title AS product_title
              ,p.title AS product_title1
              ,p.unit
              ,p.item_code
			  ,p.part_number
              ,p.hsn
              ,c.company_name
              ,c.address_flat
              ,c.address_street
              ,c.address_town
              ,c.address_state
              , (SELECT gc.name FROM geo_country gc
                 WHERE gc.country_code = c.address_country)
                AS address_country
              ,c.billing_address_flat
              ,c.billing_address_street
              ,c.billing_address_town
              ,c.billing_address_state
              , (SELECT gc.name FROM geo_country gc
                 WHERE gc.country_code = c.billing_address_country)
                AS billing_address_country
              ,c.fax
              ,c.phone
              ,c.tin_no
              ,c.cst_no
              ,c.gst_no
              ,i.invoice_date
              ,q.delivery_date
              ,q.delivery_location
              ,ini.unit_price
              ,i.invoice_code
              ,i.invoice_terms
              ,i.invoice_due_date
              ,i.notes
              ,i.cst
              ,i.vat
              ,i.sgst
              ,i.igst_cgst
              ,i.cst_value
              ,i.vat_value
              ,i.sgst_value
              ,i.igst_cgst_value
              ,i.frieght_cost
              ,i.cust_po_no
              ,i.p_f
			  ,o.order_id
			  ,o.shipping_address1
			  ,o.shipping_first_name
			  ,o.shipping_address2
			  ,o.shipping_address_city
			  ,o.shipping_address_state
			   ,(SELECT gc.name FROM geo_country gc
			     WHERE gc.country_code = o.shipping_address_country)
			     AS shipping_address_country
              ,q.quote_code
              ,q.currency
              ,ini.qty * ini.unit_price AS amount
              ,(SELECT SUM(init.qty * init.unit_price) FROM invoice_item init
               WHERE init.invoice_id = ini.invoice_id) AS sub_total
        FROM invoice_item ini
        LEFT JOIN product p ON (p.product_id = ini.record_id)
        LEFT JOIN invoice i ON (i.invoice_id = ini.invoice_id)
        LEFT JOIN `order` o ON (o.order_id = i.order_id)
        LEFT JOIN company c ON (c.company_id = o.company_id)
        LEFT JOIN quote q ON (q.quote_id = o.quote_id)
        LEFT JOIN product_group pg ON (p.product_group_id = pg.product_group_id)
        WHERE i.invoice_code = '{$invoice_code}'
          AND i.status != 'Cancelled'
        ORDER BY ini.invoice_item_id, pg.sort_order ASC, p.title
        ";
        $result = $db->sql_query($SQL);

        $numRows  = $db->sql_numrows($result);

        $today = date("Y-m-d");
		if ($numRows == 0){
            $pdf->SetXY(30,30);
            $pdf->Cell(50, 20, "Please set the values for your Order and print the PDF");
			$pdf->Output();
			return;
		}

        $count = 0;
        $total = 0;
        $discount_price = 0;
        $rows = "";
        $lineItemNumber = 1;  // To increment the line item in receipt
		$printTaxName = '';
		$gsttaxvalue = '';
		$gstvalue = '';
		$totalvalue = '';
        $totalpf = '';



        //============================================================================= //
        $pdf->SetFont('Arial','',9);
        //syed:multi text code to set width of each column and alignment
        $pdf->SetWidths(array(10, 55, 30, 25, 11, 13, 21, 25));
        $pdf->SetAligns(array('L', 'L', 'L', 'L', 'L', 'L', 'R', 'R'));

        while ($row = $db->sql_fetchrow($result)) {
            if ($count == 0){
                /* Logo of the institution */
                $pdf->Image('images/logo-print.png',80,5,55);

                //$pdf->SetXY(152,8);
                //$pdf->Cell(50, 2, 'Authorized Distributor of:');
                //$pdf->Image('images/parker.jpg',152,11, 25);

                //$pdf->Image('images/gse.png',42,25, 25);
                $creationDate   = $fn->getCPDate($row['invoice_date'], 'd-m-Y');
                $invoiceDueDate = $fn->getCPDate($row['invoice_due_date'], 'd-m-Y');
                $deliveryDate   = $fn->getCPDate($row['delivery_date'], 'd-m-Y');
				$currency = $row['currency'];

				/*$gsttaxvalue = $cpCfg['amtForGSTCalc'] ;
				$gstvalue = $row['sub_total'] * $gsttaxvalue / 100;*/
				//$totalvalue = $gstvalue + $row['sub_total'];
				$totalvalue += $row['sub_total'];

                /* Company address */
                //Address to be got from settings
                $pdf->SetXY(52,8);
                $pdf->Cell(30, 20, $cpCfg['cp.addressPdf1'] . ' ' . $cpCfg['cp.addressPdf2']. ' ' . $cpCfg['cp.addressPdf3']);
                $pdf->SetXY(66,8);
				$pdf->Cell(30,31, $cpCfg['cp.addressPdf4'] . '  '. $cpCfg['printEmailAddress']);
                $pdf->SetXY(53,19);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf7'] . '  '. $cpCfg['cp.addressPdf6']);


                /*$pdf->SetXY(130,0);
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(50, 20, $cpCfg['cp.companyName']);
                $pdf->Ln(5);
                $pdf->SetXY(130,5);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf1']);
                $pdf->Ln(5);
                $pdf->SetXY(130,10);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf2']);
                $pdf->Ln(5);
                $pdf->SetXY(130,15);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf3']);
                $pdf->Ln(5);
                $pdf->SetXY(130, 20);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf4']);
                $pdf->Ln(5);
                $pdf->SetXY(130,25);
                $pdf->Cell(50, 20, $cpCfg['printEmailAddress']);
                $pdf->Ln(5);
                $pdf->SetXY(130,30);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf7']);
                $pdf->Ln(5);
                $pdf->SetXY(130,35);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf6']);*/
//addressPdf4
                /* Header */
                $pdf->SetFont('Arial','BU',9);
                $pdf->SetXY(80, 30);
                $pdf->Cell(50, 20, $invoiceHeading . " INVOICE", 0, 0, 'C');
                $pdf->SetFont('Arial','B',9);
                $pdf->Ln(15);

                /* Company Details*/

				if ($row['shipping_address1'] != ''
					|| $row['shipping_address2'] != ''
					|| $row['shipping_address_city'] != ''
					|| $row['shipping_address_state'] != ''
					|| $row['shipping_address_country'] != '') {
						//Delivery Address Fields in Order
						$deliveryAddressFlat 	= $row['shipping_address1'];
						$deliveryAddressStreet 	= $row['shipping_address2'];
						$deliveryAddressTown 	= $row['shipping_address_city'];
						$deliveryAddressState 	= $row['shipping_address_state'];
						$deliveryAddressCountry = $row['shipping_address_country'];
						$deliveryCompanyName 	= $row['shipping_first_name'];
				} else {
					//Delivery Address Fields in client
					$deliveryAddressFlat 	= $row['address_flat'];
					$deliveryAddressStreet 	= $row['address_street'];
					$deliveryAddressTown 	= $row['address_town'];
					$deliveryAddressState 	= $row['address_state'];
					$deliveryAddressCountry = $row['address_country'];
					$deliveryCompanyName 	= $row['company_name'];
				}

                /* Invoice Details*/
                $pdf->SetFont('Arial','B',9);
                $pdf->SetFillColor(254,203,156);
                $pdf->Cell(30.5,8,"INVOICE NO :",1,0, 'L', 1);
                $pdf->SetFont('Arial','',9);
                $pdf->SetFillColor(255,255,255);
	            $pdf->Cell(30.5, 8, $row['invoice_code'], 1, 0, 'L', 1);
                $pdf->SetFont('Arial','B',9);
                $pdf->SetFillColor(254,203,156);
                $pdf->Cell(20.5,8,"DATE :",1,0, 'L', 1);
                $pdf->SetFont('Arial','',9);
                $pdf->SetFillColor(255,255,255);
	            $pdf->Cell(27.5, 8, $creationDate, 1, 0, 'L', 1);
                $pdf->SetFont('Arial','B',9);
                $pdf->SetFillColor(254,203,156);
                $pdf->Cell(50.5,8,"CUST PO NO :",1,0, 'L', 1);
                $pdf->SetFont('Arial','',9);
                $pdf->SetFillColor(255,255,255);
	            $pdf->Cell(30.5, 8, $row['cust_po_no'], 1, 0, 'L', 1);
                $pdf->Ln(12);

                /* Company Details*/

                $date = $fn->getCPDate($row['delivery_date'], 'd-m-Y');

                $pdf->SetFont('Arial','B',9);
                $pdf->SetFillColor(254,203,156);
                $pdf->Cell(95,8,"INVOICE TO",1,0, 'L', 1);
                $pdf->Cell(95,8,"DELIVERY TO",1,0, 'L', 1);
                $pdf->Ln();
                $pdf->SetFillColor(255,255,255);
                $pdf->SetFont('Arial','B',9);

                $pdf->SetFont('Arial','',9);
                $pdf->Cell(95, 8, $row['company_name'],'LR', 0, 'L', 1);
            	$pdf->Cell(95, 8, $deliveryCompanyName , 'LR', 0, 'L', 1);
                $pdf->Ln();
                $pdf->SetFont('Arial','',9);
            	$pdf->Cell(95, 5, $row['billing_address_flat'], 'LR', 0, 'L', 1);
	            $pdf->Cell(95, 5, $deliveryAddressFlat, 'LR', 0, 'L', 1);
                $pdf->Ln();
                $pdf->SetFont('Arial','',9);
	            $pdf->Cell(95, 5, $row['billing_address_street'], 'LR', 0, 'L', 1);
	            $pdf->Cell(95, 5, $deliveryAddressStreet, 'LR', 0, 'L', 1);
                $pdf->Ln();
	        	$pdf->Cell(95, 5, $row['billing_address_town'], 'LR', 0, 'L', 1);
	            $pdf->Cell(95, 5, $deliveryAddressTown, 'LR', 0, 'L', 1);
                $pdf->Ln();
                $pdf->SetFont('Arial','',9);
	            $pdf->Cell(95, 5, $row['billing_address_country'] .' - '. $row['billing_address_state'], 'LR', 0, 'L', 1);
                $pdf->SetFont('Arial','',9);
	            $pdf->Cell(95, 5, $deliveryAddressCountry .' - '. $deliveryAddressState, 'LR', 0, 'L', 1);
                $pdf->Ln();
                $pdf->Cell(95, 8, 'TIN NO:' . $row['tin_no'], 'LR', 0, 'L', 1);
                $pdf->Cell(95, 8, 'TIN NO:' .$row['tin_no'], 'LR', 0, 'L', 1);
                $pdf->Ln(6);
                $pdf->Cell(95, 8, 'CST NO:' . $row['cst_no'], 'LR', 0, 'L', 1);
                $pdf->Cell(95, 8, 'CST NO:' .$row['cst_no'], 'LR', 0, 'L', 1);
                $pdf->Ln(8);
                $pdf->Cell(95, 6, 'GST NO:' . $row['gst_no'], 'BLR', 0, 'L', 1);
                $pdf->Cell(95, 6, 'GST NO:' .$row['gst_no'], 'BLR', 0, 'L', 1);

                $pdf->Ln(10);

				$terms = $row['invoice_terms'];
				$bank = $cpCfg['cp.bankDetails'];

	            $pdf->SetFont('Arial','B',9);
	            $pdf->SetFillColor(254,203,156);
	            $pdf->Cell(95,8,"TERMS",1,0, 'L', 1);
	            $pdf->Cell(95,8,"BANK DETAILS",1,0, 'L', 1);
	            $pdf->SetFont('Arial','',9);
	            $pdf->SetFillColor(255,255,255);
                $pdf->SetXY(10,125);
	            $pdf->drawTextBox($terms, 95, 33, 'L', 'C', 1);
                $pdf->SetXY(105,125);
	            $pdf->drawTextBox($bank, 95, 33, 'L', 'C', 'BLR');
	            $pdf->Ln(20);

                /* List of order items header */
                $pdf->SetFont('Arial','B',9);
                $pdf->SetFillColor(254,203,156);
                $pdf->Cell(10,8,"S.NO",1,0, 'C', 1);
                $pdf->Cell(55,8,"NAME OF THE ITEM",1,0, 'C', 1);
                $pdf->Cell(30,8,"PART NUMBER",1,0, 'C', 1);
                $pdf->Cell(25,8,"HSN",1,0, 'C', 1);
                $pdf->Cell(11,8,"QTY",1,0, 'C', 1);
                $pdf->Cell(13,8,"UOM",1,0, 'C', 1);
                $pdf->Cell(21,8,"UP",1,0, 'C', 1);
                $pdf->Cell(25,8,"AMOUNT(" . $row['currency'] . ")",1,0, 'C', 1);
                $pdf->Ln();
            }

            //===================================MAIN TABLE============================= //
            $pdf->SetFont('Arial','',9);
            $pdf->SetFillColor(255,255,255);
            /*
            $pdf->Cell(10, 8, $lineItemNumber, 1, 0, 'C', 1);
            $pdf->Cell(65, 8, $row['product_title'], 1, 0, 'L', 1);
            $pdf->Cell(37, 8, $row['part_number'], 1, 0, 'L', 1);
            $pdf->Cell(13, 8, $row['qty'], 1, 0, 'R', 1);
            $pdf->Cell(13, 8, $row['unit'], 1, 0, 'R', 1);
            $pdf->Cell(26, 8, number_format($row['unit_price'],2), 1, 0, 'R', 1);
            $pdf->Cell(26, 8, number_format(round($row['amount']),2), 1, 0, 'R', 1);
            */
            $pdf->Row(array($lineItemNumber, $row['product_title'], $row['part_number'], $row['hsn'], $row['qty'], $row['unit'], number_format($row['unit_price'],2) , number_format($row['amount'],2) ));


            //$pdf->Ln();

            $count++;
            $lineItemNumber++;
            $sub_total = $row['sub_total'];
            $notes = $row['notes'];
            //$frieght = $row['frieght'];
            $frieght = $row['frieght_cost'];
            $pf = $row['p_f'];
            $vat = $row['vat'];
            $cst = $row['cst'];
            $sgst = $row['sgst'];
            $igst_cgst = $row['igst_cgst'];
            $vat_value = $row['vat_value'];
            $cst_value = $row['cst_value'];
            $sgst_value = $row['sgst_value'];
            $igst_cgst_value = $row['igst_cgst_value'];

        }
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(165, 8, "SUB TOTAL", 1, 0, 'R', 1);
            $pdf->Cell(25, 8, number_format(round($sub_total),2), 1, 0, 'R', 1);
            $pdf->Ln();

            $totalvalueRounded = round($totalvalue);
			$totalFrieght = $frieght;

			if($frieght > 0 ){
				$totalvalueRounded = $totalvalueRounded + $totalFrieght;
	            $pdf->SetFillColor(255,255,255);
	            //$pdf->Cell(160, 8, "ADD FRIEGHT : {$frieght}%", 1, 0, 'R', 1);
                $pdf->Cell(165, 8, "ADD FRIEGHT COST", 1, 0, 'R', 1);
	            $pdf->Cell(25, 8, number_format(round($totalFrieght), 2), 1, 0, 'R', 1);
				$pdf->Ln();
			}

			if($pf > 0 ){
                $totalpf = $sub_total * $pf / 100;
				$totalvalueRounded = $totalvalueRounded + $totalpf;
	            $pdf->SetFillColor(255,255,255);
	            $pdf->Cell(165, 8, "ADD P&F: {$pf}%", 1, 0, 'R', 1);
	            $pdf->Cell(25, 8, number_format(round($totalpf), 2), 1, 0, 'R', 1);
				$pdf->Ln();
			}

			if($vat == 1){
		        $printTaxName = $cpCfg['printTaxName'] ;
				$gsttaxvalue = $vat_value;
				$gstvalue = ($sub_total + $totalpf + $totalFrieght) * $gsttaxvalue / 100;
				//$totalvalue = $gstvalue + round($sub_total);

                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(165, 8, "{$printTaxName} {$gsttaxvalue}%", 1, 0, 'R', 1);
                $pdf->Cell(25, 8, number_format(round($gstvalue), 2), 1, 0, 'R', 1);
                $pdf->Ln();
			} else if($cst == 1 && $vat == 0){
		        $printTaxName = $cpCfg['printCstText'] ;
				$gsttaxvalue = $cst_value;
				$gstvalue = ($sub_total + $totalpf + $totalFrieght) * $gsttaxvalue / 100;
				//$totalvalue = $gstvalue + round($sub_total) ;
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(165, 8, "{$printTaxName} {$gsttaxvalue}%", 1, 0, 'R', 1);
                $pdf->Cell(25, 8, number_format(round($gstvalue), 2), 1, 0, 'R', 1);
                $pdf->Ln();
            } else if($sgst == 1){
                $printTaxName = $cpCfg['printGstText'] ;
                $gsttaxvalue = $sgst_value;
                $gstvalue = ($sub_total + $totalpf + $totalFrieght) * $gsttaxvalue / 100;
                //$totalvalue = $gstvalue + round($sub_total) ;
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(165, 8, "{$printTaxName} {$gsttaxvalue}%", 1, 0, 'R', 1);
                $pdf->Cell(25, 8, number_format(round($gstvalue), 2), 1, 0, 'R', 1);
                $pdf->Ln();
			}

            $icgstvalue = '';
            if($igst_cgst != ''){
                $printTaxName = $igst_cgst ;
                $gsttaxvalue = $igst_cgst_value;
                $icgstvalue = ($sub_total + $totalpf + $totalFrieght) * $gsttaxvalue / 100;
                //$totalvalue = $gstvalue + round($sub_total) ;
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(165, 8, "{$printTaxName} {$gsttaxvalue}%", 1, 0, 'R', 1);
                $pdf->Cell(25, 8, number_format(round($icgstvalue), 2), 1, 0, 'R', 1);
                $pdf->Ln();
            }

            $totalvalue = $totalvalue +  $totalpf + $totalFrieght + $gstvalue + $icgstvalue;

            $pdf->SetFont('Arial','B',9);
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(165, 8, 'TOTAL', 1, 0, 'R', 1);
            $pdf->Cell(25, 8, number_format(round($totalvalue), 2), 1, 0, 'R', 1);
			$pdf->Ln(20);

            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(150, 8, 'NOTE: ');
            $pdf->Ln(6);
            $pdf->SetFont('Arial','',9);
            $pdf->drawTextBox($notes, 180, 55, 'L', 'T', 0);
            $pdf->Ln(15);

	        /* Best Regards & Engex Power */
            $pdf->Cell(55, 5, $cpCfg['printBestRegards']);
	        $pdf->SetX(10);
            $pdf->Cell(55, 16, $cpCfg['printEngexPower']);

			$pdf->Output();

    }
    /**
     *
     */
    function getPrintInvoiceRecordOld() {
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $tv = Zend_Registry::get('tv');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $searchVar = Zend_Registry::get('searchVar');
        $media = Zend_Registry::get('media');
        $cpPaths = Zend_Registry::get('cpPaths');
        $dbUtil = Zend_Registry::get('dbUtil');

        ini_set('memory_limit', '512M');
        set_time_limit(50000);

        include_once(CP_LIBRARY_PATH.'lib_php/tcpdf/tcpdf.php');
        include_once(CP_LOCAL_PATH.'lib/headfoot.php');

        $pdf = new MYPDF_Local(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('USS');
        $pdf->SetSubject('Print Link');
        $pdf->SetTitle('Print Link');

        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 04', PDF_HEADER_STRING);
        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER,10);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        /*HEADER PART AND FOOTER PART FUNCTIONS HAS BEEN ADDED IN (headfoot.php) PATH INCLUDE: (admin/lib/headfoot.php)*/
        $pdf->AddPage();

        $invoiceHeading = '';

        $invoice_code = $fn->getReqParam('invoice_code');
        $invoice_type = $fn->getReqParam('invoice_type');
        if($invoice_type == 'normal'){
            $invoiceHeading = '';
        }
        else if($invoice_type == 'transporter'){
            $invoiceHeading = 'TRANSPORTER - ';
        }
        else if($invoice_type == 'proforma'){
            $invoiceHeading = 'PROFORMA - ';
        }
        else if($invoice_type == 'extra'){
            $invoiceHeading = 'EXTRA - ';
        }


        $SQL = "
        SELECT ini.*
              ,ini.item_title AS product_title
              ,p.title AS product_title1
              ,p.unit
              ,p.item_code
              ,p.part_number
              ,p.hsn
              ,c.company_name
              ,c.address_flat
              ,c.address_street
              ,c.address_town
              ,c.address_state
              , (SELECT gc.name FROM geo_country gc
                 WHERE gc.country_code = c.address_country)
                AS address_country
              ,c.billing_address_flat
              ,c.billing_address_street
              ,c.billing_address_town
              ,c.billing_address_state
              , (SELECT gc.name FROM geo_country gc
                 WHERE gc.country_code = c.billing_address_country)
                AS billing_address_country
              ,c.fax
              ,c.phone
              ,c.tin_no
              ,c.cst_no
              ,c.gst_no
              ,i.invoice_date
              ,q.delivery_date
              ,q.delivery_location
              ,ini.unit_price
              ,i.invoice_code
              ,i.invoice_terms
              ,i.invoice_due_date
              ,i.notes
              ,i.cst
              ,i.vat
              ,i.cst_value
              ,i.vat_value
              ,i.frieght_cost
              ,i.cust_po_no
              ,i.p_f
              ,o.order_id
              ,o.shipping_address1
              ,o.shipping_first_name
              ,o.shipping_address2
              ,o.shipping_address_city
              ,o.shipping_address_state
               ,(SELECT gc.name FROM geo_country gc
                 WHERE gc.country_code = o.shipping_address_country)
                 AS shipping_address_country
              ,q.quote_code
              ,q.currency
              ,ini.qty * ini.unit_price AS amount
              ,(SELECT SUM(init.qty * init.unit_price) FROM invoice_item init
               WHERE init.invoice_id = ini.invoice_id) AS sub_total
        FROM invoice_item ini
        LEFT JOIN product p ON (p.product_id = ini.record_id)
        LEFT JOIN invoice i ON (i.invoice_id = ini.invoice_id)
        LEFT JOIN `order` o ON (o.order_id = i.order_id)
        LEFT JOIN company c ON (c.company_id = o.company_id)
        LEFT JOIN quote q ON (q.quote_id = o.quote_id)
        LEFT JOIN product_group pg ON (p.product_group_id = pg.product_group_id)
        WHERE i.invoice_code = '{$invoice_code}'
          AND i.status != 'Cancelled'
        ORDER BY ini.invoice_item_id, pg.sort_order ASC, p.title
        ";
        $result = $db->sql_query($SQL);
        $result2 = $db->sql_query($SQL);
        $Row = $db->sql_fetchrow($result2);
        $numRows  = $db->sql_numrows($result);
        //============================================================================= //

        $pdf->SetFont('Courier','B',9);

        $today = date("Y-m-d");

        $tbl1 = '
            <table border="0" width="100%" style="font-size:15px;">
                <tr>
                    <td align="center" style="font-weight:bold; text-decoration: underline;">INVOICE</td>
                </tr>
            </table>
            ';

        $total = 0;
        $discount_price = 0;
        $rows = "";
        $printTaxName = '';
        $gsttaxvalue = '';
        $gstvalue = '';
        $totalvalue = '';
        $totalpf = '';
        $creationDate   = $fn->getCPDate($Row['invoice_date'], 'd-m-Y');
        $invoiceDueDate = $fn->getCPDate($Row['invoice_due_date'], 'd-m-Y');
        $deliveryDate   = $fn->getCPDate($Row['delivery_date'], 'd-m-Y');
        $currency = $Row['currency'];
        $totalvalue += $Row['sub_total'];

        /* Company Details*/

        if ($Row['shipping_address1'] != ''
            || $Row['shipping_address2'] != ''
            || $Row['shipping_address_city'] != ''
            || $Row['shipping_address_state'] != ''
            || $Row['shipping_address_country'] != '') {
                //Delivery Address Fields in Order
                $deliveryAddressFlat    = $Row['shipping_address1'];
                $deliveryAddressStreet  = $Row['shipping_address2'];
                $deliveryAddressTown    = $Row['shipping_address_city'];
                $deliveryAddressState   = $Row['shipping_address_state'];
                $deliveryAddressCountry = $Row['shipping_address_country'];
                $deliveryCompanyName    = $Row['shipping_first_name'];
        } else {
            //Delivery Address Fields in client
            $deliveryAddressFlat    = $Row['address_flat'];
            $deliveryAddressStreet  = $Row['address_street'];
            $deliveryAddressTown    = $Row['address_town'];
            $deliveryAddressState   = $Row['address_state'];
            $deliveryAddressCountry = $Row['address_country'];
            $deliveryCompanyName    = $Row['company_name'];
        }

        $tbl2 = '
            <table border="1" width="100%" style="font-size:12px; font-weight:bold;">
                <tr>
                    <td style="background-color: #FDCA9C;">INVOICE NO :</td>
                    <td>'.$Row['invoice_code'].'</td>
                    <td style="background-color: #FDCA9C;">DATE :</td>
                    <td>'.$creationDate.'</td>
                    <td style="background-color: #FDCA9C;">CUST PO NO :</td>
                    <td>'.$Row['cust_po_no'].'</td>
                </tr>
            </table>
            ';

        $tbl3 = '
            <table border="1" width="100%" style="font-size:12px; font-weight:bold;">
                <tr>
                    <td style="background-color: #FDCA9C;">INVOICE TO</td>
                    <td style="background-color: #FDCA9C;">DELIVERY TO</td>
                </tr>
                <tr>
                    <td>'.$Row['company_name'].'<br/>'.$Row['billing_address_flat'].'<br/>
                        '.$Row['billing_address_street'].'<br/>'.$Row['billing_address_town'].'<br/>
                        '.$Row['billing_address_country'].'<br/>TIN NO:'.$Row['tin_no'].'<br/>
                        CST NO:'. $Row['cst_no'].'<br/>GST NO:'. $Row['gst_no'].'
                    </td>
                    <td>'.$deliveryCompanyName.'<br/>'.$deliveryAddressFlat.'<br/>
                        '.$deliveryAddressStreet.'<br/>'.$deliveryAddressTown.'<br/>
                        '.$deliveryAddressCountry.'<br/>TIN NO:'.$Row['tin_no'].'<br/>
                        CST NO:'. $Row['cst_no'].'<br/>GST NO:'. $Row['gst_no'].'</td>
                </tr>
            </table>
            ';

            $terms = $Row['invoice_terms'];
            $bank = $cpCfg['cp.bankDetails'];

        $tbl4 = '
            <table border="1" width="100%" style="font-size:12px; font-weight:bold;">
                <tr>
                    <td style="background-color: #FDCA9C;">TERMS</td>
                    <td style="background-color: #FDCA9C;">BANK DETAILS</td>
                </tr>
                <tr>
                    <td>'.$terms.'</td>
                    <td>'.$bank.'</td>
                </tr>
            </table>
            ';

        $tbl5 = '
            <table border="1" width="100%" style="font-size:12px;">
                <thead>
                    <tr>
                        <td width="6%">S.NO</td>
                        <td width="30%">NAME OF THE ITEM</td>
                        <td width="13%">PART NO</td>
                        <td width="13%">HSN</td>
                        <td width="6%">QTY</td>
                        <td width="6%">UOM</td>
                        <td width="11%">UP</td>
                        <td width="15%">TOTAL ('.$Row['currency'].')</td>
                    </tr>
                </thead>
                <tbody>
            ';

        $count = 1;
        while($row = $db->sql_fetchrow($result)){

            $unit_price = number_format($row['unit_price'],2);
            $total = number_format($row['amount'],2);
            $tbl5 = $tbl5.'<tr>
                                <td width="6%">'.$count.'</td>
                                <td width="30%">'.$row['product_title'].'</td>
                                <td width="13%">'.$row['part_number'].'</td>
                                <td width="13%">'.$row['hsn'].'</td>
                                <td width="6%">'.$row['qty'].'</td>
                                <td width="6%">'.$row['unit'].'</td>
                                <td width="11%">'.$unit_price.'</td>
                                <td width="15%">'.$total.'</td>
                            </tr>
                           ';
                $count++;
                $sub_total = $row['sub_total'];
                $notes = $row['notes'];
                $frieght = $row['frieght_cost'];
                $pf = $row['p_f'];
                $vat = $row['vat'];
                $cst = $row['cst'];
                /*$sgst = $row['sgst'];
                $igst_cgst = $row['igst_cgst'];*/
                $vat_value = $row['vat_value'];
                $cst_value = $row['cst_value'];
                /*$sgst_value = $row['sgst_value'];
                $igst_cgst_value = $row['igst_cgst_value'];*/
        }
        $tbl5 = $tbl5.'<tr>
                        <td colspan="7" align="right" style="font-weight:bold;">SUB TOTAL</td>
                        <td>'.number_format(round($sub_total),2).'</td>
                    </tr>';

            $totalvalueRounded = round($totalvalue);
            $totalFrieght = $frieght;

            if($frieght > 0 ){
                $totalvalueRounded = $totalvalueRounded + $totalFrieght;
                $tbl5 = $tbl5.'<tr>
                        <td colspan="7" align="right" style="font-weight:bold;">ADD FRIEGHT COST</td>
                        <td>'.number_format(round($totalFrieght),2).'</td>
                    </tr>';
            }

            if($pf > 0 ){
                $totalpf = $sub_total * $pf / 100;
                $totalvalueRounded = $totalvalueRounded + $totalpf;
                $tbl5 = $tbl5.'<tr>
                        <td colspan="7" align="right" style="font-weight:bold;">ADD P&F: {$pf}%</td>
                        <td>'.number_format(round($totalpf),2).'</td>
                    </tr>';
            }

            if($vat == 1){
                $printTaxName = $cpCfg['printTaxName'] ;
                $gsttaxvalue = $vat_value;
                $gstvalue = ($sub_total + $totalpf + $totalFrieght) * $gsttaxvalue / 100;
                $tbl5 = $tbl5.'<tr>
                        <td colspan="7" align="right" style="font-weight:bold;">'.$printTaxName.''.$gsttaxvalue.'%</td>
                        <td>'.number_format(round($gstvalue),2).'</td>
                    </tr>';
            } else if($cst == 1 && $vat == 0){
                $printTaxName = $cpCfg['printCstText'] ;
                $gsttaxvalue = $cst_value;
                $gstvalue = ($sub_total + $totalpf + $totalFrieght) * $gsttaxvalue / 100;
                $tbl5 = $tbl5.'<tr>
                        <td colspan="7" align="right" style="font-weight:bold;">'.$printTaxName.''.$gsttaxvalue.'%</td>
                        <td>'.number_format(round($gstvalue),2).'</td>
                    </tr>';
            /*} else if($sgst == 1){
                $printTaxName = $cpCfg['printGstText'] ;
                $gsttaxvalue = $sgst_value;
                $gstvalue = ($sub_total + $totalpf + $totalFrieght) * $gsttaxvalue / 100;
                $tbl5 = $tbl5.'<tr>
                        <td colspan="7" align="right" style="font-weight:bold;">'.$printTaxName.''.$gsttaxvalue.'%</td>
                        <td>'.number_format(round($gstvalue),2).'</td>
                    </tr>';*/
            }

            $icgstvalue = '';
            /*if($igst_cgst != ''){
                $printTaxName = $igst_cgst ;
                $gsttaxvalue = $igst_cgst_value;
                $icgstvalue = ($sub_total + $totalpf + $totalFrieght) * $gsttaxvalue / 100;
                $tbl5 = $tbl5.'<tr>
                        <td colspan="7" align="right" style="font-weight:bold;">'.$printTaxName.''.$gsttaxvalue.'%</td>
                        <td>'.number_format(round($icgstvalue),2).'</td>
                    </tr>';
            }*/

            $totalvalue = $totalvalue +  $totalpf + $totalFrieght + $gstvalue + $icgstvalue;
            $tbl5 = $tbl5.'<tr>
                        <td colspan="7" align="right" style="font-weight:bold;">TOTAL</td>
                        <td>'.number_format(round($totalvalue),2).'</td>
                    </tr>';

        $tbl5 = $tbl5.'</tbody></table>';

        $tbl6 = '
            <table border="0" width="100%" style="font-size:14px;">
                <tr>
                    <td style="font-weight:bold;">NOTE</td>
                </tr>
                <tr>
                    <td>'.$notes.'</td>
                </tr>
            </table>
            ';
        $tbl7 = '
            <table border="0" width="100%" style="font-size:14px;">
                <tr>
                    <td>'.$cpCfg['printBestRegards'].'</td>
                </tr>
                <tr>
                    <td>'.$cpCfg['printEngexPower'].'</td>
                </tr>
            </table>
            ';

        $pdf->ln(-5);
        $pdf->writeHTML($tbl1, true, false, false, false, '');
        $pdf->writeHTML($tbl2, true, false, false, false, '');
        $pdf->writeHTML($tbl3, true, false, false, false, '');
        $pdf->writeHTML($tbl4, true, false, false, false, '');
        $pdf->writeHTML($tbl5, true, false, false, false, '');
        $pdf->writeHTML($tbl6, true, false, false, false, '');
        $pdf->writeHTML($tbl7, true, false, false, false, '');

        $pdf->Output();


    }

/**
     *
     */
    function getPrintDebitRecord() {
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $tv = Zend_Registry::get('tv');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $searchVar = Zend_Registry::get('searchVar');
        $media = Zend_Registry::get('media');
        $cpPaths = Zend_Registry::get('cpPaths');
        $dbUtil = Zend_Registry::get('dbUtil');
        $site_id  = $fn->getSessionParam('cp_site_id');

        ini_set('memory_limit', '512M');
        set_time_limit(50000);

        include_once(CP_LIBRARY_PATH.'lib_php/tcpdf/tcpdf.php');
        include_once(CP_LOCAL_PATH.'lib/headfoot.php');

        $pdf = new MYPDF_Local(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('USS');
        $pdf->SetSubject('Proforma Invoice');
        $pdf->SetTitle('Proforma Invoice');

        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 04', PDF_HEADER_STRING);
        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER,10);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        /*HEADER PART AND FOOTER PART FUNCTIONS HAS BEEN ADDED IN (headfoot.php) PATH INCLUDE: (admin/lib/headfoot.php)*/
        $pdf->AddPage();

        $invoiceHeading = '';

        $debit_note_id = $fn->getReqParam('debit_note_id');
        $invoice_code = $fn->getReqParam('invoice_code');
        $invoice_type = $fn->getReqParam('invoice_type');
        if($invoice_type == 'normal'){
            $invoiceHeading = 'ORIGINAL - ';
        }
        else if($invoice_type == 'transporter'){
            $invoiceHeading = 'TRANSPORTER - ';
        }
        else if($invoice_type == 'proforma'){
            $invoiceHeading = 'PROFORMA - ';
        }
        else if($invoice_type == 'extra'){
            $invoiceHeading = 'DUPLICATE - ';
        }

        $SQL = "
        SELECT ini.*
              ,ini.item_title AS product_title
              ,p.title AS product_title1
              ,p.unit
              ,p.item_code
              ,p.part_number
              ,p.description_short
              ,p.hsn
              ,c.company_name
              ,c.address_flat
              ,c.address_street
              ,c.address_town
              ,c.address_state
              ,c.address_po_code
              , (SELECT gc.name FROM geo_country gc
                 WHERE gc.country_code = c.address_country)
                AS address_country
              ,c.billing_address_flat
              ,c.billing_address_street
              ,c.billing_address_town
              ,c.billing_address_state
              , (SELECT gc.name FROM geo_country gc
                 WHERE gc.country_code = c.billing_address_country)
                AS billing_address_country
              ,c.fax
              ,c.phone
              ,c.tin_no
              ,c.cst_no
              ,c.gst_no
              ,i.invoice_date
              ,q.delivery_date
              ,q.delivery_location
              ,q.gst_enabled
              ,q.show_discount_percentage
              ,q.currency
              ,q.payment_terms
              ,q.delivery_terms
              ,ini.unit_price
              ,i.invoice_code
              ,i.invoice_terms
              ,i.invoice_due_date
              ,i.notes
              ,i.cst
              ,i.vat
              ,i.cst_value
              ,i.vat_value
              ,i.frieght_cost
              ,i.cust_po_no
              ,i.p_f
              ,i.debit_note_id
              ,o.order_id
              ,o.notes
              ,o.shipping_address1
              ,o.shipping_first_name
              ,o.shipping_address2
              ,o.shipping_address_city
              ,o.shipping_address_state
              ,o.gst_status
              ,o.igst_show
               ,(SELECT gc.name FROM geo_country gc
                 WHERE gc.country_code = o.shipping_address_country)
                 AS shipping_address_country
              ,q.quote_code
              ,q.currency
              ,ini.qty * ini.unit_price AS amount
              ,(SELECT SUM(init.qty * init.cost_price) FROM debit_note_item init
               WHERE init.debit_note_id = ini.debit_note_id) AS sub_total
              ,(SELECT SUM(init.qty * init.unit_price) FROM debit_note_item init
               WHERE init.debit_note_id = ini.debit_note_id) AS total
              ,CONCAT_WS(' ', co.first_name, co.last_name) AS contact_name
              ,co.salutation
              ,(SELECT invoice_code FROM invoice inv WHERE inv.order_id = o.order_id AND inv.status != 'Cancelled') AS invoiceCode
              ,(SELECT invoice_date FROM invoice inv WHERE inv.order_id = o.order_id AND inv.status != 'Cancelled') AS debitDate
        FROM debit_note_item ini
        LEFT JOIN product p ON (p.product_id = ini.record_id)
        LEFT JOIN debit_note i ON (i.debit_note_id = ini.debit_note_id)
        LEFT JOIN `order` o ON (o.order_id = i.order_id)
        LEFT JOIN company c ON (c.company_id = o.company_id)
        LEFT JOIN quote q ON (q.quote_id = o.quote_id)
        LEFT JOIN contact co ON (co.contact_id = q.contact_id)
        LEFT JOIN product_group pg ON (p.product_group_id = pg.product_group_id)
        WHERE i.debit_note_id = '{$debit_note_id}'
          AND i.status != 'Cancelled'
        ORDER BY ini.debit_note_item_id, pg.sort_order ASC, p.title
        ";
        $result = $db->sql_query($SQL);
        $result2 = $db->sql_query($SQL);
        $Row = $db->sql_fetchrow($result2);

        $numRows  = $db->sql_numrows($result);
        //============================================================================= //

        $pdf->SetFont('helvetica','', 8);
        $today = date("d-m-Y");

        if($site_id == 1) {
            $tbl1 = '
            <table border="0" width="100%" style="font-size:17px;">
                <tr>
                    <td align="center" style="font-weight:bold; text-decoration: underline;">DEBIT NOTE</td>
                </tr>
            </table>
            ';

            $tblQuote ='
            <table border="0" width="100%" cellpadding="3">
            </table>
            ';

            $contact_name = '';
            if($Row['contact_name'] != ''){
                $contact_name = "Kind Attn: {$Row['salutation']}.{$Row['contact_name']}";
            }

            $addressFlat     = $Row['address_flat'];
            $addressStreet   = $Row['address_street'];
            $addressTown     = $Row['address_town'];
            $addressState    = $Row['address_state'];
            $addressCountry  = $Row['address_country'];

            $billingAddressFlat     = $Row['billing_address_flat'];
            $billingAddressStreet   = $Row['billing_address_street'];
            $billingAddressTown     = $Row['billing_address_town'];
            $billingAddressState    = $Row['billing_address_state'];
            $billingAddressCountry  = $Row['billing_address_country'];
            $invoiceDate   = $fn->getCPDate($Row['invoice_date'], 'd-m-Y');
                        $creditDate   = $fn->getCPDate($Row['debitDate'], 'd-m-Y');


            $tbl2 ='
            <table border="0" width="100%" cellpadding="3">
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>Debit Note No : </i>'.$Row['invoice_code'].'</td>
                    <td width="50%" style="border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>Reference Invoice: </i> '.$Row['invoiceCode'].' </td>
                </tr>
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>Date Of Issue : </i>'.$invoiceDate.'</td>
                    <td width="50%" style="border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>Date Of Invoice : </i> '.$creditDate.'</td>
                </tr>
                  <tr>
                    <td width="25%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>State : </i></td>
                    <td width="13%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>Code : </i></td>
                    <td width="12%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;">'.$Row['address_po_code'].'</td>
                    <td width="50%" style="border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"></td>
                </tr>
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>From :</i></td>
                    <td width="50%" style="border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>Billed to :</i></td>
                </tr>
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;font-size:11px;font-weight:bold;">'.$cpCfg['cp.companyName'].'</td>
                    <td width="50%" style="border-right:1px solid #000000;font-size:11px;font-weight:bold;">'.$Row['company_name'].'</td>
                </tr>
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;font-size:11px;font-weight:bold;">'.$cpCfg['cp.addressPdf5'].'</td>
                    <td width="50%" style="border-right:1px solid #000000;font-size:11px;font-weight:bold;">'.$Row['address_flat'].'</td>
                </tr>
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;font-weight:bold;font-size:11px;">'.$cpCfg['cp.panNoPdf'].'</td>
                    <td width="50%" style="border-right:1px solid #000000;font-size:11px;font-weight:bold;">'.$Row['address_street'].' '.$Row['address_town'].' '.$Row['address_state'].'</td>
                </tr>
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;font-weight:bold;font-size:11px;"></td>
                    <td width="50%" style="border-right:1px solid #000000;font-size:11px;font-weight:bold;">GST IN / UIN :'.$Row['gst_no'].'</td>
                </tr>
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:11px;"><span style="font-weight:bold;">State Code: 33</span></td>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:11px;"><span style="font-weight:bold;">State Code: </span>'.$Row['address_po_code'].'</td>
                </tr>
            </table>
            ';


            if($Row['gst_status'] == "ON"){
                $tbl3 ='
                <table border="1" nobr="true" width="100%" cellpadding="3" style="font-size:10px;">
                    <thead>
                        <tr>
                            <th width="5%"  style="border:1px solid #000000;font-weight:bold;" align="center">S.No</th>
                            <th width="35%" style="border:1px solid #000000;font-weight:bold;" align="left">Product Description</th>
                            <th width="10%"  style="border:1px solid #000000;font-weight:bold;" align="center">HSN Code</th>
                            <th width="6%"  style="border:1px solid #000000;font-weight:bold;" align="center">UOM</th>
                            <th width="7%"  style="border:1px solid #000000;font-weight:bold;" align="center">Qty</th>
                            <th width="12%"  style="border:1px solid #000000;font-weight:bold;" align="right">Price</th>
                            <th width="12%"  style="border:1px solid #000000;font-weight:bold;" align="right">Taxbl Val</th>
                            <th width="13%" style="border:1px solid #000000;font-weight:bold;" align="right">Total</th>
                        </tr>
                    </thead>
                ';
            }

            else {
                if($Row['show_discount_percentage'] == 1){
                    $tbl3 = '
                    <table border="1" width="100%" cellpadding="3" style="font-size:11px;">
                        <thead>
                            <tr>
                                <td width="6%"  style="font-weight:bold;" align="center">S.No</td>
                                <td width="26%" style="font-weight:bold;" align="left">Product Description</td>
                                <td width="15%" style="font-weight:bold;" align="center">HSN Code</td>
                                <td width="10%" style="font-weight:bold;" align="center">UOM</td>
                                <td width="8%"  style="font-weight:bold;" align="center">Qty</td>
                                <td width="10%" style="font-weight:bold;" align="right">Price</td>
                                <td width="10%" style="font-weight:bold;" align="right">Discount</td>
                                <td width="15%" style="font-weight:bold;" align="right">Total ('.$Row['currency'].')</td>
                            </tr>
                        </thead>
                        <tbody>
                    ';
                } else {
                    $tbl3 = '
                    <table border="1" width="100%" cellpadding="3" style="font-size:11px;">
                        <thead>
                            <tr>
                                <td width="6%"  style="font-weight:bold;" align="center">S.No</td>
                                <td width="29%" style="font-weight:bold;" align="left">Product Description</td>
                                <td width="15%" style="font-weight:bold;" align="center">HSN Code</td>
                                <td width="10%" style="font-weight:bold;" align="center">UOM</td>
                                <td width="10%" style="font-weight:bold;" align="center">Qty</td>
                                <td width="15%" style="font-weight:bold;" align="right">Price</td>
                                <td width="15%" style="font-weight:bold;" align="right">Total ('.$Row['currency'].')</td>
                            </tr>
                        </thead>
                        <tbody>
                    ';
                }
            }

            $count = 1;
            $overallTotal = 0;
            $vatSumTotal  = 0;
            while($row = $db->sql_fetchrow($result)){

                $discount_value_for_one_qty = 0;
                $discount_value_for_display = 0;
                if($row['discount_percentage'] > 0){
                    if($row['discount_type'] == '%'){
                        $discount_value_for_one_qty  =  $row['cost_price'] * ($row['discount_percentage']/100);
                        $discount_value_for_display  =  $row['discount_percentage'] . '%';
                    }
                    else if($row['discount_type']  == 'Value'){
                        $discount_value_for_one_qty  =  $row['discount_percentage'];
                        $discount_value_for_display  =  $row['discount_percentage'];
                    }
                }
 if($row['show_discount_percentage'] != 1){
                    $selling_price = $row['unit_price'];
                    $tsp = ($row['qty'] * $selling_price);

                    $SQLTax = "
                    SELECT  p.gst
                            ,SUM(ci.unit_price * ci.qty) AS qty_amount
                    FROM `debit_note_item` ci
                    LEFT JOIN `debit_note` cn ON (cn.debit_note_id = ci.debit_note_id)
                    LEFT JOIN `product` p ON (p.product_id = ci.record_id)
                    WHERE ci.debit_note_item_id = '{$row['debit_note_item_id']}'
                    AND p.gst > 0
                    AND cn.status != 'Cancelled'
                    ";
                    $resultTax  = $db->sql_query($SQLTax);
                    $rowTax     = $db->sql_fetchrow($resultTax);

                } else {
                    $selling_price = $row['unit_price'];
                    $tsp = ($row['qty'] * $selling_price);

                    $SQLTax = "
                    SELECT  p.gst
                            ,SUM(ci.unit_price * ci.qty) AS qty_amount
                     FROM `debit_note_item` ci
                     LEFT JOIN `debit_note` cn ON (cn.debit_note_id = ci.debit_note_id)
                    LEFT JOIN `product` p ON (p.product_id = ci.record_id)
                    WHERE ci.debit_note_item_id = '{$row['debit_note_item_id']}'
                    AND p.gst > 0
                    AND cn.status != 'Cancelled'
                    ";
                    $resultTax  = $db->sql_query($SQLTax);
                    $rowTax     = $db->sql_fetchrow($resultTax);
                }
                $titledesc = $row['item_title'];

                $titledescrip = $titledesc;
                $discount_value_for_one_qty = number_format($discount_value_for_one_qty, 2);

                $totalVatSum = 0;

                $total_amount = $rowTax['qty_amount'];
                
                if($rowTax['gst'] == ''){
                    $vatPercent = '0.00';
                }
                else{
                    $vatPercent = $rowTax['gst'];
                }

                $vat_Sum  = ($total_amount * $rowTax['gst'])/100;

                $vat_Amount_total = $total_amount + $vat_Sum;
                if($vat_Sum == 0){
                    $vat_Amount_total = 0;
                }

                $vatPercentHalf = $vatPercent / 2;
                $vat_Sum_Half   = $vat_Sum / 2;

                $totalVatSum += $vat_Sum;

                $vatPercentHalf = sprintf('%0.2f', $vatPercentHalf);
                
                if($row['gst_status'] == "ON"){
                    $total = $tsp + $vat_Sum_Half + $vat_Sum_Half;
                } else {
                    $total = $tsp;
                }

                $tsp   = $tsp - $row['discount_amount'];
                $total = $total - $row['discount_amount'];
                $selling_price = number_format($selling_price,2);

                if($row['gst_status'] == "ON"){
                    $tbl3 = $tbl3.'<tr>
                                        <td width="5%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="center">'.$count.'</td>
                                        <td width="35%" style="border-left:1px solid #000000;border-right:1px solid #000000;" align="left">'.$titledescrip.'</td>
                                        <td width="10%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="center">'.$row['hsn'].'</td>
                                        <td width="6%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="center">'.$row['unit'].'</td>
                                        <td width="7%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="center">'.$row['qty'].'</td>
                                        <td width="12%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="right">'.$selling_price.'</td>
                                        <td width="12%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="right">'.$tsp.'</td>
                                        <td width="13%" style="border-left:1px solid #000000;border-right:1px solid #000000;"  align="right">'.number_format($total, 2).'</td>
                                    </tr>
                                    ';
                    $count++;
                } else {

                    if($row['show_discount_percentage'] == 1){    
                        $tbl3 = $tbl3.'<tr>
                                            <td width="6%"  align="center">'.$count.'</td>
                                            <td width="26%" align="left">'.$titledescrip.'</td>
                                            <td width="15%" align="center">'.$row['hsn'].'</td>
                                            <td width="10%" align="center">'.$row['unit'].'</td>
                                            <td width="8%"  align="center">'.$row['qty'].'</td>
                                            <td width="10%" align="right">'.$selling_price.'</td>
                                            <td width="10%" align="right">'.$discount_value_for_display.'</td>
                                            <td width="15%" align="right">'.number_format($tsp, 2).'</td>
                                        </tr>
                                       ';

                        $count++;
                    }else {
                        $tbl3 = $tbl3.'<tr>
                                            <td width="6%"  align="center">'.$count.'</td>
                                            <td width="29%" align="left">'.$titledescrip.'</td>
                                            <td width="15%" align="center">'.$row['hsn'].'</td>
                                            <td width="10%" align="center">'.$row['unit'].'</td>
                                            <td width="10%" align="center">'.$row['qty'].'</td>
                                            <td width="15%" align="right">'.$selling_price.'</td>
                                            <td width="15%" align="right">'.number_format($tsp, 2).'</td>
                                        </tr>
                                       ';

                        $count++;
                    }
                }

                $overallTotal += $total;
                $vatSumTotal  += $vat_Sum_Half;

                $total = $row['total'];
                $terms = $row['payment_terms'];
                $notes = $row['notes'];
                $delivery_terms = $row['delivery_terms'];
                $discount = 0;
                $sub_total = $total + $discount - $row['discount_amount'];
                $show_discount_percentage = $row['show_discount_percentage'];
            }
            $tbl4 = '';

            $totaldiscount = $sub_total - $discount;
            $discountPercent = $discount * 100 / $sub_total;
            $totaldiscount = number_format(round($totaldiscount), 2);
            $sub_total = number_format($sub_total,2);
            $discount = number_format($discount,2);
            $discountPercent = number_format($discountPercent,2);
            $displayDiscountPercent = '';

            if($Row['gst_status'] == "ON") {
                //$sub_total_in_words = $fn->getConvertNumber($overallTotal .'.00');
                $sub_total_in_words = $fn->getIndianCurrency($overallTotal .'.00');
                if($Row['igst_show'] == "1"){
                    $tbl3 = $tbl3.'
                                <tr>
                                    <td colspan="5" align="center" style="font-weight:bold;">Total Amount in Words</td>
                                    <td colspan="2" align="right" style="font-weight:bold;">Total Amount Before Tax</td>
                                    <td align="right">'.$totaldiscount.'</td>
                                </tr>
                                <tr>
                                    <td rowspan="5" colspan="5" align="center" style="font-weight:bold;font-size:12px;">'.strtoupper($sub_total_in_words).'</td>
                                    <td colspan="2" align="right" style="font-weight:bold;">Add: IGST 18.00%</td>
                                    <td align="right">'.number_format($totalVatSum, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold;">Total Amount After Tax</td>
                                    <td align="right">'.number_format($overallTotal, 2).'</td>
                                </tr>
                            </tbody>
                        </table>';
                } else {
                    $tbl3 = $tbl3.'
                                <tr>
                                    <td colspan="5" align="center" style="font-weight:bold;">Total Amount in Words</td>
                                    <td colspan="2" align="right" style="font-weight:bold;">Total Amount Before Tax</td>
                                    <td align="right">'.$totaldiscount.'</td>
                                </tr>
                                <tr>
                                    <td rowspan="5" colspan="5" align="center" style="font-weight:bold;font-size:12px;">'.strtoupper($sub_total_in_words).'</td>
                                    <td colspan="2" align="right" style="font-weight:bold;">Add: CGST 9.00%</td>
                                    <td align="right">'.number_format($vatSumTotal, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold;">Add: SGST 9.00%</td>
                                    <td align="right">'.number_format($vatSumTotal, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold;">Total Tax Amount</td>
                                    <td align="right">'.number_format($totalVatSum, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold;">Total Amount After Tax</td>
                                    <td align="right">'.number_format($overallTotal, 2).'</td>
                                </tr>
                            </tbody>
                        </table>';

                }
            } else {

                if($Row['show_discount_percentage'] == 1){ 
                    $tbl3 = $tbl3.'
                                    <tr>
                                        <td colspan="7" align="right" style="font-weight:bold;">TOTAL</td>
                                        <td align="right">'.$totaldiscount.'</td>
                                    </tr>
                                </tbody>
                            </table>';
                } else {
                    $tbl3 = $tbl3.'
                                    <tr>
                                        <td colspan="6" align="right" style="font-weight:bold;">TOTAL</td>
                                        <td align="right">'.$totaldiscount.'</td>
                                    </tr>
                                </tbody>
                            </table>';
                }
            }

            $tbl5 = '<table cellpadding="4" border="1" width="100%" nobr="true">';

            $tbl5 = $tbl5.'
                <tr>
                    <td width="50%" align="left" style="font-size:10px;font-weight:bold;"><b>Bank Details : </b><br/><b>'.$cpCfg['cp.bankDetails'].'</b></td>
                    <td width="20%" align="center"></td>
                    <td width="30%" align="right" rowspan="2" style="font-size:12px;font-weight:bold;">For '.$cpCfg['cp.companyName'].'<br/><br/><br/><br/><br/><br/>Authorised signatory</td>
                </tr>
                <tr>
                    <td width="50%" align=""><span style="font-weight:bold;font-size:11px;">Mode of Transport : </span><br/><br/>Vehicle No. : '.$notes.'</td>
                    <td width="20%" align="center" style="font-weight:bold;font-size:11px;vertical-align:bottom;"><br/><br/><br/><br/>Common Seal</td>
                </tr>
            </table>
            ';
        } else {
            $pdf->SetFont('calibri','', 8);
            $invoiceDate   = $fn->getCPDate($Row['invoice_date'], 'd-m-Y');
            $creditDate   = $fn->getCPDate($Row['debitDate'], 'd-m-Y');


            $tblQuote ='
            <table border="0" width="100%" cellpadding="3">
            </table>
            ';

            $contact_name = '';
            if($Row['contact_name'] != ''){
                $contact_name = "Kind Attn: {$Row['salutation']}.{$Row['contact_name']}";
            }

            $addressFlat     = $Row['address_flat'];
            $addressStreet   = $Row['address_street'];
            $addressTown     = $Row['address_town'];
            $addressState    = $Row['address_state'];
            $addressCountry  = $Row['address_country'];

            $billingAddressFlat     = $Row['billing_address_flat'];
            $billingAddressStreet   = $Row['billing_address_street'];
            $billingAddressTown     = $Row['billing_address_town'];
            $billingAddressState    = $Row['billing_address_state'];
            $billingAddressCountry  = $Row['billing_address_country'];

            $tbl1 = '
            <table border="0" width="100%" cellpadding="3">
                <tr>
                    <td width="27%" style="font-size:13px;font-weight:bold;color:#157ca7;"><strong>From :</strong><br/><font style="font-size:14px;font-weight:bold;color:#000000;">'.$cpCfg['cp.companyName'].'<br/></font><font style="font-size:11px;color:#000000;">'.$cpCfg['cp.addressPdf5'].'<br/>'.$cpCfg['cp.panNoPdf'].'<br/>State Code: 33</font></td>
                    <td width="43%" style="font-size:13px;font-weight:bold;color:#157ca7;"><strong>Billed to :</strong><br/><font style="font-size:14px;font-weight:bold;color:#000000;">'.$Row['company_name'].'<br/></font><font style="font-size:11px;color:#000000;">'.$Row['address_flat'].', '.$Row['address_street'].' '.$Row['address_town'].' '.$Row['address_state'].'<br/>GST IN / UIN :'.$Row['gst_no'].'<br/>State Code: '.$Row['address_po_code'].'</font></td>
                    <td width="30%" style="font-weight:bold;font-size:22px;">DEBIT NOTE <font style="font-size:12px;font-weight:bold;"><br/><br/><i>Code : </i>'.$Row['invoice_code'].'<br/><i>Date : </i>'.$invoiceDate.'<br/><i>Reference Invoice : </i>'.$Row['invoiceCode'].'<br/><i>Invoice Date : </i>'.$creditDate.'</font></td>
                </tr>
            </table>
            ';

            $tbl2 ='';

            if($Row['gst_status'] == "ON"){
                $tbl3 ='
                <table border="0" nobr="true" width="100%" cellpadding="4" style="font-size:11px;">
                    <thead>
                        <tr>
                            <th width="5%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">S.No</th>
                            <th width="35%" style="color:#fff;font-weight:bold; line-height:16px;" align="left" bgColor="#157ca7">Product Description</th>
                            <th width="10%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">HSN Code</th>
                            <th width="6%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">UOM</th>
                            <th width="7%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">Qty</th>
                            <th width="12%"  style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Price</th>
                            <th width="12%"  style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Taxbl Val</th>
                            <th width="13%" style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Total</th>
                        </tr>
                    </thead>
                ';
            }

            else {
                if($Row['show_discount_percentage'] == 1){
                    $tbl3 = '
                    <table border="0" width="100%" cellpadding="4" style="font-size:11px;">
                        <thead>
                            <tr>
                                <td width="6%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">S.No</td>
                                <td width="26%" style="color:#fff;font-weight:bold; line-height:16px;" align="left" bgColor="#157ca7">Product Description</td>
                                <td width="15%" style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">HSN Code</td>
                                <td width="10%" style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">UOM</td>
                                <td width="8%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">Qty</td>
                                <td width="10%" style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Price</td>
                                <td width="10%" style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Discount</td>
                                <td width="15%" style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Total ('.$Row['currency'].')</td>
                            </tr>
                        </thead>
                        <tbody>
                    ';
                } else {
                    $tbl3 = '
                    <table border="0" width="100%" cellpadding="4" style="font-size:11px;">
                        <thead>
                            <tr>
                                <td width="6%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">S.No</td>
                                <td width="29%" style="color:#fff;font-weight:bold; line-height:16px;" align="left" bgColor="#157ca7">Product Description</td>
                                <td width="15%" style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">HSN Code</td>
                                <td width="10%" style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">UOM</td>
                                <td width="10%" style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">Qty</td>
                                <td width="15%" style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Price</td>
                                <td width="15%" style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Total ('.$Row['currency'].')</td>
                            </tr>
                        </thead>
                        <tbody>
                    ';
                }
            }

            $tbl4 = '';

            $count = 1;
            $overallTotal = 0;
            $vatSumTotal  = 0;
            $total_vat_Amount_total = 0;
            $total_vat_Sum_Half = 0;
            $total_vat_Sum = 0;
            $total_total_tax = 0;
            $total_tax = 0;
            while($row = $db->sql_fetchrow($result)){

                $discount_value_for_one_qty = 0;
                $discount_value_for_display = 0;
                if($row['discount_percentage'] > 0){
                    if($row['discount_type'] == '%'){
                        $discount_value_for_one_qty  =  $row['cost_price'] * ($row['discount_percentage']/100);
                        $discount_value_for_display  =  $row['discount_percentage'] . '%';
                    }
                    else if($row['discount_type']  == 'Value'){
                        $discount_value_for_one_qty  =  $row['discount_percentage'];
                        $discount_value_for_display  =  $row['discount_percentage'];
                    }
                }

               if($row['show_discount_percentage'] != 1){
                    $selling_price = $row['unit_price'];
                    $tsp = ($row['qty'] * $selling_price);

                    $SQLTax = "
                    SELECT  p.gst
                            ,SUM(ci.unit_price * ci.qty) AS qty_amount
                    FROM `debit_note_item` ci
                    LEFT JOIN `debit_note` cn ON (cn.debit_note_id = ci.debit_note_id)
                    LEFT JOIN `product` p ON (p.product_id = ci.record_id)
                    WHERE ci.debit_note_item_id = '{$row['debit_note_item_id']}'
                    AND p.gst > 0
                    AND cn.status != 'Cancelled'
                    ";
                    $resultTax  = $db->sql_query($SQLTax);
                    $rowTax     = $db->sql_fetchrow($resultTax);

                } else {
                    $selling_price = $row['unit_price'];
                    $tsp = ($row['qty'] * $selling_price);

                    $SQLTax = "
                    SELECT  p.gst
                            ,SUM(ci.unit_price * ci.qty) AS qty_amount
                     FROM `debit_note_item` ci
                     LEFT JOIN `debit_note` cn ON (cn.debit_note_id = ci.debit_note_id)
                    LEFT JOIN `product` p ON (p.product_id = ci.record_id)
                    WHERE ci.debit_note_item_id = '{$row['debit_note_item_id']}'
                    AND p.gst > 0
                    AND cn.status != 'Cancelled'
                    ";
                    $resultTax  = $db->sql_query($SQLTax);
                    $rowTax     = $db->sql_fetchrow($resultTax);
                }

                $titledesc = $row['item_title'];

                $titledescrip = $titledesc;
                $discount_value_for_one_qty = number_format($discount_value_for_one_qty, 2);

                $totalVatSum = 0;

                $total_amount = $rowTax['qty_amount'];
                
                if($rowTax['gst'] == ''){
                    $vatPercent = '0.00';
                }
                else{
                    $vatPercent = $rowTax['gst'];
                }

                $vat_Sum  = ($total_amount * $rowTax['gst'])/100;

                $vat_Amount_total = $total_amount + $vat_Sum;
                if($vat_Sum == 0){
                    $vat_Amount_total = 0;
                }

                $vatPercentHalf = $vatPercent / 2;
                $vat_Sum_Half   = $vat_Sum / 2;

                $totalVatSum += $vat_Sum;

                $vatPercentHalf = sprintf('%0.2f', $vatPercentHalf);
                
                if($row['gst_status'] == "ON"){
                    $total = $tsp + $vat_Sum_Half + $vat_Sum_Half;
                } else {
                    $total = $tsp;
                }

                $tsp   = $tsp - $row['discount_amount'];
                $total = $total - $row['discount_amount'];
                $selling_price = number_format($selling_price,2);

                if($row['gst_status'] == "ON"){
                    $tbl3 = $tbl3.'<tr>
                                        <td width="5%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$count.'</td>
                                        <td width="35%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="left">'.$titledescrip.'</td>
                                        <td width="10%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['hsn'].'</td>
                                        <td width="6%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['unit'].'</td>
                                        <td width="7%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['qty'].'</td>
                                        <td width="12%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="right">'.$selling_price.'</td>
                                        <td width="12%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="right">'.$tsp.'</td>
                                        <td width="13%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;"  align="right">'.number_format($total, 2).'</td>
                                    </tr>
                                    ';
                    $count++;
                } else {

                    if($row['show_discount_percentage'] == 1){    
                        $tbl3 = $tbl3.'<tr>
                                            <td width="6%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$count.'</td>
                                            <td width="26%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="left">'.$titledescrip.'</td>
                                            <td width="15%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['hsn'].'</td>
                                            <td width="10%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['unit'].'</td>
                                            <td width="8%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['qty'].'</td>
                                            <td width="10%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="right">'.$selling_price.'</td>
                                            <td width="10%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="right">'.$discount_value_for_display.'</td>
                                            <td width="15%" align="right" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;">'.number_format($tsp, 2).'</td>
                                        </tr>
                                       ';

                        $count++;
                    }else {
                        $tbl3 = $tbl3.'<tr>
                                            <td width="6%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$count.'</td>
                                            <td width="29%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="left">'.$titledescrip.'</td>
                                            <td width="15%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['hsn'].'</td>
                                            <td width="10%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['unit'].'</td>
                                            <td width="10%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['qty'].'</td>
                                            <td width="15%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="right">'.$selling_price.'</td>
                                            <td width="15%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="right">'.number_format($tsp, 2).'</td>
                                        </tr>
                                       ';

                        $count++;
                    }
                }

                $overallTotal += $total;
                $vatSumTotal  += $vat_Sum_Half;

                $total = $row['total'];
                $terms = $row['payment_terms'];
                $notes = $row['notes'];
                $delivery_terms = $row['delivery_terms'];
                $discount = 0;
                $sub_total = $total + $discount - $row['discount_amount'];
                $show_discount_percentage = $row['show_discount_percentage'];
            }

            $totaldiscount = $sub_total - $discount;
            $discountPercent = $discount * 100 / $sub_total;
            $Total_in_words = $fn->getIndianCurrency($totaldiscount .'.00');
            $totaldiscount = number_format(round($totaldiscount), 2);
            $sub_total = number_format($sub_total,2);
            $discount = number_format($discount,2);
            $discountPercent = number_format($discountPercent,2);
            $displayDiscountPercent = '';
            $emptyRow = '';

            for($ic = 1; $ic <= 6; $ic++){
                if($Row['gst_status'] == "ON"){
                    if($Row['igst_show'] == "1"){
                        $emptyRow .= '
                        <tr>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                        </tr>
                        ';
                    }else{
                        $emptyRow .= '
                        <tr>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                        </tr>
                        ';
                    }
                }
                else{
                    if($Row['show_discount_percentage'] == 1){ 
                        $emptyRow .= '
                            <tr>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            </tr>
                            ';
                    } else {
                        $emptyRow .= '
                            <tr>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            </tr>
                            ';                            
                    }
                }
            }

            if($Row['gst_status'] == "ON") {
                //$sub_total_in_words = $fn->getConvertNumber($overallTotal .'.00');
                $sub_total_in_words = $fn->getIndianCurrency($overallTotal .'.00');
                if($Row['igst_show'] == "1"){
                    $tbl3 = $tbl3.'
                                '.$emptyRow.'
                                <tr>
                                    <td colspan="5" align="center" style="font-weight:bold; line-height:16px;border-top:1px solid #aeafb1;">Total Amount in Words</td>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;border-top:1px solid #aeafb1;">Total Amount Before Tax</td>
                                    <td align="right" style="border-top:1px solid #aeafb1;">'.$totaldiscount.'</td>
                                </tr>
                                <tr>
                                    <td rowspan="5" colspan="5" align="center" style="font-weight:bold;font-size:12px; line-height:16px;">'.strtoupper($sub_total_in_words).'</td>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;">Add: IGST 18.00%</td>
                                    <td align="right">'.number_format($totalVatSum, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;">Total Amount After Tax</td>
                                    <td align="right">'.number_format($overallTotal, 2).'</td>
                                </tr>
                            </tbody>
                        </table>';
                } else {
                    $tbl3 = $tbl3.'
                                '.$emptyRow.'
                                <tr>
                                    <td colspan="5" align="center" style="font-weight:bold; line-height:16px;border-top:1px solid #aeafb1;">Total Amount in Words</td>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;border-top:1px solid #aeafb1;">Total Amount Before Tax</td>
                                    <td align="right" style="border-top:1px solid #aeafb1;">'.$totaldiscount.'</td>
                                </tr>
                                <tr>
                                    <td rowspan="5" colspan="5" align="center" style="font-weight:bold;font-size:12px; line-height:16px;">'.strtoupper($sub_total_in_words).'</td>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;">Add: CGST 9.00%</td>
                                    <td align="right">'.number_format($vatSumTotal, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;">Add: SGST 9.00%</td>
                                    <td align="right">'.number_format($vatSumTotal, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;">Total Tax Amount</td>
                                    <td align="right">'.number_format($totalVatSum, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold;color:#ffffff; line-height:16px;" bgColor="#157ca7">Total Amount After Tax</td>
                                    <td align="right" bgColor="#157ca7" style="font-weight:bold;color:#ffffff;">'.number_format($overallTotal, 2).'</td>
                                </tr>
                            </tbody>
                        </table>';

                }
            } else {

                if($Row['show_discount_percentage'] == 1){ 
                    $tbl3 = $tbl3.'
                                    '.$emptyRow.'
                                    <tr>
                                        <td colspan="7" align="right" style="font-weight:bold; line-height:16px;border-top:1px solid #aeafb1;">TOTAL</td>
                                        <td align="right" style="border-top:1px solid #aeafb1;">'.$totaldiscount.'</td>
                                    </tr>
                                </tbody>
                            </table>';
                } else {
                    $tbl3 = $tbl3.'
                                    '.$emptyRow.'
                                    <tr>
                                        <td colspan="5" style="border-top:1px solid #aeafb1;"></td>
                                        <td align="right" style="font-weight:bold; color:#ffffff; line-height:16px;border-top:1px solid #aeafb1;" bgColor="#157ca7">TOTAL</td>
                                        <td align="right" style="font-weight:bold; color:#ffffff; line-height:16px;border-top:1px solid #aeafb1;" bgColor="#157ca7">'.$totaldiscount.'</td>
                                    </tr>
                                    <br/>
                                    <br/>
                                    <tr>
                                        <td colspan="7" align="right" style="">('.strtoupper($Total_in_words).')</td>
                                    </tr>
                                </tbody>
                            </table>';
                }
            }

            if($Row['gst_status'] == "ON"){

                $tbl4 = '<table cellpadding="4" border="0" width="100%" style="font-size:11px;">';

                if($Row['igst_show'] == "1"){
                    $tbl4 = $tbl4.'
                        <br/>
                        <br/>
                        <tr>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="right">Tax Rate</td>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="right">Taxable</td>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="right">IGST</td>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="right">Total Tax</td>
                        </tr>
                    ';
                }else{
                    $tbl4 = $tbl4.'
                        <br/>
                        <br/>
                        <tr>
                            <td rowspan="2" style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Tax Rate</td>
                            <td rowspan="2" style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Taxable</td>
                            <td colspan="2" style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">CGST</td>
                            <td colspan="2" style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">SGST</td>
                            <td rowspan="2" style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Total Tax</td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Rate</td>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Amount</td>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Rate</td>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Amount</td>
                        </tr>
                    ';
                }

                

                $SQLTax = "
                SELECT  p.gst
                        ,ci.debit_note_id
                        ,p.hsn AS hsn_sac
                        ,SUM(ci.unit_price * ci.qty) AS qty_amount
                FROM `debit_note_item` ci
                LEFT JOIN product p ON (p.product_id = ci.record_id)
                WHERE ci.debit_note_id = '{$Row['debit_note_id']}'
                AND p.gst > 0
                GROUP BY p.gst
                ORDER BY p.gst ASC
                ";
                $resultTax  = $db->sql_query($SQLTax);

                $totalVatSum = 0;
                $counter = 1;
                while($rowTax     = $db->sql_fetchrow($resultTax)){

                    $total_amount = $rowTax['qty_amount'];
                    
                    if($rowTax['gst'] == ''){
                        $vatPercent = '0.00';
                    }
                    else{
                        $vatPercent = $rowTax['gst'];
                    }

                    $vat_Sum  = ($total_amount * $rowTax['gst'])/100;

                    $gstRatePercent = $rowTax['gst'] / 2;

                    //$vat_Amount_total = $total_amount + $vat_Sum;
                    $vat_Amount_total = $total_amount;
                    if($vat_Sum == 0){
                        $vat_Amount_total = 0;
                    }

                    $vatPercentHalf = $vatPercent / 2;
                    $vat_Sum_Half   = $vat_Sum / 2;

                    $totalVatSum += $vat_Sum;

                    $vatPercentHalf = sprintf('%0.2f', $vatPercentHalf);
                    $total_tax = $vat_Sum_Half + $vat_Sum_Half;
                    if($Row['igst_show'] == "1"){
                        $tbl4 = $tbl4.'
                        <tr>
                            <td align="right">'.$rowTax['gst'].' %</td>
                            <td align="right">'.number_format($vat_Amount_total, 2).'</td>
                            <td align="right">'.number_format($vat_Sum, 2).'</td>
                            <td align="right">'.number_format($total_tax, 2).'</td>
                        </tr>
                        ';
                    }else{
                        $tbl4 = $tbl4.'
                        <tr>
                            <td align="right">'.$rowTax['gst'].' %</td>
                            <td align="right">'.number_format($vat_Amount_total, 2).'</td>
                            <td align="right">'.number_format($gstRatePercent, 0).'%</td>
                            <td align="right">'.number_format($vat_Sum_Half, 2).'</td>
                            <td align="right">'.number_format($gstRatePercent, 0).'%</td>
                            <td align="right">'.number_format($vat_Sum_Half, 2).'</td>
                            <td align="right">'.number_format($total_tax, 2).'</td>
                        </tr>
                        ';
                    }

                    $counter++;
                }   
            }

            if($Row['gst_status'] == "ON"){

                $total_vat_Amount_total += $vat_Amount_total;
                $total_vat_Sum_Half += $vat_Sum_Half;
                $total_total_tax += $total_tax;
                $total_vat_Sum += $vat_Sum;

                if($Row['igst_show'] == "1"){
                    $tbl4 = $tbl4.'
                    <tr>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">TOTAL</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_vat_Amount_total, 2).'</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_vat_Sum, 2).'</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_total_tax, 2).'</td>
                    </tr>
                    ';

                }else{
                    $tbl4 = $tbl4.'
                    <tr>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">TOTAL</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_vat_Amount_total, 2).'</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;"></td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_vat_Sum_Half, 2).'</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;"></td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_vat_Sum_Half, 2).'</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_total_tax, 2).'</td>
                    </tr>
                    ';
                }

                    $tbl4 = $tbl4.'</table>';

            }

            $tbl5 = '<table cellpadding="4" border="0" width="100%" nobr="true">';

            $tbl5 = $tbl5.'
                <tr>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                    <td width="48%" align="left" style="font-size:11px;font-weight:bold;color:#ffffff;" bgColor="#157ca7"><b>Bank Details : </b></td>
                    <td width="2%"></td>
                    <td width="20%" align="center" rowspan="4" style="font-weight:bold;font-size:11px;vertical-align:bottom;border:1px solid #e5e5e5;"><br/><br/><br/><br/><br/><br/><br/><br/>Common Seal</td>
                    <td width="30%" align="right" rowspan="4" style="font-size:12px;font-weight:bold;">For '.$cpCfg['cp.companyName'].'<br/><br/><br/><br/><br/><br/>Authorised signatory</td>
                </tr>
                <tr>
                    <td width="48%" align=""><span style="font-weight:bold;font-size:11px;color:#000000;"><b>'.$cpCfg['cp.bankDetails'].'</b></span></td>
                    <td width="2%"></td>
                </tr>
                <tr>
                <br/>
                    <td width="48%" align="" bgColor="#157ca7"><span style="font-weight:bold;font-size:11px; color:#ffffff;">Mode of Transport : </span></td>
                    <td width="2%"></td>
                </tr>
                <tr>
                    <td width="48%" align=""><font style="font-size:12px;">Vehicle No. : '.$notes.'</font></td>
                    <td width="2%"></td>
                </tr>
            </table>
            ';            
        }


        $pdf->ln(-5);
        $pdf->writeHTML($tbl1, true, false, false, false, '');
        $pdf->writeHTML($tblQuote, true, false, false, false, '');
        $pdf->writeHTML($tbl2, true, false, false, false, '');
        $pdf->writeHTML($tbl3, true, false, false, false, '');
        $pdf->writeHTML($tbl4, true, false, false, false, '');
        $pdf->writeHTML($tbl5, true, false, false, false, '');

        $pdf->Output();

    }


	/**
     *
     */
    function getPrintCreditRecord() {
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $tv = Zend_Registry::get('tv');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $searchVar = Zend_Registry::get('searchVar');
        $media = Zend_Registry::get('media');
        $cpPaths = Zend_Registry::get('cpPaths');
        $dbUtil = Zend_Registry::get('dbUtil');
        $site_id  = $fn->getSessionParam('cp_site_id');

        ini_set('memory_limit', '512M');
        set_time_limit(50000);

        include_once(CP_LIBRARY_PATH.'lib_php/tcpdf/tcpdf.php');
        include_once(CP_LOCAL_PATH.'lib/headfoot.php');

        $pdf = new MYPDF_Local(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('USS');
        $pdf->SetSubject('Proforma Invoice');
        $pdf->SetTitle('Proforma Invoice');

        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 04', PDF_HEADER_STRING);
        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER,10);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        /*HEADER PART AND FOOTER PART FUNCTIONS HAS BEEN ADDED IN (headfoot.php) PATH INCLUDE: (admin/lib/headfoot.php)*/
        $pdf->AddPage();

        $invoiceHeading = '';

        $credit_note_id = $fn->getReqParam('credit_note_id');
        $invoice_code = $fn->getReqParam('invoice_code');
        $invoice_type = $fn->getReqParam('invoice_type');
        if($invoice_type == 'normal'){
            $invoiceHeading = 'ORIGINAL - ';
        }
        else if($invoice_type == 'transporter'){
            $invoiceHeading = 'TRANSPORTER - ';
        }
        else if($invoice_type == 'proforma'){
            $invoiceHeading = 'PROFORMA - ';
        }
        else if($invoice_type == 'extra'){
            $invoiceHeading = 'DUPLICATE - ';
        }

        $SQL = "
        SELECT ini.*
              ,ini.item_title AS product_title
              ,p.title AS product_title1
              ,p.unit
              ,p.item_code
              ,p.part_number
              ,p.description_short
              ,p.hsn
              ,c.company_name
              ,c.address_flat
              ,c.address_street
              ,c.address_town
              ,c.address_state
              ,c.address_po_code
              , (SELECT gc.name FROM geo_country gc
                 WHERE gc.country_code = c.address_country)
                AS address_country
              ,c.billing_address_flat
              ,c.billing_address_street
              ,c.billing_address_town
              ,c.billing_address_state
              , (SELECT gc.name FROM geo_country gc
                 WHERE gc.country_code = c.billing_address_country)
                AS billing_address_country
              ,c.fax
              ,c.phone
              ,c.tin_no
              ,c.cst_no
              ,c.gst_no
              ,i.invoice_date
              ,q.delivery_date
              ,q.delivery_location
              ,q.gst_enabled
              ,q.show_discount_percentage
              ,q.currency
              ,q.payment_terms
              ,q.delivery_terms
              ,ini.unit_price
              ,i.invoice_code
              ,i.invoice_terms
              ,i.invoice_due_date
              ,i.notes
              ,i.cst
              ,i.vat
              ,i.cst_value
              ,i.vat_value
              ,i.frieght_cost
              ,i.cust_po_no
              ,i.p_f
              ,i.credit_note_id
              ,o.order_id
              ,o.notes
              ,o.shipping_address1
              ,o.shipping_first_name
              ,o.shipping_address2
              ,o.shipping_address_city
              ,o.shipping_address_state
              ,o.gst_status
              ,o.igst_show
               ,(SELECT gc.name FROM geo_country gc
                 WHERE gc.country_code = o.shipping_address_country)
                 AS shipping_address_country
              ,q.quote_code
              ,q.currency
              ,ini.qty * ini.unit_price AS amount
              ,(SELECT SUM(init.qty * init.cost_price) FROM credit_note_item init
               WHERE init.credit_note_id = ini.credit_note_id) AS sub_total
              ,(SELECT SUM(init.qty * init.unit_price) FROM credit_note_item init
               WHERE init.credit_note_id = ini.credit_note_id) AS total
              ,CONCAT_WS(' ', co.first_name, co.last_name) AS contact_name
              ,co.salutation
              ,(SELECT invoice_code FROM invoice inv WHERE inv.order_id = o.order_id AND inv.status != 'Cancelled') AS invoiceCode
              ,(SELECT invoice_date FROM invoice inv WHERE inv.order_id = o.order_id AND inv.status != 'Cancelled') AS creditDate
        FROM credit_note_item ini
        LEFT JOIN product p ON (p.product_id = ini.record_id)
        LEFT JOIN credit_note i ON (i.credit_note_id = ini.credit_note_id)
        LEFT JOIN `order` o ON (o.order_id = i.order_id)
        LEFT JOIN company c ON (c.company_id = o.company_id)
        LEFT JOIN quote q ON (q.quote_id = o.quote_id)
        LEFT JOIN contact co ON (co.contact_id = q.contact_id)
        LEFT JOIN product_group pg ON (p.product_group_id = pg.product_group_id)
        WHERE i.credit_note_id = '{$credit_note_id}'
          AND i.status != 'Cancelled'
        ORDER BY ini.credit_note_item_id, pg.sort_order ASC, p.title
        ";
        $result = $db->sql_query($SQL);
        $result2 = $db->sql_query($SQL);
        $Row = $db->sql_fetchrow($result2);

        $numRows  = $db->sql_numrows($result);
        //============================================================================= //

        $pdf->SetFont('helvetica','', 8);
        $today = date("d-m-Y");

        if($site_id == 1) {
            $tbl1 = '
            <table border="0" width="100%" style="font-size:17px;">
                <tr>
                    <td align="center" style="font-weight:bold; text-decoration: underline;">CREDIT NOTE</td>
                </tr>
            </table>
            ';

            $tblQuote ='
            <table border="0" width="100%" cellpadding="3">
            </table>
            ';

            $contact_name = '';
            if($Row['contact_name'] != ''){
                $contact_name = "Kind Attn: {$Row['salutation']}.{$Row['contact_name']}";
            }

            $addressFlat     = $Row['address_flat'];
            $addressStreet   = $Row['address_street'];
            $addressTown     = $Row['address_town'];
            $addressState    = $Row['address_state'];
            $addressCountry  = $Row['address_country'];

            $billingAddressFlat     = $Row['billing_address_flat'];
            $billingAddressStreet   = $Row['billing_address_street'];
            $billingAddressTown     = $Row['billing_address_town'];
            $billingAddressState    = $Row['billing_address_state'];
            $billingAddressCountry  = $Row['billing_address_country'];
            $invoiceDate   = $fn->getCPDate($Row['invoice_date'], 'd-m-Y');
                        $creditDate   = $fn->getCPDate($Row['creditDate'], 'd-m-Y');


            $tbl2 ='
            <table border="0" width="100%" cellpadding="3">
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>Credit Note No : </i>'.$Row['invoice_code'].'</td>
                    <td width="50%" style="border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>Reference Invoice: </i> '.$Row['invoiceCode'].' </td>
                </tr>
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>Date Of Issue : </i>'.$invoiceDate.'</td>
                    <td width="50%" style="border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>Date Of Invoice : </i> '.$creditDate.'</td>
                </tr>
                  <tr>
                    <td width="25%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>State : </i></td>
                    <td width="13%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>Code : </i></td>
                    <td width="12%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;">'.$Row['address_po_code'].'</td>
                    <td width="50%" style="border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"></td>
                </tr>
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>From :</i></td>
                    <td width="50%" style="border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>Billed to :</i></td>
                </tr>
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;font-size:11px;font-weight:bold;">'.$cpCfg['cp.companyName'].'</td>
                    <td width="50%" style="border-right:1px solid #000000;font-size:11px;font-weight:bold;">'.$Row['company_name'].'</td>
                </tr>
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;font-size:11px;font-weight:bold;">'.$cpCfg['cp.addressPdf5'].'</td>
                    <td width="50%" style="border-right:1px solid #000000;font-size:11px;font-weight:bold;">'.$Row['address_flat'].'</td>
                </tr>
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;font-weight:bold;font-size:11px;">'.$cpCfg['cp.panNoPdf'].'</td>
                    <td width="50%" style="border-right:1px solid #000000;font-size:11px;font-weight:bold;">'.$Row['address_street'].' '.$Row['address_town'].' '.$Row['address_state'].'</td>
                </tr>
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;font-weight:bold;font-size:11px;"></td>
                    <td width="50%" style="border-right:1px solid #000000;font-size:11px;font-weight:bold;">GST IN / UIN :'.$Row['gst_no'].'</td>
                </tr>
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:11px;"><span style="font-weight:bold;">State Code: 33</span></td>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:11px;"><span style="font-weight:bold;">State Code: </span>'.$Row['address_po_code'].'</td>
                </tr>
            </table>
            ';


            if($Row['gst_status'] == "ON"){
                $tbl3 ='
                <table border="1" nobr="true" width="100%" cellpadding="3" style="font-size:10px;">
                    <thead>
                        <tr>
                            <th width="5%"  style="border:1px solid #000000;font-weight:bold;" align="center">S.No</th>
                            <th width="35%" style="border:1px solid #000000;font-weight:bold;" align="left">Product Description</th>
                            <th width="10%"  style="border:1px solid #000000;font-weight:bold;" align="center">HSN Code</th>
                            <th width="6%"  style="border:1px solid #000000;font-weight:bold;" align="center">UOM</th>
                            <th width="7%"  style="border:1px solid #000000;font-weight:bold;" align="center">Qty</th>
                            <th width="12%"  style="border:1px solid #000000;font-weight:bold;" align="right">Price</th>
                            <th width="12%"  style="border:1px solid #000000;font-weight:bold;" align="right">Taxbl Val</th>
                            <th width="13%" style="border:1px solid #000000;font-weight:bold;" align="right">Total</th>
                        </tr>
                    </thead>
                ';
            }

            else {
                if($Row['show_discount_percentage'] == 1){
                    $tbl3 = '
                    <table border="1" width="100%" cellpadding="3" style="font-size:11px;">
                        <thead>
                            <tr>
                                <td width="6%"  style="font-weight:bold;" align="center">S.No</td>
                                <td width="26%" style="font-weight:bold;" align="left">Product Description</td>
                                <td width="15%" style="font-weight:bold;" align="center">HSN Code</td>
                                <td width="10%" style="font-weight:bold;" align="center">UOM</td>
                                <td width="8%"  style="font-weight:bold;" align="center">Qty</td>
                                <td width="10%" style="font-weight:bold;" align="right">Price</td>
                                <td width="10%" style="font-weight:bold;" align="right">Discount</td>
                                <td width="15%" style="font-weight:bold;" align="right">Total ('.$Row['currency'].')</td>
                            </tr>
                        </thead>
                        <tbody>
                    ';
                } else {
                    $tbl3 = '
                    <table border="1" width="100%" cellpadding="3" style="font-size:11px;">
                        <thead>
                            <tr>
                                <td width="6%"  style="font-weight:bold;" align="center">S.No</td>
                                <td width="29%" style="font-weight:bold;" align="left">Product Description</td>
                                <td width="15%" style="font-weight:bold;" align="center">HSN Code</td>
                                <td width="10%" style="font-weight:bold;" align="center">UOM</td>
                                <td width="10%" style="font-weight:bold;" align="center">Qty</td>
                                <td width="15%" style="font-weight:bold;" align="right">Price</td>
                                <td width="15%" style="font-weight:bold;" align="right">Total ('.$Row['currency'].')</td>
                            </tr>
                        </thead>
                        <tbody>
                    ';
                }
            }

            $count = 1;
            $overallTotal = 0;
            $vatSumTotal  = 0;
            while($row = $db->sql_fetchrow($result)){

                $discount_value_for_one_qty = 0;
                $discount_value_for_display = 0;
                if($row['discount_percentage'] > 0){
                    if($row['discount_type'] == '%'){
                        $discount_value_for_one_qty  =  $row['cost_price'] * ($row['discount_percentage']/100);
                        $discount_value_for_display  =  $row['discount_percentage'] . '%';
                    }
                    else if($row['discount_type']  == 'Value'){
                        $discount_value_for_one_qty  =  $row['discount_percentage'];
                        $discount_value_for_display  =  $row['discount_percentage'];
                    }
                }
 if($row['show_discount_percentage'] != 1){
                    $selling_price = $row['unit_price'];
                    $tsp = ($row['qty'] * $selling_price);

                    $SQLTax = "
                    SELECT  p.gst
                            ,SUM(ci.unit_price * ci.qty) AS qty_amount
                    FROM `credit_note_item` ci
                    LEFT JOIN `credit_note` cn ON (cn.credit_note_id = ci.credit_note_id)
                    LEFT JOIN `product` p ON (p.product_id = ci.record_id)
                    WHERE ci.credit_note_item_id = '{$row['credit_note_item_id']}'
                    AND p.gst > 0
                    AND cn.status != 'Cancelled'
                    ";
                    $resultTax  = $db->sql_query($SQLTax);
                    $rowTax     = $db->sql_fetchrow($resultTax);

                } else {
                    $selling_price = $row['unit_price'];
                    $tsp = ($row['qty'] * $selling_price);

                    $SQLTax = "
                    SELECT  p.gst
                            ,SUM(ci.unit_price * ci.qty) AS qty_amount
                     FROM `credit_note_item` ci
                     LEFT JOIN `credit_note` cn ON (cn.credit_note_id = ci.credit_note_id)
                    LEFT JOIN `product` p ON (p.product_id = ci.record_id)
                    WHERE ci.credit_note_item_id = '{$row['credit_note_item_id']}'
                    AND p.gst > 0
                    AND cn.status != 'Cancelled'
                    ";
                    $resultTax  = $db->sql_query($SQLTax);
                    $rowTax     = $db->sql_fetchrow($resultTax);
                }
                $titledesc = $row['item_title'];

                $titledescrip = $titledesc;
                $discount_value_for_one_qty = number_format($discount_value_for_one_qty, 2);

                $totalVatSum = 0;

                $total_amount = $rowTax['qty_amount'];
                
                if($rowTax['gst'] == ''){
                    $vatPercent = '0.00';
                }
                else{
                    $vatPercent = $rowTax['gst'];
                }

                $vat_Sum  = ($total_amount * $rowTax['gst'])/100;

                $vat_Amount_total = $total_amount + $vat_Sum;
                if($vat_Sum == 0){
                    $vat_Amount_total = 0;
                }

                $vatPercentHalf = $vatPercent / 2;
                $vat_Sum_Half   = $vat_Sum / 2;

                $totalVatSum += $vat_Sum;

                $vatPercentHalf = sprintf('%0.2f', $vatPercentHalf);
                
                if($row['gst_status'] == "ON"){
                    $total = $tsp + $vat_Sum_Half + $vat_Sum_Half;
                } else {
                    $total = $tsp;
                }

                $tsp   = $tsp - $row['discount_amount'];
                $total = $total - $row['discount_amount'];
                $selling_price = number_format($selling_price,2);

                if($row['gst_status'] == "ON"){
                    $tbl3 = $tbl3.'<tr>
                                        <td width="5%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="center">'.$count.'</td>
                                        <td width="35%" style="border-left:1px solid #000000;border-right:1px solid #000000;" align="left">'.$titledescrip.'</td>
                                        <td width="10%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="center">'.$row['hsn'].'</td>
                                        <td width="6%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="center">'.$row['unit'].'</td>
                                        <td width="7%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="center">'.$row['qty'].'</td>
                                        <td width="12%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="right">'.$selling_price.'</td>
                                        <td width="12%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="right">'.$tsp.'</td>
                                        <td width="13%" style="border-left:1px solid #000000;border-right:1px solid #000000;"  align="right">'.number_format($total, 2).'</td>
                                    </tr>
                                    ';
                    $count++;
                } else {

                    if($row['show_discount_percentage'] == 1){    
                        $tbl3 = $tbl3.'<tr>
                                            <td width="6%"  align="center">'.$count.'</td>
                                            <td width="26%" align="left">'.$titledescrip.'</td>
                                            <td width="15%" align="center">'.$row['hsn'].'</td>
                                            <td width="10%" align="center">'.$row['unit'].'</td>
                                            <td width="8%"  align="center">'.$row['qty'].'</td>
                                            <td width="10%" align="right">'.$selling_price.'</td>
                                            <td width="10%" align="right">'.$discount_value_for_display.'</td>
                                            <td width="15%" align="right">'.number_format($tsp, 2).'</td>
                                        </tr>
                                       ';

                        $count++;
                    }else {
                        $tbl3 = $tbl3.'<tr>
                                            <td width="6%"  align="center">'.$count.'</td>
                                            <td width="29%" align="left">'.$titledescrip.'</td>
                                            <td width="15%" align="center">'.$row['hsn'].'</td>
                                            <td width="10%" align="center">'.$row['unit'].'</td>
                                            <td width="10%" align="center">'.$row['qty'].'</td>
                                            <td width="15%" align="right">'.$selling_price.'</td>
                                            <td width="15%" align="right">'.number_format($tsp, 2).'</td>
                                        </tr>
                                       ';

                        $count++;
                    }
                }

                $overallTotal += $total;
                $vatSumTotal  += $vat_Sum_Half;

                $total = $row['total'];
                $terms = $row['payment_terms'];
                $notes = $row['notes'];
                $delivery_terms = $row['delivery_terms'];
                $discount = 0;
                $sub_total = $total + $discount - $row['discount_amount'];
                $show_discount_percentage = $row['show_discount_percentage'];
            }
            $tbl4 = '';

            $totaldiscount = $sub_total - $discount;
            $discountPercent = $discount * 100 / $sub_total;
            $totaldiscount = number_format(round($totaldiscount), 2);
            $sub_total = number_format($sub_total,2);
            $discount = number_format($discount,2);
            $discountPercent = number_format($discountPercent,2);
            $displayDiscountPercent = '';

            if($Row['gst_status'] == "ON") {
                //$sub_total_in_words = $fn->getConvertNumber($overallTotal .'.00');
                $sub_total_in_words = $fn->getIndianCurrency($overallTotal .'.00');
                if($Row['igst_show'] == "1"){
                    $tbl3 = $tbl3.'
                                <tr>
                                    <td colspan="5" align="center" style="font-weight:bold;">Total Amount in Words</td>
                                    <td colspan="2" align="right" style="font-weight:bold;">Total Amount Before Tax</td>
                                    <td align="right">'.$totaldiscount.'</td>
                                </tr>
                                <tr>
                                    <td rowspan="5" colspan="5" align="center" style="font-weight:bold;font-size:12px;">'.strtoupper($sub_total_in_words).'</td>
                                    <td colspan="2" align="right" style="font-weight:bold;">Add: IGST 18.00%</td>
                                    <td align="right">'.number_format($totalVatSum, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold;">Total Amount After Tax</td>
                                    <td align="right">'.number_format($overallTotal, 2).'</td>
                                </tr>
                            </tbody>
                        </table>';
                } else {
                    $tbl3 = $tbl3.'
                                <tr>
                                    <td colspan="5" align="center" style="font-weight:bold;">Total Amount in Words</td>
                                    <td colspan="2" align="right" style="font-weight:bold;">Total Amount Before Tax</td>
                                    <td align="right">'.$totaldiscount.'</td>
                                </tr>
                                <tr>
                                    <td rowspan="5" colspan="5" align="center" style="font-weight:bold;font-size:12px;">'.strtoupper($sub_total_in_words).'</td>
                                    <td colspan="2" align="right" style="font-weight:bold;">Add: CGST 9.00%</td>
                                    <td align="right">'.number_format($vatSumTotal, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold;">Add: SGST 9.00%</td>
                                    <td align="right">'.number_format($vatSumTotal, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold;">Total Tax Amount</td>
                                    <td align="right">'.number_format($totalVatSum, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold;">Total Amount After Tax</td>
                                    <td align="right">'.number_format($overallTotal, 2).'</td>
                                </tr>
                            </tbody>
                        </table>';

                }
            } else {

                if($Row['show_discount_percentage'] == 1){ 
                    $tbl3 = $tbl3.'
                                    <tr>
                                        <td colspan="7" align="right" style="font-weight:bold;">TOTAL</td>
                                        <td align="right">'.$totaldiscount.'</td>
                                    </tr>
                                </tbody>
                            </table>';
                } else {
                    $tbl3 = $tbl3.'
                                    <tr>
                                        <td colspan="6" align="right" style="font-weight:bold;">TOTAL</td>
                                        <td align="right">'.$totaldiscount.'</td>
                                    </tr>
                                </tbody>
                            </table>';
                }
            }

            $tbl5 = '<table cellpadding="4" border="1" width="100%" nobr="true">';

            $tbl5 = $tbl5.'
                <tr>
                    <td width="50%" align="left" style="font-size:10px;font-weight:bold;"><b>Bank Details : </b><br/><b>'.$cpCfg['cp.bankDetails'].'</b></td>
                    <td width="20%" align="center"></td>
                    <td width="30%" align="right" rowspan="2" style="font-size:12px;font-weight:bold;">For '.$cpCfg['cp.companyName'].'<br/><br/><br/><br/><br/><br/>Authorised signatory</td>
                </tr>
                <tr>
                    <td width="50%" align=""><span style="font-weight:bold;font-size:11px;">Mode of Transport : </span><br/><br/>Vehicle No. : '.$notes.'</td>
                    <td width="20%" align="center" style="font-weight:bold;font-size:11px;vertical-align:bottom;"><br/><br/><br/><br/>Common Seal</td>
                </tr>
            </table>
            ';
        } else {
            $pdf->SetFont('calibri','', 8);
            $invoiceDate   = $fn->getCPDate($Row['invoice_date'], 'd-m-Y');
            $creditDate   = $fn->getCPDate($Row['creditDate'], 'd-m-Y');


            $tblQuote ='
            <table border="0" width="100%" cellpadding="3">
            </table>
            ';

            $contact_name = '';
            if($Row['contact_name'] != ''){
                $contact_name = "Kind Attn: {$Row['salutation']}.{$Row['contact_name']}";
            }

            $addressFlat     = $Row['address_flat'];
            $addressStreet   = $Row['address_street'];
            $addressTown     = $Row['address_town'];
            $addressState    = $Row['address_state'];
            $addressCountry  = $Row['address_country'];

            $billingAddressFlat     = $Row['billing_address_flat'];
            $billingAddressStreet   = $Row['billing_address_street'];
            $billingAddressTown     = $Row['billing_address_town'];
            $billingAddressState    = $Row['billing_address_state'];
            $billingAddressCountry  = $Row['billing_address_country'];

            $tbl1 = '
            <table border="0" width="100%" cellpadding="3">
                <tr>
                    <td width="27%" style="font-size:13px;font-weight:bold;color:#157ca7;"><strong>From :</strong><br/><font style="font-size:14px;font-weight:bold;color:#000000;">'.$cpCfg['cp.companyName'].'<br/></font><font style="font-size:11px;color:#000000;">'.$cpCfg['cp.addressPdf5'].'<br/>'.$cpCfg['cp.panNoPdf'].'<br/>State Code: 33</font></td>
                    <td width="43%" style="font-size:13px;font-weight:bold;color:#157ca7;"><strong>Billed to :</strong><br/><font style="font-size:14px;font-weight:bold;color:#000000;">'.$Row['company_name'].'<br/></font><font style="font-size:11px;color:#000000;">'.$Row['address_flat'].', '.$Row['address_street'].' '.$Row['address_town'].' '.$Row['address_state'].'<br/>GST IN / UIN :'.$Row['gst_no'].'<br/>State Code: '.$Row['address_po_code'].'</font></td>
                    <td width="30%" style="font-weight:bold;font-size:22px;">CREDIT NOTE <font style="font-size:12px;font-weight:bold;"><br/><br/><i>Code : </i>'.$Row['invoice_code'].'<br/><i>Date : </i>'.$invoiceDate.'<br/><i>Reference Invoice : </i>'.$Row['invoiceCode'].'<br/><i>Invoice Date : </i>'.$creditDate.'</font></td>
                </tr>
            </table>
            ';

            $tbl2 ='';

            if($Row['gst_status'] == "ON"){
                $tbl3 ='
                <table border="0" nobr="true" width="100%" cellpadding="4" style="font-size:11px;">
                    <thead>
                        <tr>
                            <th width="5%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">S.No</th>
                            <th width="35%" style="color:#fff;font-weight:bold; line-height:16px;" align="left" bgColor="#157ca7">Product Description</th>
                            <th width="10%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">HSN Code</th>
                            <th width="6%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">UOM</th>
                            <th width="7%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">Qty</th>
                            <th width="12%"  style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Price</th>
                            <th width="12%"  style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Taxbl Val</th>
                            <th width="13%" style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Total</th>
                        </tr>
                    </thead>
                ';
            }

            else {
                if($Row['show_discount_percentage'] == 1){
                    $tbl3 = '
                    <table border="0" width="100%" cellpadding="4" style="font-size:11px;">
                        <thead>
                            <tr>
                                <td width="6%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">S.No</td>
                                <td width="26%" style="color:#fff;font-weight:bold; line-height:16px;" align="left" bgColor="#157ca7">Product Description</td>
                                <td width="15%" style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">HSN Code</td>
                                <td width="10%" style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">UOM</td>
                                <td width="8%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">Qty</td>
                                <td width="10%" style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Price</td>
                                <td width="10%" style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Discount</td>
                                <td width="15%" style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Total ('.$Row['currency'].')</td>
                            </tr>
                        </thead>
                        <tbody>
                    ';
                } else {
                    $tbl3 = '
                    <table border="0" width="100%" cellpadding="4" style="font-size:11px;">
                        <thead>
                            <tr>
                                <td width="6%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">S.No</td>
                                <td width="29%" style="color:#fff;font-weight:bold; line-height:16px;" align="left" bgColor="#157ca7">Product Description</td>
                                <td width="15%" style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">HSN Code</td>
                                <td width="10%" style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">UOM</td>
                                <td width="10%" style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">Qty</td>
                                <td width="15%" style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Price</td>
                                <td width="15%" style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Total ('.$Row['currency'].')</td>
                            </tr>
                        </thead>
                        <tbody>
                    ';
                }
            }

            $tbl4 = '';

            $count = 1;
            $overallTotal = 0;
            $vatSumTotal  = 0;
            $total_vat_Amount_total = 0;
            $total_vat_Sum_Half = 0;
            $total_vat_Sum = 0;
            $total_total_tax = 0;
            $total_tax = 0;
            while($row = $db->sql_fetchrow($result)){

                $discount_value_for_one_qty = 0;
                $discount_value_for_display = 0;
                if($row['discount_percentage'] > 0){
                    if($row['discount_type'] == '%'){
                        $discount_value_for_one_qty  =  $row['cost_price'] * ($row['discount_percentage']/100);
                        $discount_value_for_display  =  $row['discount_percentage'] . '%';
                    }
                    else if($row['discount_type']  == 'Value'){
                        $discount_value_for_one_qty  =  $row['discount_percentage'];
                        $discount_value_for_display  =  $row['discount_percentage'];
                    }
                }

               if($row['show_discount_percentage'] != 1){
                    $selling_price = $row['unit_price'];
                    $tsp = ($row['qty'] * $selling_price);

                    $SQLTax = "
                    SELECT  p.gst
                            ,SUM(ci.unit_price * ci.qty) AS qty_amount
                    FROM `credit_note_item` ci
                    LEFT JOIN `credit_note` cn ON (cn.credit_note_id = ci.credit_note_id)
                    LEFT JOIN `product` p ON (p.product_id = ci.record_id)
                    WHERE ci.credit_note_item_id = '{$row['credit_note_item_id']}'
                    AND p.gst > 0
                    AND cn.status != 'Cancelled'
                    ";
                    $resultTax  = $db->sql_query($SQLTax);
                    $rowTax     = $db->sql_fetchrow($resultTax);

                } else {
                    $selling_price = $row['unit_price'];
                    $tsp = ($row['qty'] * $selling_price);

                    $SQLTax = "
                    SELECT  p.gst
                            ,SUM(ci.unit_price * ci.qty) AS qty_amount
                     FROM `credit_note_item` ci
                     LEFT JOIN `credit_note` cn ON (cn.credit_note_id = ci.credit_note_id)
                    LEFT JOIN `product` p ON (p.product_id = ci.record_id)
                    WHERE ci.credit_note_item_id = '{$row['credit_note_item_id']}'
                    AND p.gst > 0
                    AND cn.status != 'Cancelled'
                    ";
                    $resultTax  = $db->sql_query($SQLTax);
                    $rowTax     = $db->sql_fetchrow($resultTax);
                }

                $titledesc = $row['item_title'];

                $titledescrip = $titledesc;
                $discount_value_for_one_qty = number_format($discount_value_for_one_qty, 2);

                $totalVatSum = 0;

                $total_amount = $rowTax['qty_amount'];
                
                if($rowTax['gst'] == ''){
                    $vatPercent = '0.00';
                }
                else{
                    $vatPercent = $rowTax['gst'];
                }

                $vat_Sum  = ($total_amount * $rowTax['gst'])/100;

                $vat_Amount_total = $total_amount + $vat_Sum;
                if($vat_Sum == 0){
                    $vat_Amount_total = 0;
                }

                $vatPercentHalf = $vatPercent / 2;
                $vat_Sum_Half   = $vat_Sum / 2;

                $totalVatSum += $vat_Sum;

                $vatPercentHalf = sprintf('%0.2f', $vatPercentHalf);
                
                if($row['gst_status'] == "ON"){
                    $total = $tsp + $vat_Sum_Half + $vat_Sum_Half;
                } else {
                    $total = $tsp;
                }

                $tsp   = $tsp - $row['discount_amount'];
                $total = $total - $row['discount_amount'];
                $selling_price = number_format($selling_price,2);

                if($row['gst_status'] == "ON"){
                    $tbl3 = $tbl3.'<tr>
                                        <td width="5%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$count.'</td>
                                        <td width="35%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="left">'.$titledescrip.'</td>
                                        <td width="10%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['hsn'].'</td>
                                        <td width="6%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['unit'].'</td>
                                        <td width="7%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['qty'].'</td>
                                        <td width="12%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="right">'.$selling_price.'</td>
                                        <td width="12%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="right">'.$tsp.'</td>
                                        <td width="13%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;"  align="right">'.number_format($total, 2).'</td>
                                    </tr>
                                    ';
                    $count++;
                } else {

                    if($row['show_discount_percentage'] == 1){    
                        $tbl3 = $tbl3.'<tr>
                                            <td width="6%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$count.'</td>
                                            <td width="26%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="left">'.$titledescrip.'</td>
                                            <td width="15%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['hsn'].'</td>
                                            <td width="10%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['unit'].'</td>
                                            <td width="8%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['qty'].'</td>
                                            <td width="10%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="right">'.$selling_price.'</td>
                                            <td width="10%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="right">'.$discount_value_for_display.'</td>
                                            <td width="15%" align="right" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;">'.number_format($tsp, 2).'</td>
                                        </tr>
                                       ';

                        $count++;
                    }else {
                        $tbl3 = $tbl3.'<tr>
                                            <td width="6%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$count.'</td>
                                            <td width="29%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="left">'.$titledescrip.'</td>
                                            <td width="15%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['hsn'].'</td>
                                            <td width="10%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['unit'].'</td>
                                            <td width="10%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['qty'].'</td>
                                            <td width="15%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="right">'.$selling_price.'</td>
                                            <td width="15%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="right">'.number_format($tsp, 2).'</td>
                                        </tr>
                                       ';

                        $count++;
                    }
                }

                $overallTotal += $total;
                $vatSumTotal  += $vat_Sum_Half;

                $total = $row['total'];
                $terms = $row['payment_terms'];
                $notes = $row['notes'];
                $delivery_terms = $row['delivery_terms'];
                $discount = 0;
                $sub_total = $total + $discount - $row['discount_amount'];
                $show_discount_percentage = $row['show_discount_percentage'];
            }

            $totaldiscount = $sub_total - $discount;
            $discountPercent = $discount * 100 / $sub_total;
            $Total_in_words = $fn->getIndianCurrency($totaldiscount .'.00');
            $totaldiscount = number_format(round($totaldiscount), 2);
            $sub_total = number_format($sub_total,2);
            $discount = number_format($discount,2);
            $discountPercent = number_format($discountPercent,2);
            $displayDiscountPercent = '';
            $emptyRow = '';

            for($ic = 1; $ic <= 6; $ic++){
                if($Row['gst_status'] == "ON"){
                    if($Row['igst_show'] == "1"){
                        $emptyRow .= '
                        <tr>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                        </tr>
                        ';
                    }else{
                        $emptyRow .= '
                        <tr>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                        </tr>
                        ';
                    }
                }
                else{
                    if($Row['show_discount_percentage'] == 1){ 
                        $emptyRow .= '
                            <tr>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            </tr>
                            ';
                    } else {
                        $emptyRow .= '
                            <tr>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            </tr>
                            ';                            
                    }
                }
            }

            if($Row['gst_status'] == "ON") {
                //$sub_total_in_words = $fn->getConvertNumber($overallTotal .'.00');
                $sub_total_in_words = $fn->getIndianCurrency($overallTotal .'.00');
                if($Row['igst_show'] == "1"){
                    $tbl3 = $tbl3.'
                                '.$emptyRow.'
                                <tr>
                                    <td colspan="5" align="center" style="font-weight:bold; line-height:16px;border-top:1px solid #aeafb1;">Total Amount in Words</td>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;border-top:1px solid #aeafb1;">Total Amount Before Tax</td>
                                    <td align="right" style="border-top:1px solid #aeafb1;">'.$totaldiscount.'</td>
                                </tr>
                                <tr>
                                    <td rowspan="5" colspan="5" align="center" style="font-weight:bold;font-size:12px; line-height:16px;">'.strtoupper($sub_total_in_words).'</td>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;">Add: IGST 18.00%</td>
                                    <td align="right">'.number_format($totalVatSum, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;">Total Amount After Tax</td>
                                    <td align="right">'.number_format($overallTotal, 2).'</td>
                                </tr>
                            </tbody>
                        </table>';
                } else {
                    $tbl3 = $tbl3.'
                                '.$emptyRow.'
                                <tr>
                                    <td colspan="5" align="center" style="font-weight:bold; line-height:16px;border-top:1px solid #aeafb1;">Total Amount in Words</td>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;border-top:1px solid #aeafb1;">Total Amount Before Tax</td>
                                    <td align="right" style="border-top:1px solid #aeafb1;">'.$totaldiscount.'</td>
                                </tr>
                                <tr>
                                    <td rowspan="5" colspan="5" align="center" style="font-weight:bold;font-size:12px; line-height:16px;">'.strtoupper($sub_total_in_words).'</td>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;">Add: CGST 9.00%</td>
                                    <td align="right">'.number_format($vatSumTotal, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;">Add: SGST 9.00%</td>
                                    <td align="right">'.number_format($vatSumTotal, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;">Total Tax Amount</td>
                                    <td align="right">'.number_format($totalVatSum, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold;color:#ffffff; line-height:16px;" bgColor="#157ca7">Total Amount After Tax</td>
                                    <td align="right" bgColor="#157ca7" style="font-weight:bold;color:#ffffff;">'.number_format($overallTotal, 2).'</td>
                                </tr>
                            </tbody>
                        </table>';

                }
            } else {

                if($Row['show_discount_percentage'] == 1){ 
                    $tbl3 = $tbl3.'
                                    '.$emptyRow.'
                                    <tr>
                                        <td colspan="7" align="right" style="font-weight:bold; line-height:16px;border-top:1px solid #aeafb1;">TOTAL</td>
                                        <td align="right" style="border-top:1px solid #aeafb1;">'.$totaldiscount.'</td>
                                    </tr>
                                </tbody>
                            </table>';
                } else {
                    $tbl3 = $tbl3.'
                                    '.$emptyRow.'
                                    <tr>
                                        <td colspan="5" style="border-top:1px solid #aeafb1;"></td>
                                        <td align="right" style="font-weight:bold; color:#ffffff; line-height:16px;border-top:1px solid #aeafb1;" bgColor="#157ca7">TOTAL</td>
                                        <td align="right" style="font-weight:bold; color:#ffffff; line-height:16px;border-top:1px solid #aeafb1;" bgColor="#157ca7">'.$totaldiscount.'</td>
                                    </tr>
                                    <br/>
                                    <br/>
                                    <tr>
                                        <td colspan="7" align="right" style="">('.strtoupper($Total_in_words).')</td>
                                    </tr>
                                </tbody>
                            </table>';
                }
            }

            if($Row['gst_status'] == "ON"){

                $tbl4 = '<table cellpadding="4" border="0" width="100%" style="font-size:11px;">';

                if($Row['igst_show'] == "1"){
                    $tbl4 = $tbl4.'
                        <br/>
                        <br/>
                        <tr>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="right">Tax Rate</td>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="right">Taxable</td>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="right">IGST</td>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="right">Total Tax</td>
                        </tr>
                    ';
                }else{
                    $tbl4 = $tbl4.'
                        <br/>
                        <br/>
                        <tr>
                            <td rowspan="2" style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Tax Rate</td>
                            <td rowspan="2" style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Taxable</td>
                            <td colspan="2" style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">CGST</td>
                            <td colspan="2" style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">SGST</td>
                            <td rowspan="2" style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Total Tax</td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Rate</td>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Amount</td>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Rate</td>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Amount</td>
                        </tr>
                    ';
                }

                

                $SQLTax = "
                SELECT  p.gst
                        ,ci.credit_note_id
                        ,p.hsn AS hsn_sac
                        ,SUM(ci.unit_price * ci.qty) AS qty_amount
                FROM `credit_note_item` ci
                LEFT JOIN product p ON (p.product_id = ci.record_id)
                WHERE ci.credit_note_id = '{$Row['credit_note_id']}'
                AND p.gst > 0
                GROUP BY p.gst
                ORDER BY p.gst ASC
                ";
                $resultTax  = $db->sql_query($SQLTax);

                $totalVatSum = 0;
                $counter = 1;
                while($rowTax     = $db->sql_fetchrow($resultTax)){

                    $total_amount = $rowTax['qty_amount'];
                    
                    if($rowTax['gst'] == ''){
                        $vatPercent = '0.00';
                    }
                    else{
                        $vatPercent = $rowTax['gst'];
                    }

                    $vat_Sum  = ($total_amount * $rowTax['gst'])/100;

                    $gstRatePercent = $rowTax['gst'] / 2;

                    //$vat_Amount_total = $total_amount + $vat_Sum;
                    $vat_Amount_total = $total_amount;
                    if($vat_Sum == 0){
                        $vat_Amount_total = 0;
                    }

                    $vatPercentHalf = $vatPercent / 2;
                    $vat_Sum_Half   = $vat_Sum / 2;

                    $totalVatSum += $vat_Sum;

                    $vatPercentHalf = sprintf('%0.2f', $vatPercentHalf);
                    $total_tax = $vat_Sum_Half + $vat_Sum_Half;
                    if($Row['igst_show'] == "1"){
                        $tbl4 = $tbl4.'
                        <tr>
                            <td align="right">'.$rowTax['gst'].' %</td>
                            <td align="right">'.number_format($vat_Amount_total, 2).'</td>
                            <td align="right">'.number_format($vat_Sum, 2).'</td>
                            <td align="right">'.number_format($total_tax, 2).'</td>
                        </tr>
                        ';
                    }else{
                        $tbl4 = $tbl4.'
                        <tr>
                            <td align="right">'.$rowTax['gst'].' %</td>
                            <td align="right">'.number_format($vat_Amount_total, 2).'</td>
                            <td align="right">'.number_format($gstRatePercent, 0).'%</td>
                            <td align="right">'.number_format($vat_Sum_Half, 2).'</td>
                            <td align="right">'.number_format($gstRatePercent, 0).'%</td>
                            <td align="right">'.number_format($vat_Sum_Half, 2).'</td>
                            <td align="right">'.number_format($total_tax, 2).'</td>
                        </tr>
                        ';
                    }

                    $counter++;
                }   
            }

            if($Row['gst_status'] == "ON"){

                $total_vat_Amount_total += $vat_Amount_total;
                $total_vat_Sum_Half += $vat_Sum_Half;
                $total_total_tax += $total_tax;
                $total_vat_Sum += $vat_Sum;

                if($Row['igst_show'] == "1"){
                    $tbl4 = $tbl4.'
                    <tr>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">TOTAL</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_vat_Amount_total, 2).'</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_vat_Sum, 2).'</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_total_tax, 2).'</td>
                    </tr>
                    ';

                }else{
                    $tbl4 = $tbl4.'
                    <tr>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">TOTAL</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_vat_Amount_total, 2).'</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;"></td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_vat_Sum_Half, 2).'</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;"></td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_vat_Sum_Half, 2).'</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_total_tax, 2).'</td>
                    </tr>
                    ';
                }

                    $tbl4 = $tbl4.'</table>';

            }

            $tbl5 = '<table cellpadding="4" border="0" width="100%" nobr="true">';

            $tbl5 = $tbl5.'
                <tr>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                    <td width="48%" align="left" style="font-size:11px;font-weight:bold;color:#ffffff;" bgColor="#157ca7"><b>Bank Details : </b></td>
                    <td width="2%"></td>
                    <td width="20%" align="center" rowspan="4" style="font-weight:bold;font-size:11px;vertical-align:bottom;border:1px solid #e5e5e5;"><br/><br/><br/><br/><br/><br/><br/><br/>Common Seal</td>
                    <td width="30%" align="right" rowspan="4" style="font-size:12px;font-weight:bold;">For '.$cpCfg['cp.companyName'].'<br/><br/><br/><br/><br/><br/>Authorised signatory</td>
                </tr>
                <tr>
                    <td width="48%" align=""><span style="font-weight:bold;font-size:11px;color:#000000;"><b>'.$cpCfg['cp.bankDetails'].'</b></span></td>
                    <td width="2%"></td>
                </tr>
                <tr>
                <br/>
                    <td width="48%" align="" bgColor="#157ca7"><span style="font-weight:bold;font-size:11px; color:#ffffff;">Mode of Transport : </span></td>
                    <td width="2%"></td>
                </tr>
                <tr>
                    <td width="48%" align=""><font style="font-size:12px;">Vehicle No. : '.$notes.'</font></td>
                    <td width="2%"></td>
                </tr>
            </table>
            ';            
        }


        $pdf->ln(-5);
        $pdf->writeHTML($tbl1, true, false, false, false, '');
        $pdf->writeHTML($tblQuote, true, false, false, false, '');
        $pdf->writeHTML($tbl2, true, false, false, false, '');
        $pdf->writeHTML($tbl3, true, false, false, false, '');
        $pdf->writeHTML($tbl4, true, false, false, false, '');
        $pdf->writeHTML($tbl5, true, false, false, false, '');

        $pdf->Output();

    }


    /**
     *
     */
    function getPrintInvoiceRecord() {
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $tv = Zend_Registry::get('tv');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $searchVar = Zend_Registry::get('searchVar');
        $media = Zend_Registry::get('media');
        $cpPaths = Zend_Registry::get('cpPaths');
        $dbUtil = Zend_Registry::get('dbUtil');
        $site_id  = $fn->getSessionParam('cp_site_id');

        ini_set('memory_limit', '512M');
        set_time_limit(50000);

        include_once(CP_LIBRARY_PATH.'lib_php/tcpdf/tcpdf.php');
        include_once(CP_LOCAL_PATH.'lib/headfoot.php');

        $pdf = new MYPDF_Local(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('USS');
        $pdf->SetSubject('Proforma Invoice');
        $pdf->SetTitle('Proforma Invoice');

        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 04', PDF_HEADER_STRING);
        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER,10);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        /*HEADER PART AND FOOTER PART FUNCTIONS HAS BEEN ADDED IN (headfoot.php) PATH INCLUDE: (admin/lib/headfoot.php)*/
        $pdf->AddPage();

        $invoiceHeading = '';

        $invoice_id = $fn->getReqParam('invoice_id');
        $invoice_code = $fn->getReqParam('invoice_code');
        $invoice_type = $fn->getReqParam('invoice_type');
        if($invoice_type == 'normal'){
            $invoiceHeading = 'ORIGINAL - ';
        }
        else if($invoice_type == 'transporter'){
            $invoiceHeading = 'TRANSPORTER - ';
        }
        else if($invoice_type == 'proforma'){
            $invoiceHeading = 'PROFORMA - ';
        }
        else if($invoice_type == 'extra'){
            $invoiceHeading = 'DUPLICATE - ';
        }

        $SQL = "
        SELECT ini.*
              ,ini.item_title AS product_title
              ,p.title AS product_title1
              ,p.unit
              ,p.item_code
              ,p.part_number
              ,p.description_short
              ,p.hsn
              ,c.company_name
              ,c.address_flat
              ,c.address_street
              ,c.address_town
              ,c.address_state
              ,c.address_po_code
              , (SELECT gc.name FROM geo_country gc
                 WHERE gc.country_code = c.address_country)
                AS address_country
              ,c.billing_address_flat
              ,c.billing_address_street
              ,c.billing_address_town
              ,c.billing_address_state
              , (SELECT gc.name FROM geo_country gc
                 WHERE gc.country_code = c.billing_address_country)
                AS billing_address_country
              ,c.fax
              ,c.phone
              ,c.tin_no
              ,c.cst_no
              ,c.gst_no
              ,i.invoice_date
              ,q.delivery_date
              ,q.delivery_location
              ,q.gst_enabled
              ,q.show_discount_percentage
              ,q.currency
              ,q.payment_terms
              ,q.delivery_terms
              ,ini.unit_price
              ,i.invoice_code
              ,i.invoice_terms
              ,i.invoice_due_date
              ,i.notes
              ,i.cst
              ,i.vat
              ,i.cst_value
              ,i.vat_value
              ,i.frieght_cost
              ,i.cust_po_no
              ,i.p_f
              ,o.order_id
              ,o.notes
              ,o.shipping_address1
              ,o.shipping_first_name
              ,o.shipping_address2
              ,o.shipping_address_city
              ,o.shipping_address_state
              ,o.gst_status
              ,o.igst_show
               ,(SELECT gc.name FROM geo_country gc
                 WHERE gc.country_code = o.shipping_address_country)
                 AS shipping_address_country
              ,q.quote_code
              ,q.currency
              ,ini.qty * ini.unit_price AS amount
              ,(SELECT SUM(init.qty * init.cost_price) FROM invoice_item init
               WHERE init.invoice_id = ini.invoice_id) AS sub_total
              ,(SELECT SUM(init.qty * init.unit_price) FROM invoice_item init
               WHERE init.invoice_id = ini.invoice_id) AS total
              ,CONCAT_WS(' ', co.first_name, co.last_name) AS contact_name
              ,co.salutation
        FROM invoice_item ini
        LEFT JOIN product p ON (p.product_id = ini.record_id)
        LEFT JOIN invoice i ON (i.invoice_id = ini.invoice_id)
        LEFT JOIN `order` o ON (o.order_id = i.order_id)
        LEFT JOIN company c ON (c.company_id = o.company_id)
        LEFT JOIN quote q ON (q.quote_id = o.quote_id)
        LEFT JOIN contact co ON (co.contact_id = q.contact_id)
        LEFT JOIN product_group pg ON (p.product_group_id = pg.product_group_id)
        WHERE i.invoice_id = '{$invoice_id}'
          AND i.status != 'Cancelled'
        ORDER BY ini.invoice_item_id, pg.sort_order ASC, p.title
        ";
        $result = $db->sql_query($SQL);
        $result2 = $db->sql_query($SQL);
        $Row = $db->sql_fetchrow($result2);

        $numRows  = $db->sql_numrows($result);
        //============================================================================= //

        $pdf->SetFont('helvetica','', 8);
        $today = date("d-m-Y");

        if($site_id == 1) {
            $tbl1 = '
            <table border="0" width="100%" style="font-size:17px;">
                <tr>
                    <td align="center" style="font-weight:bold; text-decoration: underline;">'.$invoiceHeading.'INVOICE</td>
                </tr>
            </table>
            ';

            $tblQuote ='
            <table border="0" width="100%" cellpadding="3">
            </table>
            ';

            $contact_name = '';
            if($Row['contact_name'] != ''){
                $contact_name = "Kind Attn: {$Row['salutation']}.{$Row['contact_name']}";
            }

            $addressFlat     = $Row['address_flat'];
            $addressStreet   = $Row['address_street'];
            $addressTown     = $Row['address_town'];
            $addressState    = $Row['address_state'];
            $addressCountry  = $Row['address_country'];

            $billingAddressFlat     = $Row['billing_address_flat'];
            $billingAddressStreet   = $Row['billing_address_street'];
            $billingAddressTown     = $Row['billing_address_town'];
            $billingAddressState    = $Row['billing_address_state'];
            $billingAddressCountry  = $Row['billing_address_country'];
            $invoiceDate   = $fn->getCPDate($Row['invoice_date'], 'd-m-Y');

            $tbl2 ='
            <table border="0" width="100%" cellpadding="3">
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>Invoice Code : </i>'.$Row['invoice_code'].'</td>
                    <td width="50%" style="border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>Invoice Date : </i>'.$invoiceDate.'</td>
                </tr>
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>From :</i></td>
                    <td width="50%" style="border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>Billed to :</i></td>
                </tr>
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;font-size:11px;font-weight:bold;">'.$cpCfg['cp.companyName'].'</td>
                    <td width="50%" style="border-right:1px solid #000000;font-size:11px;font-weight:bold;">'.$Row['company_name'].'</td>
                </tr>
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;font-size:11px;font-weight:bold;">'.$cpCfg['cp.addressPdf5'].'</td>
                    <td width="50%" style="border-right:1px solid #000000;font-size:11px;font-weight:bold;">'.$Row['address_flat'].'</td>
                </tr>
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;font-weight:bold;font-size:11px;">'.$cpCfg['cp.panNoPdf'].'</td>
                    <td width="50%" style="border-right:1px solid #000000;font-size:11px;font-weight:bold;">'.$Row['address_street'].' '.$Row['address_town'].' '.$Row['address_state'].'</td>
                </tr>
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;font-weight:bold;font-size:11px;"></td>
                    <td width="50%" style="border-right:1px solid #000000;font-size:11px;font-weight:bold;">GST IN / UIN :'.$Row['gst_no'].'</td>
                </tr>
                <tr>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:11px;"><span style="font-weight:bold;">State Code: 33</span></td>
                    <td width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:11px;"><span style="font-weight:bold;">State Code: </span>'.$Row['address_po_code'].'</td>
                </tr>
            </table>
            ';


            if($Row['gst_status'] == "ON"){
                $tbl3 ='
                <table border="1" nobr="true" width="100%" cellpadding="3" style="font-size:10px;">
                    <thead>
                        <tr>
                            <th width="5%"  style="border:1px solid #000000;font-weight:bold;" align="center">S.No</th>
                            <th width="35%" style="border:1px solid #000000;font-weight:bold;" align="left">Product Description</th>
                            <th width="10%"  style="border:1px solid #000000;font-weight:bold;" align="center">HSN Code</th>
                            <th width="6%"  style="border:1px solid #000000;font-weight:bold;" align="center">UOM</th>
                            <th width="7%"  style="border:1px solid #000000;font-weight:bold;" align="center">Qty</th>
                            <th width="12%"  style="border:1px solid #000000;font-weight:bold;" align="right">Price</th>
                            <th width="12%"  style="border:1px solid #000000;font-weight:bold;" align="right">Taxbl Val</th>
                            <th width="13%" style="border:1px solid #000000;font-weight:bold;" align="right">Total</th>
                        </tr>
                    </thead>
                ';
            }

            else {
                if($Row['show_discount_percentage'] == 1){
                    $tbl3 = '
                    <table border="1" width="100%" cellpadding="3" style="font-size:11px;">
                        <thead>
                            <tr>
                                <td width="6%"  style="font-weight:bold;" align="center">S.No</td>
                                <td width="26%" style="font-weight:bold;" align="left">Product Description</td>
                                <td width="15%" style="font-weight:bold;" align="center">HSN Code</td>
                                <td width="10%" style="font-weight:bold;" align="center">UOM</td>
                                <td width="8%"  style="font-weight:bold;" align="center">Qty</td>
                                <td width="10%" style="font-weight:bold;" align="right">Price</td>
                                <td width="10%" style="font-weight:bold;" align="right">Discount</td>
                                <td width="15%" style="font-weight:bold;" align="right">Total ('.$Row['currency'].')</td>
                            </tr>
                        </thead>
                        <tbody>
                    ';
                } else {
                    $tbl3 = '
                    <table border="1" width="100%" cellpadding="3" style="font-size:11px;">
                        <thead>
                            <tr>
                                <td width="6%"  style="font-weight:bold;" align="center">S.No</td>
                                <td width="29%" style="font-weight:bold;" align="left">Product Description</td>
                                <td width="15%" style="font-weight:bold;" align="center">HSN Code</td>
                                <td width="10%" style="font-weight:bold;" align="center">UOM</td>
                                <td width="10%" style="font-weight:bold;" align="center">Qty</td>
                                <td width="15%" style="font-weight:bold;" align="right">Price</td>
                                <td width="15%" style="font-weight:bold;" align="right">Total ('.$Row['currency'].')</td>
                            </tr>
                        </thead>
                        <tbody>
                    ';
                }
            }

            $count = 1;
            $overallTotal = 0;
            $vatSumTotal  = 0;
            while($row = $db->sql_fetchrow($result)){

                $discount_value_for_one_qty = 0;
                $discount_value_for_display = 0;
                if($row['discount_percentage'] > 0){
                    if($row['discount_type'] == '%'){
                        $discount_value_for_one_qty  =  $row['cost_price'] * ($row['discount_percentage']/100);
                        $discount_value_for_display  =  $row['discount_percentage'] . '%';
                    }
                    else if($row['discount_type']  == 'Value'){
                        $discount_value_for_one_qty  =  $row['discount_percentage'];
                        $discount_value_for_display  =  $row['discount_percentage'];
                    }
                }

                if($row['show_discount_percentage'] != 1){
                    $selling_price = $row['unit_price'];
                    $tsp = ($row['qty'] * $selling_price);

                    $SQLTax = "
                    SELECT  p.gst
                            ,SUM((oi.unit_price * oi.qty) - ((oi.unit_price * oi.discount_percentage) /100 * oi.qty)) AS qty_amount
                    FROM `order_item` oi
                    LEFT JOIN `product` p ON (p.product_id = oi.record_id)
                    WHERE oi.order_item_id = '{$row['order_item_id']}'
                    AND p.gst > 0
                    ";
                    $resultTax  = $db->sql_query($SQLTax);
                    $rowTax     = $db->sql_fetchrow($resultTax);

                } else {
                    $selling_price = $row['unit_price'];
                    $tsp = ($row['qty'] * $selling_price) - ($row['qty'] * $discount_value_for_one_qty);

                    $SQLTax = "
                    SELECT  p.gst
                            ,SUM((oi.unit_price * oi.qty) - ((oi.unit_price * oi.discount_percentage) /100 * oi.qty)) AS qty_amount
                    FROM `order_item` oi
                    LEFT JOIN `product` p ON (p.product_id = oi.record_id)
                    WHERE oi.order_item_id = '{$row['order_item_id']}'
                    AND p.gst > 0
                    ";
                    $resultTax  = $db->sql_query($SQLTax);
                    $rowTax     = $db->sql_fetchrow($resultTax);
                }

                $titledesc = $row['item_title'];

                $titledescrip = $titledesc;
                $discount_value_for_one_qty = number_format($discount_value_for_one_qty, 2);

                $totalVatSum = 0;

                $total_amount = $rowTax['qty_amount'];
                
                if($rowTax['gst'] == ''){
                    $vatPercent = '0.00';
                }
                else{
                    $vatPercent = $rowTax['gst'];
                }

                $vat_Sum  = ($total_amount * $rowTax['gst'])/100;

                $vat_Amount_total = $total_amount + $vat_Sum;
                if($vat_Sum == 0){
                    $vat_Amount_total = 0;
                }

                $vatPercentHalf = $vatPercent / 2;
                $vat_Sum_Half   = $vat_Sum / 2;

                $totalVatSum += $vat_Sum;

                $vatPercentHalf = sprintf('%0.2f', $vatPercentHalf);
                
                if($row['gst_status'] == "ON"){
                    $total = $tsp + $vat_Sum_Half + $vat_Sum_Half;
                } else {
                    $total = $tsp;
                }

                $tsp   = $tsp - $row['discount_amount'];
                $total = $total - $row['discount_amount'];
                $selling_price = number_format($selling_price,2);

                if($row['gst_status'] == "ON"){
                    $tbl3 = $tbl3.'<tr>
                                        <td width="5%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="center">'.$count.'</td>
                                        <td width="35%" style="border-left:1px solid #000000;border-right:1px solid #000000;" align="left">'.$titledescrip.'</td>
                                        <td width="10%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="center">'.$row['hsn'].'</td>
                                        <td width="6%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="center">'.$row['unit'].'</td>
                                        <td width="7%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="center">'.$row['qty'].'</td>
                                        <td width="12%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="right">'.$selling_price.'</td>
                                        <td width="12%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="right">'.$tsp.'</td>
                                        <td width="13%" style="border-left:1px solid #000000;border-right:1px solid #000000;"  align="right">'.number_format($total, 2).'</td>
                                    </tr>
                                    ';
                    $count++;
                } else {

                    if($row['show_discount_percentage'] == 1){    
                        $tbl3 = $tbl3.'<tr>
                                            <td width="6%"  align="center">'.$count.'</td>
                                            <td width="26%" align="left">'.$titledescrip.'</td>
                                            <td width="15%" align="center">'.$row['hsn'].'</td>
                                            <td width="10%" align="center">'.$row['unit'].'</td>
                                            <td width="8%"  align="center">'.$row['qty'].'</td>
                                            <td width="10%" align="right">'.$selling_price.'</td>
                                            <td width="10%" align="right">'.$discount_value_for_display.'</td>
                                            <td width="15%" align="right">'.number_format($tsp, 2).'</td>
                                        </tr>
                                       ';

                        $count++;
                    }else {
                        $tbl3 = $tbl3.'<tr>
                                            <td width="6%"  align="center">'.$count.'</td>
                                            <td width="29%" align="left">'.$titledescrip.'</td>
                                            <td width="15%" align="center">'.$row['hsn'].'</td>
                                            <td width="10%" align="center">'.$row['unit'].'</td>
                                            <td width="10%" align="center">'.$row['qty'].'</td>
                                            <td width="15%" align="right">'.$selling_price.'</td>
                                            <td width="15%" align="right">'.number_format($tsp, 2).'</td>
                                        </tr>
                                       ';

                        $count++;
                    }
                }

                $overallTotal += $total;
                $vatSumTotal  += $vat_Sum_Half;

                $total = $row['total'];
                $terms = $row['payment_terms'];
                $notes = $row['notes'];
                $delivery_terms = $row['delivery_terms'];
                $discount = 0;
                $sub_total = $total + $discount - $row['discount_amount'];
                $show_discount_percentage = $row['show_discount_percentage'];
            }
            $tbl4 = '';

            $totaldiscount = $sub_total - $discount;
            $discountPercent = $discount * 100 / $sub_total;
            $totaldiscount = number_format(round($totaldiscount), 2);
            $sub_total = number_format($sub_total,2);
            $discount = number_format($discount,2);
            $discountPercent = number_format($discountPercent,2);
            $displayDiscountPercent = '';

            if($Row['gst_status'] == "ON") {
                //$sub_total_in_words = $fn->getConvertNumber($overallTotal .'.00');
                $sub_total_in_words = $fn->getIndianCurrency($overallTotal .'.00');
                if($Row['igst_show'] == "1"){
                    $tbl3 = $tbl3.'
                                <tr>
                                    <td colspan="5" align="center" style="font-weight:bold;">Total Amount in Words</td>
                                    <td colspan="2" align="right" style="font-weight:bold;">Total Amount Before Tax</td>
                                    <td align="right">'.$totaldiscount.'</td>
                                </tr>
                                <tr>
                                    <td rowspan="5" colspan="5" align="center" style="font-weight:bold;font-size:12px;">'.strtoupper($sub_total_in_words).'</td>
                                    <td colspan="2" align="right" style="font-weight:bold;">Add: IGST 18.00%</td>
                                    <td align="right">'.number_format($totalVatSum, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold;">Total Amount After Tax</td>
                                    <td align="right">'.number_format($overallTotal, 2).'</td>
                                </tr>
                            </tbody>
                        </table>';
                } else {
                    $tbl3 = $tbl3.'
                                <tr>
                                    <td colspan="5" align="center" style="font-weight:bold;">Total Amount in Words</td>
                                    <td colspan="2" align="right" style="font-weight:bold;">Total Amount Before Tax</td>
                                    <td align="right">'.$totaldiscount.'</td>
                                </tr>
                                <tr>
                                    <td rowspan="5" colspan="5" align="center" style="font-weight:bold;font-size:12px;">'.strtoupper($sub_total_in_words).'</td>
                                    <td colspan="2" align="right" style="font-weight:bold;">Add: CGST 9.00%</td>
                                    <td align="right">'.number_format($vatSumTotal, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold;">Add: SGST 9.00%</td>
                                    <td align="right">'.number_format($vatSumTotal, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold;">Total Tax Amount</td>
                                    <td align="right">'.number_format($totalVatSum, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold;">Total Amount After Tax</td>
                                    <td align="right">'.number_format($overallTotal, 2).'</td>
                                </tr>
                            </tbody>
                        </table>';

                }
            } else {

                if($Row['show_discount_percentage'] == 1){ 
                    $tbl3 = $tbl3.'
                                    <tr>
                                        <td colspan="7" align="right" style="font-weight:bold;">TOTAL</td>
                                        <td align="right">'.$totaldiscount.'</td>
                                    </tr>
                                </tbody>
                            </table>';
                } else {
                    $tbl3 = $tbl3.'
                                    <tr>
                                        <td colspan="6" align="right" style="font-weight:bold;">TOTAL</td>
                                        <td align="right">'.$totaldiscount.'</td>
                                    </tr>
                                </tbody>
                            </table>';
                }
            }

            $tbl5 = '<table cellpadding="4" border="1" width="100%" nobr="true">';

            $tbl5 = $tbl5.'
                <tr>
                    <td width="50%" align="left" style="font-size:10px;font-weight:bold;"><b>Bank Details : </b><br/><b>'.$cpCfg['cp.bankDetails'].'</b></td>
                    <td width="20%" align="center"></td>
                    <td width="30%" align="right" rowspan="2" style="font-size:12px;font-weight:bold;">For '.$cpCfg['cp.companyName'].'<br/><br/><br/><br/><br/><br/>Authorised signatory</td>
                </tr>
                <tr>
                    <td width="50%" align=""><span style="font-weight:bold;font-size:11px;">Mode of Transport : </span><br/><br/>Vehicle No. : '.$notes.'</td>
                    <td width="20%" align="center" style="font-weight:bold;font-size:11px;vertical-align:bottom;"><br/><br/><br/><br/>Common Seal</td>
                </tr>
            </table>
            ';
        } else {
            $pdf->SetFont('calibri','', 8);
            $invoiceDate   = $fn->getCPDate($Row['invoice_date'], 'd-m-Y');

            $tblQuote ='
            <table border="0" width="100%" cellpadding="3">
            </table>
            ';

            $contact_name = '';
            if($Row['contact_name'] != ''){
                $contact_name = "Kind Attn: {$Row['salutation']}.{$Row['contact_name']}";
            }

            $addressFlat     = $Row['address_flat'];
            $addressStreet   = $Row['address_street'];
            $addressTown     = $Row['address_town'];
            $addressState    = $Row['address_state'];
            $addressCountry  = $Row['address_country'];

            $billingAddressFlat     = $Row['billing_address_flat'];
            $billingAddressStreet   = $Row['billing_address_street'];
            $billingAddressTown     = $Row['billing_address_town'];
            $billingAddressState    = $Row['billing_address_state'];
            $billingAddressCountry  = $Row['billing_address_country'];

            $tbl1 = '
            <table border="0" width="100%" cellpadding="3">
                <tr>
                    <td width="27%" style="font-size:13px;font-weight:bold;color:#157ca7;"><strong>From :</strong><br/><font style="font-size:14px;font-weight:bold;color:#000000;">'.$cpCfg['cp.companyName'].'<br/></font><font style="font-size:11px;color:#000000;">'.$cpCfg['cp.addressPdf5'].'<br/>'.$cpCfg['cp.panNoPdf'].'<br/>State Code: 33</font></td>
                    <td width="43%" style="font-size:13px;font-weight:bold;color:#157ca7;"><strong>Ship to :</strong><br/><font style="font-size:14px;font-weight:bold;color:#000000;">'.$Row['company_name'].'<br/></font><font style="font-size:11px;color:#000000;">'.$Row['address_flat'].', '.$Row['address_street'].' '.$Row['address_town'].' '.$Row['address_state'].'</font></td>
                    <td width="30%" style="font-weight:bold;font-size:22px;">'.$invoiceHeading.'INVOICE <font style="font-size:12px;font-weight:bold;"><br/><br/><i>Invoice Code : </i>'.$Row['invoice_code'].'<br/><i>Invoice Date : </i>'.$invoiceDate.'</font></td>
                </tr>
                <tr>
                    <td width="27%" style="font-size:13px;font-weight:bold;color:#157ca7;"></td>
                    <td width="43%" style="font-size:13px;font-weight:bold;color:#157ca7;"><strong>Billed to :</strong><br/><font style="font-size:14px;font-weight:bold;color:#000000;">'.$Row['company_name'].'<br/></font><font style="font-size:11px;color:#000000;">'.$Row['billing_address_flat'].', '.$Row['billing_address_street'].' '.$Row['billing_address_town'].' '.$Row['billing_address_state'].'<br/>GST IN / UIN :'.$Row['gst_no'].'<br/>State Code: '.$Row['address_po_code'].'</font></td>
                    <td width="30%" style="font-weight:bold;font-size:22px;"></td>
                </tr>
            </table>
            ';

            $tbl2 ='';

            if($Row['gst_status'] == "ON"){
                $tbl3 ='
                <table border="0" nobr="true" width="100%" cellpadding="4" style="font-size:11px;">
                    <thead>
                        <tr>
                            <th width="5%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">S.No</th>
                            <th width="35%" style="color:#fff;font-weight:bold; line-height:16px;" align="left" bgColor="#157ca7">Product Description</th>
                            <th width="10%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">HSN Code</th>
                            <th width="6%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">UOM</th>
                            <th width="7%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">Qty</th>
                            <th width="12%"  style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Price</th>
                            <th width="12%"  style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Taxbl Val</th>
                            <th width="13%" style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Total</th>
                        </tr>
                    </thead>
                ';
            }

            else {
                if($Row['show_discount_percentage'] == 1){
                    $tbl3 = '
                    <table border="0" width="100%" cellpadding="4" style="font-size:11px;">
                        <thead>
                            <tr>
                                <td width="6%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">S.No</td>
                                <td width="26%" style="color:#fff;font-weight:bold; line-height:16px;" align="left" bgColor="#157ca7">Product Description</td>
                                <td width="15%" style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">HSN Code</td>
                                <td width="10%" style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">UOM</td>
                                <td width="8%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">Qty</td>
                                <td width="10%" style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Price</td>
                                <td width="10%" style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Discount</td>
                                <td width="15%" style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Total ('.$Row['currency'].')</td>
                            </tr>
                        </thead>
                        <tbody>
                    ';
                } else {
                    $tbl3 = '
                    <table border="0" width="100%" cellpadding="4" style="font-size:11px;">
                        <thead>
                            <tr>
                                <td width="6%"  style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">S.No</td>
                                <td width="29%" style="color:#fff;font-weight:bold; line-height:16px;" align="left" bgColor="#157ca7">Product Description</td>
                                <td width="15%" style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">HSN Code</td>
                                <td width="10%" style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">UOM</td>
                                <td width="10%" style="color:#fff;font-weight:bold; line-height:16px;" align="center" bgColor="#157ca7">Qty</td>
                                <td width="15%" style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Price</td>
                                <td width="15%" style="color:#fff;font-weight:bold; line-height:16px;" align="right" bgColor="#157ca7">Total ('.$Row['currency'].')</td>
                            </tr>
                        </thead>
                        <tbody>
                    ';
                }
            }

            $tbl4 = '';

            $count = 1;
            $overallTotal = 0;
            $vatSumTotal  = 0;
            $total_vat_Amount_total = 0;
            $total_vat_Sum_Half = 0;
            $total_vat_Sum = 0;
            $total_total_tax = 0;
            $total_tax = 0;
            while($row = $db->sql_fetchrow($result)){

                $discount_value_for_one_qty = 0;
                $discount_value_for_display = 0;
                if($row['discount_percentage'] > 0){
                    if($row['discount_type'] == '%'){
                        $discount_value_for_one_qty  =  $row['cost_price'] * ($row['discount_percentage']/100);
                        $discount_value_for_display  =  $row['discount_percentage'] . '%';
                    }
                    else if($row['discount_type']  == 'Value'){
                        $discount_value_for_one_qty  =  $row['discount_percentage'];
                        $discount_value_for_display  =  $row['discount_percentage'];
                    }
                }

                if($row['show_discount_percentage'] != 1){
                    $selling_price = $row['unit_price'];
                    $tsp = ($row['qty'] * $selling_price);

                    $SQLTax = "
                    SELECT  p.gst
                            ,SUM((oi.unit_price * oi.qty) - ((oi.unit_price * oi.discount_percentage) /100 * oi.qty)) AS qty_amount
                    FROM `order_item` oi
                    LEFT JOIN `product` p ON (p.product_id = oi.record_id)
                    WHERE oi.order_item_id = '{$row['order_item_id']}'
                    AND p.gst > 0
                    ";
                    $resultTax  = $db->sql_query($SQLTax);
                    $rowTax     = $db->sql_fetchrow($resultTax);

                } else {
                    $selling_price = $row['unit_price'];
                    $tsp = ($row['qty'] * $selling_price) - ($row['qty'] * $discount_value_for_one_qty);

                    $SQLTax = "
                    SELECT  p.gst
                            ,SUM((oi.unit_price * oi.qty) - ((oi.unit_price * oi.discount_percentage) /100 * oi.qty)) AS qty_amount
                    FROM `order_item` oi
                    LEFT JOIN `product` p ON (p.product_id = oi.record_id)
                    WHERE oi.order_item_id = '{$row['order_item_id']}'
                    AND p.gst > 0
                    ";
                    $resultTax  = $db->sql_query($SQLTax);
                    $rowTax     = $db->sql_fetchrow($resultTax);
                }

                $titledesc = $row['item_title'];

                $titledescrip = $titledesc;
                $discount_value_for_one_qty = number_format($discount_value_for_one_qty, 2);

                $totalVatSum = 0;

                $total_amount = $rowTax['qty_amount'];
                
                if($rowTax['gst'] == ''){
                    $vatPercent = '0.00';
                }
                else{
                    $vatPercent = $rowTax['gst'];
                }

                $vat_Sum  = ($total_amount * $rowTax['gst'])/100;

                $vat_Amount_total = $total_amount + $vat_Sum;
                if($vat_Sum == 0){
                    $vat_Amount_total = 0;
                }

                $vatPercentHalf = $vatPercent / 2;
                $vat_Sum_Half   = $vat_Sum / 2;

                $totalVatSum += $vat_Sum;

                $vatPercentHalf = sprintf('%0.2f', $vatPercentHalf);
                
                if($row['gst_status'] == "ON"){
                    $total = $tsp + $vat_Sum_Half + $vat_Sum_Half;
                } else {
                    $total = $tsp;
                }

                $tsp   = $tsp - $row['discount_amount'];
                $total = $total - $row['discount_amount'];
                $selling_price = number_format($selling_price,2);

                if($row['gst_status'] == "ON"){
                    $tbl3 = $tbl3.'<tr>
                                        <td width="5%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$count.'</td>
                                        <td width="35%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="left">'.$titledescrip.'</td>
                                        <td width="10%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['hsn'].'</td>
                                        <td width="6%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['unit'].'</td>
                                        <td width="7%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['qty'].'</td>
                                        <td width="12%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="right">'.$selling_price.'</td>
                                        <td width="12%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="right">'.$tsp.'</td>
                                        <td width="13%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;"  align="right">'.number_format($total, 2).'</td>
                                    </tr>
                                    ';
                    $count++;
                } else {

                    if($row['show_discount_percentage'] == 1){    
                        $tbl3 = $tbl3.'<tr>
                                            <td width="6%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$count.'</td>
                                            <td width="26%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="left">'.$titledescrip.'</td>
                                            <td width="15%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['hsn'].'</td>
                                            <td width="10%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['unit'].'</td>
                                            <td width="8%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['qty'].'</td>
                                            <td width="10%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="right">'.$selling_price.'</td>
                                            <td width="10%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="right">'.$discount_value_for_display.'</td>
                                            <td width="15%" align="right" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;">'.number_format($tsp, 2).'</td>
                                        </tr>
                                       ';

                        $count++;
                    }else {
                        $tbl3 = $tbl3.'<tr>
                                            <td width="6%"  style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$count.'</td>
                                            <td width="29%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="left">'.$titledescrip.'</td>
                                            <td width="15%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['hsn'].'</td>
                                            <td width="10%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['unit'].'</td>
                                            <td width="10%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="center">'.$row['qty'].'</td>
                                            <td width="15%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="right">'.$selling_price.'</td>
                                            <td width="15%" style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;line-height:16px;" align="right">'.number_format($tsp, 2).'</td>
                                        </tr>
                                       ';

                        $count++;
                    }
                }

                $overallTotal += $total;
                $vatSumTotal  += $vat_Sum_Half;

                $total = $row['total'];
                $terms = $row['payment_terms'];
                $notes = $row['notes'];
                $delivery_terms = $row['delivery_terms'];
                $discount = 0;
                $sub_total = $total + $discount - $row['discount_amount'];
                $show_discount_percentage = $row['show_discount_percentage'];
            }

            $totaldiscount = $sub_total - $discount;
            $discountPercent = $discount * 100 / $sub_total;
            $Total_in_words = $fn->getIndianCurrency($totaldiscount .'.00');
            $totaldiscount = number_format(round($totaldiscount), 2);
            $sub_total = number_format($sub_total,2);
            $discount = number_format($discount,2);
            $discountPercent = number_format($discountPercent,2);
            $displayDiscountPercent = '';
            $emptyRow = '';

            for($ic = 1; $ic <= 6; $ic++){
                if($Row['gst_status'] == "ON"){
                    if($Row['igst_show'] == "1"){
                        $emptyRow .= '
                        <tr>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                        </tr>
                        ';
                    }else{
                        $emptyRow .= '
                        <tr>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                        </tr>
                        ';
                    }
                }
                else{
                    if($Row['show_discount_percentage'] == 1){ 
                        $emptyRow .= '
                            <tr>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            </tr>
                            ';
                    } else {
                        $emptyRow .= '
                            <tr>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                                <td style="border-left:1px solid #aeafb1;border-right:1px solid #aeafb1;"></td>
                            </tr>
                            ';                            
                    }
                }
            }

            if($Row['gst_status'] == "ON") {
                //$sub_total_in_words = $fn->getConvertNumber($overallTotal .'.00');
                $sub_total_in_words = $fn->getIndianCurrency($overallTotal .'.00');
                if($Row['igst_show'] == "1"){
                    $tbl3 = $tbl3.'
                                '.$emptyRow.'
                                <tr>
                                    <td colspan="5" align="center" style="font-weight:bold; line-height:16px;border-top:1px solid #aeafb1;">Total Amount in Words</td>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;border-top:1px solid #aeafb1;">Total Amount Before Tax</td>
                                    <td align="right" style="border-top:1px solid #aeafb1;">'.$totaldiscount.'</td>
                                </tr>
                                <tr>
                                    <td rowspan="5" colspan="5" align="center" style="font-weight:bold;font-size:12px; line-height:16px;">'.strtoupper($sub_total_in_words).'</td>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;">Add: IGST 18.00%</td>
                                    <td align="right">'.number_format($totalVatSum, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;">Total Amount After Tax</td>
                                    <td align="right">'.number_format($overallTotal, 2).'</td>
                                </tr>
                            </tbody>
                        </table>';
                } else {
                    $tbl3 = $tbl3.'
                                '.$emptyRow.'
                                <tr>
                                    <td colspan="5" align="center" style="font-weight:bold; line-height:16px;border-top:1px solid #aeafb1;">Total Amount in Words</td>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;border-top:1px solid #aeafb1;">Total Amount Before Tax</td>
                                    <td align="right" style="border-top:1px solid #aeafb1;">'.$totaldiscount.'</td>
                                </tr>
                                <tr>
                                    <td rowspan="5" colspan="5" align="center" style="font-weight:bold;font-size:12px; line-height:16px;">'.strtoupper($sub_total_in_words).'</td>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;">Add: CGST 9.00%</td>
                                    <td align="right">'.number_format($vatSumTotal, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;">Add: SGST 9.00%</td>
                                    <td align="right">'.number_format($vatSumTotal, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold; line-height:16px;">Total Tax Amount</td>
                                    <td align="right">'.number_format($totalVatSum, 2).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right" style="font-weight:bold;color:#ffffff; line-height:16px;" bgColor="#157ca7">Total Amount After Tax</td>
                                    <td align="right" bgColor="#157ca7" style="font-weight:bold;color:#ffffff;">'.number_format($overallTotal, 2).'</td>
                                </tr>
                            </tbody>
                        </table>';

                }
            } else {

                if($Row['show_discount_percentage'] == 1){ 
                    $tbl3 = $tbl3.'
                                    '.$emptyRow.'
                                    <tr>
                                        <td colspan="7" align="right" style="font-weight:bold; line-height:16px;border-top:1px solid #aeafb1;">TOTAL</td>
                                        <td align="right" style="border-top:1px solid #aeafb1;">'.$totaldiscount.'</td>
                                    </tr>
                                </tbody>
                            </table>';
                } else {
                    $tbl3 = $tbl3.'
                                    '.$emptyRow.'
                                    <tr>
                                        <td colspan="5" style="border-top:1px solid #aeafb1;"></td>
                                        <td align="right" style="font-weight:bold; color:#ffffff; line-height:16px;border-top:1px solid #aeafb1;" bgColor="#157ca7">TOTAL</td>
                                        <td align="right" style="font-weight:bold; color:#ffffff; line-height:16px;border-top:1px solid #aeafb1;" bgColor="#157ca7">'.$totaldiscount.'</td>
                                    </tr>
                                    <br/>
                                    <br/>
                                    <tr>
                                        <td colspan="7" align="right" style="">('.strtoupper($Total_in_words).')</td>
                                    </tr>
                                </tbody>
                            </table>';
                }
            }

            if($Row['gst_status'] == "ON"){

                $tbl4 = '<table cellpadding="4" border="0" width="100%" style="font-size:11px;">';

                if($Row['igst_show'] == "1"){
                    $tbl4 = $tbl4.'
                        <br/>
                        <br/>
                        <tr>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="right">Tax Rate</td>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="right">Taxable</td>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="right">IGST</td>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="right">Total Tax</td>
                        </tr>
                    ';
                }else{
                    $tbl4 = $tbl4.'
                        <br/>
                        <br/>
                        <tr>
                            <td rowspan="2" style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Tax Rate</td>
                            <td rowspan="2" style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Taxable</td>
                            <td colspan="2" style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">CGST</td>
                            <td colspan="2" style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">SGST</td>
                            <td rowspan="2" style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Total Tax</td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Rate</td>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Amount</td>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Rate</td>
                            <td style="font-weight:bold;color:#fff;" bgColor="#157ca7" align="center">Amount</td>
                        </tr>
                    ';
                }

                $SQLTax = "
                SELECT  p.gst
                        ,oi.order_id
                        ,p.hsn AS hsn_sac
                        ,SUM((oi.unit_price * oi.qty) - (((oi.unit_price * oi.discount_percentage) /100 ) * oi.qty)) AS qty_amount
                FROM `order_item` oi
                LEFT JOIN product p ON (p.product_id = oi.record_id)
                WHERE oi.order_id = '{$Row['order_id']}'
                AND p.gst > 0
                GROUP BY p.gst
                ORDER BY p.gst ASC
                ";
                $resultTax  = $db->sql_query($SQLTax);

                $totalVatSum = 0;
                $counter = 1;
                while($rowTax     = $db->sql_fetchrow($resultTax)){

                    $total_amount = $rowTax['qty_amount'];
                    
                    if($rowTax['gst'] == ''){
                        $vatPercent = '0.00';
                    }
                    else{
                        $vatPercent = $rowTax['gst'];
                    }

                    $vat_Sum  = ($total_amount * $rowTax['gst'])/100;

                    $gstRatePercent = $rowTax['gst'] / 2;

                    //$vat_Amount_total = $total_amount + $vat_Sum;
                    $vat_Amount_total = $total_amount;
                    if($vat_Sum == 0){
                        $vat_Amount_total = 0;
                    }

                    $vatPercentHalf = $vatPercent / 2;
                    $vat_Sum_Half   = $vat_Sum / 2;

                    $totalVatSum += $vat_Sum;

                    $vatPercentHalf = sprintf('%0.2f', $vatPercentHalf);
                    $total_tax = $vat_Sum_Half + $vat_Sum_Half;
                    if($Row['igst_show'] == "1"){
                        $tbl4 = $tbl4.'
                        <tr>
                            <td align="right">'.$rowTax['gst'].' %</td>
                            <td align="right">'.number_format($vat_Amount_total, 2).'</td>
                            <td align="right">'.number_format($vat_Sum, 2).'</td>
                            <td align="right">'.number_format($total_tax, 2).'</td>
                        </tr>
                        ';
                    }else{
                        $tbl4 = $tbl4.'
                        <tr>
                            <td align="right">'.$rowTax['gst'].' %</td>
                            <td align="right">'.number_format($vat_Amount_total, 2).'</td>
                            <td align="right">'.number_format($gstRatePercent, 0).'%</td>
                            <td align="right">'.number_format($vat_Sum_Half, 2).'</td>
                            <td align="right">'.number_format($gstRatePercent, 0).'%</td>
                            <td align="right">'.number_format($vat_Sum_Half, 2).'</td>
                            <td align="right">'.number_format($total_tax, 2).'</td>
                        </tr>
                        ';
                    }

                    $counter++;
                }   
            }

            if($Row['gst_status'] == "ON"){

                $total_vat_Amount_total += $vat_Amount_total;
                $total_vat_Sum_Half += $vat_Sum_Half;
                $total_total_tax += $total_tax;
                $total_vat_Sum += $vat_Sum;

                if($Row['igst_show'] == "1"){
                    $tbl4 = $tbl4.'
                    <tr>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">TOTAL</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_vat_Amount_total, 2).'</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_vat_Sum, 2).'</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_total_tax, 2).'</td>
                    </tr>
                    ';

                }else{
                    $tbl4 = $tbl4.'
                    <tr>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">TOTAL</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_vat_Amount_total, 2).'</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;"></td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_vat_Sum_Half, 2).'</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;"></td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_vat_Sum_Half, 2).'</td>
                        <td align="right" style="border-top:1px solid #aeafb1;font-weight:bold;border-bottom:1px solid #aeafb1;">'.number_format($total_total_tax, 2).'</td>
                    </tr>
                    ';
                }

                    $tbl4 = $tbl4.'</table>';

            }

            $tbl5 = '<table cellpadding="4" border="0" width="100%" nobr="true">';

            $tbl5 = $tbl5.'
                <tr>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                    <td width="48%" align="left" style="font-size:11px;font-weight:bold;color:#ffffff;" bgColor="#157ca7"><b>Bank Details : </b></td>
                    <td width="2%"></td>
                    <td width="20%" align="center" rowspan="4" style="font-weight:bold;font-size:11px;vertical-align:bottom;border:1px solid #e5e5e5;"><br/><br/><br/><br/><br/><br/><br/><br/>Common Seal</td>
                    <td width="30%" align="right" rowspan="4" style="font-size:12px;font-weight:bold;">For '.$cpCfg['cp.companyName'].'<br/><br/><br/><br/><br/><br/>Authorised signatory</td>
                </tr>
                <tr>
                    <td width="48%" align=""><span style="font-weight:bold;font-size:11px;color:#000000;"><b>'.$cpCfg['cp.bankDetails'].'</b></span></td>
                    <td width="2%"></td>
                </tr>
                <tr>
                <br/>
                    <td width="48%" align="" bgColor="#157ca7"><span style="font-weight:bold;font-size:11px; color:#ffffff;">Mode of Transport : </span></td>
                    <td width="2%"></td>
                </tr>
                <tr>
                    <td width="48%" align=""><font style="font-size:12px;">Vehicle No. : '.$notes.'</font></td>
                    <td width="2%"></td>
                </tr>
            </table>
            ';            
        }


        $pdf->ln(-5);
        $pdf->writeHTML($tbl1, true, false, false, false, '');
        $pdf->writeHTML($tblQuote, true, false, false, false, '');
        $pdf->writeHTML($tbl2, true, false, false, false, '');
        $pdf->writeHTML($tbl3, true, false, false, false, '');
        $pdf->writeHTML($tbl4, true, false, false, false, '');
        $pdf->writeHTML($tbl5, true, false, false, false, '');

        $pdf->Output();

    }

    /**
     *
     */
    function getRightPanel($row){
        $displayLinkData = Zend_Registry::get('displayLinkData');
        $media = Zend_Registry::get('media');
        $cpCfg = Zend_Registry::get('cpCfg');
        $db = Zend_Registry::get('db');

        $printText = "";
        $actionButtons = "";
        $summaryAction = "";
        $captainCopy = "";

        $links ='';
        if ($cpCfg['m.ecommerce.order.showAttachment'] == 1){
            $links .= $media->getRightPanelMediaDisplay('Attachments', 'tradingsg_order', 'attachment', $row);
        }

        $printTextButton ='';

        if ($cpCfg['m.tradingsg.order.showReceiptButton']){
            $formActionReceipt = "index.php?module=tradingsg_order&_spAction=generateReceiptForm&order_id={$row['order_id']}&showHTML=0";

            $actionButtons .="
            <div class='float_right btn btn-info mb5'>
                <a href='{$formActionReceipt}' id='generateReceipt'>CREATE RECEIPT</a>
            </div>
            ";
        }

        if ($cpCfg['m.tradingsg.order.showInvoiceButton']){
            $formActionInvoice = "index.php?module=tradingsg_order&_spAction=generateInvoiceForm&order_id={$row['order_id']}&showHTML=0";
            $actionButtons .="
            <div class='float_right btn btn-success mb5'>
                <a href='{$formActionInvoice}' id='generateInvoice'>CREATE INVOICE</a>
            </div>
            ";
        }

        $urlProformaOrderItemInvoiceAsPdf = "index.php?module=tradingsg_order&_spAction=printProformaOrderItemInvoiceRecord&id={$row['order_id']}&showHTML=0";

        $actionButtons .="
        <div class='float_right btn btn-primary mb5'>
            <a href='{$urlProformaOrderItemInvoiceAsPdf}' target='blank' id='proformaOrderItemInvoiceAsPdf'>PROFORMA INVOICE</a>
        </div>
		";

         $formActionCredit = "index.php?module=tradingsg_order&_spAction=generateCreditNoteForm&order_id={$row['order_id']}&showHTML=0";
            $actionButtons .="
            <div class='float_right btn btn-success mb5'>
                <a href='{$formActionCredit}' id='generateCredit'>CREATE CREDIT NOTE</a>
            </div>
            ";



         $formActionDebit = "index.php?module=tradingsg_order&_spAction=generateDebitNoteForm&order_id={$row['order_id']}&showHTML=0";
            $actionButtons .="
            <div class='float_right btn btn-success mb5'>
                <a href='{$formActionDebit}' id='generateDebit'>CREATE DEBIT NOTE</a>
            </div>
            ";


        $print ="
        <div class='floatbox actionBtnsDetail'>
	        <div class='orderbtnbackground floatbox mb10'>
            {$actionButtons}
	        </div>
        </div>
        ";

        if ($cpCfg['m.tradingsg.order.showInvoicePortalDisplay']){
            //$links .= $displayLinkData->getLinkPortalMain('tradingsg_order', 'tradingsg_invoiceLink', 'Invoices Linked', $row);
            $links .= $this->getInvoicePortalDisplay($row);
        }

        if ($cpCfg['m.tradingsg.order.showReceiptPortalDisplay']){
            //$links .= $displayLinkData->getLinkPortalMain('tradingsg_order', 'tradingsg_receiptLink', 'Receipt Linked', $row);
            $links .= $this->getReceiptPortalDisplay($row);
        }

            $summaryTableOrder = $this->getSummaryInOrder($row);

        $orderItem = '';
        if ($cpCfg['m.tradingsg.order.showOrderItemDisplay']){
            $orderItem = $displayLinkData->getLinkPortalMain('tradingsg_order', 'ecommerce_orderItemLink', 'Order Items', $row);
        }

        $links .= $this->getCreditPortalDisplay($row);
        $links .= $this->getDebitPortalDisplay($row);

        $text = "
        {$print}
        <!--{$summaryTableOrder}-->
        {$orderItem}
        {$links}
        ";

        return $text;
    }

    /**
     *
     */
     function getGenerateCreditNoteForm() {
        $fn = Zend_Registry::get('fn');
        $formObj = Zend_Registry::get('formObj');
        $db = Zend_Registry::get('db');
        $cpCfg = Zend_Registry::get('cpCfg');

        unset($_SESSION['selectedOrderItemIds']);

        $rows = '';

        $order_id = $fn->getReqParam('order_id');
        $date     = $fn->getCurrentDate();
        $due_date = date('Y-m-d', strtotime("+30 days"));
        $qty_balance = '';

        $sqlOrderItem = "
        SELECT * FROM order_item
        WHERE order_id = {$order_id}
        ";
        $resultOrderItem = $db->sql_query($sqlOrderItem);
        while ($rowOI = $db->sql_fetchrow($resultOrderItem)) {
            $sqlQty = "
            SELECT SUM(it.qty) AS qty_invoiced
            FROM credit_note_item it
            JOIN credit_note i ON (i.credit_note_id = it.credit_note_id)
            WHERE i.order_id = {$order_id}
             AND it.record_id = {$rowOI['record_id']}
             AND i.status != 'Cancelled'
            ";
            $resultQty = $db->sql_query($sqlQty);
            $rowQty = $db->sql_fetchrow($resultQty);

            $selling_price = $rowOI['unit_price'] * $rowOI['qty'];

            $qty_balance = $rowOI['qty'] - $rowQty['qty_invoiced'];

            $inputRow = '';
            $qtyRow = '';

            if ($rowQty['qty_invoiced'] != $rowOI['qty']) {
                $pfx = $rowOI['order_item_id'] . '_' ;
                $inputRow = "<input class='orderItemId' type='checkbox' name='orderItemId[]' value='{$rowOI['order_item_id']}'>";
                $qtyRow = "<input type='text' value='{$qty_balance}' id='fld_qty' class='text w50' name='{$pfx}qty'>";
            }

               $pfx1 = $rowOI['order_item_id'] . '_' ;
               
                $unitPriceRow = "<input type='text' value='{$rowOI['unit_price']}' id='fld_unit_price' class='text w50' name='{$pfx1}unit_price'>";


            $rows .= "
            <tr orderRowItem[] = {$rowOI['order_item_id']}>
                <td>
                    {$inputRow}
                </td>
                <td>{$rowOI['item_title']}</td>
                <td>{$rowOI['part_number']}</td>
                <td class='sellingPrice'>{$unitPriceRow}</td>
                <td class=''>{$rowOI['qty']}</td>
                <td class=''>{$qtyRow}</td>
                <td class='qtyBalance'>{$qty_balance}</td>
                <td class=''>{$rowQty['qty_invoiced']}</td>
            </tr>
            ";
        }

        $formAction = "index.php?_topRm=finance&module=tradingsg_order&_spAction=generateCreditFormSubmit&showHTML=0";

        $expNoEdit = array('isEditable' => 0);

        $icgstArr = array(
             "IGST"
            ,"CGST"
        );

        $orderRec = $fn->getRecordRowById('order', 'order_id', $order_id);

            //{$formObj->getTBRow('Add Frieght Cost', 'frieght_cost')}
        $text = "
        <form id='portalCreditForm' class='yform columnar creditNoteForm' method='post' action='{$formAction}'>
            {$formObj->getTBRow('Amount', 'invoice_amount', '', $expNoEdit)}
            {$formObj->getDateRow('Date', 'invoice_date', $date)}
            {$formObj->getDateRow('Due Date', 'invoice_due_date', $due_date)}
            {$formObj->getTBRow('Customer Purchase Order No', 'cust_po_no')}
            {$formObj->getTARow('Terms', 'invoice_terms', $orderRec['invoice_terms'])}
            {$formObj->getTARow('Notes', 'notes', $orderRec['notes'])}
            {$formObj->getTBRow('Issued By', 'staff_id', $_SESSION['userFullName'], $expNoEdit)}
            {$formObj->getTBRow('Add Frieght Cost', 'frieght_cost')}
            {$formObj->getTBRow('Add P & F(%)', 'p_f')}
            <div class='button updateTotal'>
                <a href='#'>Update Total</a>
            </div>

            <table class='thinlist room-order-table'>
                <thead>
                    <th class='click-all-topping'>
                        <a href='#' class='check-all-col'>
                            <img src='{$cpCfg['cp.commonImagesPathAlias']}icons/checkbox_checked.gif'>
                        </a>
                        <a href='#' class='uncheck-all-col'>
                            <img src='{$cpCfg['cp.commonImagesPathAlias']}icons/checkbox_unchecked.gif'>
                        </a>
                    </th>
                    <th>Product Name</th>
                    <th>Part Number</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th class=''>Qty (Current Invoice)</th>
                    <th>Qty (Balance)</th>
                    <th>Qty (Invoiced)</th>
                </thead>

                <tbody>
                    {$rows}
                </tbody>
            </table>

            <input type='hidden' name='order_id' value='{$order_id}' />
            <input type='hidden' name='qty_balance' value='{$qty_balance}' />
        </form>
        ";

        //{$formObj->getTBRow('Add Frieght(%)', 'frieght')}

        return $text;
    }


     /**
     *
     */
     function getGenerateDebitNoteForm() {
        $fn = Zend_Registry::get('fn');
        $formObj = Zend_Registry::get('formObj');
        $db = Zend_Registry::get('db');
        $cpCfg = Zend_Registry::get('cpCfg');

        unset($_SESSION['selectedOrderItemIds']);

        $rows = '';

        $order_id = $fn->getReqParam('order_id');
        $date     = $fn->getCurrentDate();
        $due_date = date('Y-m-d', strtotime("+30 days"));
        $qty_balance = '';

        $sqlOrderItem = "
        SELECT * FROM order_item
        WHERE order_id = {$order_id}
        ";
        $resultOrderItem = $db->sql_query($sqlOrderItem);
        while ($rowOI = $db->sql_fetchrow($resultOrderItem)) {
            $sqlQty = "
            SELECT SUM(it.qty) AS qty_invoiced
            FROM debit_note_item it
            JOIN debit_note i ON (i.debit_note_id = it.debit_note_id)
            WHERE i.order_id = {$order_id}
             AND it.record_id = {$rowOI['record_id']}
             AND i.status != 'Cancelled'
            ";
            $resultQty = $db->sql_query($sqlQty);
            $rowQty = $db->sql_fetchrow($resultQty);

            $selling_price = $rowOI['unit_price'] * $rowOI['qty'];

            $qty_balance = $rowOI['qty'] - $rowQty['qty_invoiced'];

            $inputRow = '';
            $qtyRow = '';

            if ($rowQty['qty_invoiced'] != $rowOI['qty']) {
                $pfx = $rowOI['order_item_id'] . '_' ;
                $inputRow = "<input class='orderItemId' type='checkbox' name='orderItemId[]' value='{$rowOI['order_item_id']}'>";
                $qtyRow = "<input type='text' value='{$qty_balance}' id='fld_qty' class='text w50' name='{$pfx}qty'>";
            }

               $pfx1 = $rowOI['order_item_id'] . '_' ;
               
                $unitPriceRow = "<input type='text' value='{$rowOI['unit_price']}' id='fld_unit_price' class='text w50' name='{$pfx1}unit_price'>";


            $rows .= "
            <tr orderRowItem[] = {$rowOI['order_item_id']}>
                <td>
                    {$inputRow}
                </td>
                <td>{$rowOI['item_title']}</td>
                <td>{$rowOI['part_number']}</td>
                <td class='sellingPrice'>{$unitPriceRow}</td>
                <td class=''>{$rowOI['qty']}</td>
                <td class=''>{$qtyRow}</td>
                <td class='qtyBalance'>{$qty_balance}</td>
                <td class=''>{$rowQty['qty_invoiced']}</td>
            </tr>
            ";
        }

        $formAction = "index.php?_topRm=finance&module=tradingsg_order&_spAction=generateDebitFormSubmit&showHTML=0";

        $expNoEdit = array('isEditable' => 0);

        $icgstArr = array(
             "IGST"
            ,"CGST"
        );

        $orderRec = $fn->getRecordRowById('order', 'order_id', $order_id);

            //{$formObj->getTBRow('Add Frieght Cost', 'frieght_cost')}
        $text = "
        <form id='portalDebitForm' class='yform columnar debitNoteForm' method='post' action='{$formAction}'>
            {$formObj->getTBRow('Amount', 'invoice_amount', '', $expNoEdit)}
            {$formObj->getDateRow('Date', 'invoice_date', $date)}
            {$formObj->getDateRow('Due Date', 'invoice_due_date', $due_date)}
            {$formObj->getTBRow('Customer Purchase Order No', 'cust_po_no')}
            {$formObj->getTARow('Terms', 'invoice_terms', $orderRec['invoice_terms'])}
            {$formObj->getTARow('Notes', 'notes', $orderRec['notes'])}
            {$formObj->getTBRow('Issued By', 'staff_id', $_SESSION['userFullName'], $expNoEdit)}
            {$formObj->getTBRow('Add Frieght Cost', 'frieght_cost')}
            {$formObj->getTBRow('Add P & F(%)', 'p_f')}
            <div class='button updateTotal'>
                <a href='#'>Update Total</a>
            </div>

            <table class='thinlist room-order-table'>
                <thead>
                    <th class='click-all-debit'>
                        <a href='#' class='check-all-col-debit'>
                            <img src='{$cpCfg['cp.commonImagesPathAlias']}icons/checkbox_checked.gif'>
                        </a>
                        <a href='#' class='uncheck-all-col-debit'>
                            <img src='{$cpCfg['cp.commonImagesPathAlias']}icons/checkbox_unchecked.gif'>
                        </a>
                    </th>
                    <th>Product Name</th>
                    <th>Part Number</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th class=''>Qty (Current Invoice)</th>
                    <th>Qty (Balance)</th>
                    <th>Qty (Invoiced)</th>
                </thead>

                <tbody>
                    {$rows}
                </tbody>
            </table>

            <input type='hidden' name='order_id' value='{$order_id}' />
            <input type='hidden' name='qty_balance' value='{$qty_balance}' />
        </form>
        ";

        //{$formObj->getTBRow('Add Frieght(%)', 'frieght')}

        return $text;
    }



    /**
    **/

    function getSummaryInOrder ($row) {
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $formObj = Zend_Registry::get('formObj');
        $dbUtil = Zend_Registry::get('dbUtil');

        $rows  = "";

        $SQL = "
        SELECT o.*
              ,(SELECT SUM(round((oi.unit_price * oi.qty),2))
               FROM order_item oi
               WHERE oi.order_id = {$row['order_id']}
               ) AS order_amount
              ,(SELECT SUM(i.invoice_amount) FROM invoice i
                WHERE i.order_id = o.order_id
                AND i.status != 'Cancelled'
                ) AS invoice_amount 
              ,(SELECT SUM(r.amount)
                FROM receipt r
                WHERE o.order_id = r.order_id
                AND r.receipt_status != 'Cancelled'
                )AS receipt_amount  
        FROM `order`o
        WHERE o.order_id = {$row['order_id']}
        ";

        $result = $db->sql_query($SQL);
        $row  = $db->sql_fetchrow($result);

        $orderAmt   = number_format(round($row['order_amount']), 2);
        $invoiceAmt = number_format($row['invoice_amount'] ,2);
        $receiptAmt = number_format($row['receipt_amount'] ,2);

        $outstandingInvoiceAmt = number_format($row['invoice_amount'] - $row['receipt_amount'], 2);
        $overallBalanceAmt     = number_format($row['order_amount'] - $row['receipt_amount'], 2);

            $rows = "
            <table class='summaryAmountDetails'>
                <tr class= 'summaryTitle'>
                    <th>SUMMARY</th>
                    <th></th>
                </tr>
                <tr>
                    <td class='totalOrderAmountLabel'>TOTAL ORDER AMOUNT</td>
                    <td class='totalOrderAmountValue'>{$orderAmt}</td>
                <tr>
                    <td class='totalOrderAmountLabel'>TOTAL INVOICE RAISED</td>
                    <td class='totalInvoiceAmountValue'>{$invoiceAmt}</td>
                <tr>
                <tr>
                    <td class='totalOrderAmountLabel'>AMOUNT PAID</td>
                    <td class='totalReciptAmountValue'>{$receiptAmt}</td>
                <tr>
                <tr>
                    <td class='totalOrderAmountLabel'>OUTSTANDING INVOICE</td>
                    <td class='totalOutstandingInvoiceAmtValue'>{$outstandingInvoiceAmt}</td>
                <tr>
                <tr>
                    <td class='totalOrderAmountLabel'>OVERALL BALANCE</td>
                    <td class='totalOverallAmountValue'>{$overallBalanceAmt}</td>
                <tr>
            </table>
            ";      

        $text = "
        {$rows}
        ";

        return $text;

    }


   /**
     *
     */
    function getPrintInvoiceRecordForPurchaseOrder() {
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $tv = Zend_Registry::get('tv');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $searchVar = Zend_Registry::get('searchVar');
        $media = Zend_Registry::get('media');
        $cpPaths = Zend_Registry::get('cpPaths');
        $dbUtil = Zend_Registry::get('dbUtil');

        ini_set('memory_limit', '512M');

        set_time_limit(50000);

        include_once(CP_LIBRARY_PATH.'lib_php/fpdf/fpdf.php');
        include_once(CP_LIBRARY_PATH.'lib_php/fpdf-extra/html2pdf.php');
        include_once(CP_LIBRARY_PATH.'lib_php/fpdf-extra/html_table1.php');

        $pdf = new MYPDF();
		$pdf->AddPage();
		$pdf->SetFont('Arial','',11);

        $invoice_code 		  = $fn->getReqParam('invoice_code');
        $purchase_order_id 	  = $fn->getReqParam('purchase_order_id');

        $SQL = "
        SELECT ini.*
              ,p.title AS product_title
              ,p.unit
              ,p.item_code
			  ,p.part_number
			  ,po.delivery_terms
			  ,po.company_id_supplier
			  ,po.notes
              ,c.company_name
              ,c.address_flat
              ,c.address_street
              ,c.address_town
              ,c.address_state
              , (SELECT gc.name FROM geo_country gc
                 WHERE gc.country_code = c.address_country)
                AS address_country
              ,c.billing_address_flat
              ,c.billing_address_street
              ,c.billing_address_town
              ,c.billing_address_state
              , (SELECT gc.name FROM geo_country gc
                 WHERE gc.country_code = c.billing_address_country)
                AS billing_address_country
              ,c.fax
              ,c.phone
              ,i.invoice_date
              ,q.delivery_date
              ,q.delivery_location
              ,ini.unit_price
              ,i.invoice_code
              ,i.invoice_terms
              ,i.invoice_due_date
              ,i.cst
              ,i.vat
              ,i.frieght
              ,i.p_f
              ,q.quote_code
              ,q.currency
              ,ini.qty * ini.unit_price AS amount
              ,(SELECT SUM(init.qty * init.unit_price) FROM invoice_item init
               WHERE init.invoice_id = ini.invoice_id) AS sub_total
        FROM invoice_item ini
        LEFT JOIN product p ON (p.product_id = ini.record_id)
        LEFT JOIN invoice i ON (i.invoice_id = ini.invoice_id)
        LEFT JOIN purchase_order po  ON (po.purchase_order_id = i.purchase_order_id)
        LEFT JOIN `order` o ON (o.order_id = i.order_id)
        LEFT JOIN company c ON (c.company_id = po.company_id_supplier)
        LEFT JOIN quote q ON (q.quote_id = o.quote_id)
        LEFT JOIN product_group pg ON (p.product_group_id = pg.product_group_id)
        WHERE i.invoice_code = '{$invoice_code}'
        ORDER BY pg.sort_order ASC, p.title
        ";
        $result = $db->sql_query($SQL);


        $numRows  = $db->sql_numrows($result);

        $today = date("Y-m-d");
		if ($numRows == 0){
            $pdf->SetXY(30,30);
            $pdf->Cell(50, 20, "Please set the values for your Order and print the PDF");
			$pdf->Output();
			return;
		}

        $count = 0;
        $total = 0;
        $discount_price = 0;
        $rows = "";
        $lineItemNumber = 1;  // To increment the line item in receipt
		$printTaxName = '';
		$gsttaxvalue = '';
		$gstvalue = '';
		$totalvalue = '';

        //============================================================================= //
        $pdf->SetFont('Arial','',11);
        while ($row = $db->sql_fetchrow($result)) {
            if ($count == 0){
                /* Logo of the institution */
                $pdf->Image('images/logo-print.png',10,5,45);
                $pdf->SetXY(10,10);
                $pdf->SetFont('Courier','B',11);
                $pdf->Cell(50, 20, 'Authorized Distributor of:');
                $pdf->SetXY(10,25);
                //$pdf->Image('images/parker.jpg',10,28, 25);
                //$pdf->Image('images/gse.png',42,25, 25);
                $creationDate   = $fn->getCPDate($row['invoice_date'], 'd-m-Y');
                $invoiceDueDate = $fn->getCPDate($row['invoice_due_date'], 'd-m-Y');
                $deliveryDate   = $fn->getCPDate($row['delivery_date'], 'd-m-Y');
				$currency = $row['currency'];

				$gsttaxvalue = $cpCfg['amtForGSTCalc'] ;
				$gstvalue = $row['sub_total'] * $gsttaxvalue / 100;
				$totalvalue = $gstvalue + $row['sub_total'];

                /* Company address */
                //Address to be got from settings
                $pdf->SetXY(130,0);
                $pdf->SetFont('Courier','B',11);
                $pdf->Cell(50, 20, $cpCfg['cp.companyName']);
                $pdf->Ln(5);
                $pdf->SetXY(130,5);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf1']);
                $pdf->Ln(5);
                $pdf->SetXY(130,10);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf2']);
                $pdf->Ln(5);
                $pdf->SetXY(130,15);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf3']);
                $pdf->Ln(5);
                $pdf->SetXY(130, 20);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf4']);
                $pdf->Ln(5);
                $pdf->SetXY(130,25);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf6']);
                $pdf->Ln(5);
                $pdf->SetXY(130,30);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf7']);
                $pdf->Ln(5);
                $pdf->SetXY(130,35);
                $pdf->Cell(50, 20, $cpCfg['printEmailAddress']);

                /* Header */
                $pdf->SetFont('Courier','BU',11);
                $pdf->SetXY(80, 40);
                $pdf->Cell(50, 20, "INVOICE", 0, 0, 'C');
                $pdf->SetFont('Courier','B',11);
                $pdf->SetX(130);
                $pdf->Cell(31, 20, "DATE : " . $creationDate, 0, 0, 'L');
                $pdf->Ln(20);

                /* Company Details*/
				$billingAddressFlat = '';
				$billingAddressStreet = '';
				$billingAddressTown = '';
				$billingAddressState = '';
				$billingAddressCountry = '';

				if ($row['billing_address_flat'] != ''
				 || $row['billing_address_street'] != ''
				 || $row['billing_address_town'] != ''
				 || $row['billing_address_state'] != ''
				 || $row['billing_address_country'] != '')
			    {
					$billingAddressFlat     = $row['billing_address_flat'];
					$billingAddressStreet   = $row['billing_address_street'];
					$billingAddressTown     = $row['billing_address_town'];
					$billingAddressState    = $row['billing_address_state'];
					$billingAddressCountry  = $row['billing_address_country'];
			    } else {
					$billingAddressFlat     = $row['address_flat'];
					$billingAddressStreet   = $row['address_street'];
					$billingAddressTown     = $row['address_town'];
					$billingAddressState    = $row['address_state'];
					$billingAddressCountry  = $row['address_country'];
				}


                /* Company Details*/
                $date = $fn->getCPDate($row['delivery_date'], 'd-m-Y');
                $pdf->SetFont('Courier','B',11);
                $pdf->SetFillColor(254,203,156);
                $pdf->Cell(95,8,"INVOICE TO",1,0, 'L', 1);
                $pdf->Cell(95,8,"DELIVERY TO",1,0, 'L', 1);
                $pdf->Ln();
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(95, 8, $cpCfg['cp.companyName'], 'LR', 0, 'L', 1);
                $pdf->Cell(95, 8, $cpCfg['cp.companyName'], 'LR', 0, 'L', 1);
                $pdf->Ln();
                $pdf->Cell(95, 5, $cpCfg['cp.addressPdf1'], 'LR', 0, 'L', 1);
                $pdf->Cell(95, 5, $cpCfg['cp.addressPdf1'], 'LR', 0, 'L', 1);
                $pdf->Ln();
                $pdf->Cell(95, 5, $cpCfg['cp.addressPdf2'], 'LR', 0, 'L', 1);
                $pdf->Cell(95, 5, $cpCfg['cp.addressPdf2'], 'LR', 0, 'L', 1);
                $pdf->Ln();
                $pdf->Cell(95, 5, $cpCfg['cp.addressPdf3'], 'LR', 0, 'L', 1);
                $pdf->Cell(95, 5, $cpCfg['cp.addressPdf3'], 'LR', 0, 'L', 1);
                $pdf->Ln();
                $pdf->Cell(95, 5, $cpCfg['cp.addressPdf4'], 'LR', 0, 'L', 1);
                $pdf->Cell(95, 5, $cpCfg['cp.addressPdf4'], 'LR', 0, 'L', 1);
                $pdf->Ln();
                $pdf->Cell(95, 5, $cpCfg['cp.addressPdf7'], 'LR', 0, 'L', 1);
                $pdf->Cell(95, 5, $cpCfg['cp.addressPdf7'], 'LR', 0, 'L', 1);
                $pdf->Ln();
                $pdf->Cell(95, 5, $cpCfg['cp.addressPdf6'], 'LRB', 0, 'L', 1);
                $pdf->Cell(95, 5, $cpCfg['cp.addressPdf6'], 'LRB', 0, 'L', 1);
                $pdf->Ln();
                $pdf->Ln(10);

                /* Invoice Details*/
                $pdf->SetFont('Courier','B',11);
                $pdf->SetFillColor(254,203,156);
                $pdf->Cell(47.5,8,"INVOICE NO :",1,0, 'L', 1);
                $pdf->SetFillColor(255,255,255);
	            $pdf->Cell(47.5, 8, $row['invoice_code'], 1, 0, 'L', 1);
                $pdf->SetFillColor(254,203,156);
                $pdf->Cell(47.5,8,"DUE DATE :",1,0, 'L', 1);
                $pdf->SetFillColor(255,255,255);
	            $pdf->Cell(47.5, 8, $invoiceDueDate, 1, 0, 'L', 1);
                $pdf->Ln(20);

				$terms = $row['invoice_terms'];
				$bank = "HDFC BANK LTD\nNO.9, MOSQUE STREET\nPALLAVARAM, CHENNAI-600043\nCURRENT A/C:50200000741296";

	            $pdf->SetFont('Courier','B',11);
	            $pdf->SetFillColor(254,203,156);
	            $pdf->Cell(95,8,"TERMS",1,0, 'L', 1);
	            $pdf->Cell(95,8,"BANK DETAILS",1,0, 'L', 1);
	            $pdf->SetFillColor(255,255,255);
                $pdf->SetXY(10,144);
	            $pdf->drawTextBox($terms, 95, 32, 'L', 'C', 1);
                $pdf->SetXY(105,144);
	            $pdf->drawTextBox($bank, 95, 32, 'L', 'C', 'BLR');
	            $pdf->Ln(28);

                /* List of order items header */
                $pdf->SetFont('Courier','B',11);
                $pdf->SetFillColor(254,203,156);
                $pdf->Cell(10,8,"S.NO",1,0, 'C', 1);
                $pdf->Cell(65,8,"NAME OF THE ITEM",1,0, 'C', 1);
                $pdf->Cell(37,8,"PART NUMBER",1,0, 'C', 1);
                $pdf->Cell(13,8,"QTY",1,0, 'C', 1);
                $pdf->Cell(13,8,"UOM",1,0, 'C', 1);
                $pdf->Cell(26,8,"UP",1,0, 'C', 1);
                $pdf->Cell(26,8,"AMOUNT(" . $row['currency'] . ")",1,0, 'C', 1);
                $pdf->Ln();
            }

            //===================================MAIN TABLE============================= //
			$company_name 	= $row['company_name'];
			$delivery_terms = $row['delivery_terms'];
			$notes 			= $row['notes'];


            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(10, 8, $lineItemNumber, 1, 0, 'C', 1);
            $pdf->Cell(65, 8, $row['product_title'], 1, 0, 'L', 1);
            $pdf->Cell(37, 8, $row['part_number'], 1, 0, 'L', 1);
            $pdf->Cell(13, 8, $row['qty'], 1, 0, 'R', 1);
            $pdf->Cell(13, 8, $row['unit'], 1, 0, 'R', 1);
            $pdf->Cell(26, 8, number_format(round($row['unit_price']),2), 1, 0, 'R', 1);
            $pdf->Cell(26, 8, number_format(round($row['amount']),2), 1, 0, 'R', 1);
            $pdf->Ln();

            $count++;
            $lineItemNumber++;
            $sub_total = $row['sub_total'];
            $notes = $row['notes'];
            $frieght = $row['frieght'];
            $pf = $row['p_f'];

			if($row['vat'] == 1 && $row['cst'] == 0){
		        $printTaxName = $cpCfg['printTaxName'] ;
				$gsttaxvalue = $cpCfg['amtForGSTCalc'] ;
				$gstvalue = round($row['sub_total']) * $gsttaxvalue / 100;
				$totalvalue = $gstvalue + round($row['sub_total']);
			} else if($row['cst'] == 1 && $row['vat'] == 0){
		        $printTaxName = $cpCfg['printCstText'] ;
				$gsttaxvalue = $cpCfg['printCstinInvoice'] ;
				$gstvalue = round($row['sub_total']) * $gsttaxvalue / 100;
				$totalvalue = $gstvalue + round($row['sub_total']) ;
			}
        }
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(164, 8, "SUB TOTAL", 1, 0, 'R', 1);
            $pdf->Cell(26, 8, number_format(round($sub_total),2), 1, 0, 'R', 1);
            $pdf->Ln();

            $pdf->SetFillColor(255,255,255);

            $pdf->Cell(164, 8, "ADD: {$printTaxName} {$gsttaxvalue}%", 1, 0, 'R', 1);
            $pdf->Cell(26, 8, number_format(round($gstvalue), 2), 1, 0, 'R', 1);
            $pdf->Ln();

            $totalvalueRounded = round($totalvalue);
			$totalFrieght = $sub_total * $frieght / 100;

			if($frieght != '' ){
				$totalvalueRounded = $totalvalueRounded + $totalFrieght;
	            $pdf->SetFillColor(255,255,255);
	            $pdf->Cell(164, 8, "ADD FRIEGHT : {$frieght}%", 1, 0, 'R', 1);
	            $pdf->Cell(26, 8, number_format($totalFrieght, 2), 1, 0, 'R', 1);
				$pdf->Ln();
			}

			if($pf != '' ){
				$totalvalueRounded = $totalvalueRounded + $pf;
	            $pdf->SetFillColor(255,255,255);
	            $pdf->Cell(164, 8, "ADD P&F", 1, 0, 'R', 1);
	            $pdf->Cell(26, 8, number_format($pf, 2), 1, 0, 'R', 1);
				$pdf->Ln();
			}

            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(164, 8, 'TOTAL', 1, 0, 'R', 1);
            $pdf->Cell(26, 8, number_format($totalvalueRounded, 2), 1, 0, 'R', 1);
			$pdf->Ln(20);

            $pdf->SetFillColor(254,203,156);
			$pdf->Cell(195,8, "Client :", 0, 0, 'L', 1);
			$pdf->Ln(12);
            $pdf->SetFillColor(255,255,255);
            $pdf->drawTextBox($company_name, 180, 55, 'L', 'T', 0);
			$pdf->Ln();
			$pdf->Ln(5);

            $pdf->SetFillColor(254,203,156);
			$pdf->Cell(195,8, "Delivery Terms :", 0, 0, 'L', 1);
			$pdf->Ln(12);
            $pdf->SetFillColor(255,255,255);
            $pdf->drawTextBox($delivery_terms, 170, 32, 'L', 'T', 0);
			$pdf->Ln();
			$pdf->Ln(5);

            $pdf->SetFillColor(254,203,156);
			$pdf->Cell(195,8, "NOTE :", 0, 0, 'L', 1);
			$pdf->Ln(12);
            $pdf->SetFillColor(255,255,255);
            $pdf->drawTextBox($notes, 170, 32, 'L', 'T', 0);
			$pdf->Ln();
			$pdf->Ln(5);

	        /* Creation of media record of the invoice */
	        $file_name = 'Refund_REF_' . date('Y-m-d') .'.pdf';
	        $outputPath = realpath($cpCfg['cp.mediaFolder']) . '/temp';

	        $outputFileName = $outputPath . '/' . $file_name;
	        //$pdf->Output($outputFileName , "F");
			$pdf->Output();

    }

    /**
     *
     */
    function getPrintReceipt() {
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $tv = Zend_Registry::get('tv');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $searchVar = Zend_Registry::get('searchVar');
        $media = Zend_Registry::get('media');
        $cpPaths = Zend_Registry::get('cpPaths');
        $dbUtil = Zend_Registry::get('dbUtil');

        ini_set('memory_limit', '512M');

        set_time_limit(50000);

        include_once(CP_LIBRARY_PATH.'lib_php/fpdf/fpdf.php');
        include_once(CP_LIBRARY_PATH.'lib_php/fpdf-extra/html_table1.php');

        $pdf = new MYPDF();

		$pdf->AddPage();
		$pdf->SetFont('Arial','',11);
		/*
		This fucntions requires
		1.total invoice amount for thie receipt
		2.Amount already paid for this invoice
		3. Amount Paid now
		4. Balance to be calculated.
		*/

        $receipt_code = $fn->getReqParam('receipt_code');
        $order_id = $fn->getReqParam('order_id');

        //$receiptRec     = $fn->getRecordRowByID('receipt', 'receipt_code', $receipt_code);

        /*$SQL = "
        SELECT r.*
        FROM receipt r
        WHERE r.receipt_code = {$receipt_code}
        ";
        $result = $db->sql_query($SQL);*/

        $SQL = "
        SELECT c.company_name
              ,c.address_flat
              ,c.address_street
              ,c.address_town
              ,c.address_state
              ,c.address_country
              ,c.billing_address_flat
              ,c.billing_address_street
              ,c.billing_address_town
              ,c.billing_address_state
              ,c.billing_address_country
              ,c.fax
              ,c.phone
              ,i.creation_date
              ,q.delivery_date
              ,q.delivery_location
              ,i.invoice_id AS invoice_id_main
              ,i.invoice_code
              ,i.invoice_amount
              ,q.quote_code
              ,q.currency
              ,r.receipt_id
              ,r.amount AS receipt_amount
              ,r.receipt_code
              ,r.mode_of_payment
              ,r.remarks
              ,r.creation_date AS receipt_date
        FROM receipt r
        LEFT JOIN invoice_receipt_history irh ON (r.receipt_id = irh.receipt_id)
        LEFT JOIN invoice i ON (i.invoice_id = irh.invoice_id)
        LEFT JOIN `order` o ON (o.order_id = i.order_id)
        LEFT JOIN company c ON (c.company_id = o.company_id)
        LEFT JOIN quote q ON (q.quote_id = o.quote_id)
        WHERE r.receipt_code = '{$receipt_code}'
          AND i.order_id = {$order_id}
        ";
        $result = $db->sql_query($SQL);

        $numRows  = $db->sql_numrows($result);

        $today = date("Y-m-d");
		if ($numRows == 0){
		    $pdf->SetXY(30,30);
		    $pdf->Cell(50, 20, "Please set the values for your Order and print the PDF");
			$pdf->Output();
			return;
		}

        $previous_paid_amount = '';
        $total_amount = '';
        $count = 0;
        $total = 0;
        $discount_price = 0;
        $rows = "";
        $lineItemNumber = 1;  // To increment the line item in receipt


        //============================================================================= //
        $pdf->SetFont('Arial','',11);
        while ($row = $db->sql_fetchrow($result)) {

            if ($count == 0){
                /* Logo of the institution */
                $pdf->Image('images/logo-print.png',10,5,45);
                $pdf->SetXY(10,10);
                //$pdf->Image('images/gse.png',42,25, 25);
                $creationDate = $fn->getCPDate($row['receipt_date'], 'd-m-Y');
                $deliveryDate = $fn->getCPDate($row['delivery_date'], 'd-m-Y');
				$currency = $row['currency'];

                /* Company address */
                //Address to be got from settings
                $pdf->SetXY(105,0);
                $pdf->SetFont('Courier','B',10);
                $pdf->Cell(50, 20, $cpCfg['cp.companyName']);
                $pdf->Ln(5);
                $pdf->SetXY(105, 12);
                $pdf->MultiCell(100, 5, strtoupper($cpCfg['cp.addressPdf1']));
                //$pdf->Cell(50, 20, $cpCfg['cp.addressPdf1']);
                $pdf->Ln(5);
                $pdf->SetXY(105, 22);
                $pdf->MultiCell(100, 5, strtoupper($cpCfg['cp.addressPdf2']));
                //$pdf->Cell(50, 20, $cpCfg['cp.addressPdf2']);
                $pdf->Ln(5);
                $pdf->SetXY(105, 24);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf3']);
                $pdf->Ln(5);
                $pdf->SetXY(105, 25);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf4']);
                $pdf->Ln(5);
                $pdf->SetXY(105, 30);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf6'].'  '.$cpCfg['cp.addressPdf7']);
                $pdf->Ln(5);
                $pdf->SetXY(105, 35);
                $pdf->Cell(50, 20, $cpCfg['printEmailAddress']);
                $pdf->Ln(5);
                /*$pdf->SetXY(140,25);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf5']);*/

                /* Header */
                $pdf->SetFont('Courier','BU',11);
                $pdf->SetXY(100, 43);
                $pdf->Cell(21, 20, "RECEIPT", 0, 0, 'C');
                $pdf->Ln(20);

                /* Company Details*/
				$billingAddressFlat = '';
				$billingAddressStreet = '';
				$billingAddressTown = '';
				$billingAddressState = '';
				$billingAddressCountry = '';

				if ($row['billing_address_flat'] != ''
				 || $row['billing_address_street'] != ''
				 || $row['billing_address_town'] != ''
				 || $row['billing_address_state'] != ''
				 || $row['billing_address_country'] != '')
				{
					$billingAddressFlat     = $row['billing_address_flat'];
					$billingAddressStreet   = $row['billing_address_street'];
					$billingAddressTown     = $row['billing_address_town'];
					$billingAddressState    = $row['billing_address_state'];
					$billingAddressCountry  = $row['billing_address_country'];
				} else {
					$billingAddressFlat     = $row['address_flat'];
					$billingAddressStreet   = $row['address_street'];
					$billingAddressTown     = $row['address_town'];
					$billingAddressState    = $row['address_state'];
					$billingAddressCountry  = $row['address_country'];
				}

                /* Address of the Company */
                $pdf->SetXY(10, 50);
                $pdf->SetFont('Courier','B',11);
                $pdf->Cell(50, 20, "Received from");
                $pdf->SetFillColor(224,235,255);
                $pdf->Rect(10, 63, 72, 30, 'D');
                $pdf->SetXY(10, 56);
                //$pdf->SetFont('Arial','',10);
                $pdf->Cell(50, 20, $row['company_name']);
                $pdf->SetXY(10, 61);
                $pdf->Cell(50, 20, $billingAddressFlat);
                $pdf->SetXY(10, 66);
                $pdf->Cell(50, 20, $billingAddressStreet);
                $pdf->SetXY(10, 71);
                $pdf->Cell(50, 20, $billingAddressTown);
                $pdf->SetXY(10, 76);
                $pdf->Cell(50, 20, $billingAddressState . ' ' . $billingAddressCountry);
                $pdf->SetXY(10, 81);
                $pdf->Ln(20);

                /* Recepit code and date */
                $code = 'Receipt No : '. $row['receipt_code'];
                $pdf->SetXY(135, 50);
                $pdf->Cell(50, 20, $code );
                $pdf->Ln(5);

                $pdf->SetX(135);
                $date = $fn->getCPDate($row['receipt_date'], 'd-M-Y');
                $pdf->Cell(11, 20, "Date : ");
                $pdf->Cell(50, 20, $date);
                $pdf->Ln(45);

                /* List of order items header */
                $pdf->SetFillColor(254,203,156);
                $pdf->Cell(135,8,"Description",1,0, 'L', 1);
                $pdf->Cell(55,8,"Amount(" . $row['currency'] . ")",1,0, 'R', 1);
                $pdf->Ln();
            }

            //===================================MAIN TABLE============================= //
            $count++;
            $lineItemNumber++;

           /*This sql used to find the previous amount paid for the invoice */
            $sqlPreviousPayment = "
            SELECT SUM(irhist.amount) AS total_amount_paid
            FROM invoice_receipt_history irhist
            LEFT JOIN receipt r ON (irhist.receipt_id = r.receipt_id)
            WHERE irhist.invoice_id = {$row['invoice_id_main']}
              AND irhist.receipt_id != {$row['receipt_id']}
              AND r.receipt_status != 'Cancelled'
            ";
            $resultPreviousPayment = $db->sql_query($sqlPreviousPayment);
            $rowPreviousPayment    = $db->sql_fetchrow($resultPreviousPayment);
            $previous_paid_amount += $rowPreviousPayment['total_amount_paid'];

            $sqlInvoiceAmount = "
            SELECT i.invoice_amount
            FROM invoice i
            WHERE i.invoice_id = {$row['invoice_id_main']}
            ";
            $resultInvAmount = $db->sql_query($sqlInvoiceAmount);
            $rowInvoiceAmount= $db->sql_fetchrow($resultInvAmount);

            $total_amount += $rowInvoiceAmount['invoice_amount'];

            $invoice_code = $row['invoice_code'];
            $mode_of_payment = $row['mode_of_payment'];
            $remarks = $row['remarks'];
            $receipt_amount = $row['receipt_amount'];
        }

            $balance_due          = $total_amount - $previous_paid_amount - $receipt_amount;

            /* Total amount to be paid */
            $pdf->SetFont('Arial','',10);
            $pdf->SetFillColor(255,255,255);
            $label = 'Invoice Amount (Invoice Code : ' . $invoice_code . ')';
            $pdf->Cell(135, 8, $label, 1, 0, 'L', 1);
            $pdf->Cell(55, 8, number_format(round($total_amount), 2), 1, 0, 'R');
            $pdf->Ln();

            /* Total amount paid earlier */
            $pdf->Cell(135, 8,'Amount already Paid ', 1, 0, 'L', 1);
            $pdf->Cell(55, 8, number_format(round($previous_paid_amount), 2), 1, 0, 'R');
            $pdf->Ln();

            /* Total amount paid */
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(135, 8,'Amount Received Now', 1, 0, 'L', 1);
            $pdf->Cell(55, 8, number_format(round($receipt_amount), 2), 1, 0, 'R');
            $pdf->Ln();

            /* Total balance amount to be paid */
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(135, 8,'Balance Amount to be Paid', 1, 0, 'L', 1);
            $pdf->Cell(55, 8, number_format(round($balance_due), 2), 1, 0, 'R');
            $pdf->Ln(15);

            /* Cheque Details */
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(20, 8, 'Payment Method');
            $pdf->Ln(5);

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(130, 8, $mode_of_payment);
            $pdf->Ln(10);

            /* Notes */
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(150, 8, 'Notes:');
            $pdf->Ln(4);

            $pdf->SetFont('Arial','',8);
            $pdf->Cell(150, 8, $remarks);
            $pdf->Ln();

	        /* Best Regards & Engex Power */
            $pdf->SetFont('Courier','B',11);
            $pdf->Cell(55, 5, $cpCfg['printBestRegards']);
	        $pdf->SetX(10);
            $pdf->Cell(55, 16, $cpCfg['printEngexPower']);

            /*$pdf->SetFillColor(255,255,255);
            $pdf->Cell(10, 8, $lineItemNumber, 1, 0, 'C', 1);
            $pdf->Cell(80, 8, $row['product_title'], 1, 0, 'L', 1);
            $pdf->Cell(37, 8, $row['part_number'], 1, 0, 'L', 1);
            $pdf->Cell(10, 8, $row['qty'], 1, 0, 'R', 1);
            $pdf->Cell(10, 8, $row['unit'], 1, 0, 'R', 1);
            $pdf->Cell(19, 8, number_format(round($row['unit_price']),2), 1, 0, 'R', 1);
            $pdf->Cell(25, 8, number_format(round($row['amount']),2), 1, 0, 'R', 1);
            $pdf->Ln();*/

			/*if($row['vat'] == 1 && $row['cst'] == 0){
		        $printTaxName = $cpCfg['printTaxName'] ;
				$gsttaxvalue = $cpCfg['amtForGSTCalc'] ;
				$gstvalue = round($row['sub_total']) * $gsttaxvalue / 100;
				$totalvalue = $gstvalue + round($row['sub_total']);
			} else if($row['cst'] == 1 && $row['vat'] == 0){
		        $printTaxName = $cpCfg['printCstText'] ;
				$gsttaxvalue = $cpCfg['printCstinInvoice'] ;
				$gstvalue = round($row['sub_total']) * $gsttaxvalue / 100;
				$totalvalue = $gstvalue + round($row['sub_total']) ;
			} */

            /*$pdf->SetFillColor(255,255,255);
            $pdf->Cell(166, 8, "SUB TOTAL {$currency}", 1, 0, 'R', 1);
            $pdf->Cell(25, 8, $sub_total, 1, 0, 'R', 1);
            $pdf->Ln();

			$printTaxName = $cpCfg['printTaxName'] ;

            $pdf->Cell(166, 8, "ADD: {$printTaxName} {$gsttaxvalue}%", 1, 0, 'R', 1);
            $pdf->Cell(25, 8, number_format(round($gstvalue), 2), 1, 0, 'R', 1);
            $pdf->Ln();

            $totalvalueRounded = round($totalvalue);
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(166, 8, 'TOTAL', 1, 0, 'R', 1);
            $pdf->Cell(25, 8, number_format($totalvalueRounded, 2), 1, 0, 'R', 1);
			$pdf->Ln(20);

			$pdf->Cell(30,15,"(Note : The above receipt is paid for the invoice (INV-1003, INV-1004))",0,0, 'L', 1);
            $pdf->Ln(10);*/

	        /* Creation of media record of the invoice */
	        //$file_name = 'Refund_REF_' . date('Y-m-d') .'.pdf';
	        //$outputPath = realpath($cpCfg['cp.mediaFolder']) . '/temp';

	        //$outputFileName = $outputPath . '/' . $file_name;
	        //$pdf->Output($outputFileName , "F");
            $pdf->Output();

    }

    /**
     *
     */
    function getReceiptPortalDisplay($row){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $formObj = Zend_Registry::get('formObj');

        $rows = "";
        $links= "";
        $sqlAppend = '';
        $exp = array('isEditable' => 1);

        $receiptRec = $fn->getRecordRowByID('receipt', 'order_id', $row['order_id']);

        $SQL = "
        SELECT DISTINCT r.receipt_id
              ,r.*
        FROM receipt r
        LEFT JOIN (invoice_receipt_history irh) ON (r.receipt_id = irh.receipt_id)
        WHERE r.order_id = {$row['order_id']}
              {$sqlAppend}
        ORDER BY r.receipt_id
        ";
        $result   = $db->sql_query($SQL);
        $numRows  = $db->sql_numrows($result);

        $total = '';
        $discount = '';
        $tdCheckBox = '';
        $count = 1;

        while ($rowReceipt = $db->sql_fetchrow($result)) {

            $urlPrint = "index.php?_topRm=finance&module=tradingsg_order&_spAction=printReceipt&receipt_code={$rowReceipt['receipt_code']}&order_id={$row['order_id']}&showHTML=0";

            $expMedia = array('condn' => " AND media_type = 'attachment' AND actual_file_name LIKE '%{$rowReceipt['receipt_code']}%'");
            $mediaRec = $fn->getRecordRowByID('media', 'record_id', $rowReceipt['receipt_id'], $expMedia);
            $mediaLink = "index.php?plugin=common_media&_spAction=saveMedia&room=pms_receipt&recordType=attachment&media_id={$mediaRec['media_id']}&showHTML=0";

            $receipt_date = $fn->getCPDate($rowReceipt['date'], 'd-m-Y');

            $cancelReceiptLink = '';
            if ($rowReceipt['receipt_status'] != 'Cancelled') {
                $cancelReceiptLink = "<a href='#' class='cancelReceipt' receipt_code='{$rowReceipt['receipt_code']}'>Cancel Receipt</a>";
            }
            if ($rowReceipt['receipt_status'] == 'Cancelled') {
                $cancelReceiptLink = "Cancelled";
            }

            $rows .= "
            <tr>
                <td>{$rowReceipt['receipt_code']}</td>
                <td>{$receipt_date}</td>
                <td>{$rowReceipt['mode_of_payment']}</td>
                <td align='right'>{$rowReceipt['amount']}</td>
                <td><a href='{$urlPrint}' target='_blank'>Print Receipt</a></td>
                <td>{$cancelReceiptLink}</td>
            </tr>
            ";
            if($rowReceipt['receipt_status'] == 'Paid'){
                $total += $rowReceipt['amount'];
            }
            $count++;
        }
        $total = "
            <tr style='background-color:#EAEAE8;text-align:center;font-weight:bold;'>
                <td colspan=7>Total : $total</td>
            </tr>
        ";

        $header ="
        <tr style='background-color:#EAEAE8;'>
        <th>Receipt Code</th>
        <th>Receipt Date</th>
        <th>Mode of Payment</th>
        <th>Receipt Amount</th>
        <th>Print</th>
        <th>Cancel</th>
        </tr>
        ";

        $formAction = "index.php?_topRm=finance&module=pms_order&_spAction=generateRefundForm&showHTML=0&order_id={$row['order_id']}&receipt_id={$receiptRec['receipt_id']}";

        $text = "
        <h2>Receipt(s)</h2>
        <tr class=''>
        <td>
            <div id='' class='linkPortalWrapper pms_company__pms_orderLink'>
                <form id='orderItemPrint' class='' method='post'
                action='{$formAction}'>
                <table class='thinlist'>
                    {$header}
                    {$rows}
                </table>
                <input type='hidden' name='order_id' value='{$row['order_id']}' />
                <input type='hidden' name='receipt_id' value='{$receiptRec['receipt_id']}' />
                </form>
            </div>
        </td>
        </tr>
        ";

        return $text;
    }

    /**
     *
     */
    function getPrintProformaOrderItemInvoiceRecord() {
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $tv = Zend_Registry::get('tv');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $searchVar = Zend_Registry::get('searchVar');
        $media = Zend_Registry::get('media');
        $cpPaths = Zend_Registry::get('cpPaths');
        $dbUtil = Zend_Registry::get('dbUtil');

        ini_set('memory_limit', '512M');

        set_time_limit(50000);

        include_once(CP_LIBRARY_PATH.'lib_php/fpdf/fpdf.php');
        include_once(CP_LIBRARY_PATH.'lib_php/fpdf-extra/html2pdf.php');
        include_once(CP_LIBRARY_PATH.'lib_php/fpdf-extra/html_table1.php');
        include_once(CP_LIBRARY_PATH.'lib_php/fpdf-extra/mc_table.php');

        //$pdf = new MYPDF();
        $pdf = new PDF_MC_Table();
		$pdf->AddPage();
		$pdf->SetFont('Arial','',11);

        $order_id = $fn->getReqParam('id');

		$SQL = "
		SELECT oi.*
			  ,o.order_id
			  ,o.creation_date
			  ,o.notes
			  ,o.invoice_terms
  			  ,o.shipping_address1
			  ,o.shipping_first_name
			  ,o.shipping_address2
			  ,o.shipping_address_city
			  ,o.shipping_address_state
			 ,(SELECT gc.name FROM geo_country gc
			     WHERE gc.country_code = o.shipping_address_country)
			     AS shipping_address_country
              ,c.company_name
              ,c.address_flat
              ,c.address_street
              ,c.address_town
              ,c.address_state
              , (SELECT gc.name FROM geo_country gc
                 WHERE gc.country_code = c.address_country)
                AS address_country
              ,c.billing_address_flat
              ,c.billing_address_street
              ,c.billing_address_town
              ,c.billing_address_state
              , (SELECT gc.name FROM geo_country gc
                 WHERE gc.country_code = c.billing_address_country)
                AS billing_address_country
              ,c.fax
              ,c.phone
              ,c.tin_no
              ,c.cst_no
              ,oi.qty * oi.unit_price AS amount
              ,(SELECT SUM(oi.qty * oi.unit_price) FROM order_item oi
               WHERE oi.order_id = o.order_id) AS sub_total
		FROM `order_item` oi
		LEFT JOIN `order` o ON (o.order_id = oi.order_id)
        LEFT JOIN company c ON (c.company_id = o.company_id)
        WHERE o.order_id = '{$order_id}'
		";


        $result = $db->sql_query($SQL);

        $numRows  = $db->sql_numrows($result);

        $today = date("Y-m-d");
		if ($numRows == 0){
            $pdf->SetXY(30,30);
            $pdf->Cell(50, 20, "Please set the values for your Order and print the PDF");
			$pdf->Output();
			return;
		}

        $count = 0;
        $lineItemNumber = 1;  // To increment the line item in receipt
		$totalvalue = '';



        //============================================================================= //
        $pdf->SetFont('Arial','',9);
        //syed:multi text code to set width of each column and alignment
        $pdf->SetWidths(array(10, 80, 30, 12, 28, 30));
        $pdf->SetAligns(array('L', 'L', 'L', 'L', 'L', 'R'));

        while ($row = $db->sql_fetchrow($result)) {
            if ($count == 0){
                /* Logo of the institution */
                $pdf->Image('images/logo-print.png',10,5,45);

                $pdf->SetXY(105,8);
                $pdf->Cell(50, 2, 'Authorized Distributor of:');
                //$pdf->Image('images/parker.jpg',152,11, 25);

                //$pdf->Image('images/gse.png',42,25, 25);
                $orderDate   = $fn->getCPDate($row['creation_date'], 'd-m-Y');

                /* Company address */
                //Address to be got from settings
                /*$pdf->SetXY(130,0);
                $pdf->SetFont('Arial','B',9);
                $pdf->Cell(50, 20, $cpCfg['cp.companyName']);
                $pdf->Ln(5);
                $pdf->SetXY(130,5);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf1']);
                $pdf->Ln(5);
                $pdf->SetXY(130,10);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf2']);
                $pdf->Ln(5);
                $pdf->SetXY(130,15);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf3']);
                $pdf->Ln(5);
                $pdf->SetXY(130, 20);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf4']);
                $pdf->Ln(5);
                $pdf->SetXY(130,25);
                $pdf->Cell(50, 20, $cpCfg['printEmailAddress']);
                $pdf->Ln(5);
                $pdf->SetXY(130,30);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf7']);
                $pdf->Ln(5);
                $pdf->SetXY(130,35);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf6']);*/

                $pdf->SetXY(105,12);
                $pdf->MultiCell(100, 4, strtoupper($cpCfg['cp.addressPdf1']. ' ' . $cpCfg['cp.addressPdf2']));
                //$pdf->Cell(30, 20, $cpCfg['cp.addressPdf1'] . ' ' . $cpCfg['cp.addressPdf2']);
                $pdf->SetXY(105,11);
				$pdf->Cell(30,31, $cpCfg['cp.addressPdf3'] . ' '. $cpCfg['cp.addressPdf4']);
                $pdf->Ln(5);

                $pdf->SetXY(105,21);
                $pdf->Cell(50, 20, $cpCfg['printEmailAddress']);
                $pdf->Ln(5);
                $pdf->SetXY(105,26);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf7']);
                $pdf->Ln(5);
                $pdf->SetXY(105,31);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf6']);

                /* Header */
                $pdf->SetFont('Arial','BU',9);
                $pdf->SetXY(80, 45);
                $pdf->Cell(50, 20, " PROFORMA INVOICE", 0, 0, 'C');
                $pdf->Ln(15);

                /* ORDER DETAILS */
                $pdf->SetFont('Arial','B',9);
                $pdf->SetFillColor(254,203,156);
                $pdf->Cell(47.5,8,"ORDER NO :",1,0, 'L', 1);
                $pdf->SetFont('Arial','',9);
                $pdf->SetFillColor(255,255,255);
	            $pdf->Cell(47.5, 8, $row['order_id'], 1, 0, 'L', 1);
                $pdf->SetFont('Arial','B',9);
                $pdf->SetFillColor(254,203,156);
                $pdf->Cell(47.5,8,"DATE :",1,0, 'L', 1);
                $pdf->SetFont('Arial','',9);
                $pdf->SetFillColor(255,255,255);
	            $pdf->Cell(47.5, 8, $orderDate, 1, 0, 'L', 1);
                $pdf->SetFillColor(254,203,156);
                $pdf->Ln(12);

                /* Company Details*/

				if ($row['shipping_address1'] != ''
					|| $row['shipping_address2'] != ''
					|| $row['shipping_address_city'] != ''
					|| $row['shipping_address_state'] != ''
					|| $row['shipping_address_country'] != '') {
						//Delivery Address Fields in Order
						$deliveryAddressFlat 	= $row['shipping_address1'];
						$deliveryAddressStreet 	= $row['shipping_address2'];
						$deliveryAddressTown 	= $row['shipping_address_city'];
						$deliveryAddressState 	= $row['shipping_address_state'];
						$deliveryAddressCountry = $row['shipping_address_country'];
						$deliveryCompanyName 	= $row['shipping_first_name'];
				} else {
					//Delivery Address Fields in client
					$deliveryAddressFlat 	= $row['address_flat'];
					$deliveryAddressStreet 	= $row['address_street'];
					$deliveryAddressTown 	= $row['address_town'];
					$deliveryAddressState 	= $row['address_state'];
					$deliveryAddressCountry = $row['address_country'];
					$deliveryCompanyName 	= $row['company_name'];
				}

                /* Company Details*/

               // $date = $fn->getCPDate($row['delivery_date'], 'd-m-Y');

                $pdf->SetFont('Arial','B',9);
                $pdf->SetFillColor(254,203,156);
                $pdf->Cell(95,8,"ORDER TO",1,0, 'L', 1);
                $pdf->Cell(95,8,"DELIVERY TO",1,0, 'L', 1);
                $pdf->Ln();
                $pdf->SetFillColor(255,255,255);
                $pdf->SetFont('Arial','',9);

                $pdf->SetFont('Arial','',9);
                $pdf->Cell(95, 8, $row['company_name'],'LR', 0, 'L', 1);
            	$pdf->Cell(95, 8, $deliveryCompanyName , 'LR', 0, 'L', 1);
                $pdf->Ln();
                $pdf->SetFont('Arial','',9);
            	$pdf->Cell(95, 5, $row['billing_address_flat'], 'LR', 0, 'L', 1);
	            $pdf->Cell(95, 5, $deliveryAddressFlat, 'LR', 0, 'L', 1);
                $pdf->Ln();
                $pdf->SetFont('Arial','',9);
	            $pdf->Cell(95, 5, $row['billing_address_street'], 'LR', 0, 'L', 1);
	            $pdf->Cell(95, 5, $deliveryAddressStreet, 'LR', 0, 'L', 1);
                $pdf->Ln();
	        	$pdf->Cell(95, 5, $row['billing_address_town'], 'LR', 0, 'L', 1);
	            $pdf->Cell(95, 5, $deliveryAddressTown, 'LR', 0, 'L', 1);
                $pdf->Ln();
                $pdf->SetFont('Arial','',9);
	            $pdf->Cell(95, 5, $row['billing_address_country'] .' - '. $row['billing_address_state'], 'LR', 0, 'L', 1);
                $pdf->SetFont('Arial','',9);
	            $pdf->Cell(95, 5, $deliveryAddressCountry .' - '. $deliveryAddressState, 'LR', 0, 'L', 1);
                $pdf->Ln();
                $pdf->Cell(95, 8, 'TIN NO:' . $row['tin_no'], 'LR', 0, 'L', 1);
                $pdf->Cell(95, 8, 'TIN NO:' .$row['tin_no'], 'LR', 0, 'L', 1);
                $pdf->Ln(6);
                $pdf->Cell(95, 8, 'CST NO:' . $row['cst_no'], 'BLR', 0, 'L', 1);
                $pdf->Cell(95, 8, 'CST NO:' .$row['cst_no'], 'BLR', 0, 'L', 1);

                $pdf->Ln(10);

				$orderTerms = $row['invoice_terms'];
				$bank 		= $cpCfg['cp.bankDetails'];

	            $pdf->SetFont('Arial','B',9);
	            $pdf->SetFillColor(254,203,156);
	            $pdf->Cell(95,8,"TERMS",1,0, 'L', 1);
	            $pdf->Cell(95,8,"BANK DETAILS",1,0, 'L', 1);
                $pdf->SetFont('Arial','',9);
	            $pdf->SetFillColor(255,255,255);
                $pdf->SetXY(10,132);
	            $pdf->drawTextBox($orderTerms, 95, 32, 'L', 'C', 1);
                $pdf->SetXY(105,132);
	            $pdf->drawTextBox(strip_tags($bank), 95, 32, 'L', 'C', 'BLR');
	            $pdf->Ln(20);

                /* List of order items header */
                $pdf->SetFont('Arial','B',9);
                $pdf->SetFillColor(254,203,156);
                $pdf->Cell(10,8,"S.NO",1,0, 'C', 1);
                $pdf->Cell(80,8,"PRODUCT",1,0, 'C', 1);
                $pdf->Cell(30,8,"PART NUMBER",1,0, 'C', 1);
                $pdf->Cell(12,8,"QTY",1,0, 'C', 1);
                $pdf->Cell(28,8,"UNIT PRICE",1,0, 'C', 1);
                $pdf->Cell(30,8,"AMOUNT",1,0, 'C', 1);
                $pdf->Ln();
            }

            //===================================MAIN TABLE============================= //
            $pdf->SetFont('Arial','',9);
            $pdf->SetFillColor(255,255,255);

            $pdf->Row(array($lineItemNumber, $row['item_title'] , $row['part_number'], $row['qty'], number_format(round($row['unit_price']),2), number_format(round($row['amount']),2) ));


            //$pdf->Ln();

            $count++;
            $lineItemNumber++;
            $sub_total 	= $row['sub_total'];
			$totalvalue = $sub_total;
           	$orderNotes = $row['notes'];

        }
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(160, 8, "SUB TOTAL", 1, 0, 'R', 1);
            $pdf->Cell(30, 8, number_format(round($sub_total),2), 1, 0, 'R', 1);
            $pdf->Ln();

            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(160, 8, 'TOTAL', 1, 0, 'R', 1);
            $pdf->Cell(30, 8, number_format(round($totalvalue), 2), 1, 0, 'R', 1);
			$pdf->Ln(20);

            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(150, 8, 'NOTE: ');
            $pdf->Ln(6);
            $pdf->SetFont('Arial','',9);
            $pdf->drawTextBox($orderNotes, 180, 55, 'L', 'T', 0);
            $pdf->Ln(15);

	        /* Best Regards & Engex Power */
            $pdf->Cell(55, 5, $cpCfg['printBestRegards']);
	        $pdf->SetX(10);
            $pdf->Cell(55, 16, $cpCfg['printEngexPower']);

			$pdf->Output();

    }

    /**
     */
    function getInvoicePortalDisplayDetail($row){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $formObj = Zend_Registry::get('formObj');
        $dbUtil = Zend_Registry::get('dbUtil');

        $rows  = "";
        $rowsPvt  = "";
        $links = "";
        $sqlAppend = "";

        $status = $fn->getReqParam('status');

        if ($status) {
            $sqlAppend .= "AND i.status = '{$status}'";
        }

        $_SESSION['selectedInvoiceIds'] = array();
        $exp = array('isEditable' => 1);

        $SQL = "
        SELECT i.*
            ,(
            SELECT GROUP_CONCAT(r.receipt_code ORDER BY r.receipt_code SEPARATOR ', ')
            FROM receipt r, invoice_receipt_history invrecpt
            WHERE r.receipt_id = invrecpt.receipt_id
            AND i.invoice_id = invrecpt.invoice_id
            ) AS receipt_codes_history
            {$sqlAppend}
        FROM invoice i
        WHERE i.order_id = {$row['order_id']}
          AND i.invoice_type = 'Client'
        ORDER BY i.invoice_id
        ";

        $result   = $db->sql_query($SQL);
        $discount = '';
        $tdCheckBox = '';
        $checkBoxStatus = '';
        $count = 1;
        $invoice_code = '';
        $add_registration_fee = '';
        $invoice_hist_amount  = '';

        while ($rowInvoice = $db->sql_fetchrow($result)) {
            $gstvalue = '';
            $gsttaxvalue = '';
            $pfvalue = '';
            $frieghtValue = '';
            $total = '';
            $selectedValuePaid   = '';
            $selectedValueDue    = '';
            $selectedValueCancel = '';

            $urlPrint 		= "index.php?_topRm=finance&module=tradingsg_order&_spAction=printInvoiceRecord&invoice_code={$rowInvoice['invoice_code']}&invoice_id={$rowInvoice['invoice_id']}&invoice_type=normal&footer_logo=yes&showHTML=0";
            $urlProforma 		= "index.php?_topRm=finance&module=tradingsg_order&_spAction=printInvoiceRecord&invoice_code={$rowInvoice['invoice_code']}&invoice_id={$rowInvoice['invoice_id']}&invoice_type=proforma&footer_logo=yes&showHTML=0";
            $urlTransporter = "index.php?_topRm=finance&module=tradingsg_order&_spAction=printInvoiceRecord&invoice_code={$rowInvoice['invoice_code']}&invoice_id={$rowInvoice['invoice_id']}&invoice_type=transporter&footer_logo=yes&showHTML=0";
            $urlExtra  		= "index.php?_topRm=finance&module=tradingsg_order&_spAction=printInvoiceRecord&invoice_code={$rowInvoice['invoice_code']}&invoice_id={$rowInvoice['invoice_id']}&invoice_type=extra&footer_logo=yes&showHTML=0";


            $expMedia = array('condn' => " AND media_type = 'attachment' AND actual_file_name LIKE '%{$rowInvoice['invoice_code']}%'");
            $mediaRec = $fn->getRecordRowByID('media', 'record_id', $rowInvoice['invoice_id'], $expMedia);
            $mediaLink = "index.php?plugin=common_media&_spAction=saveMedia&room=tradingsg_invoice&recordType=attachment&media_id={$mediaRec['media_id']}&showHTML=0";

            if($rowInvoice['status'] != 'Cancelled'){
                $total += $rowInvoice['invoice_amount'];
            }

            //if($invoice_code == '' || $invoice_code != $rowInvoice['invoice_code']){

                /* Half way done. Need to do submit functioanlity. Move $editRow = ''; from below to this comment line */
                $editRow = '<td></td>';
                if ($rowInvoice['status'] == 'Due'
                 || $rowInvoice['status'] == ''
                 || $rowInvoice['status'] == 'Partial Payment'
                ) {
                    $editURL = "index.php?_topRm=finance&module=tradingsg_order&_spAction=editInvoiceForm&showHTML=0&invoice_id={$rowInvoice['invoice_id']}&order_id={$row['order_id']}";
                    $editRow = "<td><a href='{$editURL}' id='editInvoice'>Edit</a></td>";
                }

                $cancelInvoiceLink = '';
                if ($rowInvoice['status'] != 'Cancelled'){
                    $cancelInvoiceLink = "<a href='#' class='cancelInvoice' invoice_code='{$rowInvoice['invoice_code']}'>Cancel Invoice</a>";
                }

                $invoice_date = $fn->getCPDate($rowInvoice['invoice_date'], 'd-m-Y');
                $totalvalueRounded = number_format(round($total),2);

                $rows .= "
                <tr>
                    <td>{$rowInvoice['invoice_code']}</td>
                    <td>{$rowInvoice['status']}</td>
                    <td>{$invoice_date}</td>
                    <td align='right'>$totalvalueRounded</td>
                    <td><a href='{$urlPrint}' target='_blank'>Print Invoice</a></td>
                    <td><a href='{$urlExtra}' target='_blank'>Duplicate Invoice</a></td>
                    <td>{$cancelInvoiceLink}</td>
                </tr>
                ";
            }

            //$invoice_code = $rowInvoice['invoice_code'];
        //}

        $header ="
        <tr style='background-color:#EAEAE8;'>
        <th>Invoice Code</th>
        <th>Status</th>
        <th>Invoice Date</th>
        <th>Amount</th>
        <th>Print</th>
        <th>Invoice - Duplicate</th>
        <th>Cancel</th>
        </tr>
        ";

        $text = "
        <table class='thinlist'>
            {$header}
            {$rows}
            {$rowsPvt}
        </table>
        ";

        return $text;
    }


  /**
     */
    function getCreditPortalDisplay($row){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $formObj = Zend_Registry::get('formObj');
        $dbUtil = Zend_Registry::get('dbUtil');

        $formAction = '';

        $text = "
        <tr class=''>
        <td>
            <div id='' class='invoiceDisplay'>
                <h2>Credit Note(s)</h2>
                <form id='orderItemPrint' class='' method='post' action='{$formAction}'>
                    <div id='invoicePortalOuter'>
                        {$this->getCreditPortalDisplayDetail($row)}
                    </div>
                </form>
            </div>
        </td>
        </tr>
        ";

        return $text;
    }

    
    function getCreditPortalDisplayDetail($row){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $formObj = Zend_Registry::get('formObj');
        $dbUtil = Zend_Registry::get('dbUtil');

        $rows  = "";
        $rowsPvt  = "";
        $links = "";
        $sqlAppend = "";

        $status = $fn->getReqParam('status');

        if ($status) {
            $sqlAppend .= "AND i.status = '{$status}'";
        }

        $_SESSION['selectedInvoiceIds'] = array();
        $exp = array('isEditable' => 1);

        $SQL = "
        SELECT i.*
            
            {$sqlAppend}
        FROM credit_note i
        WHERE i.order_id = {$row['order_id']}
          AND i.invoice_type = 'Client'
        ORDER BY i.credit_note_id
        ";

        $result   = $db->sql_query($SQL);
        $discount = '';
        $tdCheckBox = '';
        $checkBoxStatus = '';
        $count = 1;
        $invoice_code = '';
        $add_registration_fee = '';
        $invoice_hist_amount  = '';

        while ($rowInvoice = $db->sql_fetchrow($result)) {
            $gstvalue = '';
            $gsttaxvalue = '';
            $pfvalue = '';
            $frieghtValue = '';
            $total = '';
            $selectedValuePaid   = '';
            $selectedValueDue    = '';
            $selectedValueCancel = '';

            $urlPrint       = "index.php?_topRm=finance&module=tradingsg_order&_spAction=printCreditRecord&invoice_code={$rowInvoice['invoice_code']}&credit_note_id={$rowInvoice['credit_note_id']}&invoice_type=normal&footer_logo=yes&showHTML=0";
           
           
            if($rowInvoice['status'] != 'Cancelled'){
                $total += $rowInvoice['invoice_amount'];
            }


                $cancelInvoiceLink = '';
                if ($rowInvoice['status'] != 'Cancelled'){
                    $cancelInvoiceLink = "<a href='#' class='cancelCreditNote' invoice_code='{$rowInvoice['invoice_code']}'>Cancel Credit Note</a>";
                }

                $invoice_date = $fn->getCPDate($rowInvoice['invoice_date'], 'd-m-Y');
                $totalvalueRounded = number_format(round($total),2);

                $rows .= "
                <tr>
                    <td>{$rowInvoice['invoice_code']}</td>
                    <td>{$rowInvoice['status']}</td>
                    <td>{$invoice_date}</td>
                    <td align='right'>$totalvalueRounded</td>
                    <td><a href='{$urlPrint}' target='_blank'>Print Credit Note</a></td>
                    <td>{$cancelInvoiceLink}</td>
                </tr>
                ";
            }

            //$invoice_code = $rowInvoice['invoice_code'];
        //}

        $header ="
        <tr style='background-color:#EAEAE8;'>
        <th>Credit Code</th>
        <th>Status</th>
        <th>Credit Date</th>
        <th>Amount</th>
        <th>Print</th>
        <th>Cancel</th>
        </tr>
        ";

        $text = "
        <table class='thinlist'>
            {$header}
            {$rows}
            {$rowsPvt}
        </table>
        ";

        return $text;
    }

      /**
     */
    function getDebitPortalDisplay($row){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $formObj = Zend_Registry::get('formObj');
        $dbUtil = Zend_Registry::get('dbUtil');

        $formAction = '';

        $text = "
        <tr class=''>
        <td>
            <div id='' class='invoiceDisplay'>
                <h2>Debit Note(s)</h2>
                <form id='orderItemPrint' class='' method='post' action='{$formAction}'>
                    <div id='invoicePortalOuter'>
                        {$this->getDebitPortalDisplayDetail($row)}
                    </div>
                </form>
            </div>
        </td>
        </tr>
        ";

        return $text;
    }

    
    function getDebitPortalDisplayDetail($row){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $formObj = Zend_Registry::get('formObj');
        $dbUtil = Zend_Registry::get('dbUtil');

        $rows  = "";
        $rowsPvt  = "";
        $links = "";
        $sqlAppend = "";

        $status = $fn->getReqParam('status');

        if ($status) {
            $sqlAppend .= "AND i.status = '{$status}'";
        }

        $_SESSION['selectedInvoiceIds'] = array();
        $exp = array('isEditable' => 1);

        $SQL = "
        SELECT i.*
            
            {$sqlAppend}
        FROM debit_note i
        WHERE i.order_id = {$row['order_id']}
          AND i.invoice_type = 'Client'
        ORDER BY i.debit_note_id
        ";

        $result   = $db->sql_query($SQL);
        $discount = '';
        $tdCheckBox = '';
        $checkBoxStatus = '';
        $count = 1;
        $invoice_code = '';
        $add_registration_fee = '';
        $invoice_hist_amount  = '';

        while ($rowInvoice = $db->sql_fetchrow($result)) {
            $gstvalue = '';
            $gsttaxvalue = '';
            $pfvalue = '';
            $frieghtValue = '';
            $total = '';
            $selectedValuePaid   = '';
            $selectedValueDue    = '';
            $selectedValueCancel = '';

            $urlPrint       = "index.php?_topRm=finance&module=tradingsg_order&_spAction=printDebitRecord&invoice_code={$rowInvoice['invoice_code']}&debit_note_id={$rowInvoice['debit_note_id']}&invoice_type=normal&footer_logo=yes&showHTML=0";
           
           
            if($rowInvoice['status'] != 'Cancelled'){
                $total += $rowInvoice['invoice_amount'];
            }


                $cancelInvoiceLink = '';
                if ($rowInvoice['status'] != 'Cancelled'){
                    $cancelInvoiceLink = "<a href='#' class='cancelDebitNote' invoice_code='{$rowInvoice['invoice_code']}'>Cancel Debit Note</a>";
                }

                $invoice_date = $fn->getCPDate($rowInvoice['invoice_date'], 'd-m-Y');
                $totalvalueRounded = number_format(round($total),2);

                $rows .= "
                <tr>
                    <td>{$rowInvoice['invoice_code']}</td>
                    <td>{$rowInvoice['status']}</td>
                    <td>{$invoice_date}</td>
                    <td align='right'>$totalvalueRounded</td>
                    <td><a href='{$urlPrint}' target='_blank'>Print Debit Note</a></td>
                    <td>{$cancelInvoiceLink}</td>
                </tr>
                ";
            }

            //$invoice_code = $rowInvoice['invoice_code'];
        //}

        $header ="
        <tr style='background-color:#EAEAE8;'>
        <th>Debit Code</th>
        <th>Status</th>
        <th>Debit Date</th>
        <th>Amount</th>
        <th>Print</th>
        <th>Cancel</th>
        </tr>
        ";

        $text = "
        <table class='thinlist'>
            {$header}
            {$rows}
            {$rowsPvt}
        </table>
        ";

        return $text;
    }

    /**
     *
     */
    function getQuickSearch() {
        $db = Zend_Registry::get('db');
        $dbUtil = Zend_Registry::get('dbUtil');
        $cpUtil = Zend_Registry::get('cpUtil');
        $tv = Zend_Registry::get('tv');
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $formObj = Zend_Registry::get('formObj');
        $ln = Zend_Registry::get('ln');

        $creation_date1 = $fn->getReqParam('creation_date_1');
        $creation_date2 = $fn->getReqParam('creation_date_2');
        $order_status   = $fn->getReqParam('order_status');
        $gst_status   = $fn->getReqParam('gst_status');
        $shipment_status   = $fn->getReqParam('shipment_status');
        $shipping_address_country_code = $fn->getReqParam('shipping_address_country_code');

        $gstStatusArr = array(
            "GST ON"
           ,"GST OFF"
        );

        /*
        <!--<td class='fieldValue'>
            <select name='shipping_address_country_code'>
                <option value=''>Country</option>
                {$dbUtil->getDropDownFromSQLCols2($db, $fn->getGeoCountrySQL(), $shipping_address_country_code)}
            </select>
        </td>-->
        */

        $text = "
        <td>
            {$formObj->getDateRangeRow('Creation Date:', 'creation_date', $creation_date1, $creation_date2)}
        </td>
        <td class='fieldValue'>
            <select name='order_status'>
                <option value=''>Status</option>
                {$cpUtil->getDropDown1($cpCfg['m.ecommerce.order.statusArr'], $order_status)}
            </select>
        </td>
        <td class='fieldValue'>
            <select name='gst_status'>
                <option value=''>GST Search</option>
                {$cpUtil->getDropDown1($gstStatusArr, $gst_status)}
            </select>
        </td>
        ";


        return $text;
    }
}