<?
class CPL_Admin_Widgets_Tradingsg_DetailSummaryByClient_View extends CP_Admin_Widgets_Tradingsg_DetailSummaryByClient_View
{
    //==================================================================//
    function getWidget() {
        $db       = Zend_Registry::get('db');
        $fn       = Zend_Registry::get('fn');
        $ln       = Zend_Registry::get('ln');
        $cpCfg    = Zend_Registry::get('cpCfg');
        $dateUtil = Zend_Registry::get('dateUtil');

        $company_id     = $fn->getReqParam('company_id');
        $start_date     = $fn->getReqParam('start_date');
        $end_date       = $fn->getReqParam('end_date');
        $monthVal       = $fn->getReqParam('month');
        $yearVal        = $fn->getReqParam('year');
        $current_date   = date('Y-m-d');
        $month          = date('m');
        $year           = date('Y');

        if ($start_date != '' && $end_date == '') {
            $start_date = $start_date;
            $end_date   = $current_date;
        } else if ($start_date == '' && $end_date != ''){
            $start_date = $year . '-' . $month . '-' . '01';
            $start_date = $start_date;
            $end_date   = $end_date;
        } else if ($start_date != '' && $end_date != '') {
            $start_date = $start_date;
            $end_date   = $end_date;
        } else {
            if($yearVal != '') {
                $year = $yearVal;
            }

            if($monthVal != '') {
                $month = $monthVal;
            }

            $start_date = $year . '-' . $month . '-' . '01';
            $end_date   = $year . '-' . $month . '-' . '31';
        }

        $start_date_formatted = $dateUtil->formatDate($start_date, 'DD/MM/YYYY');
        $end_date_formatted   = $dateUtil->formatDate($end_date, 'DD/MM/YYYY');


        $company_name = '';
        if($company_id == ''){
            $company_name = "<th>Client Name</th>";
        }

        $company_Title = '';
        if($company_id != ''){
            $SQLCompany = "
            SELECT company_name
            FROM  company
            WHERE company_id = {$company_id}
            ";
            $resultCompany = $db->sql_query($SQLCompany);
            $rowCompany    = $db->sql_fetchrow($resultCompany);

            $company_Title = "<b>{$rowCompany['company_name']}</b>";
        
        }else{
            $company_Title = 'Client';
        }

        $text = "
        <h2>Detail Summary By {$company_Title}</h2>
		<div class = 'tableOuter scroll-pane'>
            <table class='thinlist summaryTable mb20'>
                <thead>
                    <th colspan='6'>Summary</th>
                </thead>
                <tr>
                    <td>Start Date : {$start_date_formatted}</td>
                    <td>End Date : {$end_date_formatted}</td>
                </tr>
            </table>
    		<table class='thinlist'>
    			<thead>
    				<tr>
    					{$company_name}
    					<th>Date</th>
    					<th>Invoice Code</th>
    					<th>Invoice Amount</th>
    					<th>Paid</th>
    					<th>Amount Due</th>
    				</tr>
    			</thead>
    			<tbody>
    				{$this->getRowsHTML()}
    			</tbody>
    		</table>
		</div>
        ";
        return $text;
    }

    function getRowsHTML() {
        $fn           = Zend_Registry::get('fn');
        $db           = Zend_Registry::get('db');
        $cpCfg        = Zend_Registry::get('cpCfg');
        $start_date   = $fn->getReqParam('start_date');
        $end_date     = $fn->getReqParam('end_date');
        $monthVal     = $fn->getReqParam('month');
        $yearVal      = $fn->getReqParam('year');
        $current_date = date('Y-m-d');
        $month        = date('m');
        $year         = date('Y');
        $company_id   = $fn->getReqParam('company_id');
        
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

        $company_id = $fn->getReqParam('company_id');

        $rows = '';
		$siteTitle = '' ;
        $totalInvoiceAmount = 0;
        $totalBalanceAmount = 0;
        $totalPaidAmount = 0;

        foreach($this->model->dataArray as $row){
            $appendSql = "";
            if($row['company_id'] != ''){
                $appendSql = "AND o.company_id = {$row['company_id']}";
            }

            $appendSqlSite = "";
            if ($cpCfg['cp.hasMultiUniqueSites']){
                $cpSiteIdSession = $fn->getSessionParam('cp_site_id');
                $appendSqlSite   = "  AND inv.site_id = '{$cpSiteIdSession}'";
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
              {$startDateAppendSql}
              {$appendSql}
              {$appendSqlSite}
            ";

            $resultInv = $db->sql_query($SQLInv);
            $invoice_amount  = '';

            while ($rowInv = $db->sql_fetchrow($resultInv)) {
        		$invoice_amount = $rowInv['invoice_amount'];
        		$balance_amount  = $invoice_amount - $rowInv['total_amount_paid'];
                $totalInvoiceAmount += $invoice_amount;
                $totalBalanceAmount += $balance_amount;
                $totalPaidAmount += $rowInv['total_amount_paid'];
                $invoice_amount = number_format($invoice_amount);
                $balance_amount = number_format($balance_amount);
        		$rowInv['total_amount_paid'] = number_format($rowInv['total_amount_paid']);

                $invoiceCode = $rowInv['invoice_code'];
                $todaylink = "<a target = '_blank' href = 'index.php?_topRm=finance&module=tradingsg_order&record_id={$rowInv['order_id']}&_action=edit'>";

                $company_name = '';
                if($company_id == ''){
                    $company_name = "<td>{$row['company_name']}</td>";
                }

			    $rows .= "
				<tr>
					{$company_name}
					<td>{$fn->getCPDate($rowInv['invoice_date'], 'd-m-Y')}</td>
					<td>{$todaylink}{$invoiceCode}</td>
					<td align='right'>{$invoice_amount}</td>
					<td align='right'>{$rowInv['total_amount_paid']}</td>
					<td align='right'>{$balance_amount}</td>
				</tr>
				";
            }

        }

        $totalInvoiceAmount = number_format($totalInvoiceAmount,2);
        $totalBalanceAmount = number_format($totalBalanceAmount,2);
        $totalPaidAmount    = number_format($totalPaidAmount,2);

        $total_th = '';
        if($company_id == ''){
            $total_th = "<th colspan='2' class='lastRowBgColor'></th>";
        }else{
            $total_th = "<th class='lastRowBgColor'></th>";
        }

        $text = "
        {$rows}
        <tr>
            {$total_th}
            <th class='lastRowBgColor'>TOTAL</th>
            <th class='lastRowBgColor txtRight'>{$totalInvoiceAmount}</th>
            <th class='lastRowBgColor txtRight'>{$totalPaidAmount}</th>
            <th class='lastRowBgColor txtRight'>{$totalBalanceAmount}</th>
        </tr>
        ";

        return $text;
    }

}