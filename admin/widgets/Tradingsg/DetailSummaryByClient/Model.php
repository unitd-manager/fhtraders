<?
class CPL_Admin_Widgets_Tradingsg_DetailSummaryByClient_Model extends CP_Admin_Widgets_Tradingsg_DetailSummaryByClient_Model
{
    /**
     *
     */
    function getSQL(){
        $cpCfg = Zend_Registry::get('cpCfg');

	// **** THIS CONDITION HAS BEEN USED ONLY FOR MULTI LOCATION SITE IN BLOSSOMS **** \\

        $SQL = "
        SELECT DISTINCT c.company_id
              ,c.company_name
              ,i.invoice_id
              ,o.gst_status
        FROM `invoice` i
        LEFT JOIN `order` o ON (o.order_id = i.order_id)
        LEFT JOIN `company` c ON (c.company_id = o.company_id)
        ";
        return $SQL;
    }

    /**
     *
     */
    function setSearchVar() {
        $fn    = Zend_Registry::get('fn');
        $cpCfg = Zend_Registry::get('cpCfg');

        $searchVar = $this->searchVar;
        $searchVar->mainTableAlias = 'o';

        $start_date     = $fn->getReqParam('start_date');
        $end_date       = $fn->getReqParam('end_date');
        $monthVal       = $fn->getReqParam('month');
        $yearVal        = $fn->getReqParam('year');
        $current_date   = date('Y-m-d');
        $month          = date('m');
        $year           = date('Y');
        $company_id     = $fn->getReqParam('company_id');
        $gst_status     = $fn->getReqParam('gst_status');

        if ($start_date != '' && $end_date == '') {
            $searchVar->sqlSearchVar[] = "i.invoice_date >= '{$start_date}' AND i.invoice_date <= '{$current_date}'";
        } else if ($start_date == '' && $end_date != ''){
            $start_date = $year . '-' . $month . '-' . '01';
            $searchVar->sqlSearchVar[] = "i.invoice_date >= '{$start_date}' AND i.invoice_date <= '{$end_date}'";
        } else if ($start_date != '' && $end_date != '') {
            $searchVar->sqlSearchVar[] = "i.invoice_date >= '{$start_date}' AND i.invoice_date <= '{$end_date}'";
        } else {
            if($yearVal != '') {
                $year = $yearVal;
            }

            if($monthVal != '') {
                $month = $monthVal;
            }

            $start_date = $year . '-' . $month . '-' . '01';
            $end_date = $year . '-' . $month . '-' . '31';
            $searchVar->sqlSearchVar[] = "i.invoice_date >= '{$start_date}' AND i.invoice_date <= '{$end_date}'";
        }

        $searchVar->sqlSearchVar[] = "i.status != 'Cancelled'";

		if ($company_id != '') {
            $searchVar->sqlSearchVar[] = "c.company_id = {$company_id}";
		}

        if ($gst_status == 'GST ON') {
            $searchVar->sqlSearchVar[] = "o.gst_status = 'ON'";
        }

        if ($gst_status == 'GST OFF') {
            $searchVar->sqlSearchVar[] = "o.gst_status = 'OFF'";
        }

        if ($cpCfg['cp.hasMultiUniqueSites']){
            $cpSiteIdSession = $fn->getSessionParam('cp_site_id');
            $searchVar->sqlSearchVar[] = "i.site_id = '{$cpSiteIdSession}'";
        }

        $searchVar->sortOrder = "c.company_name ASC";
    }

    /**
     *
     * @param <type> $SQL
     * @return <type>
     */
    function getDataArray() {
        $ln = Zend_Registry::get('ln');
        $fn = Zend_Registry::get('fn');
        $dbUtil = Zend_Registry::get('dbUtil');

        $modelHelper = Zend_Registry::get('modelHelper');
        $dataArray = $modelHelper->getWidgetDataArray($this->controller, 'tradingsg_detailSummaryByClient');

        $this->dataArray = $dataArray;
        return $this->dataArray;
    }

    /**
     */
    function getExportToExcel(){
        $db = Zend_Registry::get('db');
        $cpCfg = Zend_Registry::get('cpCfg');
        $tv = Zend_Registry::get('tv');
        $cpUtil = Zend_Registry::get('cpUtil');
        $dateUtil = Zend_Registry::get('dateUtil');
        $fn = Zend_Registry::get('fn');

        $location_id    = $fn->getReqParam('location_id');
        $company_id     = $fn->getReqParam('company_id');

        $rows = '';


        set_time_limit(50000);
        ini_set('memory_limit', '512M');

        require_once("PHPExcel.php");
        include 'PHPExcel/IOFactory.php';

        $file_name = "Detail_Summary_by_Client__" . date("d-m-Y") . ".xls";

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename={$file_name}");
        header("Content-Transfer-Encoding: binary ");

        $objPHPExcel = new PHPExcel();

        //--------------------------------------------------//
        $rowc = 1;
        $colc = 0;
        $appendSql = '';
        $actSheet = &$objPHPExcel->getActiveSheet();

        if($company_id == ''){
            $actSheet->setCellValueByColumnAndRow($colc++, $rowc, 'Client Name');
        }

        $actSheet->setCellValueByColumnAndRow($colc++, $rowc, 'Date');
        $actSheet->setCellValueByColumnAndRow($colc++, $rowc, 'Invoice Code');
        $actSheet->setCellValueByColumnAndRow($colc++, $rowc, 'Invoice Amount');
        $actSheet->setCellValueByColumnAndRow($colc++, $rowc, 'Paid');
        $actSheet->setCellValueByColumnAndRow($colc++, $rowc, 'Amount Due');
        if($cpCfg['cp.hasMultiUniqueSites']){
            $actSheet->setCellValueByColumnAndRow($colc++, $rowc, 'Location');
         }
        /******************** FORMAT HEADER *******************/
        $headStyle = array(
            'font' => array('bold' => true)
        );

        $lastCol    = $actSheet->getHighestColumn();
        $lastColInd = PHPExcel_Cell::columnIndexFromString($lastCol);
        $actSheet->getStyle("A1:{$lastCol}1")->applyFromArray($headStyle);

        for ($i=0; $i < $lastColInd; $i++){
            $colAlphabet = PHPExcel_Cell::stringFromColumnIndex($i);
            $actSheet->getColumnDimension($colAlphabet)->setAutoSize(true);
        }

        $start_date     = $fn->getReqParam('start_date');
        $end_date       = $fn->getReqParam('end_date');
        $monthVal       = $fn->getReqParam('month');
        $yearVal        = $fn->getReqParam('year');
        $current_date   = date('Y-m-d');
        $month          = date('m');
        $year           = date('Y');

        $companyId = '';
        if ($company_id != '') {
            $companyId = "AND c.company_id = {$company_id}";
        }

        $totalBalanceAmount = 0 ;
        $paymentTotalofInvoiceAmount = 0 ;
        $totalPaidAmount = 0 ;

        $startDateAppendSql = '';
        if ($start_date != '' && $end_date == '') {
            $startDateAppendSql = "AND inv.invoice_date >= '{$start_date}' AND inv.invoice_date <= '{$current_date}'";
        } else if ($start_date == '' && $end_date != ''){
            $start_date = $year . '-' . $month . '-' . '01';
            $startDateAppendSql = "AND inv.invoice_date >= '{$start_date}' AND inv.invoice_date <= '{$end_date}'";
        } else if ($start_date != '' && $end_date != '') {
            $startDateAppendSql = "AND inv.invoice_date >= '{$start_date}' AND inv.invoice_date <= '{$end_date}'";
        } else {
            if($yearVal != '') {
                $year = $yearVal;
            }

            if($monthVal != '') {
                $month = $monthVal;
            }

            $start_date = $year . '-' . $month . '-' . '01';
            $end_date = $year . '-' . $month . '-' . '31';
            $startDateAppendSql = "AND inv.invoice_date >= '{$start_date}' AND inv.invoice_date <= '{$end_date}'";
        }

        $appendSqlSite = "";
        if ($cpCfg['cp.hasMultiUniqueSites']){
            $cpSiteIdSession = $fn->getSessionParam('cp_site_id');
            $appendSqlSite   = "  AND inv.site_id = '{$cpSiteIdSession}'";
        }

        $SQL = "
        SELECT DISTINCT c.company_id
              ,c.company_name
              ,inv.invoice_id
        FROM `invoice` inv
        LEFT JOIN `order` o ON (o.order_id = inv.order_id)
        LEFT JOIN `company` c ON (c.company_id = o.company_id)
        WHERE inv.status != 'Cancelled'
          {$companyId}
          {$startDateAppendSql}
          {$appendSqlSite}
        ORDER BY c.company_name ASC
        ";

        $result1 = $db->sql_query($SQL);

        while ($row = $db->sql_fetchrow($result1)) {
            $appendSql = "";
            if($row['company_id'] != ''){
                $appendSql = "AND o.company_id = {$row['company_id']}";
            }

            $SQLInv = "
            SELECT inv.*
                  ,o.order_id
                  ,(SELECT SUM(invh.amount)
                    FROM invoice_receipt_history invh
                    LEFT JOIN (receipt rcp) ON (invh.receipt_id = rcp.receipt_id)
                    WHERE invh.invoice_id = inv.invoice_id
                      AND rcp.receipt_status = 'Paid'
                  ) AS total_amount_paid
            FROM invoice inv
            LEFT JOIN `order` o ON (o.order_id = inv.order_id)
            WHERE inv.status != 'Cancelled'
              AND inv.invoice_id = {$row['invoice_id']}
              AND inv.invoice_amount > 0
              {$appendSql}
              {$startDateAppendSql}
              {$appendSqlSite}
            ";

            $resultInv = $db->sql_query($SQLInv);
            $invoice_amount  = '';

                if ($cpCfg['cp.hasMultiUniqueSites']  == 1) {
                    $siteRecSql ="
                    SELECT s.title
                    FROM site s
                    WHERE s.site_id = {$row['site_id']}
                    ";

                    $resultSiteLocation = $db->sql_query($siteRecSql);
                    $rowSite            = $db->sql_fetchrow($resultSiteLocation);
                 }

            while ($rowInv = $db->sql_fetchrow($resultInv)) {

                $colc = 0;
                $rowc++;

                $invoiceCode = $rowInv['invoice_code'];

                $invoice_amount = $rowInv['invoice_amount'];
                $balance_amount  = $invoice_amount - $rowInv['total_amount_paid'];
                $paymentTotalofInvoiceAmount += $invoice_amount;
                $totalBalanceAmount += $balance_amount;
                $totalPaidAmount += $rowInv['total_amount_paid'];
                $invoice_amount = number_format($invoice_amount);
                $balance_amount = number_format($balance_amount);
                $rowInv['total_amount_paid'] = number_format($rowInv['total_amount_paid']);

                if($company_id == ''){
                    $actSheet->setCellValueByColumnAndRow($colc++, $rowc, $row['company_name']);
                }
                $actSheet->setCellValueByColumnAndRow($colc++, $rowc, $fn->getCPDate($rowInv['invoice_date'], 'd-m-Y'));
                $actSheet->setCellValueByColumnAndRow($colc++, $rowc, $invoiceCode);
                $actSheet->setCellValueByColumnAndRow($colc++, $rowc, $invoice_amount);
                $actSheet->setCellValueByColumnAndRow($colc++, $rowc, $rowInv['total_amount_paid']);
                $actSheet->setCellValueByColumnAndRow($colc++, $rowc, $balance_amount);
            }
        }

        $colc = 0;
        $rowc++;

        if($company_id == ''){
           $actSheet->setCellValueByColumnAndRow($colc++, $rowc, '');
        }
        $actSheet->setCellValueByColumnAndRow($colc++, $rowc, '');
        $actSheet->setCellValueByColumnAndRow($colc++, $rowc, 'Total');
        $actSheet->setCellValueByColumnAndRow($colc++, $rowc, number_format($paymentTotalofInvoiceAmount,2));
        $actSheet->setCellValueByColumnAndRow($colc++, $rowc, number_format($totalPaidAmount,2));
        $actSheet->setCellValueByColumnAndRow($colc++, $rowc, number_format($totalBalanceAmount,2));

        $actSheet->getStyle("A{$rowc}:E{$rowc}")->applyFromArray($headStyle);
        if($company_id == ''){
            $actSheet->getStyle("A{$rowc}:F{$rowc}")->applyFromArray($headStyle);
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

}