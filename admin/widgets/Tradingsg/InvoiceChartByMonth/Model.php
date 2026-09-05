<?
class CPL_Admin_Widgets_Tradingsg_InvoiceChartByMonth_Model extends CP_Admin_Widgets_Tradingsg_InvoiceChartByMonth_Model
{
    /**
     *
     */
    function getSQL(){

        $SQL = "
        SELECT DATE_FORMAT(i.invoice_date, '%b %Y') AS invoice_month
              ,(SUM(i.invoice_amount)) AS invoice_amount_monthly
        FROM invoice i
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
        $searchVar->mainTableAlias = 'i';

        $searchVar->sqlSearchVar[] = "i.status != 'Cancelled'";

        $last12Month = date('Y-m-d',mktime (0,0,0,date("m")-12,1, date("Y")));
        $today       = date('Y-m-d');

        if ($cpCfg['cp.hasMultiUniqueSites']){
            $cpSiteIdSession = $fn->getSessionParam('cp_site_id');
            $searchVar->sqlSearchVar[] = "i.site_id = '{$cpSiteIdSession}'";
        }

        $searchVar->sqlSearchVar[] = "(i.invoice_date BETWEEN '{$last12Month}' AND '{$today}')";
        $searchVar->groupBy = "DATE_FORMAT(i.invoice_date, '%Y-%m')";		
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
        $dataArray = $modelHelper->getWidgetDataArray($this->controller, 'tradingsg_invoiceChartByMonth');

        $this->dataArray = $dataArray;
        return $this->dataArray;
    }

}