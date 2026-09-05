<?
class CPL_Admin_Modules_Tradingsg_Invoice_Model extends CP_Admin_Modules_Tradingsg_Invoice_Model
{
    /**
     *
     */
    function getGenerateInvoiceFormSubmit() {
        $fn       = Zend_Registry::get('fn');
        $validate = Zend_Registry::get('validate');
        $db       = Zend_Registry::get('db');
        $dbUtil   = Zend_Registry::get('dbUtil');
        $cpCfg    = Zend_Registry::get('cpCfg');

        if (!$this->getGenerateInvoiceFormValidate()){
            return $validate->getErrorMessageXML();
        }

        $orderRowItem       = $fn->getPostParam('orderRowItem', array());
        $orderItemIds       = $fn->getPostParam('orderItemId', array());
        $invoice_amount     = $fn->getPostParam('invoice_amount');
        $invoice_date       = $fn->getPostParam('invoice_date');
        $invoice_due_date   = $fn->getPostParam('invoice_due_date');
        $invoice_terms      = $fn->getPostParam('invoice_terms');
        $notes              = $fn->getPostParam('notes');
        $order_id           = $fn->getReqParam('order_id');
        //$qty_arr          = $fn->getReqParam('qty', array());
        $qty_balance        = $fn->getReqParam('qty_balance');
        $cst        		= $fn->getReqParam('cst');
        $sgst               = $fn->getReqParam('sgst');
        $igst_cgst          = $fn->getReqParam('igst_cgst');
        $vat        		= $fn->getReqParam('vat');
        $cust_po_no        	= $fn->getReqParam('cust_po_no');
        //$frieght        	= $fn->getReqParam('frieght');
        $frieght_cost       = $fn->getReqParam('frieght_cost');
        $pf          		= $fn->getReqParam('p_f');
        $vat_value         	= $fn->getReqParam('vat_value');
        $cst_value          = $fn->getReqParam('cst_value');
        $sgst_value         = $fn->getReqParam('sgst_value');
        $igst_cgst_value    = $fn->getReqParam('igst_cgst_value');
        $site_id            = $fn->getSessionParam('cp_site_id');
        //print_r ($orderItemIds);
        //print_r ($qty_arr);
        //exit();

        $SQL = "SELECT invoice_id FROM invoice ORDER BY invoice_id DESC LIMIT 0,1 ";
        $result = $db->sql_query($SQL);
        $row = $db->sql_fetchrow($result);

        $invoiceRec = $fn->getRecordByCondition('invoice', "invoice_id = '{$row['invoice_id']}'");

        if ($invoiceRec['status'] == 'Cancelled'){
            $invoice_code = $fn->getSettingsValueByKey("nextInvoiceCode");
        } else {
            $appendSQLSiteInv = "";
            $invoice_code = $fn->getSettingsValueByKey("nextInvoiceCode");
            if ($cpCfg['cp.hasMultiUniqueSites']){
                $appendSQLSiteInv = " AND site_id = '{$site_id}'";
            }

            $SQLUpdate = "
            UPDATE setting SET value = (value + 1)
            WHERE key_text = 'nextInvoiceCode'
            {$appendSQLSiteInv}
            ";
            $resultUpdate = $db->sql_query($SQLUpdate);
        }

        //$invoice_code = $fn->getSettingsValueByKey("nextInvoiceCode");

        if($invoice_code < 10){
            $invoice_code = '00' . $invoice_code;
        }
        else if($invoice_code < 99){
            $invoice_code = '0' . $invoice_code;
        }
        else{
            $invoice_code = $invoice_code;
        }

        $fa = array();
        $fa['invoice_code']     = 'INV - '. date("Y") .' - ' . $invoice_code;
        $fa['invoice_amount']   = $invoice_amount;
        $fa['invoice_date']     = $invoice_date;
        $fa['invoice_due_date'] = $invoice_due_date;
        $fa['invoice_terms']    = $invoice_terms;
        $fa['notes']            = $notes;
        $fa['order_id']         = $order_id;
        $fa['status']           = 'Due';
        $fa['staff_id']         = $_SESSION['staff_id'];
        $fa['creation_date']    = date("Y-m-d H:i:s");
        $fa['created_by']       = $fn->getSessionParam('userName');
        $fa['invoice_type']     = 'Client';
        $fa['cust_po_no']     	= $cust_po_no;
        $fa['cst']     			= $cst;
        /*$fa['sgst']             = $sgst;
        $fa['igst_cgst']        = $igst_cgst;*/
        $fa['vat']     			= $vat;
        //$fa['frieght']     	= $frieght;
        $fa['frieght_cost']     = $frieght_cost;
        $fa['p_f']     			= $pf;

        if($vat == 1){
            $fa['vat_value'] = $vat_value;
        }

        if($cst == 1){
            $fa['cst_value'] = $cst_value;
        }

        if($sgst == 1){
            $fa['sgst_value'] = $sgst_value;
        }

        if($igst_cgst != ''){
            $fa['igst_cgst_value'] = $igst_cgst_value;
        }

        if ($cpCfg['cp.hasMultiUniqueSites']) {
            $fa['site_id'] = $site_id;
        }

        $insertInvoiceSQL   = $dbUtil->getInsertSQLStringFromArray($fa, 'invoice');
        $resultSQL          = $db->sql_query($insertInvoiceSQL);
        $invoice_id         = $db->sql_nextid();

        $count = count($orderItemIds);
        $recCount = 0;
        foreach ($orderItemIds as $key=> $value){
            $orderItemRec = $fn->getRecordRowByID('order_item', 'order_item_id', $value);
            $pfx  = $value . '_' ;
            $qty  = $fn->getPostParam("{$pfx}qty");

            if ($invoice_id > 0){
                $fa = array();
                $fa['invoice_id']   = $invoice_id;
                $fa['record_id']    = $orderItemRec['record_id'];
                $fa['qty']          = $qty;
                $fa['unit_price']   = $orderItemRec['unit_price'];
                $fa['cost_price']   = $orderItemRec['cost_price'];
                $fa['item_title']   = $orderItemRec['item_title'];
                $fa['module']       = $orderItemRec['module'];
                $fa['supplier_id']  = $orderItemRec['supplier_id'];
                $fa['part_number']  = $orderItemRec['part_number'];
                $fa['order_item_id']= $value;

                $invoice_item_id = $fn->addRecord($fa, 'invoice_item');
                //print_r ($fa);
                $recCount++;
            }
        }

        $sql ="
        SELECT SUM(it.qty * it.unit_price) As amount
        FROM invoice_item it
        WHERE it.invoice_id = {$invoice_id}
        ";
        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);

        $pfVal = 0;
        if($pf != ''){
            $pfVal = $row['amount'] * $pf / 100;
        }

        $frieghtCost = 0;
        if($frieght_cost != ''){
            $frieghtCost = $frieght_cost;
        }

        if($cst == 0 && $vat == 1){
			$gstvalue = ($row['amount'] + $pfVal + $frieghtCost) * $vat_value / 100;
			$amount = $gstvalue + $row['amount'];
		} else if($cst == 1 && $vat == 0){
			$gstvalue = ($row['amount'] + $pfVal + $frieghtCost) * $cst_value / 100;
			$amount = $gstvalue + $row['amount'];
        } else if($sgst == 1){
            $gstvalue = ($row['amount'] + $pfVal + $frieghtCost) * $sgst_value / 100;
            $amount = $gstvalue + $row['amount'];
        } else {
			$amount = $row['amount'];
        }

        if($igst_cgst != ''){
            $icgstvalue = ($row['amount'] + $pfVal + $frieghtCost) * $igst_cgst_value / 100;
            $amount = $icgstvalue + $amount;
        }

        //$frieghtVal = 0;
        /*if($frieght != ''){
            $frieghtVal = $row['amount'] * $frieght / 100;
        }*/

        //$amount = $amount + $frieghtVal + $pfVal;
        $amount = $amount + $frieghtCost + $pfVal;
        $amount = round($amount);

        $fa2 = array();
        $fa2['invoice_amount']  = $amount;

        $whereCondition = "
        WHERE invoice_id = {$invoice_id}
        ";
        $SQLInvoice = $dbUtil->getUpdateSQLStringFromArray($fa2, 'invoice', $whereCondition);
        $db->sql_query($SQLInvoice);

        //$this->getGenerateInvoiceForMedia($invoice_id);

        return $validate->getSuccessMessageXML();
    }

    /**
     *
     */
    function getGenerateInvoiceFormValidate() {
        $validate = Zend_Registry::get('validate');
        $fn = Zend_Registry::get('fn');

        $qty = $fn->getReqParam('qty');
        $qty_balance = $fn->getReqParam('qty_balance');

        $validate->resetErrorArray();
        //$validate->validateData('qty', 'Please enter the qty');

        /*if($qty_balance < $qty){
            $validate->errorArray['qty']['name'] = "qty";
            $validate->errorArray['qty']['msg']  = 'Please enter less qty';
        }*/

        if (count($validate->errorArray) == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     */
    function getEditInvoiceFormSubmit() {
        $fn       = Zend_Registry::get('fn');
        $validate = Zend_Registry::get('validate');
        $db       = Zend_Registry::get('db');
        $dbUtil   = Zend_Registry::get('dbUtil');
        $cpCfg    = Zend_Registry::get('cpCfg');

        $invoiceItemIds     = $fn->getPostParam('invoiceItemId', array());
        $invoice_amount     = $fn->getPostParam('invoice_amount');
        $invoice_date       = $fn->getPostParam('invoice_date');
        $invoice_due_date   = $fn->getPostParam('invoice_due_date');
        $invoice_terms      = $fn->getPostParam('invoice_terms');
        $notes              = $fn->getPostParam('notes');
        $order_id           = $fn->getReqParam('order_id');
        $invoice_id         = $fn->getReqParam('invoice_id');
        $qty_arr            = $fn->getReqParam('qty', array());
        $qty_balance        = $fn->getReqParam('qty_balance');
        $cst        		= $fn->getReqParam('cst');
        $sgst               = $fn->getReqParam('sgst');
        $igst_cgst          = $fn->getReqParam('igst_cgst');
        $vat        		= $fn->getReqParam('vat');
        $cust_po_no         = $fn->getReqParam('cust_po_no');
        $cst_value        	= $fn->getReqParam('cst_value');
        $sgst_value         = $fn->getReqParam('sgst_value');
        $igst_cgst_value    = $fn->getReqParam('igst_cgst_value');
        $vat_value        	= $fn->getReqParam('vat_value');
        //$frieght        	= $fn->getReqParam('frieght');
        $frieght_cost       = $fn->getReqParam('frieght_cost');
        $pf        			= $fn->getReqParam('p_f');
        $site_id            = $fn->getSessionParam('cp_site_id');

        $fa = array();
        $fa['invoice_amount']   = $invoice_amount;
        $fa['invoice_date']     = $invoice_date;
        $fa['invoice_due_date'] = $invoice_due_date;
        $fa['invoice_terms']    = $invoice_terms;
        $fa['notes']            = $notes;
        $fa['order_id']         = $order_id;
        $fa['staff_id']         = $_SESSION['staff_id'];
        $fa['modification_date']= date("Y-m-d H:i:s");
        $fa['created_by']       = $fn->getSessionParam('userName');
        $fa['cst']     			= $cst;
        $fa['sgst']             = $sgst;
        $fa['igst_cgst']        = $igst_cgst;
        $fa['vat']     			= $vat;
        $fa['cst_value']     	= $cst_value;
        $fa['sgst_value']       = $sgst_value;
        $fa['igst_cgst_value']  = $igst_cgst_value;
        $fa['vat_value']     	= $vat_value;

        if ($cpCfg['cp.hasMultiUniqueSites']) {
            $fa['site_id'] = $site_id;
        }
        //$fa['frieght']    	= $frieght;
        $fa['frieght_cost']     = $frieght_cost;
        $fa['p_f']     			= $pf;
        $fa['cust_po_no']     	= $cust_po_no;

        $totalvalue = '';

        $whereCondition = "WHERE invoice_id = {$invoice_id}";
        $sqlUpdate = $dbUtil->getUpdateSQLStringFromArray($fa, "invoice", $whereCondition);
        $resultUpdate      = $db->sql_query($sqlUpdate);

        $count = count($invoiceItemIds);
        $recCount = 0;
        for ($i= 0; $i< $count; $i++){
            $invoice_item_id = $invoiceItemIds[$i];
            $qty = $qty_arr[$i];

            $fa = array();
            $fa['qty']          = $qty;

            $whereCondition = "WHERE invoice_item_id = {$invoice_item_id}";
            $sqlUpdate = $dbUtil->getUpdateSQLStringFromArray($fa, "invoice_item", $whereCondition);
            $resultUpdate      = $db->sql_query($sqlUpdate);

            $recCount++;
        }

        $sql ="
        SELECT SUM(it.qty * it.unit_price) As amount
        FROM invoice_item it
        WHERE it.invoice_id = {$invoice_id}
        ";
        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);
        $sub_total = $row['amount'];

        /*
        if($cst == 0 && $vat == 1){
			$gsttaxvalue = $vat_value ;
			$gstvalue = ($sub_total * $gsttaxvalue) / 100;
			$amount = $gstvalue + $sub_total;
		} else if($cst == 1 && $vat == 0){
			$gsttaxvalue = $cst_value ;
			$gstvalue = ($sub_total * $gsttaxvalue) / 100;
			$amount = $gstvalue + $sub_total;
        } else {
			$amount = $row['amount'];
        }
        */

        $totalFrieght = 0;
        /*if($frieght != ''){
            $totalFrieght = ($sub_total * $frieght) / 100;
        }*/
        if ($frieght_cost !='') {
            $totalFrieght = $frieght_cost;
        }

        $totalpf = 0;
        if($pf != ''){
            $totalpf = ($sub_total * $pf) / 100;
        }

        if($vat == 1 && $cst == 0){
            $gsttaxvalue = $vat_value;
            $gstvalue = ($sub_total + $totalpf + $totalFrieght) * $gsttaxvalue / 100;
            //$gstvalue = ($sub_total + $totalpf) * $gsttaxvalue / 100;
            $totalvalue = $gstvalue + $sub_total;
        } else if($cst == 1 && $vat == 0){
            $gsttaxvalue = $cst_value;
            $gstvalue = ($sub_total + $totalpf + $totalFrieght) * $gsttaxvalue / 100;
            //$gstvalue = ($sub_total + $totalpf) * $gsttaxvalue / 100;
            $totalvalue = $gstvalue + $sub_total;
        } else if($sgst == 1){
            $gsttaxvalue = $sgst_value;
            $gstvalue = ($sub_total + $totalpf + $totalFrieght) * $gsttaxvalue / 100;
            $totalvalue = $gstvalue + $sub_total;
        }
        else{
            $totalvalue = $sub_total ;
        }

        if($igst_cgst != ''){
            $icgstvalue = ($sub_total + $totalpf + $totalFrieght) * $igst_cgst_value / 100;
            $totalvalue = $icgstvalue + $totalvalue;
        }

        $totalvalue = $totalvalue + $totalFrieght + $totalpf;
        $totalvalue = round($totalvalue);
        $fa2 = array();
        $fa2['invoice_amount']  = $totalvalue;

        $whereCondition = "
        WHERE invoice_id = {$invoice_id}
        ";
        $SQLInvoice = $dbUtil->getUpdateSQLStringFromArray($fa2, 'invoice', $whereCondition);
        $db->sql_query($SQLInvoice);

        return $validate->getSuccessMessageXML();
    }

    /**
     *
     */
    function setSearchVar() {
        $tv = Zend_Registry::get('tv');
        $fn = Zend_Registry::get('fn');
        $searchVar = Zend_Registry::get('searchVar');
        $searchVar->mainTableAlias = 'i';

        $invoice_id   = $fn->getReqParam('invoice_id');

        if ($invoice_id != "") {
            $searchVar->sqlSearchVar[] = "i.invoice_id = '{$invoice_id}'";
        } else if ($tv['record_id'] != '') {
            $searchVar->sqlSearchVar[] = "i.invoice_id = '{$tv['record_id']}'";
        } else {

            if ($tv['keyword'] != "") {
                $searchVar->sqlSearchVar[] = "(
                    i.invoice_code LIKE '%{$tv['keyword']}%' OR
                    i.order_id LIKE '%{$tv['keyword']}%' OR
                    c.company_name LIKE '%{$tv['keyword']}%'
                )";
            }

            $searchVar->sortOrder = "i.invoice_code DESC";
        }
    }

}