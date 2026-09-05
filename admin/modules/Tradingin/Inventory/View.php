<?
class CPL_Admin_Modules_Tradingin_Inventory_View extends CP_Admin_Modules_Tradingin_Inventory_View
{
    /**
     *
     */
    function getList($dataArray){
        $listObj = Zend_Registry::get('listObj');
        $db      = Zend_Registry::get('db');

        $count   = 0;
        $rows    = '';

        //TO CREATE INVENTORY RECORDS FROM PRODUCT RECORD
        $this->getCreateInventoryRecords();
         $stock = '';
        foreach ($dataArray as $row){

            /*$StockSql = "
            SELECT
                (SELECT SUM(qty) FROM po_product
                WHERE product_id = {$row['product_id']}) as product_qty_purchased
                ,(SELECT SUM(ordItem.qty) FROM order_item ordItem
                LEFT JOIN (`order` o) ON (o.order_id = ordItem.order_id)
                WHERE ordItem.record_id = {$row['product_id']}
                AND (o.order_status = 'Paid' || o.order_status = 'Due')
                ) as product_qty_sold_from_quote
            ";

            $resultStockSql = $db->sql_query($StockSql);
            $rowStockSql    = $db->sql_fetchrow($resultStockSql);*/

            //$stock = $rowStockSql['product_qty_purchased'] - $rowStockSql['product_qty_sold_from_quote'];

            $stock = $row['stock'] + $row['changed_stock'];

            $SQLUpdate = "
            update inventory set actual_stock = {$stock}
            WHERE inventory_id = {$row['inventory_id']}
            ";
            $result1 = $db->sql_query($SQLUpdate);

            $rows .= "
            {$listObj->getListRowHeader($row, $count)}
            {$listObj->getGoToDetailText($count, $row['product_name'])}
            {$listObj->getListDataCell($row['part_number'])}
            {$listObj->getListDataCell($row['product_group_title'])}
            {$listObj->getListDataCell($row['company_name'])}
            {$listObj->getListDataCell($row['unit'])}
            {$listObj->getListDataCell($stock)}
            {$listObj->getListRowEnd($row['inventory_id'])}
            ";

            $count++ ;
        }

        $text = "
        {$listObj->getListHeader()}
        {$listObj->getListHeaderCell('Name', 'product_name')}
        {$listObj->getListHeaderCell('Part Number', 'part_number')}
        {$listObj->getListHeaderCell('Product Group', 'product_group_title')}
        {$listObj->getListHeaderCell('Supplier', 'company_name')}
        {$listObj->getListHeaderCell('UOM', 'i.unit')}
        {$listObj->getListHeaderCell('Stock', 'stock' )}
        {$listObj->getListHeaderEnd()}
        {$rows}
        {$listObj->getListFooter()}
        ";

        return $text;
    }

    /**
     *
     */
    function getNew(){
        $formObj = Zend_Registry::get('formObj');

        $text = "
        ";

        return $text;
    }

    /**
     *
     */
    function getEdit($row){
        $formObj = Zend_Registry::get('formObj');
        $cpCfg   = Zend_Registry::get('cpCfg');
        $tv      = Zend_Registry::get('tv');
        $fn      = Zend_Registry::get('fn');
        $db      = Zend_Registry::get('db');
        $db = Zend_Registry::get('db');

        $formObj->mode = $tv['action'];
        $expNoEdit  = array('isEditable' => 0);

            /*,(SELECT SUM(ordItem.qty) FROM order_item ordItem
            LEFT JOIN (`order` o) ON (o.order_id = ordItem.order_id)
            WHERE ordItem.record_id = {$row['product_id']}
            AND (o.order_status = 'Paid' || o.order_status = 'Due')
            ) as product_qty_sold_from_quote*/
        $StockSql = "
        SELECT
            (SELECT SUM(qty) FROM po_product
            WHERE product_id = {$row['product_id']} AND status = 'print') as product_qty_purchased

            ,(SELECT SUM(invItem.qty) FROM invoice_item invItem
            LEFT JOIN (invoice inv) ON (inv.invoice_id = invItem.invoice_id AND inv.status != 'Cancelled' )
            LEFT JOIN (`order` o) ON (o.order_id = inv.order_id)
            WHERE record_id = {$row['product_id']}
            ) as product_qty_sold_from_quote
        ";
        $resultStockSql = $db->sql_query($StockSql);
        $rowStockSql    = $db->sql_fetchrow($resultStockSql);

        $stock = $row['stock'] + $row['changed_stock'];

        $fieldset1 = "
        {$formObj->getTBRow('Name', 'product_name', $row['product_name'], $expNoEdit)}
        {$formObj->getTBRow('Part Number', 'part_number', $row['part_number'], $expNoEdit)}
        {$formObj->getTBRow('UOM', 'unit', $row['unit'], $expNoEdit)}
        {$formObj->getTBRow('Total Available Qty', 'total_available_qty', $stock, $expNoEdit)}
        {$formObj->getTBRow('Total Sold Qty', 'total_sold_qty',  $rowStockSql['product_qty_sold_from_quote'], $expNoEdit)}
        {$formObj->getTBRow('Change Stock', 'changed_stock', $row['changed_stock'])}
        {$formObj->getTARow('Notes', 'notes', $row['notes'])}
        ";

        $text = "
        {$formObj->getFieldSetWrapped('Product Details', $fieldset1)}
        {$formObj->getCreationModificationText($row)}
        ";

        return $text;
    }

    /**
     *
     */
    function getPrintDetail($row){
        $db = Zend_Registry::get('db');
        return $this->getDetail($row);
    }

    /**
     *
     */
    function getRightPanel($row){
        $fn = Zend_Registry::get('fn');
        $displayLinkData = Zend_Registry::get('displayLinkData');
        $media = Zend_Registry::get('media');

        $text = "
        {$this->getPurchaseOrderDisplay($row)}
        {$this->getOrderDisplay($row)}
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
        $fn = Zend_Registry::get('fn');

        $supplier_id         = $fn->getReqParam('supplier_id');
        $product_group_id         = $fn->getReqParam('product_group_id');

        $spArray = array(
            "Flagged"
           ,"Not-Flagged"
        );

        $sqlSupplier = "
        SELECT c.company_id
              ,c.company_name
        FROM company c
        WHERE c.category = 'Supplier'
        ORDER BY c.company_name
        ";

        $sqlProGroup = "
        SELECT p.product_group_id
              ,p.title
        FROM product_group p
        ";

        $text = "
        <td>
            <select name='supplier_id'>
                <option value=''>Supplier Name</option>
                {$dbUtil->getDropDownFromSQLCols2($db, $sqlSupplier, $supplier_id)}
            </select>
        </td>
        <td>
            <select name='product_group_id'>
                <option value=''>Product Group</option>
                {$dbUtil->getDropDownFromSQLCols2($db, $sqlProGroup, $product_group_id)}
            </select>
        </td>
        <td>
            <select name='special_search'>
                <option value=''>Special Search</option
                {$cpUtil->getDropDown1($spArray, $tv['special_search'])}
           </select>
        </td>
        ";

        return $text;
    }
    /**
     *
     */
    function getCreateInventoryRecords() {
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $cpUtil = Zend_Registry::get('cpUtil');

        $SQL = "
        SELECT p.product_id
        FROM product p
        WHERE p.product_id NOT IN(
            SELECT invent.product_id
            FROM inventory invent
        )
        ORDER BY p.product_id
       ";

       $SQL = "
        SELECT p.product_id
        FROM product p
        LEFT JOIN inventory inv ON inv.product_id = p.product_id
        WHERE inv.product_id IS NULL
       ";

        $result = $db->sql_query($SQL);

        while ($row = $db->sql_fetchrow($result)) {

            $fa = array();

            $fa['product_id']     = $row['product_id'];
            $fa['creation_date']  = date('Y-m-d H:i:s');

            $inventory_id = $fn->addRecord($fa, 'inventory');
        }
    }

    /**
     */
    function getOrderDisplay($row){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $formObj = Zend_Registry::get('formObj');
        $dbUtil = Zend_Registry::get('dbUtil');

        $formAction = '';

        $text = "
        <tr class=''>
        <td>
            <div class='header'>Orders Linked</div>
            <div id='' class='orderDisplay'>
                <form id='orderItemPrint' class='' method='post' action='{$formAction}'>
                    <div id='invoicePortalOuter'>
                        {$this->getOrderDisplayDetail($row)}
                    </div>
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
    function getOrderDisplayDetail($row){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $formObj = Zend_Registry::get('formObj');
        $dbUtil = Zend_Registry::get('dbUtil');

        $rows  = "";
        $rowsPvt  = "";
        $links = "";
        $leftJoin  = "";
        $sqlAppend = "";

        $SQL = "
        SELECT DISTINCT o.order_id
              ,oi.order_item_id
              ,oi.item_title
              ,oi.unit_price
              ,oi.qty
              ,oi.qty * oi.unit_price
              ,o.order_date
              ,o.record_type
              ,com.company_name
        FROM `order_item` oi
        LEFT JOIN (`order` o) ON (o.order_id = oi.order_id)
        LEFT JOIN (`invoice` inv) ON (o.order_id = inv.order_id)
        LEFT JOIN company com ON com.company_id = o.company_id
        WHERE oi.record_id = {$row['product_id']}
        AND (o.order_status = 'Paid' || o.order_status = 'Due')
        AND (inv.status = 'Paid' || inv.status = 'Due' || inv.status = 'Partial Payment')
        ";

        $result   = $db->sql_query($SQL);
        $client = '';
        while ($rowOI = $db->sql_fetchrow($result)) {
            if($rowOI['record_type'] == 'POS'){
                $client = 'POS';
            }
            else{
                $client = $rowOI['company_name'];
            }

            $SQLINV = "
            SELECT DISTINCT i.invoice_id
                  ,it.unit_price
                  ,it.qty
                  ,i.invoice_date
                  ,i.invoice_code
            FROM `invoice_item` it
            LEFT JOIN (`invoice` i) ON (i.invoice_id = it.invoice_id)
            WHERE it.record_id = {$row['product_id']}
            AND i.order_id = {$rowOI['order_id']}
            AND (i.status = 'Paid' || i.status = 'Due' || i.status = 'Partial Payment')
            ";

            $resultINV   = $db->sql_query($SQLINV);
            $rowsInv = '';
            while ($rowINV = $db->sql_fetchrow($resultINV)) {
                $urlPrint = "index.php?_topRm=finance&module=tradingsg_order&_spAction=printInvoiceRecord&invoice_code={$rowINV['invoice_code']}&invoice_type=normal&showHTML=0";
                $rowsInv .="
                <tr>
                    <td><a href='{$urlPrint}' target='_blank'>{$rowINV['invoice_code']}</a></td>
                    <td>{$fn->getCPDate($rowINV['invoice_date'], 'd-m-Y')}</td>
                    <td>{$rowINV['unit_price']}</td>
                    <td>{$rowINV['qty']}</td>
                </tr>
                ";
            }

            $rows .= "
            <tr>
                <td>{$rowOI['order_id']}</td>
                <td>{$fn->getCPDate($rowOI['order_date'], 'd-m-Y')}</td>
                <td>{$rowOI['unit_price']}</td>
                <td>{$rowOI['qty']}</td>
                <td>{$client}</td>

            </tr>
            <tr>
                <td colspan='5'>
                <table class='thinlist'>
                    <tr style='background-color:#0F9191; color:#ffffff'>
                    <th>Invoice Code</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Qty</th>
                    </tr>
                    {$rowsInv}
                </table>
                </td>
            </tr>
            ";
        }

        $header ="
        <tr style='background-color:#EAEAE8;'>
        <th>Order Id</th>
        <th>Date</th>
        <th>Amount</th>
        <th>Qty</th>
        <th>Client</th>
        </tr>
        ";

        $text = "
        <table class='thinlist'>
            {$header}
            {$rows}
        </table>
        ";

        return $text;
    }

    /**
     */
    function getPurchaseOrderDisplay($row){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $formObj = Zend_Registry::get('formObj');
        $dbUtil = Zend_Registry::get('dbUtil');

        $formAction = '';

        $text = "
        <tr class=''>
        <td>
            <div class='header'>Purchase Orders Linked</div>
            <div id='' class='poDisplay'>
                <form id='poPrint' class='' method='post' action='{$formAction}'>
                    <div id='invoicePortalOuter'>
                        {$this->getPurchaseOrderDisplayDetail($row)}
                    </div>
                </form>
            </div>
        </td>
        </tr>
        ";

        return $text;
    }

    /**
     */
    function getPurchaseOrderDisplayDetail($row){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $formObj = Zend_Registry::get('formObj');
        $dbUtil = Zend_Registry::get('dbUtil');

        $rows  = "";
        $rowsPvt  = "";
        $links = "";
        $leftJoin  = "";
        $sqlAppend = "";
        $tdForSiteId = "";
        $thForSiteId = "";
        $leftjnAppend = "";

        if($cpCfg['cp.hasMultiUniqueSites']  == true){
            $sqlAppend = ",st.title as site_title";
            $leftjnAppend = "
            LEFT JOIN site st ON st.site_id = po.site_id";
        }

        $SQL = "
        SELECT pop.price
              ,pop.qty
              ,com.company_name AS supplier_name
              ,po.po_code
              ,po.purchase_order_date
              ,po.purchase_order_id
              ,po.creation_date
              {$sqlAppend}
        FROM po_product pop
        LEFT JOIN purchase_order po ON po.purchase_order_id = pop.purchase_order_id
        LEFT JOIN company com ON pop.supplier_id = com.company_id
        {$leftjnAppend}
        WHERE pop.product_id = {$row['product_id']}
          AND pop.status = 'print'
        ";

        $result   = $db->sql_query($SQL);

        while ($rowPo = $db->sql_fetchrow($result)) {
            if($cpCfg['cp.hasMultiUniqueSites']  == true){
                $tdForSiteId = "<td>{$rowPo['site_title']}</td>";
            }

            if($rowPo['purchase_order_date'] == '' || $rowPo['purchase_order_date'] == 0){
                $purchase_order_date = $fn->getCPDate($rowPo['creation_date'], 'd-m-Y');
            }
            else{
                $purchase_order_date = $fn->getCPDate($rowPo['purchase_order_date'], 'd-m-Y');
            }
            $po_code = "<a href='index.php?_topRm=order&module=tradingsg_purchaseOrder&record_id={$rowPo['purchase_order_id']}&_action=edit'>{$rowPo['po_code']}</a>";

            $rows .= "
            <tr>
                <td>{$po_code}</td>
                {$tdForSiteId}
                <td>{$purchase_order_date}</td>
                <td>{$rowPo['price']}</td>
                <td>{$rowPo['qty']}</td>
                <td>{$rowPo['supplier_name']}</td>
            </tr>
            ";
        }

        if($cpCfg['cp.hasMultiUniqueSites']  == true){
            $thForSiteId = "<th>Location</th>";
        }
        $header ="
        <tr style='background-color:#EAEAE8;'>
        <th>PO Code</th>
        {$thForSiteId}
        <th>Date</th>
        <th>Amount</th>
        <th>Qty</th>
        <th>Supplier</th>
        </tr>
        ";

        $text = "
        <table class='thinlist'>
            {$header}
            {$rows}
        </table>
        ";

        return $text;
    }

}