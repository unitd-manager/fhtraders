<?
class CPL_Admin_Widgets_Tradingsg_ProfitLossReport_View extends CP_Common_Lib_WidgetViewAbstract
{
    //==================================================================//
    function getWidget() {
        $db = Zend_Registry::get('db');
        $fn = Zend_Registry::get('fn');
        $tv = Zend_Registry::get('tv');
        $cpCfg = Zend_Registry::get('cpCfg');
        
        $text = "
        <h2>Profit Loss Report</h2>
        <div class = 'tableOuter scroll-pane'>
            <table class='thinlist' width='100%'>
                <thead>
                    <tr>
                        <th>Income</th>
                        <th>Expense</th>
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
        $fn = Zend_Registry::get('fn');
        $db    = Zend_Registry::get('db');
        $cpCfg = Zend_Registry::get('cpCfg');
        $cpSiteIdSession = $fn->getSessionParam('cp_site_id');

        $rows = '';

        $total_receipt_amount      = 0;
        $total_invoice_amount      = 0;
        $totaltestamount          = 0;
        $totaltestamount1         = 0;
        $totaltestamount2         = 0;
        $totalOverAllLabtest      = 0;
        $totalOverAllinPatient    = 0;
        $totalAllIPAdminCharges   = 0;
        $totalAllIPTheatreCharges = 0;

        $monthValAppendSql  = '';
        $yearValAppendSql   = '';
        $startDateAppendSql = '';

        $start_date   = $fn->getReqParam('start_date');
        $end_date     = $fn->getReqParam('end_date');
        $monthVal     = $fn->getReqParam('month');
        $yearVal      = $fn->getReqParam('year');
        $month        = date('m');
        $year         = date('Y');
        $current_date = date('Y-m-d');

        if ($start_date != '' && $end_date != '') {
            $startDateAppendSql = "AND DATE_FORMAT(r.date, '%Y-%m-%d') >= '{$start_date}' AND DATE_FORMAT(r.date, '%Y-%m-%d') <= '{$end_date}'";
        } else if ($monthVal == '' && $yearVal == ''){
            $start_date = $year . '-' . $month . '-' . '01';
            $end_date   = $year . '-' . $month . '-' . '31';
            $startDateAppendSql = "AND DATE_FORMAT(r.date, '%Y-%m-%d') >= '{$start_date}' AND DATE_FORMAT(r.date, '%Y-%m-%d') <= '{$end_date}'";
        } else if ($monthVal != '' && $yearVal != ''){
            $monthValAppendSql = "AND DATE_FORMAT(r.date, '%m') = '{$monthVal}'" ;
            $yearValAppendSql  = "AND DATE_FORMAT(r.date, '%Y') = '{$yearVal}'" ;
        }

        $appendSQLRec = "";
        $appendSQLInv = "";
        $appendSQLExp = "";
        $appendSQL    = "";
        if ($cpCfg['cp.hasMultiUniqueSites']){
            $cpSiteIdSession = $fn->getSessionParam('cp_site_id');
            $appendSQLRec    = " AND r.site_id = '{$cpSiteIdSession}'";
            $appendSQLInv    = " AND i.site_id = '{$cpSiteIdSession}'";
            $appendSQLExp    = " AND e.site_id = '{$cpSiteIdSession}'";
            $appendSQL       = " WHERE site_id = '{$cpSiteIdSession}'";
        }

        $SQLSub = "
        SELECT (SUM(r.amount)) AS receipt_amount
        FROM receipt r
        WHERE r.receipt_status = 'Paid'
          {$appendSQLRec}
          {$startDateAppendSql}
          {$monthValAppendSql}
          {$yearValAppendSql}
        ";
        $resultSub = $db->sql_query($SQLSub);
        while ($rowSub = $db->sql_fetchrow($resultSub)) {
            $sum_receipt_amount = $rowSub['receipt_amount'];
        }
        $total_receipt_amount += $sum_receipt_amount;


        $startDateAppendSql = '';
        $monthValAppendSql = '';
        $yearValAppendSql = '';

        if ($start_date != '' && $end_date != '') {
            $startDateAppendSql = "AND i.invoice_date >= '{$start_date}' AND i.invoice_date <= '{$end_date}'";
        } else if ($monthVal == '' && $yearVal == ''){
            $start_date = $year . '-' . $month . '-' . '01';
            $end_date = $year . '-' . $month . '-' . '31';
            $startDateAppendSql = "AND i.invoice_date >= '{$start_date}' AND i.invoice_date <= '{$end_date}'";
        } else if ($monthVal != '' && $yearVal != ''){
            $monthValAppendSql = "AND DATE_FORMAT(i.invoice_date, '%m') = '{$monthVal}'" ;
            $yearValAppendSql  = "AND DATE_FORMAT(i.invoice_date, '%Y') = '{$yearVal}'" ;
        }

        $SQLIP = "
        SELECT (SUM(i.invoice_amount)) AS invoice_amount
        FROM invoice i
        WHERE (i.status = 'Due')
          {$appendSQLInv}
          {$startDateAppendSql}
          {$monthValAppendSql}
          {$yearValAppendSql}
        ";
        $resultIP = $db->sql_query($SQLIP);
        while ($rowIP    = $db->sql_fetchrow($resultIP)) {
            $sum_invoice_amount = $rowIP['invoice_amount'];
        }
        $total_invoice_amount += $sum_invoice_amount;

        //Expense related codes// 
        $sqlgroup = "
        SELECT expense_group_id 
              ,title
        FROM expense_group
        {$appendSQL}
        ";
        $resultgroup = $db->sql_query($sqlgroup);
        $expense_group = '';
        $amount = 0;
        $expense_amount ='';
        $overAllExpense1 = 0;
        while ($rowgroup    = $db->sql_fetchrow($resultgroup)) {
            $source = '';

            $startDateAppendSql = '';
            $monthValAppendSql = '';
            $yearValAppendSql = '';
            if ($start_date != '' && $end_date != '') {
                $startDateAppendSql = "AND e.date >= '{$start_date}' AND e.date <= '{$current_date}'";
            } else if ($monthVal == '' && $yearVal == ''){
                $start_date = $year . '-' . $month . '-' . '01';
                $end_date = $year . '-' . $month . '-' . '31';
                $startDateAppendSql = "AND e.date >= '{$start_date}' AND e.date <= '{$end_date}'";
            } else if ($monthVal != '' && $yearVal != ''){
                $monthValAppendSql = "AND DATE_FORMAT(e.date, '%m') = '{$monthVal}'" ;
                $yearValAppendSql  = "AND DATE_FORMAT(e.date, '%Y') = '{$yearVal}'" ;
            }

            $sqlexp = "
            SELECT SUM(e.amount) AS amount
                  ,e.group
                  ,e.source
                  ,es.title AS sub_title
            FROM expense e
            LEFT JOIN expense_sub_group es ON (es.expense_sub_group_id = e.sub_group)
            WHERE e.group = {$rowgroup['expense_group_id']}
              {$appendSQLExp}
              {$startDateAppendSql}
              {$monthValAppendSql}
              {$yearValAppendSql}
            GROUP BY e.group
            ";
            $resultexp = $db->sql_query($sqlexp);
            $amount = 0;
            while ($rowexp = $db->sql_fetchrow($resultexp)) {
                $amount += $rowexp['amount'];
            }
            $amountFormat = number_format($amount, 2);

            $sqlexp1 = "
            SELECT SUM(e.amount) AS amount
                  ,e.group
                  ,e.source
                  ,es.title AS sub_title
            FROM expense e
            LEFT JOIN expense_sub_group es ON (es.expense_sub_group_id = e.sub_group)
            WHERE e.group = {$rowgroup['expense_group_id']}
              {$appendSQLExp}
              {$startDateAppendSql}
              {$monthValAppendSql}
              {$yearValAppendSql}
            GROUP BY es.expense_sub_group_id
            ORDER BY es.title ASC
            ";
            $resultexp1 = $db->sql_query($sqlexp1);
            $subtitle = '';
            while ($rowexp1 = $db->sql_fetchrow($resultexp1)) {
                $class = '';

                $sqlexpOverall = "
                SELECT SUM(e.amount) AS amount
                      ,e.group
                      ,e.source
                      ,es.title AS sub_title
                FROM expense e
                LEFT JOIN expense_sub_group es ON (es.expense_sub_group_id = e.sub_group)
                WHERE e.group = {$rowexp1['group']}
                  {$appendSQLExp}
                  {$startDateAppendSql}
                  {$monthValAppendSql}
                  {$yearValAppendSql}
                AND es.title = '{$rowexp1['sub_title']}'
                ";
                $resultexpOverall = $db->sql_query($sqlexpOverall);
                $amountOverall = 0;
                $rowexpOverall = $db->sql_fetchrow($resultexpOverall);
                $amountOverall = $rowexpOverall['amount'];
                $amountOverallFormat = number_format($amountOverall, 2);

                $sqlexpSubDetail = "
                SELECT e.amount
                      ,e.group
                      ,es.title AS sub_title
                      ,e.description
                      ,e.source
                      ,e.date
                FROM expense e
                LEFT JOIN expense_sub_group es ON (es.expense_sub_group_id = e.sub_group)
                WHERE e.group = {$rowexp1['group']}
                  {$appendSQLExp}
                  {$startDateAppendSql}
                  {$monthValAppendSql}
                  {$yearValAppendSql}
                AND es.title = '{$rowexp1['sub_title']}'
                ";
                $resultexpSubDetail = $db->sql_query($sqlexpSubDetail);
                $subHeadtitle = '';
                while ($rowexpSubDetail = $db->sql_fetchrow($resultexpSubDetail)) {
                    $date = $fn->getCPDate($rowexpSubDetail['date'],"d-m-Y");

                    $subHeadtitle .= "
                    <tr class='{$class}'>
                        <td>{$date}</td>
                        <td>{$rowexpSubDetail['sub_title']}</td>
                        <td>{$rowexpSubDetail['description']}</td>
                        <td>{$rowexpSubDetail['source']}</td>
                        <td align='right'>{$rowexpSubDetail['amount']}</td>
                    </tr>";
                }

                $subtitle .= "
                <tr class='{$class}'>
                    <td class='expenseDetailsSubHead'>
                        <div class='expenseSubHeadDetails'>+ {$rowexp1['sub_title']}</div>
                        <div class='subTitlesWithoutGroup'><table>{$subHeadtitle}</table></div>
                    </td>
                    <td align='right'>{$rowexp1['amount']}</td>
                    <td>[Overall Amount: {$amountOverallFormat}]</td>
                </tr>";
            }

            $expense_group .= "
            <table width=100%>
                <tr>
                    <td width = 85% class='expenseDetailsHead'>
                    <div class='expenseDetails'>+ {$rowgroup['title']}</div>
                    <div class='subTitles'><table>{$subtitle}</table></div>
                    </td>
                    <td width = 15% align='right'>
                    {$amountFormat}
                    </td>
                </tr>
            </table>
            "; 

            $overAllExpense1 += $amount;
        }
 
        $overAllExpense = $overAllExpense1;

        $balance_amount  = $this->getBalanceAmount();
        $balance_amount_formatted  = number_format($balance_amount, 2);

        $overAllIncome            = $total_receipt_amount + $balance_amount;
        $overAllProfit            = $overAllIncome - $overAllExpense;
        $overAllIncome_formatted  = number_format($overAllIncome, 2);
        $overAllExpense_formatted = number_format($overAllExpense, 2);
        $overAllProfit            = number_format($overAllProfit, 2);
        $total_invoice_amount_formatted  = number_format($total_invoice_amount, 2);
        $total_receipt_amount_formatted  = number_format($total_receipt_amount, 2);

        $text = "
        <tr>
            <td class='incomeReport' width='40%'>
                <table width=100%>
                    <tr>
                        <td width = '70%'>
                            <span>Balance Brought Forward</span>
                        </td>
                        <td width = '30%' align='right'>
                            {$balance_amount_formatted}
                            <input type='hidden' name='balance_amount' value='{$balance_amount}'>
                        </td>
                    </tr>
                    <tr>
                        <td width = '70%'>
                            <input type='checkbox' class='incomePaymentCheckBox' name='paymentReceivedIncome' value='Payment Received' checked>
                            <span>Payment Received</span>
                        </td>
                        <td width = '30%' align='right'>
                            {$total_receipt_amount_formatted}
                            <input type='hidden' name='total_receipt_amount' value='{$total_receipt_amount}'>
                        </td>
                    </tr>
                    <tr>
                        <td width = '70%'>
                            <input type='checkbox' class='incomePaymentCheckBox' name='paymentDueIncome' value='Payment Due'>
                            <span>Payment Due</span>
                        </td>
                        <td width = '30%' align='right'>
                            {$total_invoice_amount_formatted}
                            <input type='hidden' name='total_invoice_amount' value='{$total_invoice_amount}'>
                        </td>
                    </tr>
                </table>
            </td>
            <td class='incomeReport' width='60%'>
                {$expense_group}
            </td>
        </tr>
        <tr>
            <td class='totalValue'>
                <div class='float_left IncomeTotalSubTotal'>Total</div> 
                <div class='float_right overallIncome'>
                    {$overAllIncome_formatted}
                    <input type='hidden' name='overAllIncome' value='{$overAllIncome}'>
                </div>
            </td>
            <td class='totalValue' align='right'>
                <div class='float_left'>Total</div> 
                <div class='float_right'>
                    {$overAllExpense_formatted}
                    <input type='hidden' name='overAllExpense' value='{$overAllExpense}'>
                </div>
            </td>
        </tr>
        <tr>
            <td class='totalValue lastRowBgColor'>
                <div class='float_left IncomeExpenseOverallBalance'>Balance</div>
                <div class='float_right overAllProfit'>{$overAllProfit}</div>
            </td>
            <td class='' align='right'>
            </td>
        </tr>
        ";

        return $text;
    }

    function getBalanceAmount() {
        $fn = Zend_Registry::get('fn');
        $db    = Zend_Registry::get('db');
        $cpCfg = Zend_Registry::get('cpCfg');
        $cpSiteIdSession = $fn->getSessionParam('cp_site_id');

        $rows = '';

        $total_receipt_amount      = 0;
        $total_invoice_amount      = 0;
        $totaltestamount          = 0;
        $totaltestamount1         = 0;
        $totaltestamount2         = 0;
        $totalOverAllLabtest      = 0;
        $totalOverAllinPatient    = 0;
        $totalAllIPAdminCharges   = 0;
        $totalAllIPTheatreCharges = 0;

        $monthValAppendSql  = '';
        $yearValAppendSql   = '';
        $startDateAppendSql = '';

        //$start_date   = '2019-01-01';
        $start_date     = $fn->getReqParam('start_date');
        $end_date     = $fn->getReqParam('end_date');
        $monthVal     = $fn->getReqParam('month');
        $yearVal      = $fn->getReqParam('year');
        $month        = date('m');
        $year         = date('Y');
        $current_date = date('Y-m-d');
        $month1 = '';

        if ($start_date != '' && $end_date != '') {
            $month1 = date('m', strtotime($start_date));
            $year = date('Y', strtotime($start_date));
            $startDateAppendSql = "AND DATE_FORMAT(r.date, '%m') < '{$month1}' AND DATE_FORMAT(r.date, '%Y') = '{$year}'";
        } else if ($monthVal == '' && $yearVal == ''){
            if($month == 01){                
                $month = 12;
            } else {
                $month = $month - 1;
                $month = '0'.$month;
            }
            $start_date = $year . '-' . $month . '-' . '01';
            $end_date   = $year . '-' . $month . '-' . '31';
            $startDateAppendSql = "AND DATE_FORMAT(r.date, '%Y-%m-%d') >= '{$start_date}' AND DATE_FORMAT(r.date, '%Y-%m-%d') < '{$end_date}'";
        } else if ($monthVal != '' && $yearVal != ''){
            $monthValAppendSql = "AND DATE_FORMAT(r.date, '%m') < '{$monthVal}'" ;
            $yearValAppendSql  = "AND DATE_FORMAT(r.date, '%Y') = '{$yearVal}'" ;
        }

        $appendSQLRec = "";
        $appendSQLInv = "";
        $appendSQLExp = "";
        $appendSQL    = "";
        if ($cpCfg['cp.hasMultiUniqueSites']){
            $cpSiteIdSession = $fn->getSessionParam('cp_site_id');
            $appendSQLRec    = " AND r.site_id = '{$cpSiteIdSession}'";
            $appendSQLInv    = " AND i.site_id = '{$cpSiteIdSession}'";
            $appendSQLExp    = " AND e.site_id = '{$cpSiteIdSession}'";
            $appendSQL       = " WHERE site_id = '{$cpSiteIdSession}'";
        }

        $SQLSub = "
        SELECT (SUM(r.amount)) AS receipt_amount
        FROM receipt r
        WHERE r.receipt_status = 'Paid'
          {$appendSQLRec}
          {$startDateAppendSql}
          {$monthValAppendSql}
          {$yearValAppendSql}
        ";
        $resultSub = $db->sql_query($SQLSub);
        while ($rowSub = $db->sql_fetchrow($resultSub)) {
            $sum_receipt_amount = $rowSub['receipt_amount'];
        }
        $total_receipt_amount += $sum_receipt_amount;


        $startDateAppendSql = '';
        $monthValAppendSql = '';
        $yearValAppendSql = '';
        $monthVal     = $fn->getReqParam('month');
        $month        = date('m');

        if ($start_date != '' && $end_date != '') {
            $month1 = date('m', strtotime($start_date));
            $year = date('Y', strtotime($start_date));
            $startDateAppendSql = "AND DATE_FORMAT(i.invoice_date, '%m') < '{$month1}' AND DATE_FORMAT(i.invoice_date, '%Y') = '{$year}'";
        } else if ($monthVal == '' && $yearVal == ''){
            if($month == 01){                
                $month = 12;
            } else {
                $month = $month - 1;
                $month = '0'.$month;                
            }
            $start_date = $year . '-' . $month . '-' . '01';
            $end_date = $year . '-' . $month . '-' . '31';
            $startDateAppendSql = "AND i.invoice_date >= '{$start_date}' AND i.invoice_date <= '{$end_date}'";
        } else if ($monthVal != '' && $yearVal != ''){
            $monthValAppendSql = "AND DATE_FORMAT(i.invoice_date, '%m') < '{$monthVal}'" ;
            $yearValAppendSql  = "AND DATE_FORMAT(i.invoice_date, '%Y') = '{$yearVal}'" ;
        }

        $SQLIP = "
        SELECT (SUM(i.invoice_amount)) AS invoice_amount
        FROM invoice i
        WHERE (i.status = 'Due')
          {$appendSQLInv}
          {$startDateAppendSql}
          {$monthValAppendSql}
          {$yearValAppendSql}
        ";
        $resultIP = $db->sql_query($SQLIP);
        while ($rowIP    = $db->sql_fetchrow($resultIP)) {
            $sum_invoice_amount = $rowIP['invoice_amount'];
        }
        $total_invoice_amount += $sum_invoice_amount;

        //Expense related codes// 
        $sqlgroup = "
        SELECT expense_group_id 
              ,title
        FROM expense_group
        {$appendSQL}
        ";
        $resultgroup = $db->sql_query($sqlgroup);
        $expense_group = '';
        $amount = 0;
        $expense_amount ='';
        $overAllExpense1 = 0;
        while ($rowgroup    = $db->sql_fetchrow($resultgroup)) {
            $appendSqlSite = '';
            $source = '';

            $startDateAppendSql = '';
            $monthValAppendSql = '';
            $yearValAppendSql = '';
            $monthVal     = $fn->getReqParam('month');
            $month        = date('m');
            if ($start_date != '' && $end_date != '') {
                $month1 = date('m', strtotime($start_date));
                $year = date('Y', strtotime($start_date));
                $startDateAppendSql = "AND DATE_FORMAT(e.date, '%m') < '{$month1}' AND DATE_FORMAT(e.date, '%Y') = '{$year}'";
            } else if ($monthVal == '' && $yearVal == ''){
                if($month == 01){                
                    $month = 12;
                } else {
                    $month = $month - 1;
                    $month = '0'.$month;                    
                }
                $start_date = $year . '-' . $month . '-' . '01';
                $end_date = $year . '-' . $month . '-' . '31';
                $startDateAppendSql = "AND e.date >= '{$start_date}' AND e.date <= '{$end_date}'";
            } else if ($monthVal != '' && $yearVal != ''){
                $monthValAppendSql = "AND DATE_FORMAT(e.date, '%m') < '{$monthVal}'" ;
                $yearValAppendSql  = "AND DATE_FORMAT(e.date, '%Y') = '{$yearVal}'" ;
            }

            $sqlexp = "
            SELECT SUM(e.amount) AS amount
                  ,e.group
                  ,e.source
                  ,es.title AS sub_title
            FROM expense e
            LEFT JOIN expense_sub_group es ON (es.expense_sub_group_id = e.sub_group)
            WHERE e.group = {$rowgroup['expense_group_id']}
              {$appendSQLExp}
              {$startDateAppendSql}
              {$monthValAppendSql}
              {$yearValAppendSql}
            GROUP BY e.group
            ";
            $resultexp = $db->sql_query($sqlexp);
            $amount = 0;
            while ($rowexp = $db->sql_fetchrow($resultexp)) {
                $amount += $rowexp['amount'];
            }
            $amountFormat = number_format($amount, 2);

            $sqlexp1 = "
            SELECT SUM(e.amount) AS amount
                  ,e.group
                  ,e.source
                  ,es.title AS sub_title
            FROM expense e
            LEFT JOIN expense_sub_group es ON (es.expense_sub_group_id = e.sub_group)
            WHERE e.group = {$rowgroup['expense_group_id']}
              {$appendSQLExp}
              {$startDateAppendSql}
              {$monthValAppendSql}
              {$yearValAppendSql}
            GROUP BY es.expense_sub_group_id
            ORDER BY es.title ASC
            ";
            $resultexp1 = $db->sql_query($sqlexp1);
            $subtitle = '';
            while ($rowexp1 = $db->sql_fetchrow($resultexp1)) {
                $class = '';

                $sqlexpOverall = "
                SELECT SUM(e.amount) AS amount
                      ,e.group
                      ,e.source
                      ,es.title AS sub_title
                FROM expense e
                LEFT JOIN expense_sub_group es ON (es.expense_sub_group_id = e.sub_group)
                WHERE e.group = {$rowexp1['group']}
                  {$appendSQLExp}
                  {$startDateAppendSql}
                  {$monthValAppendSql}
                  {$yearValAppendSql}
                AND es.title = '{$rowexp1['sub_title']}'
                ";
                $resultexpOverall = $db->sql_query($sqlexpOverall);
                $amountOverall = 0;
                $rowexpOverall = $db->sql_fetchrow($resultexpOverall);
                $amountOverall = $rowexpOverall['amount'];
                $amountOverallFormat = number_format($amountOverall, 2);

                $sqlexpSubDetail = "
                SELECT e.amount
                      ,e.group
                      ,es.title AS sub_title
                      ,e.description
                      ,e.source
                      ,e.date
                FROM expense e
                LEFT JOIN expense_sub_group es ON (es.expense_sub_group_id = e.sub_group)
                WHERE e.group = {$rowexp1['group']}
                  {$appendSQLExp}
                  {$startDateAppendSql}
                  {$monthValAppendSql}
                  {$yearValAppendSql}
                AND es.title = '{$rowexp1['sub_title']}'
                ";
                $resultexpSubDetail = $db->sql_query($sqlexpSubDetail);
                $subHeadtitle = '';
                while ($rowexpSubDetail = $db->sql_fetchrow($resultexpSubDetail)) {
                    $date = $fn->getCPDate($rowexpSubDetail['date'],"d-m-Y");

                    $subHeadtitle .= "
                    <tr class='{$class}'>
                        <td>{$date}</td>
                        <td>{$rowexpSubDetail['sub_title']}</td>
                        <td>{$rowexpSubDetail['description']}</td>
                        <td>{$rowexpSubDetail['source']}</td>
                        <td align='right'>{$rowexpSubDetail['amount']}</td>
                    </tr>";
                }

                $subtitle .= "
                <tr class='{$class}'>
                    <td class='expenseDetailsSubHead'>
                        <div class='expenseSubHeadDetails'>+ {$rowexp1['sub_title']}</div>
                        <div class='subTitlesWithoutGroup'><table>{$subHeadtitle}</table></div>
                    </td>
                    <td align='right'>{$rowexp1['amount']}</td>
                    <td>[Overall Amount: {$amountOverallFormat}]</td>
                </tr>";
            }

            $expense_group .= "
            <table width=100%>
                <tr>
                    <td width = 85% class='expenseDetailsHead'>
                    <div class='expenseDetails'>+ {$rowgroup['title']}</div>
                    <div class='subTitles'><table>{$subtitle}</table></div>
                    </td>
                    <td width = 15% align='right'>
                    {$amountFormat}
                    </td>
                </tr>
            </table>
            "; 

            $overAllExpense1 += $amount;
        }
 
        $overAllExpense = $overAllExpense1;
        $overAllIncome  = $total_receipt_amount ;
        $overAllProfit  = $overAllIncome - $overAllExpense;

        return $overAllProfit;
    }
}