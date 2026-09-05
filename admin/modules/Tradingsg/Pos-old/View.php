<?
class CPL_Admin_Modules_Tradingsg_Pos_View extends CP_Common_Lib_ModuleViewAbstract
{
    /**
     *
     */
    function getList($dataArray){
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $tv = Zend_Registry::get('tv');
        $cpUtil = Zend_Registry::get('cpUtil');

        $text = '';
        $rows = '';
        $readonly = '';
        $OrderItems = '';
        $modeOfPayment = '';
        $overallNetTotal = '';
        $mopArray = array(
            "Cash"
           ,"Credit Card"
        );

        $session_order_id = isset($_SESSION['order_id']) ? $_SESSION['order_id']  : '';
        /*<input type='submit' class='button ml10' value='Add'>*/
        $rowOrder = $fn->getRecordRowByID('order', 'order_id', $session_order_id);

        $site_id        = $fn->getSessionParam('cp_site_id');

        if($session_order_id < 10){
            $orderId = '0000' . $session_order_id;
        }
        else if($session_order_id < 99){
            $orderId = '000' . $session_order_id;
        }
        else if($session_order_id < 999){
            $orderId = '00' . $session_order_id;
        }
        else if($session_order_id < 9999){
            $orderId = '0' . $session_order_id;
        }
        else{
            $orderId = $session_order_id;
        }

        if($session_order_id == ''){
            $readonly = 'readonly';
            $css = "style='background-color:grey;'";
            $buttonCss = "allButtonsHide";
            $showOrderId = '';
        }
        if($session_order_id != ''){
            $OrderItems = $this->getOrderItems();
            $css = '';
            $buttonCss = 'allButtonsShow';
            $showOrderId = "ORD NO: {$orderId}";

            $modeOfPayment ="
            Mode Of Payment
            <select id='fld_mode_of_payment' name='mode_of_payment'>
                <option value=''>Please Select</option>
                {$cpUtil->getDropDown1($mopArray, 'Cash')}
            </select>
            ";
        }
        $SQLInvoice = "
        SELECT *
        FROM `invoice`
        WHERE order_id = '{$session_order_id}'
        ";
        $resultInvoice = $db->sql_query($SQLInvoice);
        $rec = $db->sql_fetchrow($resultInvoice);

        $invoice_code = $rec['invoice_code'];

            // CHECK PENDING ORDER //
        $checkPendingOrder = '' ;
        $printThermalPrinter = '';
        $thermalPrinter = '';

        if ($session_order_id !=''){
            $checkPendingOrder ="<input type='button' id='changeStatusPending' class='button mb10' value='Change Status To Pending'>" ;
        } else {
            $checkPendingOrder ="<input type='button' id='checkPendingOrder' class='button mb10' value='Check Pending Order'>" ;
        }

        //$url = "index.php?_topRm=pos&module={$tv['module']}&_spAction=printBill&invoice_code={$invoice_code}&order_id={$session_order_id}&showHTML=0";
        //$returnUrl = "<a href='{$url}' target=_blank ></a>";
                    //<input type='button' id='checkPendingOrder' class='button mb10' value='Check Pending Order'>
        $formUpdateDiscount = "index.php?_topRm=pos&module=tradingsg_pos&_spAction=applyDiscount&showHTML=0";
        $formAddClient = "index.php?_topRm=pos&module=tradingsg_pos&_spAction=addClient&showHTML=0";

        //if(strpos($_SERVER['HTTP_HOST'], 'localhost') || strpos($_SERVER['HTTP_HOST'], 'testpilotweb')){
            //<a href='PrintExcel.php' id='thermalPrinter'>Thermal Print</a>
            $printThermalPrinter ="
            <div class='button float_right thermalPrintHide'>
                <a onclick=\"javascript:jsWebClientPrint.print('orderId={$session_order_id}');\"
                id='thermalPrinter'>Thermal Print</a>
            </div>
            ";

            $thermalPrinter ="
            <div class='button float_right'>
                <a href='#' id='thermalPrinterPrint'>Print</a>
            </div>
            ";
        //}

        /*<th>Discount Type</th>
          <th>Discount Value</th>*/
        $customerInfo ='';
        $removeClient = '';

        if($rowOrder['cust_company_name'] != ''){
            $customerInfo ="
            <div class='mt10'>
                <div>Company Name: {$rowOrder['cust_company_name']}</div>
                <div>Mobile: {$rowOrder['cust_phone']}</div>
                <div>Email : {$rowOrder['cust_email']}</div>
                <div>Address: {$rowOrder['cust_address1']} ,{$rowOrder['cust_address2']} ,{$rowOrder['cust_address_city']} {$rowOrder['cust_address_state']}</div>
            </div>
            ";

            $removeClient = "
            <div class='button float_left mt10'>
                <a href='javascript:void(0);' id='removeClient'>Remove Client</a>
            </div>
            ";
        }

        $text = "
        <!-- OLD ONE FOR POS LIST VIEW BY THAMIM -->
        <!--<div class='floatbox mt20'>
            <div class='float_left'>
                <input type='button' id='newOrder' class='button mb10' value='New Order'>
                <div>{$showOrderId}</div>
            </div>
            <div class='addProduct'>
                Product Name / Model No / Item Code/ Carton No: <input type='text' value='' id='fld_product_title' class='text' name='product_title' {$readonly} {$css}>
                    {$checkPendingOrder}
                <div class='floatbox mt5'>
                    <div class='float_left'>Vat: Included in all products</div>
                    <div class='float_right'>
                        {$modeOfPayment}
                    </div>
                </div>
            </div>
        </div>-->

        <div class='panel panel-info'>
             <div class='panel-heading'>
                <div class='OrderNoOnTop'>
                    {$showOrderId}
                </div>
            </div>
            <div class='panel-body'>
                <table class=''>
                    <tr>
                        <td>
                            <div class='float_left'>
                                <!--<div class='mt5'><input type='button' id='newOrder' class='button mb10' value='Start Order'></div>
                                <div class='OrderNoOnTop'>{$showOrderId}</div>-->
                                <!--<div class='mt5'><input type='submit' id='cancelOrder' class='button mb10' value='End Order'></div>-->
                            </div>
                        </td>
                        <td>
                            <div class='floatbox mt5'>
                                <div class='addProduct'>
                                         Product Name/ Item Code: <input type='text' value='' id='fld_product_title' class='text' name='product_title' {$readonly} {$css}>
                                          {$checkPendingOrder}
                                    <div class='floatbox mt5'>
                                        <div class='float_left'>Vat: Included in all products</div>
                                        <div class='float_right'>
                                            {$modeOfPayment}
                                        </div>
                                    </div>
                                </div>
                             </div>
                        </td>
                        <td>
                            <div class='float_right'>
                            <div class='mt5'><input type='button' id='newOrder' class='buttonpos btn btn-success mb10' value='Start Order'></div>
                            <div class='mt5'><input type='submit' id='cancelOrder' class='buttonpos2 btn btn-danger mb10' value='End Order'></div>
                            </div>
                    </tr>

                </table>
            </div>
        </div>

        <div class='panel panel-info'>
            <div class='panel-body'>        
                <table class='list thinlist'>
                    <thead>
                        <tr class='txt_16px'>
                            <th width='4%' >S.No</th>
                            <th width='31%'>Item Name</th>
                            <th width='11%'>HSN Code</th>
                            <th width='5%' >UOM</th>
                            <th width='5%' >Qty</th>
                            <th width='9%' >Unit Price</th>
                            <th width='11%'>Total</th>
                            <th width='3%' >Delete</th>
                        </tr>
                    </thead>
                    <tbody id='orderItems'>
                        {$OrderItems}
                    </tbody>
                </table>
                <div class='floatbox mt20 $buttonCss'>
                    <!-- <input type='text' id='closeOrder' class='button float_right' value='Close Order'> -->
                    <!--<input type='submit' id='cancelOrder' class='button float_right' value='Close Order'>-->
                    {$thermalPrinter}
                    <input type='submit' id='generateBill' class='button float_right' value='Export'>
                    <div class='button float_right'>
                        <a href='{$formUpdateDiscount}' id='applyDiscount'>Apply Discount</a>
                    </div>
                    {$printThermalPrinter}
                    <!--<div class='button float_right'>
                        <a href='' id='closeOrder'>Close Order</a>
                    </div>-->
                    <div class='button float_left'>
                        <a href='{$formAddClient}' id='addClient'>Add Client</a>
                    </div>
                    <input type='text' value='' id='fld_customer_name' class='text' name='customer_name' {$readonly} {$css}>
                </div>
                <div class='floatbox' id='customerDetailsDisplay'>
                    {$customerInfo}
                    {$removeClient}
                </div>
            </div>
        </div>
        ";

        return $text;
    }

    /**
     *
     */
    function getOrderItems(){
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $dbUtil = Zend_Registry::get('dbUtil');
        $formObj = Zend_Registry::get('formObj');
        $cpCfg = Zend_Registry::get('cpCfg');
        $expNoEdit  = array('isEditable' => 0);
        $session_order_id = isset($_SESSION['order_id']) ? $_SESSION['order_id']  : false;

        $text = '';
        $qtytotal = '';
        $rows = '';
        $subTotal = 0;
        $netTotal = 0;
        $discount = '';
        $discount_percentage_amount_sum = '';
        $discountValue = '';
        $Overallsubtotalwithoutdiscount = 0;

        //New Changes
        //$sqlDiscountVal = $fn->getValueListSQL('discountValue');
        $cpSiteIdSession = $fn->getSessionParam('cp_site_id');
        $sqlDiscountVal = "SELECT CONCAT_WS(' ',value,'%') FROM valuelist
                                    WHERE key_text='discountValue'
                                    ORDER BY value ASC";

        $expVl      = array('sqlType' => 'OneField','firstOptionLabel' => 'No Discount');

        //TO CHECK IF THE SUM OF DISCOUNT TYPE(%) HAS VALUE OR NOT AND ASSIGN THE QUERY IF IT HAS VALUE ELSE ASSIGN ZERO
        $subSqlForPercentSum = "
        SELECT SUM(round(((oi.unit_price * oi.discount_percentage )/100)* oi.qty,2)) as discount_sum
        FROM order_item oi
        WHERE oi.order_id = {$session_order_id}
          AND oi.discount_type = '%'
        ";
        $resultSubSql = $db->sql_query($subSqlForPercentSum);
        $rowSql       = $db->sql_fetchrow($resultSubSql);
        if($rowSql['discount_sum'] > 0){
            $subSqlForPercentSum = "
            SELECT SUM(round(((oi.unit_price * oi.discount_percentage )/100)* oi.qty,2))
            FROM order_item oi
            WHERE oi.order_id = {$session_order_id}
              AND oi.discount_type = '%'
            ";
        }
        else{
            $subSqlForPercentSum = 0;
        }


        //TO CHECK IF THE SUM OF DISCOUNT TYPE(VALUE) HAS VALUE OR NOT AND ASSIGN THE QUERY IF IT HAS VALUE ELSE ASSIGN ZERO
        $subSqlForValueSum ="
        SELECT SUM(round(oi.discount_amount  * oi.qty,2)) as discount_sum
        FROM order_item oi
        WHERE oi.order_id = {$session_order_id}
          AND oi.discount_type = 'Value'
        ";
        $resultSubSql = $db->sql_query($subSqlForValueSum);
        $rowSql       = $db->sql_fetchrow($resultSubSql);
        if($rowSql['discount_sum'] > 0){
            $subSqlForValueSum ="
            SELECT SUM(round(oi.discount_amount  * oi.qty,2))
            FROM order_item oi
            WHERE oi.order_id = {$session_order_id}
              AND oi.discount_type = 'Value'
            ";
        }
        else{
            $subSqlForValueSum = 0;
        }

        $SQL    = "
        SELECT oi.*
              ,o.discount
              ,CONCAT_WS('::', p.hsn) as code
              ,p.unit
              ,(SELECT
              ($subSqlForPercentSum)
               +
              ($subSqlForValueSum)) as discount_percentage_amount_sum
        FROM order_item oi
        LEFT JOIN (`order` o) ON (o.order_id = oi.order_id)
        LEFT JOIN (product p) ON (p.product_id = oi.record_id)
        WHERE oi.order_id = {$session_order_id}
        ";
        $result = $db->sql_query($SQL);
        $count = 1;
        while ($row = $db->sql_fetchrow($result)) {
            $discount_value_for_one_qty = '';
            $discountValue = 0;
            $discount_percentage = '';
            $discount_percentage_type =0;
            if($row['discount_percentage'] > 0 || $row['discount_amount'] > 0){
                if($row['discount_type'] == '%'){
                    $discount_value_for_one_qty  =  $row['unit_price'] * ($row['discount_percentage']/100);
                    $discountValue = $discount_value_for_one_qty * $row['qty'];
                    $discount_percentage_type = $row['discount_percentage'];
                    $discount_percentage = '';
                }
                else if($row['discount_type']  == 'Value'){
                    $discount_value_for_one_qty  =  $row['discount_amount'];
                    $discountValue = $discount_value_for_one_qty * $row['qty'];
                    $discount_percentage = $row['discount_amount'];
                    $discount_percentage_type = $row['discount_amount'];
                }
                $discountValue = number_format($discountValue, 2);
            }
            //$discount_percentage = number_format($discount_percentage,2);
            $subtotalwithoutdiscount = $row['qty'] * $row['unit_price'];
            $total = $row['qty'] * ($row['unit_price'] - $discount_value_for_one_qty);
            $subTotal += $total;
            $Overallsubtotalwithoutdiscount += $subtotalwithoutdiscount;
            $total = number_format($total, 2);
            $discount = $row['discount'];
            $netTotal = $subTotal - $discount;
            $discount_percentage_amount_sum = $row['discount_percentage_amount_sum'];

            $StockSql = "
            SELECT
                (SELECT SUM(qty) FROM po_product
                WHERE product_id = {$row['record_id']}) as product_qty_purchased
                ,(SELECT SUM(oi.qty) FROM order_item oi
                LEFT JOIN (`order` o) ON (o.order_id = oi.order_id)
                WHERE record_id = {$row['record_id']}
                  AND o.order_status = 'Paid'
                  AND o.link_stock = 1
                ) as product_qty_sold
                ,(SELECT SUM(srh.qty_return) FROM sales_return_history srh
                LEFT JOIN (invoice_item ini) ON (ini.invoice_item_id = srh.invoice_item_id)
                LEFT JOIN (invoice inv) ON (inv.invoice_id = srh.invoice_id AND inv.status != 'Cancelled' )
                WHERE ini.record_id = {$row['record_id']}
                  AND srh.status IS NULL
                ) as sales_return_qty
                ,(SELECT po.damaged_qty FROM product po
                WHERE product_id = {$row['record_id']}
                ) as damaged_qty
            ";
            $resultStockSql = $db->sql_query($StockSql);
            $rowStockSql    = $db->sql_fetchrow($resultStockSql);

            $stock = $rowStockSql['product_qty_purchased'] - $rowStockSql['product_qty_sold'] + $rowStockSql['sales_return_qty'] - $rowStockSql['damaged_qty'];
            $expDiscountType = array('sqlType' => 'OneField');

            $subtotalwithoutdiscount = number_format($subtotalwithoutdiscount, 2);

            $rows .= "
            <tr class ='{$row['order_item_id']} txt_16px'>
            <td class='txtRight'>{$count}</td>
            <td class='w25p'>{$row['item_title']}</td>
            <td>{$row['code']}</td>
            <td>{$row['unit']}</td>
            <td class='w40'><input type='text' value='{$row['qty']}' id='fld_qty' class='text w40 txtRight txt_16px' name='qty' order_item_id='{$row['order_item_id']}' stock='{$stock}'></td>
            <td class='unitPrice txtRight'>{$row['unit_price']}</td>
            <td class='txtRight'>{$subtotalwithoutdiscount}</td>
            <td><a href='#' class='deleteItem' order_item_id='{$row['order_item_id']}'>Delete</a></td>
            </tr>
            ";

            $qtytotal += $row['qty'];
            $count++;
        }

            $overallSubTotal = number_format($subTotal, 2);
            $overallNetTotal = number_format($netTotal, 2);
            $Overallsubtotalwithoutdiscount = number_format($Overallsubtotalwithoutdiscount, 2);

        $text = "
        {$rows}
        <!--<tr>
            <td colspan='9' class='txtRight totalFontSize'>Total</td>
            <td class='txtRight paymentTotal totalFontSize'>{$overallSubTotal}</td>
            <td></td>
        </tr>-->
        <tr>
            <td colspan='4' class='totalQty'>Total Qty</td>
            <td class='totalQty'>{$qtytotal}</td>
            <td colspan='1' class='totalDiscount totalFontSize'>Total Discount</td>
            <td id = 'fld_totalDiscount_amount' class='totalDiscount totalFontSize'>{$discount_percentage_amount_sum}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan='6' class='netTotal'>Net Total</td>
            <td id = 'fld_netTotal_amount' class='txtRight netTotal'>{$overallNetTotal}</td>
            <td></td>
        </tr>
        <tr class='txt_20px'>
            <td colspan='6' class='txtRight'>Amount Paid</td>
            <td class='txtRight amountGiven'>
                <input type='text' value='' id='fld_amount_given' class='text w150 txtRight txt_20px' name='amount_given' total='{$netTotal}'>
            </td>
            <td></td>
        </tr>
        <tr class='balanceRow'>
            <td colspan='8' class='netTotal'>Change</td>
            <td class='netTotal balance'></td>
            <td></td>
        </tr>
        <input type='hidden' id = 'fld_subtotal_amount' name='subtotal_amount' value='{$Overallsubtotalwithoutdiscount}'>
        <input type='hidden' id = 'fld_qty_total' name='qty_total' value='{$qtytotal}'>
        ";

            /*<td class='txtRight totalDiscount'>
                <input type='text' value='{$discount}' id='fld_discount' class='text w50 txtRight' name='discount' order_id='{$session_order_id}'>
            </td>*/

        return $text;
    }

    /**
     *
     */
    function getOrderItems1(){
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $dbUtil = Zend_Registry::get('dbUtil');
        $formObj = Zend_Registry::get('formObj');
        $cpCfg = Zend_Registry::get('cpCfg');
        $session_order_id = isset($_SESSION['order_id']) ? $_SESSION['order_id']  : false;

        $text = '';
        $qtytotal = '';
        $rows = '';
        $subTotal = 0;
        $netTotal = 0;
        $discount = '';
        $discount_percentage_amount_sum = '';
        $discountValue = '';
        $Overallsubtotalwithoutdiscount = 0;

        //New Changes
        //$sqlDiscountVal = $fn->getValueListSQL('discountValue');
        $cpSiteIdSession = $fn->getSessionParam('cp_site_id');
        $sqlDiscountVal = "SELECT CONCAT_WS(' ',value,'%') FROM valuelist
                                    WHERE key_text='discountValue'
                                    ORDER BY value ASC";

        $expVl      = array('sqlType' => 'OneField','firstOptionLabel' => 'No Discount');

        //TO CHECK IF THE SUM OF DISCOUNT TYPE(%) HAS VALUE OR NOT AND ASSIGN THE QUERY IF IT HAS VALUE ELSE ASSIGN ZERO
        $subSqlForPercentSum = "
        SELECT SUM(round(((oi.unit_price * oi.discount_percentage )/100)* oi.qty,2)) as discount_sum
        FROM order_item oi
        WHERE oi.order_id = {$session_order_id}
          AND oi.discount_type = '%'
        ";
        $resultSubSql = $db->sql_query($subSqlForPercentSum);
        $rowSql       = $db->sql_fetchrow($resultSubSql);
        if($rowSql['discount_sum'] > 0){
            $subSqlForPercentSum = "
            SELECT SUM(round(((oi.unit_price * oi.discount_percentage )/100)* oi.qty,2))
            FROM order_item oi
            WHERE oi.order_id = {$session_order_id}
              AND oi.discount_type = '%'
            ";
        }
        else{
            $subSqlForPercentSum = 0;
        }


        //TO CHECK IF THE SUM OF DISCOUNT TYPE(VALUE) HAS VALUE OR NOT AND ASSIGN THE QUERY IF IT HAS VALUE ELSE ASSIGN ZERO
        $subSqlForValueSum ="
        SELECT SUM(round(oi.discount_percentage  * oi.qty,2)) as discount_sum
        FROM order_item oi
        WHERE oi.order_id = {$session_order_id}
          AND oi.discount_type = 'Value'
        ";
        $resultSubSql = $db->sql_query($subSqlForValueSum);
        $rowSql       = $db->sql_fetchrow($resultSubSql);
        if($rowSql['discount_sum'] > 0){
            $subSqlForValueSum ="
            SELECT SUM(round(oi.discount_percentage  * oi.qty,2))
            FROM order_item oi
            WHERE oi.order_id = {$session_order_id}
              AND oi.discount_type = 'Value'
            ";
        }
        else{
            $subSqlForValueSum = 0;
        }

        $SQL    = "
        SELECT oi.*
              ,o.discount
              ,CONCAT_WS(' ', p.item_code) as code
              ,p.unit
              ,(SELECT
              ($subSqlForPercentSum)
               +
              ($subSqlForValueSum)) as discount_percentage_amount_sum
        FROM order_item oi
        LEFT JOIN (`order` o) ON (o.order_id = oi.order_id)
        LEFT JOIN (product p) ON (p.product_id = oi.record_id)
        WHERE oi.order_id = {$session_order_id}
        ";
        $result = $db->sql_query($SQL);
        $count = 1;
        while ($row = $db->sql_fetchrow($result)) {
            $discount_value_for_one_qty = '';
            $discountValue = 0;
            $discount_percentage = '';
            $discount_percentage_type =0;
            if($row['discount_percentage'] > 0){
                if($row['discount_type'] == '%'){
                    $discount_value_for_one_qty  =  $row['unit_price'] * ($row['discount_percentage']/100);
                    $discountValue = $discount_value_for_one_qty * $row['qty'];
                    $discount_percentage_type = $row['discount_percentage']+'%';
                    $discount_percentage = '';
                }
                else if($row['discount_type']  == 'Value'){
                    $discount_value_for_one_qty  =  $row['discount_percentage'];
                    $discountValue = $discount_value_for_one_qty * $row['qty'];
                    $discount_percentage =$row['discount_percentage'];
                    $discount_percentage_type = $row['discount_type'];
                }
                $discountValue = number_format($discountValue, 2);
            }
            //$discount_percentage = number_format($discount_percentage,2);
            $subtotalwithoutdiscount = $row['qty'] * $row['unit_price'];
            $total = $row['qty'] * ($row['unit_price'] - $discount_value_for_one_qty);
            $subTotal += $total;
            $Overallsubtotalwithoutdiscount += $subtotalwithoutdiscount;
            $total = number_format($total, 2);
            //$total = number_format($subtotalwithoutdiscount, 2);
            $discount = $row['discount'];
            $netTotal = $subTotal - $discount;
            $discount_percentage_amount_sum = $row['discount_percentage_amount_sum'];

            $StockSql = "
            SELECT
                (SELECT SUM(qty) FROM po_product
                WHERE product_id = {$row['record_id']}) as product_qty_purchased
                ,(SELECT SUM(oi.qty) FROM order_item oi
                LEFT JOIN (`order` o) ON (o.order_id = oi.order_id)
                WHERE record_id = {$row['record_id']}
                  AND o.order_status = 'Paid'
                  AND o.link_stock = 1
                ) as product_qty_sold
                ,(SELECT SUM(srh.qty_return) FROM sales_return_history srh
                LEFT JOIN (invoice_item ini) ON (ini.invoice_item_id = srh.invoice_item_id)
                LEFT JOIN (invoice inv) ON (inv.invoice_id = srh.invoice_id AND inv.status != 'Cancelled' )
                WHERE ini.record_id = {$row['record_id']}
                  AND srh.status IS NULL
                ) as sales_return_qty
                ,(SELECT po.damaged_qty FROM product po
                WHERE product_id = {$row['record_id']}
                ) as damaged_qty
            ";
            $resultStockSql = $db->sql_query($StockSql);
            $rowStockSql    = $db->sql_fetchrow($resultStockSql);

            $stock = $rowStockSql['product_qty_purchased'] - $rowStockSql['product_qty_sold'] + $rowStockSql['sales_return_qty'] - $rowStockSql['damaged_qty'];
            $expDiscountType = array('sqlType' => 'OneField');

            $rows .= "
            <tr class ='{$row['order_item_id']} txt_16px'>
            <td class='txtRight'>{$count}</td>
            <td class='w25p'>{$row['item_title']}</td>
            <td>{$row['code']}</td>
            <td>{$row['unit']}</td>
            <td class='w40'><input type='text' value='{$row['qty']}' id='fld_qty' class='text w40 txtRight txt_16px' name='qty' order_item_id='{$row['order_item_id']}' stock='{$stock}'></td>
            <td class='unitPrice txtRight'>{$row['unit_price']}</td>
            <td  class='txtRight netTotalForProd'>{$subtotalwithoutdiscount}</td>
            <td><a href='#' class='deleteItem' order_item_id='{$row['order_item_id']}' >Delete</a></td>
            </tr>
            ";

            //{$formObj->getDDRowByArr('', 'discount_type', $cpCfg['m.trading.quote.markUpTypeArr'], $row['discount_type'], $expDiscountType)}

            $qtytotal += $row['qty'];
            $count++;
        }

            $overallSubTotal = number_format($subTotal, 2);
            $overallNetTotal = number_format($netTotal, 2);
            $Overallsubtotalwithoutdiscount = number_format($Overallsubtotalwithoutdiscount, 2);

        $text = "
        {$rows}
        <tr>
            <td colspan=6 class='txtRight totalFontSize'>Total</td>
            <td class='txtRight paymentTotal totalFontSize'>{$Overallsubtotalwithoutdiscount}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan='4' class='totalQty'>Total Qty</td>
            <td class='totalQty'>{$qtytotal}</td>
            <td class='totalFontSize'>Discount</td>
            <td id = 'fld_totalDiscount_amount' class='totalFontSize'>{$discount_percentage_amount_sum}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan='6' class='netTotal'>Net Total</td>
            <td id = 'fld_netTotal_amount' class='txtRight netTotal'>{$overallNetTotal}</td>
            <td></td>
        </tr>
        <tr class='txt_20px'>
            <td colspan='6' class='txtRight'>Amount Paid</td>
            <td class='txtRight amountGiven'>
                <input type='text' value='' id='fld_amount_given' class='text w150 txtRight txt_20px' name='amount_given' total='{$netTotal}'>
            </td>
            <td></td>
        </tr>
        <tr class='balanceRow'>
            <td colspan='6' class='netTotal'>Balance</td>
            <td class='netTotal balance'></td>
            <td></td>
        </tr>
        <input type='hidden' id = 'fld_subtotal_amount' name='subtotal_amount' value='{$Overallsubtotalwithoutdiscount}'>
        <input type='hidden' id = 'fld_qty_total' name='qty_total' value='{$qtytotal}'>
        ";

            /*<td class='txtRight totalDiscount'>
                <input type='text' value='{$discount}' id='fld_discount' class='text w50 txtRight' name='discount' order_id='{$session_order_id}'>
            </td>*/

        return $text;
    }

    /**
     *
     */

     function getApplyDiscount() {
        $fn = Zend_Registry::get('fn');
        $formObj = Zend_Registry::get('formObj');

        $formAction = "index.php?_topRm=pos&module=tradingsg_pos&_spAction=applyDiscountSubmit&showHTML=0";
        $sqlDiscountVal = $fn->getValueListSQL('discountValue');
        $expVl      = array('sqlType' => 'OneField');

        $text = "
        <form id='portalForm' class='yform columnar' method='post' action='{$formAction}'>
            {$formObj->getDDRowBySQL('Discount %', 'discount_percentage', $sqlDiscountVal,'', $expVl)}
        </form>
        ";
        return $text;

    }

    /**
     *
     */

     function getAddClient() {
        $fn = Zend_Registry::get('fn');
        $formObj = Zend_Registry::get('formObj');

        $formAction = "index.php?_topRm=pos&module=tradingsg_pos&_spAction=addClientSubmit&showHTML=0";
        $sqlCountry = getCPModelObj('common_geoCountry')->getCountryDDSQL();
        //$this->model->getUpdateOrderLineItems2();
        $text = "
        <form id='portalForm' class='yform columnar' method='post' action='{$formAction}'>
            {$formObj->getTBRow('Company Name', 'company_name')}
            {$formObj->getTBRow('Mobile', 'mobile')}
            {$formObj->getTBRow('Email', 'email')}
            {$formObj->getTBRow('Address1', 'address_flat')}
            {$formObj->getTBRow('Address2', 'address_street')}
            {$formObj->getTBRow('District/ Town', 'address_town')}
            {$formObj->getTBRow('State/ Zip', 'address_state')}
            {$formObj->getDDRowBySQL('Country', 'address_country', $sqlCountry)}
        </form>
        ";
        return $text;

    }


    /**
     *
     */
    function getPrintBillPdf($printOnly = '') {
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

        $session_order_id = isset($_SESSION['order_id']) ? $_SESSION['order_id']  : '';
        $invoice_code = $fn->getReqParam('invoice_code');
        //$order_id = $fn->getReqParam('order_id');
        $orderNo = $fn->getReqParam('orderNo');
        $discountValueTotal = 0;

        if($printOnly == '') {
            $printOnly = $fn->getReqParam('printOnly');
        }

        if ($printOnly != 1 ) {
            $SQL    = "
            UPDATE `order`
            set order_status = 'Paid'
            WHERE order_id = {$session_order_id}
            ";
            $result = $db->sql_query($SQL);
        }

        if ($orderNo == '' ) {
            if($session_order_id < 10){
                $orderId = '0000' . $session_order_id;
            }
            else if($session_order_id < 99){
                $orderId = '000' . $session_order_id;
            }
            else if($session_order_id < 999){
                $orderId = '00' . $session_order_id;
            }
            else if($session_order_id < 9999){
                $orderId = '0' . $session_order_id;
            }
            else{
                $orderId = $session_order_id;
            }
        } else {
            if($orderNo < 10){
                $orderId = '0000' . $orderNo;
            }
            else if($orderNo < 99){
                $orderId = '000' . $orderNo;
            }
            else if($orderNo < 999){
                $orderId = '00' . $orderNo;
            }
            else if($orderNo < 9999){
                $orderId = '0' . $orderNo;
            }
            else{
                $orderId = $orderNo;
            }
        }


        $SQL = "
        SELECT oi.*
              ,p.title AS product_title
              ,p.unit
              ,p.item_code
              ,CONCAT_WS('::', p.part_number, p.model) code
              ,o.order_date
              ,o.order_id
              ,i.vat AS invoice_vat
              ,i.invoice_code_vat
              ,i.invoice_code
              ,oi.qty * oi.unit_price AS amount
              ,(SELECT SUM(oit.qty * oit.unit_price) FROM order_item oit
               WHERE oit.order_id = oi.order_id) AS sub_total
        FROM order_item oi
        LEFT JOIN product p ON (p.product_id = oi.record_id)
        LEFT JOIN `order` o ON (o.order_id = oi.order_id)
        LEFT JOIN invoice i ON (i.order_id = o.order_id)
        WHERE o.order_id = '{$orderId}'
        ORDER BY p.title
        ";
        $result = $db->sql_query($SQL);

        $numRows  = $db->sql_numrows($result);

        $today = date('d-m-Y');
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
        $discountValue =0;
        $lineItemNumber = 1;  // To increment the line item in receipt

        //============================================================================= //
        $pdf->SetFont('Arial','',10);
        while ($row = $db->sql_fetchrow($result)) {
            $discount_value_for_one_qty = 0;
            $discountValue =0;
            if($row['discount_percentage'] > 0){
                if($row['discount_type'] == '%'){
                    $discount_value_for_one_qty  =  $row['unit_price'] * ($row['discount_percentage']/100);
                    $discountValue = $discount_value_for_one_qty * $row['qty'];
                }
                else if($row['discount_type']  == 'Value'){
                    $discount_value_for_one_qty  =  $row['discount_percentage'];
                    $discountValue = $discount_value_for_one_qty * $row['qty'];
                }
                $discountValueTotal += $discountValue;
            }

            if ($count == 0){
                /* Logo of the institution */
                $pdf->Image('images/logo-print.gif',10,5,45);
                $pdf->SetFont('Courier','B',9);
                $pdf->Cell(50, 20, $cpCfg['cp.companyName']);
                $pdf->Ln(5);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf7']);
                $pdf->Ln(5);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf6']);
                $pdf->Ln(5);
                $pdf->Cell(50, 20, $cpCfg['printWebAddress']);

                $creationDate   = $fn->getCPDate($row['order_date'], 'd-m-Y');

                /* Company address */
                //Address to be got from settings
                $pdf->SetXY(130,0);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf1']);
                $pdf->Ln(5);
                $pdf->SetXY(130,5);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf2']);
                $pdf->Ln(5);
                $pdf->SetXY(130,10);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf3']);
                $pdf->Ln(5);
                $pdf->SetXY(130, 15);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf4']);
                $pdf->Ln(5);
                $pdf->SetXY(130,20);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf5'  ]);
                $pdf->Ln(5);
                /*$pdf->SetXY(130,25);
                $pdf->Cell(50, 20, $cpCfg['printTelephoneAndFax']);
                $pdf->Ln(5);*/
                $pdf->SetXY(130,25);
                $pdf->Cell(50, 20, $cpCfg['printEmailAddress']);

                /* Header */
                $pdf->SetFont('Courier','BU',9);
                $pdf->SetXY(80, 45);
                $pdf->Cell(50, 20, "BILL", 0, 0, 'C');
                $pdf->SetFont('Courier','B',9);
                $pdf->SetX(130);
                $pdf->Cell(31, 20, "DATE : " . $creationDate, 0, 0, 'L');
                $pdf->Ln(20);

                if($row['invoice_vat'] == 1){
                    $invoiceCode = 'INVT -' . $row['invoice_code_vat'];
                } else {
                    $invoiceCode = $row['invoice_code'];
                }

                /* Invoice Details*/
                $pdf->SetFont('Courier','B',9);
                $pdf->SetFillColor(254,203,156);
                $pdf->Cell(30,8,"BILL NO :",1,0, 'L', 1);
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(66, 8, $invoiceCode, 1, 0, 'L', 1);
                $pdf->SetFillColor(254,203,156);
                $pdf->Cell(30,8,"ORD NO :",1,0, 'L', 1);
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(66, 8, $orderId, 1, 0, 'L', 1);
                $pdf->Ln(12);

                /* List of order items header */
                $pdf->SetFillColor(254,203,156);
                $pdf->Cell(10,8,"S.NO",1,0, 'C', 1);
                $pdf->Cell(48,8,"NAME OF THE ITEM",1,0, 'C', 1);
                $pdf->Cell(57,8,"ITEM CODE",1,0, 'C', 1);
                $pdf->Cell(10,8,"UOM",1,0, 'C', 1);
                $pdf->Cell(10,8,"QTY",1,0, 'C', 1);
                $pdf->Cell(20,8,"UNIT PRICE",1,0, 'C', 1);
                $pdf->Cell(18,8,"DISCOUNT",1,0, 'C', 1);
                $pdf->Cell(22,8,"AMOUNT" ,1,0, 'C', 1);
                $pdf->Ln();
            }

            //===================================MAIN TABLE============================= //
            $amount = $row['amount'] - $discountValue;
            $discount_value_for_one_qty = number_format($discount_value_for_one_qty, 2);

            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(10, 8, $lineItemNumber, 1, 0, 'C', 1);
            $pdf->Cell(48, 8, $row['product_title'], 1, 0, 'L', 1);
            $pdf->Cell(57, 8, $row['code'], 1, 0, 'L', 1);
            $pdf->Cell(10, 8, $row['unit'], 1, 0, 'R', 1);
            $pdf->Cell(10, 8, $row['qty'], 1, 0, 'R', 1);
            $pdf->Cell(20, 8, $row['unit_price'], 1, 0, 'R', 1);
            $pdf->Cell(18, 8, '- ' . $discount_value_for_one_qty, 1, 0, 'R', 1);
            $pdf->Cell(22, 8, number_format($amount, 2), 1, 0, 'R', 1);
            $pdf->Ln();

            $count++;
            $lineItemNumber++;
            $sub_total = $row['sub_total'];
            $discount = '';
            $total = $row['sub_total'] - $discountValueTotal;
        }
            /*$pdf->SetFillColor(255,255,255);
            $pdf->Cell(168, 8, "SUB TOTAL", 1, 0, 'R', 1);
            $pdf->Cell(25, 8, $sub_total, 1, 0, 'R', 1);
            $pdf->Ln();*/

            $printTaxName = $cpCfg['printTaxName'] ;
            $discountValueTotal = number_format($discountValueTotal, 2);

            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(173, 8, "Total Discount", 1, 0, 'R', 1);
            $pdf->Cell(22, 8, '- ' . $discountValueTotal, 1, 0, 'R', 1);
            $pdf->Ln();

            //$totalvalueRounded = round($totalvalue);
            //$totalvalueRounded = $totalvalue;
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(173, 8, 'TOTAL', 1, 0, 'R', 1);
            $pdf->Cell(22, 8, number_format($total, 2), 1, 0, 'R', 1);
            $pdf->Ln(10);
            $pdf->Cell(190, 8, $cpCfg['cp.invoiceVatInclusive'], 0, 0, 'L');
            $pdf->Ln(10);

            /* Creation of media record of the invoice */
            //$pdf->Output($outputFileName , "F");
            $pdf->Output();
            //$pdf->Output($invoiceCode.'.PDF', 'D');

    }


    /**
     *
     */
    function getPrintBillForPrinter($printOnly = '') {
        $cpCfg = Zend_Registry::get('cpCfg');
        $ln = Zend_Registry::get('ln');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $media = Zend_Registry::get('media');

        //-----------------------------------------------------------------//
        include_once(CP_LIBRARY_PATH.'lib_php/tbs_us/tbs_class.php');
        include_once(CP_LIBRARY_PATH.'lib_php/tbs_us/plugins/tbs_plugin_opentbs.php');
        include_once(CP_LIBRARY_PATH.'lib_php/tbs_us/plugins/tbs_plugin_html.php');

        $TBS = new clsTinyButStrong;
        $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);

        $session_order_id = isset($_SESSION['order_id']) ? $_SESSION['order_id']  : '';
        $template = 'Pos-Invoice.xlsx';
        $templatePath = $cpCfg['cp.localPath'].'lib/template/' . $template;
        $TBS->LoadTemplate($templatePath);
        $rnd_no = mt_rand();
        $file_name = 'Pos-Invoice_' . $session_order_id . '_' . $rnd_no;
        $file_name = $session_order_id . '.xlsx';
        //$file_name = str_replace('.', '_' . date('Y-m-d') . '.', $file_name);

        //if ($cpCfg['local']['site'] == 'local') {
            $path = realpath($cpCfg['cp.mediaFolder']) . '\temp\invoicePrint';
            $file_name_save = $path . '\\' . $file_name;
        //} else {
        //    $path = realpath($cpCfg['cp.mediaFolder']) . '/temp/invoicePrint';
        //    $file_name_save = $path . '//' . $file_name;
       // }
        $sourceFilePath = $file_name_save;

        $invoice_code = $fn->getReqParam('invoice_code');
        //$order_id = $fn->getReqParam('order_id');
        $orderNo = $fn->getReqParam('orderNo');
        $discountValueTotal = 0;
        $total_qty = 0;

        if($printOnly == '') {
            $printOnly = $fn->getReqParam('printOnly');
        }

        if ($printOnly != 1 ) {
            $SQL    = "
            UPDATE `order`
            set order_status = 'Paid'
            WHERE order_id = {$session_order_id}
            ";
            $result = $db->sql_query($SQL);
        }

        if ($orderNo == '' ) {
            if($session_order_id < 10){
                $orderId = '0000' . $session_order_id;
            }
            else if($session_order_id < 99){
                $orderId = '000' . $session_order_id;
            }
            else if($session_order_id < 999){
                $orderId = '00' . $session_order_id;
            }
            else if($session_order_id < 9999){
                $orderId = '0' . $session_order_id;
            }
            else{
                $orderId = $session_order_id;
            }
        } else {
            if($orderNo < 10){
                $orderId = '0000' . $orderNo;
            }
            else if($orderNo < 99){
                $orderId = '000' . $orderNo;
            }
            else if($orderNo < 999){
                $orderId = '00' . $orderNo;
            }
            else if($orderNo < 9999){
                $orderId = '0' . $orderNo;
            }
            else{
                $orderId = $orderNo;
            }
        }


        $SQL = "
        SELECT oi.*
              ,p.title AS product_title
              ,p.unit
              ,p.item_code
              ,p.batch_no
              ,p.carton_no
              ,CONCAT_WS('::', p.part_number, p.model) code
              ,o.order_date
              ,o.order_id
              ,i.vat AS invoice_vat
              ,i.invoice_code_vat
              ,i.invoice_code
              ,oi.qty * oi.unit_price AS amount
              ,(SELECT SUM(oit.qty * oit.unit_price) FROM order_item oit
               WHERE oit.order_id = oi.order_id) AS sub_total
        FROM order_item oi
        LEFT JOIN product p ON (p.product_id = oi.record_id)
        LEFT JOIN `order` o ON (o.order_id = oi.order_id)
        LEFT JOIN invoice i ON (i.order_id = o.order_id)
        WHERE o.order_id = '{$orderId}'
        ORDER BY p.title
        ";
        $result = $db->sql_query($SQL);

        $numRows  = $db->sql_numrows($result);

        $today = date("Y-m-d");

        global $TeamList;
        $TeamList    = array();

        $serialNo       = 1;
        $arr            = array();
        $blkMain        = array();
        /*$blkProduct     = array();
        $blkQty         = array();
        $blkUom         = array();
        $blkPrice       = array();
        $blkSerialNo    = array();
        $blkAmount    = array();*/
        $count = -1;
        while ($row = $db->sql_fetchrow($result)) {
            $discount_value_for_one_qty = 0;
            $discountValue =0;
            $count++;
            if($row['discount_percentage'] > 0){
                if($row['discount_type'] == '%'){
                    $discount_value_for_one_qty  =  $row['unit_price'] * ($row['discount_percentage']/100);
                    $discountValue = $discount_value_for_one_qty * $row['qty'];
                }
                else if($row['discount_type']  == 'Value'){
                    $discount_value_for_one_qty  =  $row['discount_percentage'];
                    $discountValue = $discount_value_for_one_qty * $row['qty'];
                }
                $discountValueTotal += $discountValue;
            }

            //repeating rows of product values
            /*$arr1 = array('product_title' => $row['product_title']);
            $blkProduct[] = $arr1;

            $arr2 = array('qty' => $row['qty']);
            $blkQty[] = $arr2;

            $arr3 = array('serial_no' => $serialNo);
            $blkSerialNo[] = $arr3;

            $arr4 = array('unit_price' => number_format($row['unit_price'],2));
            $blkPrice[] = $arr4;

            $arr5 = array('amount' => number_format($row['amount'], 2));
            $blkAmount[] = $arr5;

            $arr6 = array('item_code' => $row['carton_no']);
            $blkCode[] = $arr6;

            $total_qty += $row['qty'];
            $sub_total = $row['sub_total'];
            $invoice_code = $row['invoice_code'];

            $serialNo++;*/

            $TeamList[$count] = array(
                'product_title'    => $row['product_title'],
                'qty'              => $row['qty'],
                'carton_no'        => $row['item_code'] . ':' . $row['carton_no'],
                'unit_price'       => number_format($row['unit_price'],2),
                'amount'           => number_format($row['amount'], 2),
                'qty'              =>$row['qty'],
                'serial_no'        => $serialNo
                );

            $TeamList[$count]['matches'][] = array(
                'unit_discount'    => $discount_value_for_one_qty,
                'discount_allqty'  => $discountValue
            );

            $total_qty    += $row['qty'];
            $sub_total    = $row['sub_total'];
            $invoice_code = $row['invoice_code'];
            /*$company_id = $row['company_id'];
            $company_name = $row['company_name'];
            $mobile = $row['mobile'];*/

            $serialNo++;

        }
        //Header Part and Total/subtotal
        /*$arr['sub_total'] = number_format($sub_total, 2);
        $arr['discount'] = number_format($discountValueTotal, 2);
        $arr['total_qty'] = $total_qty;
        $arr['invoice_code'] = $invoice_code;
        $arr['date'] = $fn->getCPDate($today, 'd-m-Y');
        $arr['total'] =  number_format($sub_total - $discountValueTotal, 2);
        $blkMain[] = $arr;*/

        $arr['sub_total']     = number_format($sub_total, 2);
        $arr['discount']      = number_format($discountValueTotal, 2);
        $arr['total_qty']     = $total_qty;
        $arr['invoice_code']  = $invoice_code;
        $arr['date']          = $fn->getCPDate($today, 'd-m-Y');
        $arr['total']         =  number_format($sub_total - $discountValueTotal, 2);
        $arr['items']         = $numRows;

        /*$arr['company_id']    = $company_id;
        $arr['company_name']  = $company_name;
        $arr['mobile']        = $mobile;*/
        $blkMain[]            = $arr;

        /*$TBS->MergeBlock('blkMain', $blkMain);
        $TBS->MergeBlock('blkProduct', $blkProduct);
        $TBS->MergeBlock('blkQty', $blkQty);
        $TBS->MergeBlock('blkSerialNo', $blkSerialNo);
        $TBS->MergeBlock('blkPrice', $blkPrice);
        $TBS->MergeBlock('blkAmount', $blkAmount);
        $TBS->MergeBlock('blkCode', $blkCode);*/
        //$TBS->Show(OPENTBS_DOWNLOAD, $file_name);

        $TBS->MergeBlock('blkMain', $blkMain);
        $TBS->MergeBlock('mb', $TeamList);
        $TBS->MergeBlock('mb','array','TeamList');
        $TBS->MergeBlock('sb','array','TeamList[%p1%][matches]');

        $TBS->Show(OPENTBS_FILE, $sourceFilePath);
        echo "<script>window.close();</script>";

        //return $this->getPrintbillcondition($printOnly);
        //$this->model->getCreateNewOrder();
    }


    /**
     *
     */
    function getPrintBillActual($printOnly = '') {
        $cpCfg = Zend_Registry::get('cpCfg');
        $ln = Zend_Registry::get('ln');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $media = Zend_Registry::get('media');

        //-----------------------------------------------------------------//
        include_once(CP_LIBRARY_PATH.'lib_php/tbs_us/tbs_class.php');
        include_once(CP_LIBRARY_PATH.'lib_php/tbs_us/plugins/tbs_plugin_opentbs.php');
        include_once(CP_LIBRARY_PATH.'lib_php/tbs_us/plugins/tbs_plugin_html.php');

        $TBS = new clsTinyButStrong;
        $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);

        $session_order_id = isset($_SESSION['order_id']) ? $_SESSION['order_id']  : '';
        $template = 'Pos-Invoice.xlsx';
        $templatePath = $cpCfg['cp.localPath'].'lib/template/' . $template;
        $TBS->LoadTemplate($templatePath);
        $rnd_no = mt_rand();
        $file_name = 'Pos-Invoice_' . $session_order_id . '_' . $rnd_no;
        //$file_name = $session_order_id . '.xlsx';
        $file_name = str_replace('.', '_' . date('Y-m-d') . '.', $file_name);

        $path = realpath($cpCfg['cp.mediaFolder']) . '\temp\invoicePrint';
        $file_name_save = $path . '\\' . $file_name;
        $sourceFilePath = $file_name_save;

        $invoice_code = $fn->getReqParam('invoice_code');
        //$order_id = $fn->getReqParam('order_id');
        $orderNo = $fn->getReqParam('orderNo');
        $discountValueTotal = 0;
        $total_qty = 0;

        if($printOnly == '') {
            $printOnly = $fn->getReqParam('printOnly');
        }

        if ($printOnly != 1 ) {
            $SQL    = "
            UPDATE `order`
            set order_status = 'Paid'
            WHERE order_id = {$session_order_id}
            ";
            $result = $db->sql_query($SQL);
        }

        if ($orderNo == '' ) {
            if($session_order_id < 10){
                $orderId = '0000' . $session_order_id;
            }
            else if($session_order_id < 99){
                $orderId = '000' . $session_order_id;
            }
            else if($session_order_id < 999){
                $orderId = '00' . $session_order_id;
            }
            else if($session_order_id < 9999){
                $orderId = '0' . $session_order_id;
            }
            else{
                $orderId = $session_order_id;
            }
        } else {
            if($orderNo < 10){
                $orderId = '0000' . $orderNo;
            }
            else if($orderNo < 99){
                $orderId = '000' . $orderNo;
            }
            else if($orderNo < 999){
                $orderId = '00' . $orderNo;
            }
            else if($orderNo < 9999){
                $orderId = '0' . $orderNo;
            }
            else{
                $orderId = $orderNo;
            }
        }


        $SQL = "
        SELECT oi.*
              ,p.title AS product_title
              ,p.unit
              ,p.item_code
              ,p.batch_no
              ,p.carton_no
              ,CONCAT_WS('::', p.carton_no, p.batch_no, p.model) code
              ,o.order_date
              ,o.order_id
              ,i.vat AS invoice_vat
              ,i.invoice_code_vat
              ,i.invoice_code
              ,oi.qty * oi.unit_price AS amount
              ,(SELECT SUM(oit.qty * oit.unit_price) FROM order_item oit
               WHERE oit.order_id = oi.order_id) AS sub_total
        FROM order_item oi
        LEFT JOIN product p ON (p.product_id = oi.record_id)
        LEFT JOIN `order` o ON (o.order_id = oi.order_id)
        LEFT JOIN invoice i ON (i.order_id = o.order_id)
        WHERE o.order_id = '{$orderId}'
        ORDER BY p.title
        ";
        $result = $db->sql_query($SQL);

        $numRows  = $db->sql_numrows($result);

        $today = date("Y-m-d");

        $serialNo       = 1;
        $arr            = array();
        $blkMain        = array();
        $blkProduct     = array();
        $blkQty         = array();
        $blkUom         = array();
        $blkPrice       = array();
        $blkSerialNo    = array();
        $blkAmount    = array();

        while ($row = $db->sql_fetchrow($result)) {
            $discount_value_for_one_qty = 0;
            $discountValue =0;
            if($row['discount_percentage'] > 0){
                if($row['discount_type'] == '%'){
                    $discount_value_for_one_qty  =  $row['unit_price'] * ($row['discount_percentage']/100);
                    $discountValue = $discount_value_for_one_qty * $row['qty'];
                }
                else if($row['discount_type']  == 'Value'){
                    $discount_value_for_one_qty  =  $row['discount_percentage'];
                    $discountValue = $discount_value_for_one_qty * $row['qty'];
                }
                $discountValueTotal += $discountValue;
            }

            //repeating rows of product values
            $arr1 = array('product_title' => $row['product_title']);
            $blkProduct[] = $arr1;

            $arr2 = array('qty' => $row['qty']);
            $blkQty[] = $arr2;

            $arr3 = array('serial_no' => $serialNo);
            $blkSerialNo[] = $arr3;

            $arr4 = array('unit_price' => number_format($row['unit_price'],2));
            $blkPrice[] = $arr4;

            $arr5 = array('amount' => number_format($row['amount'], 2));
            $blkAmount[] = $arr5;

            $arr6 = array('item_code' => $row['carton_no']);
            $blkCode[] = $arr6;

            $total_qty += $row['qty'];
            $sub_total = $row['sub_total'];
            $invoice_code = $row['invoice_code'];

            $serialNo++;
        }
        //Header Part and Total/subtotal
        $arr['sub_total'] = number_format($sub_total, 2);
        $arr['discount'] = number_format($discountValueTotal, 2);
        $arr['total_qty'] = $total_qty;
        $arr['invoice_code'] = $invoice_code;
        $arr['date'] = $fn->getCPDate($today, 'd-m-Y');
        $arr['total'] =  number_format($sub_total - $discountValueTotal, 2);
        $blkMain[] = $arr;

        $TBS->MergeBlock('blkMain', $blkMain);
        $TBS->MergeBlock('blkProduct', $blkProduct);
        $TBS->MergeBlock('blkQty', $blkQty);
        $TBS->MergeBlock('blkSerialNo', $blkSerialNo);
        $TBS->MergeBlock('blkPrice', $blkPrice);
        $TBS->MergeBlock('blkAmount', $blkAmount);
        $TBS->MergeBlock('blkCode', $blkCode);
        $TBS->Show(OPENTBS_DOWNLOAD, $file_name);

        return $this->getPrintbillcondition($printOnly);
        //$this->model->getCreateNewOrder();
    }

    /**
     *
     */
    function getPrintBill($printOnly = '') {
        $cpCfg = Zend_Registry::get('cpCfg');
        $ln = Zend_Registry::get('ln');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $media = Zend_Registry::get('media');

        //-----------------------------------------------------------------//
        include_once(CP_LIBRARY_PATH.'lib_php/tbs_us/tbs_class.php');
        include_once(CP_LIBRARY_PATH.'lib_php/tbs_us/plugins/tbs_plugin_opentbs.php');
        include_once(CP_LIBRARY_PATH.'lib_php/tbs_us/plugins/tbs_plugin_html.php');

        $TBS = new clsTinyButStrong;
        $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);

        $session_order_id = isset($_SESSION['order_id']) ? $_SESSION['order_id']  : '';
        $template = 'Pos-Invoice.xlsx';
        $templatePath = $cpCfg['cp.localPath'].'lib/template/' . $template;
        $TBS->LoadTemplate($templatePath);
        $rnd_no = mt_rand();
        $file_name = 'Pos-Invoice_' . $session_order_id . '_' . $rnd_no;
        //$file_name = $session_order_id . '.xlsx';
        $file_name = str_replace('.', '_' . date('Y-m-d') . '.', $file_name);

        $path = realpath($cpCfg['cp.mediaFolder']) . '\temp\invoicePrint';
        $file_name_save = $path . '\\' . $file_name;
        $sourceFilePath = $file_name_save;

        $invoice_code = $fn->getReqParam('invoice_code');
        //$order_id = $fn->getReqParam('order_id');
        $orderNo = $fn->getReqParam('orderNo');
        $discountValueTotal = 0;
        $total_qty = 0;

        if($printOnly == '') {
            $printOnly = $fn->getReqParam('printOnly');
        }

        if ($printOnly != 1 ) {
            $SQL    = "
            UPDATE `order`
            set order_status = 'Paid'
            WHERE order_id = {$session_order_id}
            ";
            $result = $db->sql_query($SQL);
        }

        if ($orderNo == '' ) {
            if($session_order_id < 10){
                $orderId = '0000' . $session_order_id;
            }
            else if($session_order_id < 99){
                $orderId = '000' . $session_order_id;
            }
            else if($session_order_id < 999){
                $orderId = '00' . $session_order_id;
            }
            else if($session_order_id < 9999){
                $orderId = '0' . $session_order_id;
            }
            else{
                $orderId = $session_order_id;
            }
        } else {
            if($orderNo < 10){
                $orderId = '0000' . $orderNo;
            }
            else if($orderNo < 99){
                $orderId = '000' . $orderNo;
            }
            else if($orderNo < 999){
                $orderId = '00' . $orderNo;
            }
            else if($orderNo < 9999){
                $orderId = '0' . $orderNo;
            }
            else{
                $orderId = $orderNo;
            }
        }


        $SQL = "
        SELECT oi.*
              ,p.title AS product_title
              ,p.unit
              ,p.item_code
              ,p.batch_no
              ,p.carton_no
              ,CONCAT_WS('::', p.carton_no, p.batch_no, p.model) code
              ,o.order_date
              ,o.order_id
              ,i.vat AS invoice_vat
              ,i.invoice_code_vat
              ,i.invoice_code
              ,oi.qty * oi.unit_price AS amount
              ,(SELECT SUM(oit.qty * oit.unit_price) FROM order_item oit
               WHERE oit.order_id = oi.order_id) AS sub_total
        FROM order_item oi
        LEFT JOIN product p ON (p.product_id = oi.record_id)
        LEFT JOIN `order` o ON (o.order_id = oi.order_id)
        LEFT JOIN invoice i ON (i.order_id = o.order_id)
        WHERE o.order_id = '{$orderId}'
        ORDER BY p.title
        ";
        $result = $db->sql_query($SQL);

        $numRows  = $db->sql_numrows($result);

        $today = date("Y-m-d");

        global $TeamList;
        $TeamList    = array();

        $serialNo       = 1;
        $arr            = array();
        $blkMain        = array();

        $count = -1;
        while ($row = $db->sql_fetchrow($result)) {
            $count++;
            $discount_value_for_one_qty = 0;
            $discountValue =0;
            if($row['discount_percentage'] > 0){
                if($row['discount_type'] == '%'){
                    $discount_value_for_one_qty  =  $row['unit_price'] * ($row['discount_percentage']/100);
                    $discountValue = $discount_value_for_one_qty * $row['qty'];
                }
                else if($row['discount_type']  == 'Value'){
                    $discount_value_for_one_qty  =  $row['discount_percentage'];
                    $discountValue = $discount_value_for_one_qty * $row['qty'];
                }
                $discountValueTotal += $discountValue;
            }

            $TeamList[$count] = array(
                'product_title'    => $row['product_title'],
                'qty'              => $row['qty'],
                'carton_no'        => $row['carton_no'],
                'unit_price'       => number_format($row['unit_price'],2),
                'amount'           => number_format($row['amount'], 2),
                'qty'              =>$row['qty'],
                'serial_no'        => $serialNo
                );

            $TeamList[$count]['matches'][] = array(
                'unit_discount'    => $discount_value_for_one_qty,
                'discount_allqty'  => $discountValue
            );

            $total_qty    += $row['qty'];
            $sub_total    = $row['sub_total'];
            $invoice_code = $row['invoice_code'];

            $serialNo++;
        }
        //Total/subtotal
        $arr['sub_total']     = number_format($sub_total, 2);
        $arr['discount']      = number_format($discountValueTotal, 2);
        $arr['total_qty']     = $total_qty;
        $arr['invoice_code']  = $invoice_code;
        $arr['date']          = $fn->getCPDate($today, 'd-m-Y');
        $arr['total']         =  number_format($sub_total - $discountValueTotal, 2);
        $arr['items']         = $numRows;
        $blkMain[]            = $arr;

        $TBS->MergeBlock('blkMain', $blkMain);
        $TBS->MergeBlock('mb', $TeamList);
        $TBS->MergeBlock('mb','array','TeamList');
        $TBS->MergeBlock('sb','array','TeamList[%p1%][matches]');

        $TBS->Show(OPENTBS_DOWNLOAD, $file_name);

        return $this->getPrintbillcondition($printOnly);
        //$this->model->getCreateNewOrder();
    }


    /**
     *
     */
    function getPrintbillcondition($printOnly=''){
         $fn = Zend_Registry::get('fn');
         $db = Zend_Registry::get('db');
         $cpCfg = Zend_Registry::get('cpCfg');
         if ($printOnly != 1 ) {
            $_SESSION['order_id'] = '';
            return $this->model->getCreateNewOrder();
         }
     }


    /**
     *
     */
    function getPrintbillconditionForPrinter($printOnly=''){
         $fn = Zend_Registry::get('fn');
         $db = Zend_Registry::get('db');
         $cpCfg = Zend_Registry::get('cpCfg');
         if ($printOnly != 1 ) {
            $session_order_id = isset($_SESSION['order_id']) ? $_SESSION['order_id']  : '';
            $session_order_id = $session_order_id - 1;
            $file_name = $session_order_id. '.xlsx';

            if ($cpCfg['local']['site'] == 'local') {
                $path = realpath($cpCfg['cp.mediaFolder']) . '\temp\invoicePrint';
                $file_name_delete = $path . '\\' . $file_name;
            } else {
                $path = realpath($cpCfg['cp.mediaFolder']) . '/temp/invoicePrint';
                $file_name_delete = $path . '//' . $file_name;
            }
            unlink($file_name_delete);
            $_SESSION['order_id'] = '';
            echo "<script>window.close();</script>";
            return $this->model->getCreateNewOrder();
         }
     }


    /********************************* PROCESS ************************************
    ACTION: IN POS MODULE - WHEN YOU CLICK 'GENERATE/UPDATE BILL' BUTTON
    STEP 1: SQL,LOOP THE RECORDS OF ORDER ITEM TO FIND THE DISCOUNT AMOUNT, INVOICE AMOUNT
    STEP 2: GENERATE NEXT INVOICE CODE - GENERATE INVOICE CODE VAT - UPDATE/INSERT INVOICE RECORDS IN INVOICE TABLE
    STEP 3: LOOP THE RECORDS OF ORDER ITEM AND UPDATE/INSERT INVOICE ITEM RECORDS IN INVOICE ITEM TABLE
    STEP 4: UPDATE/INSERT RECEIPT RECORDS IN RECEIPT TABLE - UPDATE/INSERT RECEIPT HISTORY RECORDS IN INVOICE
    RECEIPT HISTORY TABLE  - UPDATE ORDER STATUS TO PAID FOR ORDER TABLE
    ******************************* END PROCESS **********************************/

    function getGenerateBill() {

        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $tv = Zend_Registry::get('tv');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $searchVar = Zend_Registry::get('searchVar');
        $media = Zend_Registry::get('media');
        $cpPaths = Zend_Registry::get('cpPaths');
        $dbUtil = Zend_Registry::get('dbUtil');
        $cpUtil = Zend_Registry::get('cpUtil');
        $mode_of_payment = $fn->getReqParam('mode_of_payment');

        $session_order_id = isset($_SESSION['order_id']) ? $_SESSION['order_id']  : '';
        $discountValueTotal = '';
        $invoice_amount = '';
        $discount = 0;

        /********************************** STEP 1 **************************************/
        $SQL = "
        SELECT o.discount
              ,oi.discount_percentage
              ,oi.discount_type
              ,oi.qty
              ,oi.unit_price
              ,oi.vat
        FROM order_item oi
        LEFT JOIN (`order` o) ON (o.order_id = oi.order_id)
        WHERE oi.order_id = '{$session_order_id}'
        ";
        $result = $db->sql_query($SQL);
        //$row = $db->sql_fetchrow($result);
        while ($row = $db->sql_fetchrow($result)) {
            $invoice_amount += $row['unit_price'] * $row['qty'];
            $discount = $row['discount'];

            $discount_value_for_one_qty = '';
            $discountValue = '';
            if($row['discount_percentage'] > 0){
                if($row['discount_type'] == '%'){
                    $discount_value_for_one_qty  =  $row['unit_price'] * ($row['discount_percentage']/100);
                    $discountValue = $discount_value_for_one_qty * $row['qty'];
                }
                else if($row['discount_type']  == 'Value'){
                    $discount_value_for_one_qty  =  $row['discount_percentage'];
                    $discountValue = $discount_value_for_one_qty * $row['qty'];
                }
            }
            $discountValueTotal += $discountValue;
        }
        $invoice_amount = $invoice_amount - $discountValueTotal;
        /********************************** STEP 1 ENDS HERE ****************************/

        /********************************** STEP 2 **************************************/
        $SQLUpdate = "UPDATE setting SET value = (value+1) WHERE key_text = 'nextInvoiceCode'";
        $resultUpdate = $db->sql_query($SQLUpdate);
        $invoice_code = $fn->getSettingsValueByKey("nextInvoiceCode");

        $date     = $fn->getCurrentDate();
        $SQLInvoice = "
        SELECT *
        FROM `invoice`
        WHERE order_id = '{$session_order_id}'
        ";
        $resultInvoice = $db->sql_query($SQLInvoice);
        $invoiceRec = $db->sql_fetchrow($resultInvoice);

        $fa = array();
        $fa['invoice_amount']   = $invoice_amount;
        $fa['invoice_date']     = $date;
        $fa['order_id']         = $session_order_id;
        $fa['discount']         = $discount;
        $fa['staff_id']         = $_SESSION['staff_id'];
        $fa['creation_date']    = date("Y-m-d H:i:s");
        $fa['created_by']       = $fn->getSessionParam('userName');
        $fa['vat']              = 1;
        $fa['mode_of_payment']  = $mode_of_payment;

        $SQLICV = "
        SELECT max(invoice_code_vat) AS invoice_code_vat
        FROM `invoice`
        WHERE vat = 1
        ";
        $resultICV = $db->sql_query($SQLICV);
        $rowICV = $db->sql_fetchrow($resultICV);
        if ($rowICV['invoice_code_vat'] == '' || $rowICV['invoice_code_vat'] == 0){
            $invoice_code_vat = 1;
        } else {
            $invoice_code_vat = $rowICV['invoice_code_vat'] + 1;
        }

        if (is_array($invoiceRec)) {
            $whereCondition = "WHERE order_id = {$session_order_id}";
            $sqlOiUpdate = $dbUtil->getUpdateSQLStringFromArray($fa, "invoice", $whereCondition);
            $resultOiUpdate      = $db->sql_query($sqlOiUpdate);
            $invoice_id         = $invoiceRec['invoice_id'];
        } else {
            $fa['invoice_code']     = 'INV - ' . $invoice_code;
            $fa['status']           = 'Paid';
            $fa['invoice_type']     = 'Client';
            $fa['invoice_code_vat'] = $invoice_code_vat;

            $insertInvoiceSQL   = $dbUtil->getInsertSQLStringFromArray($fa, 'invoice');
            $resultSQL          = $db->sql_query($insertInvoiceSQL);
            $invoice_id         = $db->sql_nextid();
        }
        $invoiceCode = 'INV - ' . $invoice_code;

        /********************************** STEP 2 ENDS HERE ****************************/

        /********************************** STEP 3 **************************************/
        $SQL = "
        SELECT *
        FROM order_item
        WHERE order_id = '{$session_order_id}'
        ";
        $result = $db->sql_query($SQL);
        while ($row = $db->sql_fetchrow($result)) {
            $fa = array();
            $fa['invoice_id']   = $invoice_id;
            $fa['record_id']    = $row['record_id'];
            $fa['qty']          = $row['qty'];
            $fa['unit_price']   = $row['unit_price'];
            $fa['cost_price']   = $row['cost_price'];
            $fa['item_title']   = $row['item_title'];
            $fa['item_code']    = $row['item_code'];
            $fa['model']        = $row['model'];
            $fa['order_item_id']= $row['order_item_id'];
            $fa['vat']          = $row['vat'];
            $fa['discount_type'] = $row['discount_type'];
            $fa['discount_percentage'] = $row['discount_percentage'];

            if($cpCfg['cp.hasMultiUniqueSites'] == 1){
                $cpSiteIdSession = $fn->getSessionParam('cp_site_id');
                $appendSQL = "AND site_id = '{$cpSiteIdSession}'";
            }

            $SQLInvoiceItem = "
            SELECT *
            FROM `invoice_item`
            WHERE invoice_id = '{$invoice_id}'
              AND record_id = '{$row['record_id']}'
            ";
            $resultInvoiceItem = $db->sql_query($SQLInvoiceItem);
            $invoiceItemRec = $db->sql_fetchrow($resultInvoiceItem);

            if(is_array($invoiceItemRec)){
                $whereCondition = "WHERE invoice_item_id = {$invoiceItemRec['invoice_item_id']}";
                $sqlOiUpdate = $dbUtil->getUpdateSQLStringFromArray($fa, "invoice_item", $whereCondition);
                $resultOiUpdate      = $db->sql_query($sqlOiUpdate);
            } else {
                $invoice_item_id = $fn->addRecord($fa, 'invoice_item');
            }
        }
        /********************************** STEP 3 ENDS HERE ****************************/

        /********************************** STEP 4 **************************************/
        $SQLUpdate = "UPDATE setting SET value = (value+1) WHERE key_text = 'nextReceiptCode'";
        $resultUpdate = $db->sql_query($SQLUpdate);
        $receipt_code = $fn->getSettingsValueByKey("nextReceiptCode");

        $SQLReceipt = "
        SELECT *
        FROM `receipt`
        WHERE order_id = '{$session_order_id}'
        ";
        $resultReceipt = $db->sql_query($SQLReceipt);
        $receiptRec = $db->sql_fetchrow($resultReceipt);
        $fa = array();
        $fa['amount']         = $invoice_amount;
        $fa['order_id']       = $session_order_id;
        $fa['date']           = date("Y-m-d");
        $fa['receipt_status'] = 'Paid';
        $fa['creation_date']  = date("Y-m-d H:i:s");
        $fa['created_by']     = $fn->getSessionParam('userName');

        if (is_array($receiptRec)) {
            $whereCondition = "WHERE order_id = {$session_order_id}";
            $sqlOiUpdate = $dbUtil->getUpdateSQLStringFromArray($fa, "receipt", $whereCondition);
            $resultOiUpdate      = $db->sql_query($sqlOiUpdate);
            $receipt_id         = $receiptRec['receipt_id'];
        } else {
            $fa['receipt_code']   = 'RCPT - ' . $receipt_code;

            $insertReceiptSQL   = $dbUtil->getInsertSQLStringFromArray($fa, 'receipt');
            $resultSQL          = $db->sql_query($insertReceiptSQL);
            $receipt_id         = $db->sql_nextid();
        }

        $SQLReceiptHis = "
        SELECT *
        FROM `invoice_receipt_history`
        WHERE invoice_id = '{$invoice_id}'
          AND receipt_id = '{$receipt_id}'
        ";
        $resultReceiptHis = $db->sql_query($SQLReceiptHis);
        $receiptHisRec = $db->sql_fetchrow($resultReceiptHis);

        $fa = array();
        $fa['receipt_id']    = $receipt_id;
        $fa['invoice_id']    = $invoice_id;
        $fa['amount']        = $invoice_amount;
        $fa['creation_date'] = date("Y-m-d H:i:s");

        if(is_array($receiptHisRec)){
            $whereCondition = "WHERE invoice_receipt_history_id = {$receiptHisRec['invoice_receipt_history_id']}";
            $sqlOiUpdate = $dbUtil->getUpdateSQLStringFromArray($fa, "invoice_receipt_history", $whereCondition);
            $resultOiUpdate      = $db->sql_query($sqlOiUpdate);
        } else {
            $histId = $fn->addRecord($fa, 'invoice_receipt_history');
        }

        $rowInvoiceCode = $fn->getRecordRowByID('invoice', 'invoice_id', $invoice_id);
        
        $SQL    = "
        UPDATE `order`
        set order_status = 'Paid'
        WHERE order_id = {$session_order_id}
        ";
        $result = $db->sql_query($SQL);

        return $session_order_id;

        /********************************** STEP 4 ENDS HERE ****************************/
    }

    /**
     *
     */
    function getPrintBillFromInvoiceOld($printOnly = '') {
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

        $session_order_id = isset($_SESSION['order_id']) ? $_SESSION['order_id']  : '';
        $invoice_code = $fn->getReqParam('invoice_code');
        //$order_id = $fn->getReqParam('order_id');
        $orderNo = $fn->getReqParam('orderNo');

        if($printOnly == '') {
            $printOnly = $fn->getReqParam('printOnly');
        }

        if ($printOnly != 1 ) {
            $SQL    = "
            UPDATE `order`
            set order_status = 'Paid'
            WHERE order_id = {$session_order_id}
            ";
            $result = $db->sql_query($SQL);
        }

        $SQL = "
        SELECT ini.*
              ,p.title AS product_title
              ,p.unit
              ,p.item_code
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
              ,i.notes
              ,i.discount
              ,q.quote_code
              ,q.currency
              ,oi.discount_type
              ,oi.discount_percentage
              ,ini.qty * ini.unit_price AS amount
              ,(SELECT SUM(init.qty * init.unit_price) FROM invoice_item init
               WHERE init.invoice_id = ini.invoice_id) AS sub_total
        FROM invoice_item ini
        LEFT JOIN product p ON (p.product_id = ini.record_id)
        LEFT JOIN invoice i ON (i.invoice_id = ini.invoice_id)
        LEFT JOIN `order` o ON (o.order_id = i.order_id)
        LEFT JOIN order_item oi ON (oi.order_item_id = ini.order_item_id)
        LEFT JOIN company c ON (c.company_id = o.company_id)
        LEFT JOIN quote q ON (q.quote_id = o.quote_id)
        WHERE i.invoice_code = '{$invoice_code}'
        ORDER BY p.title
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
        $discountValue ='';
        $lineItemNumber = 1;  // To increment the line item in receipt

        if ($orderNo == '' ) {
            if($session_order_id < 10){
                $orderId = '0000' . $session_order_id;
            }
            else if($session_order_id < 99){
                $orderId = '000' . $session_order_id;
            }
            else if($session_order_id < 999){
                $orderId = '00' . $session_order_id;
            }
            else if($session_order_id < 9999){
                $orderId = '0' . $session_order_id;
            }
            else{
                $orderId = $session_order_id;
            }
        } else {
            if($orderNo < 10){
                $orderId = '0000' . $orderNo;
            }
            else if($orderNo < 99){
                $orderId = '000' . $orderNo;
            }
            else if($orderNo < 999){
                $orderId = '00' . $orderNo;
            }
            else if($orderNo < 9999){
                $orderId = '0' . $orderNo;
            }
            else{
                $orderId = $orderNo;
            }
        }


        //============================================================================= //
        $pdf->SetFont('Arial','',11);
        while ($row = $db->sql_fetchrow($result)) {
            $discount_value_for_one_qty = '';
            if($row['discount_percentage'] > 0){
                if($row['discount_type'] == '%'){
                    $discount_value_for_one_qty  =  $row['unit_price'] * ($row['discount_percentage']/100);
                    $discountValue = $discount_value_for_one_qty * $row['qty'];
                }
                else if($row['discount_type']  == 'Value'){
                    $discount_value_for_one_qty  =  $row['discount_percentage'];
                    $discountValue = $discount_value_for_one_qty * $row['qty'];
                }
            }

            if ($count == 0){
                /* Logo of the institution */
                $pdf->Image('images/logo-print.gif',10,5,45);
                $pdf->SetFont('Courier','B',11);
                $pdf->Cell(50, 20, $cpCfg['cp.companyName']);
                $pdf->Ln(5);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf7']);
                $pdf->Ln(5);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf6']);
                $pdf->Ln(5);
                $pdf->Cell(50, 20, $cpCfg['printWebAddress']);

                $creationDate   = $fn->getCPDate($row['invoice_date'], 'd-m-Y');

                /* Company address */
                //Address to be got from settings
                $pdf->SetFont('Courier','B',11);
                $pdf->SetXY(130,0);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf1']);
                $pdf->Ln(5);
                $pdf->SetXY(130,5);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf2']);
                $pdf->Ln(5);
                $pdf->SetXY(130,10);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf3']);
                $pdf->Ln(5);
                $pdf->SetXY(130, 15);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf4']);
                $pdf->Ln(5);
                $pdf->SetXY(130,20);
                $pdf->Cell(50, 20, $cpCfg['cp.addressPdf5'  ]);
                $pdf->Ln(5);
                $pdf->SetXY(130,25);
                $pdf->Cell(50, 20, $cpCfg['printTelephoneAndFax']);
                $pdf->Ln(5);
                $pdf->SetXY(130,30);
                $pdf->Cell(50, 20, $cpCfg['printEmailAddress']);

                /* Header */
                $pdf->SetFont('Courier','BU',11);
                $pdf->SetXY(80, 45);
                $pdf->Cell(50, 20, "BILL", 0, 0, 'C');
                $pdf->SetFont('Courier','B',11);
                $pdf->SetX(130);
                $pdf->Cell(31, 20, "DATE : " . $creationDate, 0, 0, 'L');
                $pdf->Ln(20);

                /* Invoice Details*/
                $pdf->SetFont('Courier','B',11);
                $pdf->SetFillColor(254,203,156);
                $pdf->Cell(30,8,"BILL NO :",1,0, 'L', 1);
                $pdf->SetFillColor(255,255,255);
	            $pdf->Cell(66, 8, $row['invoice_code'], 1, 0, 'L', 1);
                $pdf->SetFillColor(254,203,156);
                $pdf->Cell(30,8,"ORD NO :",1,0, 'L', 1);
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(66, 8, $orderId, 1, 0, 'L', 1);
                $pdf->Ln(12);

                /* List of order items header */
                $pdf->SetFillColor(254,203,156);
                $pdf->Cell(15,8,"S.NO",1,0, 'C', 1);
                $pdf->Cell(75,8,"NAME OF THE ITEM",1,0, 'C', 1);
                $pdf->Cell(15,8,"QTY",1,0, 'C', 1);
                $pdf->Cell(15,8,"UOM",1,0, 'C', 1);
                $pdf->Cell(24,8,"UNIT PRICE",1,0, 'C', 1);
                $pdf->Cell(24,8,"DISCOUNT",1,0, 'C', 1);
                $pdf->Cell(25,8,"AMOUNT" ,1,0, 'C', 1);
                $pdf->Ln();
            }

            //===================================MAIN TABLE============================= //
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(15, 8, $lineItemNumber, 1, 0, 'C', 1);
            $pdf->Cell(75, 8, $row['product_title'], 1, 0, 'L', 1);
            $pdf->Cell(15, 8, $row['qty'], 1, 0, 'R', 1);
            $pdf->Cell(15, 8, $row['unit'], 1, 0, 'R', 1);
            $pdf->Cell(24, 8, $row['unit_price'], 1, 0, 'R', 1);
            $pdf->Cell(24, 8, number_format($discountValue, 2), 1, 0, 'R', 1);
            $pdf->Cell(25, 8, $row['amount'], 1, 0, 'R', 1);
            $pdf->Ln();

            $count++;
            $lineItemNumber++;
            $sub_total = $row['sub_total'];
            $discount = $row['discount'];
            $total = $row['sub_total'] - $row['discount'];
        }
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(168, 8, "SUB TOTAL", 1, 0, 'R', 1);
            $pdf->Cell(25, 8, $sub_total, 1, 0, 'R', 1);
            $pdf->Ln();

	        $printTaxName = $cpCfg['printTaxName'] ;

            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(168, 8, "Discount", 1, 0, 'R', 1);
            $pdf->Cell(25, 8, $discount, 1, 0, 'R', 1);
            $pdf->Ln();

            //$totalvalueRounded = round($totalvalue);
            //$totalvalueRounded = $totalvalue;
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(168, 8, 'TOTAL', 1, 0, 'R', 1);
            $pdf->Cell(25, 8, number_format($total, 2), 1, 0, 'R', 1);
			$pdf->Ln(20);

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
    function getProductPrice(){

		$productDisplay = $this->getProductPriceDisplay();

		$text = "
		<div class='checkProductPrice'>
			Product Name / Model No / Item Code/ Carton No: <input type='text' value='' id='fld_product_title' class='text' name='product_title'>
			(Please enter words related to the label)
		</div>
		<table class='list thinlist'>
			<thead>
				<tr>
					<th>Item Code</th>
					<th>Item Name</th>
					<th>Model No</th>
					<th>Carton No</th>
					<th>Batch No</th>
					<th>List Price</th>
					<th>Unit</th>
					<th>Available Stock Quantity</th>
					<th>FC Ref Code</th>
				</tr>
			</thead>
			<tbody id='productDisplay'>
				{$productDisplay}
			</tbody>
		</table>
		";

        return $text;
    }

    /**
     *
     */
    function getCheckPendingOrderDetails(){
        $db = Zend_Registry::get('db');
        $fn = Zend_Registry::get('fn');
        $cpCfg = Zend_Registry::get('cpCfg');

        $appendSqlSite ='';
        if($cpCfg['cp.hasMultiUniqueSites'] == 1){
            $cpSiteIdSession = $fn->getSessionParam('cp_site_id');
            $appendSqlSite = "AND o.site_id = {$cpSiteIdSession}";
        }

        $SQL = "
        SELECT o.*
              ,(SELECT SUM((oi.qty)*(oi.unit_price))
               FROM order_item oi
               WHERE o.order_id = oi.order_id
              ) AS order_amount
        FROM `order` o
        WHERE o.order_status = 'Pending'
           OR o.order_status = 'New'
           {$appendSqlSite}
       ORDER BY order_id DESC
        ";

        $result  = $db->sql_query($SQL);

        $rows = '';

            while ($row = $db->sql_fetchrow($result)) {
                $rows .= "
                    <tr order_id={$row['order_id']}>
                        <td><a href='#'  class='pendingOrderID'>{$row['order_id']}</a></td>
                        <td>{$fn->getCPDate($row['order_date'], 'd-m-Y')}</td>
                        <td>{$row['order_status']}</td>
                        <td>{$row['created_by']}</td>
                    </tr>
                ";
            }

        $text = "
        <table class='list thinlist'>
            <thead>
                <tr>
                    <th>Order Code</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Staff</th>
                </tr>
            </thead>
            <h1>*Please click the order code</h1>
            {$rows}
        </table>
        ";

        return $text;
    }

    /**
     *
     */
    function getOrderStatusToPending() {
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');

        $session_order_id = isset($_SESSION['order_id']) ? $_SESSION['order_id']  : '';

        $SQL    = "
        UPDATE `order`
        set order_status = 'Pending'
        WHERE order_id = {$session_order_id}
        ";
        $result = $db->sql_query($SQL);

        $_SESSION['order_id'] = '';
    }

    /**
     *
     */
   function getInsertOldOrder() {
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');

        $order_id = $fn->getReqParam('order_id');

        $_SESSION['order_id'] = $order_id;
    }

    /**
     *
     */
    function getProductPriceDisplay(){
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');

        $product_id = $fn->getReqParam('product_id');

        $SQL    = "
        SELECT p.*
        FROM product p
        WHERE p.product_id = '{$product_id}'
        ";
        $result = $db->sql_query($SQL);
        $row = $db->sql_fetchrow($result);

        $StockSql = "
        SELECT
            (SELECT SUM(qty) FROM po_product
            WHERE product_id = '{$product_id}'
            ) as product_qty_purchased
            ,(SELECT SUM(oi.qty) FROM order_item oi
            LEFT JOIN (`order` o) ON (o.order_id = oi.order_id)
            WHERE record_id = '{$product_id}'
              AND o.order_status = 'Paid'
            ) as product_qty_sold
        ";
        $resultStockSql = $db->sql_query($StockSql);
        $rowStockSql    = $db->sql_fetchrow($resultStockSql);

        $stock = $rowStockSql['product_qty_purchased'] - $rowStockSql['product_qty_sold'];

		$rows = "
		<tr>
			<td>{$row['item_code']}</td>
			<td class='w25p'>{$row['title']}</td>
			<td>{$row['model']}</td>
			<td>{$row['carton_no']}</td>
			<td>{$row['batch_no']}</td>
			<td class='unitPrice txtRight'>{$row['price']}</td>
			<td>{$row['unit']}</td>
			<td>{$stock}</td>
			<td>{$row['fc_price_code']}</td>
		</tr>
		";

		$text = "
		{$rows}
		";

        return $text;
    }

    /**
     *
     */
    function getPrintInvoiceRecordOld() {
        $db = Zend_Registry::get('db');
        $fn = Zend_Registry::get('fn');
        $cpCfg = Zend_Registry::get('cpCfg');

        ini_set('memory_limit', '512M');
        set_time_limit(50000);

        include_once(CP_LIBRARY_PATH.'lib_php/tcpdf/tcpdf.php');
        include_once(CP_LOCAL_PATH.'lib/headfootBillPrint.php');

        //$pdf = new MYPDF_Local(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        //$pdf = new MYPDF_Local('L', 'px', array('261.250', '110.48'), true, 'UTF-8', false);
        $orderNo = $fn->getReqParam('orderNo');
        $discountValueTotal = 0;

        $SQL = "
        SELECT oi.*
              ,p.title AS product_title
              ,p.unit
              ,p.item_code
              ,p.batch_no
              ,p.hsn_sac
              ,p.carton_no
              ,CONCAT_WS('::', p.carton_no, p.batch_no, p.model) code
              ,o.order_date
              ,o.order_id
              ,i.vat AS invoice_vat
              ,i.invoice_date
              ,DATE_FORMAT(o.creation_date, '%d-%m-%Y %r')AS invoice_creation_date
              ,i.invoice_code_vat
              ,i.invoice_code
              ,oi.qty * oi.unit_price AS amount
              ,(SELECT SUM(oit.qty * oit.unit_price) FROM order_item oit
               WHERE oit.order_id = oi.order_id) AS sub_total
        FROM order_item oi
        LEFT JOIN product p ON (p.product_id = oi.record_id)
        LEFT JOIN `order` o ON (o.order_id = oi.order_id)
        LEFT JOIN invoice i ON (i.order_id = o.order_id)
        WHERE o.order_id = '{$orderNo}'
        ORDER BY p.title
        ";
        $result  = $db->sql_query($SQL);
        $numRows = $db->sql_numrows($result);
        $result2 = $db->sql_query($SQL);
        $company = $db->sql_fetchrow($result2);
        //============================================================================= //

        $today = date("d-m-Y");
        $invoice_date = $company['invoice_creation_date'];

        $tbl1 ='<table border="0" width="100%" cellpadding="2">
                    <tr>
                        <th align="left" width="18%">Bill No.</th>
                        <th align="left" width="17%">'.$company['order_id'].'</th>
                        <th width="5%"></th>
                        <th align="right" width="12%">Date:</th>
                        <th align="right" width="48%">'.$invoice_date.'</th>
                    </tr>
                </table>
        ';

        $tbl2 ='<table border="0" width="100%" cellpadding="2">
                    <tr>
                        <th width="6%"  align="left"   style="border-top:1px dashed black;">#</th>
                        <th width="70%" align="left"   style="border-top:1px dashed black;">Product Name</th>
                        <th width="24%" align="left"   style="border-top:1px dashed black;">HSN-SAC</th>
                    </tr>
                    <tr>
                        <th width="15%" align="center" style="border-bottom:1px dashed black;">Qty</th>
                        <th width="35%" align="right"  style="border-bottom:1px dashed black;">Price</th>
                        <th width="50%" align="right"  style="border-bottom:1px dashed black;">Value</th>
                    </tr>
        ';

        $total_qty = 0;
        $serialNo = 1;
        $overall_vat_Sum = 0;
        $heightPage = 290;
        while ($row = $db->sql_fetchrow($result)) {

            $discountValueTotal += $row['discounted_amount'];
            $tbl2 = $tbl2.'
                    <tr>
                        <td width="6%"  align="left">'.$serialNo.'</td>
                        <td width="70%" align="left">['.$row['product_title'].']</td>
                        <td width="24%" align="left">'.$row['hsn_sac'].'</td>
                    </tr>
                    <tr>
                        <td width="15%" align="center">'.$row['qty'].'</td>
                        <td width="35%" align="right">'.number_format($row['unit_price'],2).'</td>
                        <td width="50%" align="right">'.number_format($row['amount'], 2).'</td>
                    </tr>
            ';

            $total_qty    += $row['qty'];
            $sub_total    = $row['sub_total'];

            $overall_vat_Sum  += ($row['total_amount'] * $row['vat'])/100;

            $heightPage = $heightPage + 40;
            $serialNo++;
        }

        $tbl2 = $tbl2.'</table>';

        $tbl3 = '<table>';

        $serialNoTotal = $serialNo - 1;

        $tbl3 = $tbl3.'
                <tr>
                    <td align="right" style="border-top:1px dashed black;"><font style="font-size: 11px;">Bill Total</font></td>
                    <td align="right" style="border-top:1px dashed black;"><font style="font-size: 11px;">'.number_format($sub_total,2).'</font></td>
                </tr>
                <tr>
                    <td align="right"><font style="font-size: 11px;">Total Items</font></td>
                    <td align="right"><font style="font-size: 11px;">'.$serialNoTotal.'</font></td>
                </tr>
                <tr>
                    <td align="right"><font style="font-size: 11px;">Total Qty</font></td>
                    <td align="right"><font style="font-size: 11px;">'.$total_qty.'</font></td>
                </tr>
                <tr>
                    <td align="right"><font style="font-size: 11px;">Tax Amount</font></td>
                    <td align="right"><font style="font-size: 11px;">'.number_format($overall_vat_Sum, 2).'</font></td>
                </tr>
                <tr>
                    <td align="right" style="border-top:1px dashed black;border-bottom:1px dashed black;line-height:20px;"><font style="font-size:14px;">Total</font></td>
                    <td align="right" style="border-top:1px dashed black;border-bottom:1px dashed black;line-height:20px;"><font style="font-size:14px;">'.number_format($sub_total - $discountValueTotal, 2).'</font></td>
                </tr>
        ';
        
        $tbl3 = $tbl3.'</table>';

        $tbl4 = '<table cellpadding="4" border="0">';

        $tbl4 = $tbl4.'
                <tr>
                    <td align="left" colspan="3"><font style="font-size: 11px;">Tax Summary:</font></td>
                </tr>
                <tr>
                    <td align="left"  style="border-top:1px dashed black;border-bottom:1px dashed black;">Tax Type</td>
                    <td align="right" style="border-top:1px dashed black;border-bottom:1px dashed black;">Taxable</td>
                    <td align="right" style="border-top:1px dashed black;border-bottom:1px dashed black;">Tax Amt</td>
                </tr>
        ';

        $SQLTax = "
        SELECT  vat
               ,sum(total_amount) AS total_amount
        FROM `order_item` 
        WHERE order_id = '{$orderNo}'
        GROUP BY vat
        ORDER BY vat ASC
        ";
        $resultTax  = $db->sql_query($SQLTax);

        $totalVatSum = 0;
        while($rowTax     = $db->sql_fetchrow($resultTax)){
            if($rowTax['vat'] == ''){
                $vatPercent = '0.00';
            }
            else{
                $vatPercent = $rowTax['vat'];
            }

            $vat_Sum    = ($rowTax['total_amount'] * $rowTax['vat'])/100;

            $vat_Amount_total = $rowTax['total_amount'] - $vat_Sum;
            if($vat_Sum == 0){
                $vat_Amount_total = 0;
            }

            $vatPercentHalf = $vatPercent / 2;
            $vat_Sum_Half = $vat_Sum / 2;

            $totalVatSum += $vat_Sum;

            $vatPercentHalf = sprintf('%0.2f', $vatPercentHalf);

            $tbl4 = $tbl4.'
            <tr>
                <td align="left">SGST '.$vatPercentHalf.' %</td>
                <td align="right">'.number_format($vat_Amount_total, 2).'</td>
                <td align="right">'.number_format($vat_Sum_Half, 2).'</td>
            </tr>
            <tr>
                <td align="left">CGST '.$vatPercentHalf.' %</td>
                <td align="right">'.number_format($vat_Amount_total, 2).'</td>
                <td align="right">'.number_format($vat_Sum_Half, 2).'</td>
            </tr>
            ';

            $heightPage = $heightPage + 10;
        }

        $tbl4 = $tbl4.'
        <tr>
            <td align="left"  style="border-top:1px dashed black;border-bottom:1px dashed black;" >TOTAL</td>
            <td align="right" style="border-top:1px dashed black;border-bottom:1px dashed black;" colspan="2">'.number_format($totalVatSum, 2).'</td>
        </tr>
        <tr>
            <td align="left" colspan="3">PERISHABLE ITEMS NO RETURNS</td>
        </tr>
        <tr>
            <td align="left" colspan="3">CASH NOT REFUNDABLE</td>
        </tr>
        ';

        $tbl4 = $tbl4.'</table>';

        $pdf = new MYPDF_Local('P', 'px', array('261.250', $heightPage), true, 'UTF-8', false);

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
        $pdf->SetFont('Courier','B',8);

        $pdf->ln(10);
        $pdf->writeHTML($tbl1, true, false, false, false, '');
        $pdf->ln(-10);
        $pdf->writeHTML($tbl2, true, false, false, false, '');
        $pdf->writeHTML($tbl3, true, false, false, false, '');
        $pdf->writeHTML($tbl4, true, false, false, false, '');
        $download_title = $company['order_id'] . '-Invoice.pdf';
        $pdf->Output($download_title, 'I');

        //return $this->getPrintbillcondition($printOnly);
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
            $invoiceHeading = 'DUPLICATE - ';
        }
        $orderNo = $fn->getReqParam('orderNo');

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
        WHERE i.order_id = '{$orderNo}'
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

            $tbl2 ='
            <table border="0" width="100%" cellpadding="3">
                <tr>
                    <td colspan="2" width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>Billed to :</i></td>
                    <td width="50%" style="border-right:1px solid #000000;border-top:1px solid #000000;font-size:11px;font-weight:bold;"><i>Shipped to :</i></td>
                </tr>
                <tr>
                    <td colspan="2" width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;font-size:11px;font-weight:bold;">'.$Row['company_name'].'</td>
                    <td width="50%" style="border-right:1px solid #000000;font-size:11px;font-weight:bold;">'.$Row['company_name'].'</td>
                </tr>
                <tr>
                    <td colspan="2" width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;font-size:11px;font-weight:bold;">'.$Row['address_flat'].'</td>
                    <td width="50%" style="border-right:1px solid #000000;font-size:11px;font-weight:bold;">'.$Row['billing_address_flat'].'</td>
                </tr>
                <tr>
                    <td colspan="2" width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;font-weight:bold;font-size:11px;">'.$Row['address_street'].' '.$Row['address_town'].' '.$Row['address_state'].'</td>
                    <td width="50%" style="border-right:1px solid #000000;font-size:11px;font-weight:bold;">'.$Row['billing_address_street'].' '.$Row['billing_address_town'].' '.$Row['billing_address_state'].'</td>
                </tr>
                <tr>
                    <td colspan="2" width="50%" style="border-left:1px solid #000000;border-right:1px solid #000000;font-weight:bold;font-size:11px;">GST IN / UIN :'.$Row['gst_no'].'</td>
                    <td width="50%" style="border-right:1px solid #000000;font-size:11px;font-weight:bold;">GST IN / UIN :'.$Row['gst_no'].'</td>
                </tr>
                <tr>
                    <td width="35%" style="border-top:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:11px;"><span style="font-weight:bold;">State : </span>'.$Row['address_state'].'</td>
                    <td width="15%" style="border-top:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:11px;"><span style="font-weight:bold;">code : </span>'.$Row['address_po_code'].'</td>
                    <td width="35%" style="border-top:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:11px;"><span style="font-weight:bold;">State : </span>'.$Row['billing_address_state'].'</td>
                    <td width="15%" style="border-top:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000;border-bottom:1px solid #000000;font-size:11px;"><span style="font-weight:bold;">code : </span>'.$Row['address_po_code'].'</td>
                </tr>
            </table>
            ';


            if($Row['gst_enabled'] == 1){
                $tbl3 ='
                <table border="1" nobr="true" width="100%" cellpadding="3" style="font-size:10px;">
                    <thead>
                        <tr>
                            <th width="5%"  style="border:1px solid #000000;font-weight:bold;" align="center">S.No</th>
                            <th width="21%" style="border:1px solid #000000;font-weight:bold;" align="left">Product Description</th>
                            <th width="8%"  style="border:1px solid #000000;font-weight:bold;" align="center">HSN Code</th>
                            <th width="6%"  style="border:1px solid #000000;font-weight:bold;" align="center">UOM</th>
                            <th width="4%"  style="border:1px solid #000000;font-weight:bold;" align="center">Qty</th>
                            <th width="8%"  style="border:1px solid #000000;font-weight:bold;" align="right">Price</th>
                            <th width="6%"  style="border:1px solid #000000;font-weight:bold;" align="right">Disc.</th>
                            <th width="8%"  style="border:1px solid #000000;font-weight:bold;" align="right">Taxbl Val</th>
                            <th width="6%"  style="border:1px solid #000000;font-weight:bold;" align="center">CGST Rate</th>
                            <th width="6%"  style="border:1px solid #000000;font-weight:bold;" align="right">CGST Amt</th>
                            <th width="6%"  style="border:1px solid #000000;font-weight:bold;" align="center">SGST Rate</th>
                            <th width="6%"  style="border:1px solid #000000;font-weight:bold;" align="right">SGST Amt</th>
                            <th width="10%" style="border:1px solid #000000;font-weight:bold;" align="right">Total</th>
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
                if($row['description_short'] != ''){
                    $titledesc = $titledesc . ' : ' . $row['description_short'];
                }
                if($row['notes'] != ''){
                    $titledesc = $titledesc . ' : ' . $row['notes'];
                }

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
                
                if($row['gst_enabled'] == 1){
                    $total = $tsp + $vat_Sum_Half + $vat_Sum_Half;
                } else {
                    $total = $tsp;
                }

                $tsp = number_format($tsp,2);
                $selling_price = number_format($selling_price,2);

                if($row['gst_enabled'] == 1){
                    $tbl3 = $tbl3.'<tr>
                                        <td width="5%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="center">'.$count.'</td>
                                        <td width="21%" style="border-left:1px solid #000000;border-right:1px solid #000000;" align="left">'.$titledescrip.'</td>
                                        <td width="8%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="center">'.$row['hsn'].'</td>
                                        <td width="6%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="center">'.$row['unit'].'</td>
                                        <td width="4%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="center">'.$row['qty'].'</td>
                                        <td width="8%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="right">'.$selling_price.'</td>
                                        <td width="6%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="right">'.$discount_value_for_display.'</td>
                                        <td width="8%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="right">'.$tsp.'</td>
                                        <td width="6%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="center">'.$vatPercentHalf.' %</td>
                                        <td width="6%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="right">'.number_format($vat_Sum_Half, 2).'</td>
                                        <td width="6%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="center">'.$vatPercentHalf.' %</td>
                                        <td width="6%"  style="border-left:1px solid #000000;border-right:1px solid #000000;" align="right">'.number_format($vat_Sum_Half, 2).'</td>
                                        <td width="10%" style="border-left:1px solid #000000;border-right:1px solid #000000;"  align="right">'.number_format($total, 2).'</td>
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
                                            <td width="15%" align="right">'.$tsp.'</td>
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
                                            <td width="15%" align="right">'.$tsp.'</td>
                                        </tr>
                                       ';

                        $count++;
                    }
                }

                $overallTotal += $total;
                $vatSumTotal  += $vat_Sum_Half;

                $total = $row['total'];
                $terms = $row['payment_terms'];
                $delivery_terms = $row['delivery_terms'];
                $discount = 0;
                $sub_total = $total + $discount;
                $show_discount_percentage = $row['show_discount_percentage'];
            }

            $totaldiscount = $sub_total - $discount;
            $discountPercent = $discount * 100 / $sub_total;
            $totaldiscount = number_format(round($totaldiscount), 2);
            $sub_total = number_format($sub_total,2);
            $discount = number_format($discount,2);
            $discountPercent = number_format($discountPercent,2);
            $displayDiscountPercent = '';

            if($Row['gst_enabled'] == 1) {
                $sub_total_in_words = $fn->getConvertNumber($overallTotal .'.00');

                $tbl3 = $tbl3.'
                            <tr>
                                <td colspan="7" align="center" style="font-weight:bold;">Total Amount in Words</td>
                                <td colspan="5" align="right" style="font-weight:bold;">Total Amount Before Tax</td>
                                <td align="right">'.$totaldiscount.'</td>
                            </tr>
                            <tr>
                                <td rowspan="5" colspan="7" align="center" style="font-weight:bold;font-size:12px;">'.strtoupper($sub_total_in_words).'</td>
                                <td colspan="5" align="right" style="font-weight:bold;">Add: CGST</td>
                                <td align="right">'.number_format($vatSumTotal, 2).'</td>
                            </tr>
                            <tr>
                                <td colspan="5" align="right" style="font-weight:bold;">Add: SGST</td>
                                <td align="right">'.number_format($vatSumTotal, 2).'</td>
                            </tr>
                            <tr>
                                <td colspan="5" align="right" style="font-weight:bold;">Total Tax Amount</td>
                                <td align="right">'.number_format($totalVatSum, 2).'</td>
                            </tr>
                            <tr>
                                <td colspan="5" align="right" style="font-weight:bold;">Total Amount After Tax</td>
                                <td align="right">'.number_format($overallTotal, 2).'</td>
                            </tr>
                        </tbody>
                    </table>';
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
                    <td width="50%" align="center"><span style="font-weight:bold;font-size:11px;">Terms & conditions</span><br/>'.$terms.'</td>
                    <td width="20%" align="center" style="font-weight:bold;font-size:11px;vertical-align:bottom;"><br/><br/><br/><br/>Common Seal</td>
                </tr>
            </table>
            ';


        $pdf->ln(-5);
        $pdf->writeHTML($tbl1, true, false, false, false, '');
        $pdf->writeHTML($tblQuote, true, false, false, false, '');
        $pdf->writeHTML($tbl2, true, false, false, false, '');
        $pdf->writeHTML($tbl3, true, false, false, false, '');
        $pdf->writeHTML($tbl5, true, false, false, false, '');

        $pdf->Output();

    }
}