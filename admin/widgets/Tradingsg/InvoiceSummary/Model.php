<?
class CPL_Admin_Widgets_Tradingsg_InvoiceSummary_Model extends CP_Admin_Widgets_Tradingsg_InvoiceSummary_Model
{

    function getCurPfx() {
        $db = Zend_Registry::get('db');
        $fn = Zend_Registry::get('fn');
        $cpCfg = Zend_Registry::get('cpCfg');

        if ($cpCfg['m.project.hasMultiCurrency'] == 1){
            return $cpCfg['baseCurrency'];
        } else {
            return 'Rs ';
        }
    }

    //==================================================================//
    function getTotalOutstandingInvoices() {
        $db = Zend_Registry::get('db');
        $fn = Zend_Registry::get('fn');
        $cpCfg = Zend_Registry::get('cpCfg');

        $appendSqlSite = "";
		if ($cpCfg['cp.hasMultiUniqueSites']){
		    $cpSiteIdSession = $fn->getSessionParam('cp_site_id');
		    $appendSqlSite = "  AND i.site_id = '{$cpSiteIdSession}'";
		}

        $SQL = "
        SELECT CONCAT('{$this->getCurPfx()}', FORMAT(SUM(invoice_amount{$this->getFldSfx()}), 0)) AS total
        FROM invoice i
        WHERE LOWER(i.status) != 'paid' 
        AND LOWER(i.status) != 'cancelled' 
        {$appendSqlSite}
        ";
        $result  = $db->sql_query($SQL);
        $row = $db->sql_fetchrow($result);
        
        return $row['total'];
    }

    //==================================================================//
    function getTotalLateInvoices() {
        $db = Zend_Registry::get('db');
        $fn = Zend_Registry::get('fn');
        $cpCfg = Zend_Registry::get('cpCfg');

        $appendSqlSite = "";
		if ($cpCfg['cp.hasMultiUniqueSites']){
		    $cpSiteIdSession = $fn->getSessionParam('cp_site_id');
		    $appendSqlSite = "  AND i.site_id = '{$cpSiteIdSession}'";
		}

        $SQL = "
        SELECT CONCAT('{$this->getCurPfx()}', FORMAT(SUM(invoice_amount{$this->getFldSfx()}), 0)) AS total
        FROM invoice i
        WHERE LOWER(i.status) = 'late' 
        {$appendSqlSite}
        ";
        $result  = $db->sql_query($SQL);
        $row = $db->sql_fetchrow($result);
        
        return $row['total'];
    }

    //==================================================================//
    function getTotalOverDueInvoices() {
        $db = Zend_Registry::get('db');
        $fn = Zend_Registry::get('fn');
        $cpCfg = Zend_Registry::get('cpCfg');

        $appendSqlSite = "";
		if ($cpCfg['cp.hasMultiUniqueSites']){
		    $cpSiteIdSession = $fn->getSessionParam('cp_site_id');
		    $appendSqlSite = "  AND i.site_id = '{$cpSiteIdSession}'";
		}
        
        $ninetyDaysBefore = date('Y-m-d', mktime (0,0,0,date('m'),date('d')-90, date('Y')));

        $SQL = "
        SELECT CONCAT('{$this->getCurPfx()}', FORMAT(SUM(invoice_amount{$this->getFldSfx()}), 0)) AS total
        FROM invoice i
        WHERE LOWER(i.status) = 'late' 
          AND i.invoice_due_date < '{$ninetyDaysBefore}'
          {$appendSqlSite}
        ";
        
        $result  = $db->sql_query($SQL);
        $row = $db->sql_fetchrow($result);
        
        return $row['total'];
    }

    //==================================================================//
    function getTotalInvoicesThisMonth() {
        $db = Zend_Registry::get('db');
        $fn = Zend_Registry::get('fn');
        $cpCfg = Zend_Registry::get('cpCfg');

        $appendSqlSite = "";
		if ($cpCfg['cp.hasMultiUniqueSites']){
		    $cpSiteIdSession = $fn->getSessionParam('cp_site_id');
		    $appendSqlSite = "  AND i.site_id = '{$cpSiteIdSession}'";
		}
        
        $thisMonth = date('Y-m');
        
        $SQL = "
        SELECT CONCAT('{$this->getCurPfx()}', FORMAT(SUM(invoice_amount{$this->getFldSfx()}), 0)) AS total
        FROM invoice i
        WHERE DATE_FORMAT(i.invoice_date, '%Y-%m') = '{$thisMonth}'
          AND LOWER(i.status) != 'cancelled' 
          {$appendSqlSite}
        ";

        $result  = $db->sql_query($SQL);
        $row = $db->sql_fetchrow($result);
        
        return $row['total'];
    }

    //==================================================================//
    function getTotalInvoicesDueThisMonth() {
        $db = Zend_Registry::get('db');
        $fn = Zend_Registry::get('fn');
        $cpCfg = Zend_Registry::get('cpCfg');

        $appendSqlSite = "";
		if ($cpCfg['cp.hasMultiUniqueSites']){
		    $cpSiteIdSession = $fn->getSessionParam('cp_site_id');
		    $appendSqlSite = "  AND i.site_id = '{$cpSiteIdSession}'";
		}
        
        $thisMonth = date('Y-m');
        
        $SQL = "
        SELECT CONCAT('{$this->getCurPfx()}', FORMAT(SUM(invoice_amount{$this->getFldSfx()}), 0)) AS total
        FROM invoice i
        WHERE DATE_FORMAT(i.invoice_due_date, '%Y-%m') = '{$thisMonth}'
          AND LOWER(i.status) != 'cancelled' 
          AND LOWER(i.status) != 'paid' 
          {$appendSqlSite}
        ";

        $result  = $db->sql_query($SQL);
        $row = $db->sql_fetchrow($result);
        
        return $row['total'];
    }

    //==================================================================//
    function getTotalInvoiceLastMonth() {
        $db = Zend_Registry::get('db');
        $fn = Zend_Registry::get('fn');
        $cpCfg = Zend_Registry::get('cpCfg');

        $appendSqlSite = "";
		if ($cpCfg['cp.hasMultiUniqueSites']){
		    $cpSiteIdSession = $fn->getSessionParam('cp_site_id');
		    $appendSqlSite = "  AND i.site_id = '{$cpSiteIdSession}'";
		}
        
        $lastMonth = date('Y-m',mktime (0,0,0,date("m")-1,date("d"), date("Y")));
        
        $SQL = "
        SELECT CONCAT('{$this->getCurPfx()}', FORMAT(SUM(invoice_amount{$this->getFldSfx()}), 0)) AS total
        FROM invoice i
        WHERE DATE_FORMAT(i.invoice_date, '%Y-%m') = '{$lastMonth}'
          AND LOWER(i.status) != 'cancelled' 
          {$appendSqlSite}
        ";

        $result  = $db->sql_query($SQL);
        $row = $db->sql_fetchrow($result);
        
        return $row['total'];
    }

    //==================================================================//
    function getTotalInvoicesPaidThisMonth() {
        $db = Zend_Registry::get('db');
        $fn = Zend_Registry::get('fn');
        $cpCfg = Zend_Registry::get('cpCfg');

        $appendSqlSite = "";
		if ($cpCfg['cp.hasMultiUniqueSites']){
		    $cpSiteIdSession = $fn->getSessionParam('cp_site_id');
		    $appendSqlSite = "  AND i.site_id = '{$cpSiteIdSession}'";
		}
        
        $thisMonth = date('Y-m');
        
        $SQL = "
        SELECT CONCAT('{$this->getCurPfx()}', FORMAT(SUM(invoice_amount{$this->getFldSfx()}), 0)) AS total
        FROM invoice i
        WHERE DATE_FORMAT(i.invoice_date, '%Y-%m') = '{$thisMonth}'
          AND LOWER(i.status) = 'paid' 
          AND LOWER(i.status) != 'cancelled' 
          {$appendSqlSite}
        ";

        $result  = $db->sql_query($SQL);
        $row = $db->sql_fetchrow($result);
        
        return $row['total'];
    }

    //==================================================================//
    function getTotalInvoicesPaidLastMonth() {
        $db = Zend_Registry::get('db');
        $fn = Zend_Registry::get('fn');
        $cpCfg = Zend_Registry::get('cpCfg');

        $appendSqlSite = "";
		if ($cpCfg['cp.hasMultiUniqueSites']){
		    $cpSiteIdSession = $fn->getSessionParam('cp_site_id');
		    $appendSqlSite = "  AND i.site_id = '{$cpSiteIdSession}'";
		}
        
        $lastMonth = date('Y-m',mktime (0,0,0,date("m")-1,date("d"), date("Y")));
        
        $SQL = "
        SELECT CONCAT('{$this->getCurPfx()}', FORMAT(SUM(invoice_amount{$this->getFldSfx()}), 0)) AS total
        FROM invoice i
        WHERE DATE_FORMAT(i.invoice_date, '%Y-%m') = '{$lastMonth}'
          AND LOWER(i.status) = 'paid' 
          AND LOWER(i.status) != 'cancelled' 
          {$appendSqlSite}
        ";

        $result  = $db->sql_query($SQL);
        $row = $db->sql_fetchrow($result);
        
        return $row['total'];
    }
    
    //==================================================================//
    function getTotalSalesThisYear() {
        $db = Zend_Registry::get('db');
        $fn = Zend_Registry::get('fn');
        $cpCfg = Zend_Registry::get('cpCfg');

        $appendSqlSite = "";
		if ($cpCfg['cp.hasMultiUniqueSites']){
		    $cpSiteIdSession = $fn->getSessionParam('cp_site_id');
		    $appendSqlSite = "  AND i.site_id = '{$cpSiteIdSession}'";
		}
        
        $thisYear = date('Y');
        
        $SQL = "
        SELECT CONCAT('{$this->getCurPfx()}', FORMAT(SUM(project_value{$this->getFldSfx()}), 0)) AS total
        FROM project p
        WHERE DATE_FORMAT(start_date, '%Y') = '{$thisYear}'
          AND LOWER(p.status) != 'cancelled' 
          {$appendSqlSite}
        ";

        $result  = $db->sql_query($SQL);
        $row = $db->sql_fetchrow($result);
        
        return $row['total'];
    }
}