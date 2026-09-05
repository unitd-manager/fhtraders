<?
class CPL_Admin_Modules_Tradingsg_Supplier_View extends CP_Common_Lib_ModuleViewAbstract
{
    /**
     *
     */
    function getList($dataArray){
        $listObj = Zend_Registry::get('listObj');

        $count   = 0;
        $rows    = '';

        foreach ($dataArray as $row){
            $email     = $row['email'];

            $rows .= "
            {$listObj->getListRowHeader($row, $count)}
            {$listObj->getGoToDetailText($count, $row['company_name'])}
            {$listObj->getListDataCell($row['phone'])}
            {$listObj->getListDataCell($row['category'])}
            {$listObj->getListDataCell($row['gst_no'])}
            {$listObj->getListPublishedImage($row['published'], $row['supplier_id'])}
            {$listObj->getListRowEnd($row['supplier_id'])}
            ";

            $count++ ;
        }

        $text = "
        {$listObj->getListHeader()}
        {$listObj->getListHeaderCell('Name', 'c.company_name')}
        {$listObj->getListHeaderCell('Main Phone Number', 's.phone' )}
        {$listObj->getListHeaderCell('Category', 's.category' )}
        {$listObj->getListHeaderCell('GST No', 's.gst_no' )}
        {$listObj->getListHeaderCell('Published', 's.published', 'headerCenter')}
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

        $fielset1 = "
        {$formObj->getTBRow('Supplier Name', 'company_name')}
        ";

        $text = "
        {$formObj->getFieldSetWrapped('Key Details', $fielset1)}
        ";

        return $text;
    }
    /**
     *
     */
    function getEdit($row){
        $formObj = Zend_Registry::get('formObj');
        $cpCfg = Zend_Registry::get('cpCfg');
        $tv = Zend_Registry::get('tv');
        $fn = Zend_Registry::get('fn');

        $formObj->mode = $tv['action'];

        $discountPercent = '';
        $cstNo = '';
        $tinNo = '';

        $sqlStatus   = $fn->getValueListSQL('supplierStatus');
        $sqlSupplier = $fn->getValueListSQL('supplierType');
        $sqlIndustry = $fn->getValueListSQL('companyIndustry');
        $sqlSize     = $fn->getValueListSQL('companySize');
        $sqlSource   = $fn->getValueListSQL('companySource');

        $sqlCountry = getCPModelObj('common_geoCountry')->getCountryDDSQL();
        $expCountry = array('detailValue' => $row['country_name']);

        $expVl = array('sqlType' => 'OneField');

        $createLogin = '';
        $creation_date = $fn->getCPDate($row['creation_date'], 'd-m-Y-H-i-s');
        $modification_date = $fn->getCPDate($row['modification_date'], 'd-m-Y-H-i-s');
        //<td>{$formObj->getTBRow('Discount Percent', 'discount_percent', $row['discount_percent'])}</td>

        if($row['address_country'] == ''){
            $row['address_country'] =  'IN';
        }
        
        $categoryArr = array(
            "Medicine"
            ,"Lab"
        );


        $text = "
        <div class='linkPortalWrapper'>
            <div expanded='0' class='header'>
                <div class='floatbox'>
                    <div class='float_left'>Supplier Details</div>
                    <div class='float_right'>Creation : {$row['created_by']} on {$creation_date} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Modified : {$row['modified_by']} {$modification_date}</div>
                    <div class='toggle'></div>
                    {$createLogin}
                </div>
            </div>
            <div>
                <div class='linkPortalDataWrapper'>
                    <table class='thinlist'>
                        <tbody>
                            <tr>
                                <td>{$formObj->getTBRow('Name', 'company_name', $row['company_name'])}</td>
                                <td>{$formObj->getTBRow('Main Phone Number', 'phone', $row['phone'])}</td>
                                <td>{$formObj->getTBRow('Alternate Phone Number', 'contact_phone', $row['contact_phone'])}</td>
                                <td>{$formObj->getDDRowBySQL('Status', 'status', $sqlStatus, $row['status'], $expVl)}</td>
                                <td>{$formObj->getDDRowByArr('Category', 'category', $categoryArr, $row['category'])}</td>
                            </tr>
                            <tr>
                                <td>{$formObj->getTBRow('Main Fax', 'fax', $row['fax'])}</td>
                                <td>{$formObj->getTBRow('Gst No', 'gst_no', $row['gst_no'])}</td>
                                <td>{$formObj->getTBRow('Tin No', 'tin_no', $row['tin_no'])}</td>
                                <td>{$formObj->getTBRow('Dl No', 'cst_no', $row['cst_no'])}</td>
                                <td>{$formObj->getTBRow('Email', 'email', $row['email'])}</td>
                            </tr>
                            <tr>
                                <th colspan='5'>Address</th>
                            </tr>

                            <tr>
                                <td>{$formObj->getTBRow('Street Address', 'address_street', $row['address_street'])}</td>
                                <td>{$formObj->getTBRow('District/ Town', 'address_town', $row['address_town'])}</td>
                                <td>{$formObj->getTBRow('State', 'address_state', $row['address_state'])}</td>
                                <td>{$formObj->getTBRow('Postal Code', 'address_po_code', $row['address_po_code'])}</td>
                                <td>{$formObj->getDDRowBySQL('Country', 'address_country', $sqlCountry, $row['address_country'], $expCountry)}</td>
                            </tr>

                            <!--<tr>
                                <th colspan='6'>More Details</th>
                            </tr>

                            <tr>
                                <td>{$formObj->getDDRowBySQL('Supplier Type', 'supplier_type', $sqlSupplier, $row['supplier_type'], $expVl)}</td>
                                <td>{$formObj->getDDRowBySQL('Industry', 'industry', $sqlIndustry, $row['industry'], $expVl)}</td>
                                <td>{$formObj->getDDRowBySQL('Company Size', 'company_size', $sqlSize, $row['company_size'], $expVl)}</td>
                                <td>{$formObj->getDDRowBySQL('Company Source', 'source', $sqlSource, $row['source'], $expVl)}</td>
                            </tr>-->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        ";

        return $text;
    }
    /**
     *
     */
    function getEdit1($row){
        $formObj = Zend_Registry::get('formObj');
        $cpCfg = Zend_Registry::get('cpCfg');
        $tv = Zend_Registry::get('tv');
        $fn = Zend_Registry::get('fn');

        $formObj->mode = $tv['action'];

        $discountPercent = '';
        $cstNo = '';
        $tinNo = '';

        $sqlStatus   = $fn->getValueListSQL('companyStatus');
        $sqlSupplier = $fn->getValueListSQL('supplierType');
        $sqlIndustry = $fn->getValueListSQL('companyIndustry');
        $sqlSize     = $fn->getValueListSQL('companySize');
        $sqlSource   = $fn->getValueListSQL('companySource');

        $sqlCountry = getCPModelObj('common_geoCountry')->getCountryDDSQL();
        $expCountry = array('detailValue' => $row['country_name']);

        $expVl = array('sqlType' => 'OneField');

        $createLogin = '';
        $creation_date = $fn->getCPDate($row['creation_date'], 'd-m-Y-H-i-s');
        $modification_date = $fn->getCPDate($row['modification_date'], 'd-m-Y-H-i-s');


        $text = "
        <div class='linkPortalWrapper'>
            <div expanded='1' class='header'>
                <div class='floatbox'>
                    <div class='float_left'>Supplier Details</div>
                    <div class='toggle'></div>
                    <div class='float_right'>Creation : {$row['created_by']} on {$creation_date} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Modified : {$row['modified_by']} {$modification_date}</div>
                    {$createLogin}
                </div>
            </div>
            <div>
                <div class='linkPortalDataWrapper'>
                    <table class='thinlist'>
                        <tbody>
                            <tr>
                                <td>{$formObj->getTBRow('Name', 'company_name', $row['company_name'])}</td>
                                <td>{$formObj->getTBRow('Website', 'website', $row['website'])}</td>
                                <td>{$formObj->getTBRow('Main Phone', 'phone', $row['phone'])}</td>
                                <td>{$formObj->getTBRow('Main Fax', 'fax', $row['fax'])}</td>
                                <td>{$formObj->getTBRow('Email', 'email', $row['email'])}</td>
                                <td>{$formObj->getTBRow('Alternate Email', 'notification_email', $row['notification_email'])}</td>
                            </tr>
                            <tr>
                                <td>{$formObj->getTBRow('TIN No.', 'tin_no', $row['tin_no'])}</td>
                                <td>{$formObj->getTBRow('CST No.', 'cst_no', $row['cst_no'])}</td>
                            </tr>

                            <tr>
                                <th colspan='6'>Supplier Address</th>
                            </tr>

                            <tr>
                                <td>{$formObj->getTBRow('Address1', 'address_flat', $row['address_flat'])}</td>
                                <td>{$formObj->getTBRow('Address2', 'address_street', $row['address_street'])}</td>
                                <td>{$formObj->getTBRow('District/ Town', 'address_town', $row['address_town'])}</td>
                                <td>{$formObj->getTBRow('State/ Zip', 'address_state', $row['address_state'])}</td>
                                <td>{$formObj->getDDRowBySQL('Country', 'address_country', $sqlCountry, $row['address_country'], $expCountry)}</td>
                            </tr>

                            <tr>
                                <th colspan='6'>Return Address</th>
                            </tr>

                            <tr>
                                <td>{$formObj->getTBRow('Address1', 'return_address_flat', $row['return_address_flat'])}</td>
                                <td>{$formObj->getTBRow('Address2', 'return_address_street', $row['return_address_street'])}</td>
                                <td>{$formObj->getTBRow('District/ Town', 'return_address_town', $row['return_address_town'])}</td>
                                <td>{$formObj->getTBRow('State/ Zip', 'return_address_state', $row['return_address_state'])}</td>
                                <td>{$formObj->getDDRowBySQL('Country', 'return_address_country', $sqlCountry, $row['return_address_country'], $expCountry)}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        ";

        return $text;
    }

    /**
     *
     */
    function getCreateLoginForm() {
        $fn = Zend_Registry::get('fn');
        $dbUtil = Zend_Registry::get('dbUtil');
        $cpUtil = Zend_Registry::get('cpUtil');
        $cpCfg = Zend_Registry::get('cpCfg');
        $db = Zend_Registry::get('db');
        $formObj = Zend_Registry::get('formObj');

        $supplier_id = $fn->getReqParam('supplier_id');
        $email = $fn->getReqParam('email');

        $formAction = "index.php?_topRm=utils&module=tradingsg_supplier&_spAction=createLoginFormSubmit&showHTML=0";

        $text = "
        <form id='createLoginForm' class='createLoginForm yform columnar' method='post' action='{$formAction}'>
            {$formObj->getTBRow('First Name', 'first_name', '')}
            {$formObj->getTBRow('Last Name', 'last_name', '')}
            {$formObj->getTBRow('Email', 'email', $email)}
            {$formObj->getTBRow('Password', 'pass_word', '')}
            <input type='hidden' name='supplier_id' value='{$supplier_id}' />
        </form>
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
    function getSearch(){
        $fn = Zend_Registry::get('fn');
        $formObj = Zend_Registry::get('formObj');

        $sqlCategory = $fn->getValueListSQL('companyCategory');
        $sqlStatus   = $fn->getValueListSQL('companyStatus');
        $expVl = array('sqlType' => 'OneField');

        $spArray = array(
            "Flagged"
           ,"Not-Flagged"
        );

        $fielset = "
        {$formObj->getTBRow('Company Name', 'company_name')}
        {$formObj->getDDRowBySQL('Choose Category', 'category', $sqlCategory, 'Client', $expVl)}
        {$formObj->getDDRowBySQL('Status', 'status', $sqlStatus, 'Current', $expVl)}
        {$formObj->getDDRowByArr('Special Search', 'special_search', $spArray)}
        ";

        $text = "
        {$formObj->getFieldSetWrapped('Company Details', $fielset)}
        ";

        return $text;
    }

    /**
     *
     */
    function getRightPanel($row){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $displayLinkData = Zend_Registry::get('displayLinkData');
        $media = Zend_Registry::get('media');

        $text = "
        {$media->getRightPanelMediaDisplay('Attachments', 'tradingsg_supplier', 'attachment', $row)}
        ";

        $sqlSupplier = "
        SELECT s.*
        FROM supplier s
        WHERE s.supplier_id = {$row['supplier_id']}
        ";

        $resultSupplier = $db->sql_query($sqlSupplier);
        $rowSupplier = $db->sql_fetchrow($resultSupplier);

        $printText ="";
        if ($rowSupplier['supplier_id'] != '') {
            $printText .="
            <div id='renewalLinkPortal'>{$this->getAddPurchaseOrder($row['supplier_id'])}</div>
            ";                
        }
        $printText .="
        <div id='advancePaymentPortal' class='c50l'>{$this->getAdvancePaymentPortal($row['supplier_id'])}</div>
        <div id='advancePaymentUsedPortal' class='c50r'>{$this->getAdvancePaymentUsedPortal($row['supplier_id'])}</div>
        ";                
        $text = $printText . $text;

        return $text;
    }
    /**
     *
     */
    function getProductDetailDisplay($row){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $formObj = Zend_Registry::get('formObj');
        $dbUtil = Zend_Registry::get('dbUtil');

        $rows  = "";
        

        $SQLPD = "
        SELECT pop.cost_price
              ,p.title AS product
        FROM `po_product` pop
        LEFT JOIN (product p) ON (p.product_id = pop.product_id)
        LEFT JOIN (supplier s) ON (s.supplier_id = pop.supplier_id)
        WHERE pop.cost_price != 0.00
        AND pop.supplier_id = {$row['supplier_id']}
        GROUP BY pop.product_id
        ORDER BY pop.product_id DESC
        ";

        $resultPD   = $db->sql_query($SQLPD);
        $recCount = $db->sql_numrows($resultPD);
        while ($rowPD = $db->sql_fetchrow($resultPD)) {

            $rows .= "
            <tr>
                <td>{$rowPD['product']}</td>
                <td>{$rowPD['free_items']}</td>
                <td>{$rowPD['cost_price']}</td>
            </tr>
            ";
        }

        $header ="
        <tr>
          <th>Medicine Name</th>
          <th>Free</th>
          <th>Cost Price</th>
        </tr>
        ";

        if($recCount == 0){
            $header = "<tr><td align='center'>No Records Linked<br/><br/></td></tr>";
        }



        $text = "
        <div class='linkPortalWrapper tradingsg_supplier_productDetailDisplayLink'>
          <div class='panel panel-primary'>
            <div class='panel-heading'>
              <div expanded='1'>
                  <div class='floatbox'>
                      <div class='float_left RightPanelHeading'>Product Detail Display</div>
                      <div class='txtRight'>
                          <span class='count' id='ProductDetailDisplayPortalCount'>({$recCount})</span>
                          <div class='toggle'></div>
                      </div>
                  </div>
              </div>
            </div>
            <div class='panel-body'>
                <div class='linkPortalDataWrapper'>
                    <form>
                        <table class='ProductDetailDisplayList'>
                            <thead>
                               {$header}
                            </thead>
                            <tbody id='ProductDetailDisplayPortal'>
                                {$rows}
                            </tbody>
                        </table>
                </form>
            </div>
            </div>
          </div>
        </div>
        ";

        return $text;
    }
    /**
     *
     */
    function getAddPurchaseOrder($supplier_id=''){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $formObj = Zend_Registry::get('formObj');
        $dbUtil = Zend_Registry::get('dbUtil');
        $cpUtil = Zend_Registry::get('cpUtil');

        if($supplier_id == ''){
            $supplier_id = $fn->getReqParam('supplier_id');
        }

        $PurchaseOrder = $this->getAddPurchaseOrderDetail($supplier_id);

        $recCount = $fn->getRecordCount('purchase_order', "company_id_supplier = '{$supplier_id}'");

        $header ="
        <thead>
            <tr>
                <th width='8%' >PO Code</th>
                <th width='8%' >PO Date</th>
                <th width='14%' class='txtRight'>PO Value</th>
                <th width='15%' class='txtRight'>Balance</th>
                <th width='15%'>Payment Status</th>
                <th width='15%'></th>
            </tr>
        </thead>
        ";

        if($recCount == 0){
            $header ="<thead></thead>";
        }

        $actionButtons = '';

        $SQLPO = "
        SELECT p.purchase_order_id
        FROM purchase_order p
        WHERE p.company_id_supplier = {$supplier_id}
        AND (p.payment_status != 'Cancelled'
        OR p.payment_status IS NULL)
        ";
        $resultPO = $db->sql_query($SQLPO);
        $numRowsPO = $db->sql_numrows($resultPO);

        if($numRowsPO > 0){
            $formActionPurchaseOrder = "index.php?module=tradingsg_supplier&_spAction=generatePurchaseOrderForm&supplier_id={$supplier_id}&showHTML=0";

            $actionButtons .="
            <div class='header'>
                <div class='floatbox'>
                    <div class='btn btn-info'>
                        <a href='{$formActionPurchaseOrder}' id='generatePO'>Make Supplier Payment</a>
                    </div>
                </div>
            </div>
            ";
        }

        $monthSearch    = '';

        $month  = $fn->getReqParam('month');
        if ($month == '') {
            $month = date('m');
        }

        $arr = array (
                '01' => 'January'
               ,'02' => 'February'
               ,'03' => 'March'
               ,'04' => 'April'
               ,'05' => 'May'
               ,'06' => 'June'
               ,'07' => 'July'
               ,'08' => 'August'
               ,'09' => 'September'
               ,'10' => 'October'
               ,'11' => 'November'
               ,'12' => 'December'
               );


        $monthSearch = "
        <div class='float_left mt5 mb5'>
            <td class='fieldValue'>
                <select name='month'>
                    <option value=''>Select Month</option>
                    {$cpUtil->getDropDownFromArr($arr, $month)}
                </select>
            </td>
        </div>
        ";

        $text = "
        <div class='linkPortalWrapper tradingsg_supplier__tradingsg_purchase_OrderLink' id='purchaseordermonthfilter'>
            {$actionButtons}
            <div class='header' expanded='1'>
                <div class='floatbox'>
                    <div class='float_left'>Purchase Order Linked</div>
                    <div class='txtRight float_right'>
                        <span class='count'>({$recCount})</span>
                        <div class='toggle'></div>
                    </div>
                </div>
            </div>
            <div class='linkPortalDataWrapper'>
                <form>
                    <table class='renewallist'>
                        {$header}
                        <tbody>
                            {$PurchaseOrder}
                        </tbody>
                    </table>
                    <input type='hidden' name='supplier_id' value='{$supplier_id}' />
                </form>
            </div>
        </div>
        ";

        return $text;

    }
    /**
     *
     */
    function getAddPurchaseOrderDetail($supplier_id=''){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $formObj = Zend_Registry::get('formObj');
        $dbUtil = Zend_Registry::get('dbUtil');

        if($supplier_id == ''){
            $supplier_id = $fn->getReqParam('supplier_id');
        } 

        $site_id = $fn->getReqParam('site_id');

        $appendSite = '';
        if($site_id != "") {
            $appendSite = "AND pc.site_id = {$site_id}";
        }

        //$company_id_supplier = $fn->getReqParam('company_id_supplier');

        $rows  = "";

        $month       = $fn->getReqParam('month');

        if ($month == '') {
            $month = date('m');
        } else {
            $month = $month;
        }

        $SQL="
        SELECT pc.*
        FROM purchase_order pc
        LEFT JOIN supplier su ON pc.company_id_supplier = su.supplier_id
        WHERE pc.company_id_supplier = '{$supplier_id}'
        AND pc.status != 'Cancelled'
        {$appendSite}
        order by pc.purchase_order_id
        ";
        $result   = $db->sql_query($SQL);
        $numRows = $db->sql_numrows($result);
        $OveraatotalCost  = 0;
        $OverallBalance   = 0;
        $overall_discount = 0;

        $count = 1;
        while ($row = $db->sql_fetchrow($result)) {

            $purchase_order_date = $fn->getCPDate($row['purchase_order_date'], 'd-m-Y');

            $SQLTotal = "
            SELECT SUM(pop.qty * pop.cost_price) AS total_cost
                  ,SUM(((pop.qty * pop.cost_price) * pop.gst) / 100) AS GST_Total
            FROM po_product pop WHERE pop.purchase_order_id = {$row['purchase_order_id']}
            ";
            $resultTotal = $db->sql_query($SQLTotal);
            $rowTotal = $db->sql_fetchrow($resultTotal);
            $totalCost   = $rowTotal['total_cost'] + $rowTotal['GST_Total'];
            $OveraatotalCost += round($totalCost);

            //$totalCost = $rowTotal['total_cost'];
            //$totalCost = number_format($rowTotal['total_cost'], 2);
            $totalCost   = number_format(round($totalCost));
            $purchaseOrderLink = "index.php?_topRm=inventory&module=tradingsg_purchaseOrder&_action=edit&purchase_order_id={$row['purchase_order_id']}";

            $SQLPartialPayment = "
            SELECT SUM(srh.amount) AS Po_partial_payment
            FROM supplier_receipt_history srh
            LEFT JOIN supplier_receipt sr ON (sr.supplier_receipt_id = srh.supplier_receipt_id)
            WHERE srh.purchase_order_id = {$row['purchase_order_id']}
              AND sr.receipt_status    != 'Cancelled'
            ";
            $resultPartialPayment = $db->sql_query($SQLPartialPayment);
            $rowPartialPayment    = $db->sql_fetchrow($resultPartialPayment);

            $Balance = $rowTotal['total_cost'] + $rowTotal['GST_Total'] - $rowPartialPayment['Po_partial_payment'];

            $OverallBalance += round($Balance);
            $Balance = number_format(round($Balance));
            if($row['site_id'] == 2){
                $color =  'style=background-color:#DDEBF9';
            }
            else{
                $color =  'style=background-color:#FFFFFF';
            }
            $viewHistoryUrl = "index.php?module=tradingsg_supplier&_spAction=receiptHistoryForSupplier&purchase_order_id={$row['purchase_order_id']}&showHTML=0";
            $viewHistory = "
            <a href='{$viewHistoryUrl}' purchase_order_id='{$row['purchase_order_id']}' class='receiptViewHistory'><u>View History</u></a>";

            $rows .= "
                <tr $color> 
                    <td width='8%'>{$row['po_code']}</td>
                    <td width='8%' ><a href='{$purchaseOrderLink}' target='_blank'><u>{$purchase_order_date}</u></a></td>
                    <td width='14%' style='color:blue'; class='txtRight'>{$totalCost}</td>
                    <td width='15%' style='color:blue'; class='txtRight'>{$Balance}</td>
                    <td width='15%'>{$row['payment_status']}</td>
                    <td width='15%'>{$viewHistory}</td>
                </tr>
            ";

            //$OveraatotalCost += $totalCost;
            //$OverallBalance   += $Balance;

            $count++;
        }
        $OveraatotalCost = number_format($OveraatotalCost, 2);
        $OverallBalance = number_format($OverallBalance, 2);
        
        $rows .= "
            <tr>
                <td class='txtRight lastRowBgColor' colspan='2'>Total</td>
                <td style='color:blue'; class='txtRight lastRowBgColor' ><b>{$OveraatotalCost}</b></td>
                <td style='color:blue'; class='txtRight lastRowBgColor' ><b>{$OverallBalance}</b></td>
                <td  class='txtRight lastRowBgColor'></td>
            </tr>
        ";

        if($numRows == 0){
            $rows = "
                <tr>
                    <td class='noRenewal'>No Records Linked</td>
                </tr>
            ";

        }
        $text="{$rows}";

        return $text;
    }

    /**
     *
     */
    function getAdvancePaymentPortal($supplier_id=''){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $formObj = Zend_Registry::get('formObj');
        $dbUtil = Zend_Registry::get('dbUtil');
        $cpUtil = Zend_Registry::get('cpUtil');

        if($supplier_id == ''){
            $supplier_id = $fn->getReqParam('supplier_id');
        }

        $advancePaymentDetail = $this->getAdvancePaymentDetail($supplier_id);

        $recCount = $fn->getRecordCount('advance_payment_history', "supplier_id = '{$supplier_id}'");

        $header ="
        <thead>
            <tr>
                <th width='8%' >Date</th>
                <th width='14%' class='txtRight'>Amount Added</th>
            </tr>
        </thead>
        ";

        if($recCount == 0){
            $header ="<thead></thead>";
        }

        $actionButtons = '';
        $formAction = "index.php?module=tradingsg_supplier&_spAction=generateAdvancePaymentForm&supplier_id={$supplier_id}&showHTML=0";

        $actionButtons .="
        <div class='header'>
            <div class='floatbox'>
                <div class='btn btn-info'>
                    <a href='{$formAction}' id='generateAdvancePayment'>Make Advance Payment</a>
                </div>
            </div>
        </div>
        ";

        $text = "
        <div class='linkPortalWrapper tradingsg_supplier__tradingsg_purchase_OrderLink' id='advancePaymentPortalDisplay'>
            {$actionButtons}
            <div class='header' expanded='1'>
                <div class='floatbox'>
                    <div class='float_left'>Advance Payment</div>
                    <div class='txtRight float_right'>
                        <span class='count'>({$recCount})</span>
                        <div class='toggle'></div>
                    </div>
                </div>
            </div>
            <div class='linkPortalDataWrapper'>
                <form>
                    <table class='renewallist'>
                        {$header}
                        <tbody>
                            {$advancePaymentDetail}
                        </tbody>
                    </table>
                    <input type='hidden' name='supplier_id' value='{$supplier_id}' />
                </form>
            </div>
        </div>
        ";

        return $text;

    }
    /**
     *
     */
    function getAdvancePaymentDetail($supplier_id=''){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $formObj = Zend_Registry::get('formObj');
        $dbUtil = Zend_Registry::get('dbUtil');

        if($supplier_id == ''){
            $supplier_id = $fn->getReqParam('supplier_id');
        } 
        $rows  = "";

        $SQL="
        SELECT a.*
        FROM advance_payment_history a
        LEFT JOIN supplier su ON a.supplier_id = su.supplier_id
        WHERE a.supplier_id = '{$supplier_id}'
        order by a.advance_payment_history_id
        ";
        $result   = $db->sql_query($SQL);
        $numRows = $db->sql_numrows($result);
        $Overalltotal  = 0;
        $OverallBalance   = 0;
        $overall_discount = 0;

        $count = 1;
        while ($row = $db->sql_fetchrow($result)) {

            $creation_date = $fn->getCPDate($row['creation_date'], 'd-m-Y');

            $rows .= "
                <tr> 
                    <td width='8%'>{$creation_date}</td>
                    <td width='14%' style='color:blue'; class='txtRight'>{$row['amount']}</td>
                </tr>
            ";

            $Overalltotal += $row['amount'];

            $count++;
        }
        $Overalltotal = number_format($Overalltotal, 2);
        
        $rows .= "
            <tr>
                <td class='txtRight lastRowBgColor'>Total</td>
                <td style='color:blue'; class='txtRight lastRowBgColor' ><b>{$Overalltotal}</b></td>
            </tr>
        ";

        if($numRows == 0){
            $rows = "
                <tr>
                    <td class='noRenewal'>No Records Linked</td>
                </tr>
            ";

        }
        $text="{$rows}";

        return $text;
    }

    /**
     *
     */
    function getAdvancePaymentUsedPortal($supplier_id=''){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $formObj = Zend_Registry::get('formObj');
        $dbUtil = Zend_Registry::get('dbUtil');
        $cpUtil = Zend_Registry::get('cpUtil');

        if($supplier_id == ''){
            $supplier_id = $fn->getReqParam('supplier_id');
        }

        $advancePaymentDetail = $this->getAdvancePaymentUsedDetail($supplier_id);

        $recCount = $fn->getRecordCount('supplier_receipt', "supplier_id = '{$supplier_id}' AND advance_payment_used > 0 AND receipt_status !='Cancelled'");

        $header ="
        <thead>
            <tr>
                <th width='8%' >PO Code</th>
                <th width='8%' >Date</th>
                <th width='14%' class='txtRight'>Amount Used</th>
            </tr>
        </thead>
        ";

        if($recCount == 0){
            $header ="<thead></thead>";
        }

        $actionButtons = '';

        $text = "
        <div class='linkPortalWrapper tradingsg_supplier__tradingsg_purchase_OrderLink' id='advancePaymentPortalUsedDisplay'>
            <div class='header' expanded='1'>
                <div class='floatbox'>
                    <div class='float_left'>Advance Payment Used</div>
                    <div class='txtRight float_right'>
                        <span class='count'>({$recCount})</span>
                        <div class='toggle'></div>
                    </div>
                </div>
            </div>
            <div class='linkPortalDataWrapper'>
                <form>
                    <table class='renewallist'>
                        {$header}
                        <tbody>
                            {$advancePaymentDetail}
                        </tbody>
                    </table>
                    <input type='hidden' name='supplier_id' value='{$supplier_id}' />
                </form>
            </div>
        </div>
        ";

        return $text;

    }
    /**
     *
     */
    function getAdvancePaymentUsedDetail($supplier_id=''){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $formObj = Zend_Registry::get('formObj');
        $dbUtil = Zend_Registry::get('dbUtil');

        if($supplier_id == ''){
            $supplier_id = $fn->getReqParam('supplier_id');
        } 
        $rows  = "";

        $SQL="
        SELECT sr.*
              ,po.po_code
        FROM supplier_receipt_history srh
        LEFT JOIN supplier_receipt sr ON (sr.supplier_receipt_id = srh.supplier_receipt_id)
        LEFT JOIN purchase_order po ON (po.purchase_order_id = srh.purchase_order_id)
        WHERE sr.supplier_id = '{$supplier_id}'
          AND sr.advance_payment_used > 0
          AND sr.receipt_status !='Cancelled'
        order by sr.supplier_receipt_id
        ";
        $result   = $db->sql_query($SQL);
        $numRows = $db->sql_numrows($result);
        $Overalltotal  = 0;
        $OverallBalance   = 0;
        $overall_discount = 0;

        $count = 1;
        while ($row = $db->sql_fetchrow($result)) {

            $creation_date = $fn->getCPDate($row['date'], 'd-m-Y');

            $rows .= "
                <tr> 
                    <td width='8%'>{$row['po_code']}</td>
                    <td width='8%'>{$creation_date}</td>
                    <td width='14%' style='color:blue'; class='txtRight'>{$row['advance_payment_used']}</td>
                </tr>
            ";

            $Overalltotal += $row['advance_payment_used'];

            $count++;
        }
        $Overalltotal = number_format($Overalltotal, 2);
        
        $rows .= "
            <tr>
                <td class='txtRight lastRowBgColor' colspan='2'>Total</td>
                <td style='color:blue'; class='txtRight lastRowBgColor' ><b>{$Overalltotal}</b></td>
            </tr>
        ";

        if($numRows == 0){
            $rows = "
                <tr>
                    <td class='noRenewal'>No Records Linked</td>
                </tr>
            ";

        }
        $text="{$rows}";

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

        $status   = $fn->getReqParam('status');

        $sqlStatus = $fn->getValueListSQL('supplierStatus');

        $spArray = array(
            "Flagged"
           ,"Not-Flagged"
        );

        $text = "
        <td>
            <select name='status' >
                <option value=''>Status</option>
                {$dbUtil->getDropDownFromSQLCols1($db, $sqlStatus, $status)}
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
    function getGeneratePurchaseOrderForm() {
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $formObj = Zend_Registry::get('formObj');
        $db = Zend_Registry::get('db');
        $cpUtil = Zend_Registry::get('cpUtil');

        $overall_discount = 0;
        unset($_SESSION['selectedPOIds']);

        $rows   = '';
        $today  = date('Y-m-d');
        $month  = $fn->getReqParam('month');
        if ($month == '') {
            $month = date('m');
        }else{
            $month = $month;
        }

        $supplier_id = $fn->getReqParam('supplier_id');

        $monthSearch = '';
        $month = $fn->getReqParam('month');
        if ($month == '') {
            $month = date('m');
        }

        $arr = array (
                '01' => 'January'
               ,'02' => 'February'
               ,'03' => 'March'
               ,'04' => 'April'
               ,'05' => 'May'
               ,'06' => 'June'
               ,'07' => 'July'
               ,'08' => 'August'
               ,'09' => 'September'
               ,'10' => 'October'
               ,'11' => 'November'
               ,'12' => 'December'
            );


        $monthSearch = "
        <div class='float_right  monthfilter mt5 mb5'>
            <td class='fieldValue'>
                <select name='month'>
                    <option>Select Month</option>
                    {$cpUtil->getDropDownFromArr($arr, $month)}
                </select>
            </td>
        </div>
        ";

        $SQL = "
        SELECT i.*
            ,(
            SELECT SUM(supHist.amount) AS prev_sum
            FROM supplier_receipt_history supHist
            LEFT JOIN supplier_receipt r ON (r.supplier_receipt_id = supHist.supplier_receipt_id)
            WHERE supHist.purchase_order_id =  i.purchase_order_id
            AND r.receipt_status != 'Cancelled'
            ) as prev_inv_amount
            ,o.supplier_id
        FROM purchase_order i
        LEFT JOIN `supplier` o ON (i.company_id_supplier = o.supplier_id)
        WHERE i.company_id_supplier = {$supplier_id}
        AND (i.payment_status = 'Due' || i.payment_status = 'Partially Paid' || i.payment_status IS NULL)
        AND i.status != 'Cancelled'
        order by i.purchase_order_id
        ";
        $result = $db->sql_query($SQL);
        $numRows = $db->sql_numrows($result);

        /*if ($numRows == 0) {
            return "Sorry no po is available or all the po are closed";
        }*/

        $header ="
        <thead>
            <tr height='40px'>
                <th class='click-all-top'>
                    <a href='#' class='check-all'>
                        <img src='{$cpCfg['cp.commonImagesPathAlias']}icons/checkbox_checked.gif'>
                    </a>
                    <a href='#' class='uncheck-all'>
                        <img src='{$cpCfg['cp.commonImagesPathAlias']}icons/checkbox_unchecked.gif'>
                    </a>
                </th>                        
                <th>Po Code</th>
                <input type='hidden' name='supplier_id' value='{$supplier_id}' />
            </tr>
        </thead>
        ";
        
        $SupplierPayment = $this->getSupplierPaymentDetail();

        $formAction = "index.php?_topRm=inventory&module=tradingsg_supplier&_spAction=generatePurchaseOrderFormSubmit&showHTML=0";       
        $expNoEdit  = array('isEditable' => 0);

        $SQLAP="
        SELECT SUM(a.amount) AS advance_amount
        FROM advance_payment_history a
        WHERE a.supplier_id = '{$supplier_id}'
        ";
        $resultAP   = $db->sql_query($SQLAP);
        $rowAP = $db->sql_fetchrow($resultAP);

        $SQLAPU="
        SELECT SUM(a.advance_payment_used) AS advance_payment_used
        FROM supplier_receipt a
        WHERE a.supplier_id = '{$supplier_id}'
          AND a.receipt_status != 'Cancelled'
        ";
        $resultAPU   = $db->sql_query($SQLAPU);
        $rowAPU = $db->sql_fetchrow($resultAPU);

        $total_advance_amount = $rowAP['advance_amount'] - $rowAPU['advance_payment_used'];

        $text = "
        <form id='portalForm' class='yform columnar receiptForm' method='post' action='{$formAction}'>
            <h3>Please select Purchase Order</h3>
            <div id='supplierpaymentmonthfilter'>
                <table border='1' width='100%' cellpadding='4' class='renewallist thinlist room-poCode-table'>
                    {$header}
                    <tbody>
                        {$SupplierPayment}
                    </tbody>
                </table>
            </div>
            {$formObj->getDateRow('Date', 'date', '')}
            {$formObj->getTBRow('Amount', 'amount', '')}
            <input type='hidden' name='totalAmountPo' value='' />
            {$formObj->getTBRow('Advance', 'total_advance_amount', $total_advance_amount, $expNoEdit)}
            <input type='checkbox' class='inputCheckboxForAdvanceAmount advanceAmount' name='advanceAmountchk' value=''> Use Advance Payment
            <input type='text' value='{$total_advance_amount}' id='fld_advance_amount' class='text' name='advance_amount'>
            {$formObj->getDDRowByVL('Mode of Payment', 'mode_of_payment',  'paymentType')}
            {$formObj->getTextAreaRow('Note', 'remarks')}
            <input type='hidden' name='supplier_id' value='{$supplier_id}' />
        </form>
        ";

        return $text;

    }

    /**
     *
     */
    function getGenerateAdvancePaymentForm() {
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $formObj = Zend_Registry::get('formObj');
        $db = Zend_Registry::get('db');
        $cpUtil = Zend_Registry::get('cpUtil');

        $overall_discount = 0;
        unset($_SESSION['selectedPOIds']);

        $rows   = '';
        $today  = date('Y-m-d');

        $supplier_id = $fn->getReqParam('supplier_id');
        
        $formAction = "index.php?_topRm=inventory&module=tradingsg_supplier&_spAction=generateAdvancePaymentFormSubmit&showHTML=0";       
        $expNoEdit  = array('isEditable' => 0);

        $text = "
        <form id='portalForm' class='yform columnar advancePaymentForm' method='post' action='{$formAction}'>
            {$formObj->getDateRow('Date', 'date', '')}
            {$formObj->getTBRow('Amount', 'amount', '')}
            <input type='hidden' name='supplier_id' value='{$supplier_id}' />
        </form>
        ";

        return $text;
    }

    /**
     *
     */
    function getSupplierPaymentDetail(){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $formObj = Zend_Registry::get('formObj');
        $dbUtil = Zend_Registry::get('dbUtil');

        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $formObj = Zend_Registry::get('formObj');
        $db = Zend_Registry::get('db');
        $cpUtil = Zend_Registry::get('cpUtil');

        $overall_discount = 0;
        unset($_SESSION['selectedPOIds']);

        $rows   = '';
        $today  = date('Y-m-d');
        $month  = $fn->getReqParam('month');
        if ($month == '') {
            $month = date('m');
        }else{
            $month = $month;
        }

        $supplier_id = $fn->getReqParam('supplier_id');

        $SQL = "
        SELECT i.*
            ,(
            SELECT SUM(supHist.amount) AS prev_sum
            FROM supplier_receipt_history supHist
            LEFT JOIN supplier_receipt r ON (r.supplier_receipt_id = supHist.supplier_receipt_id)
            WHERE supHist.purchase_order_id =  i.purchase_order_id
            AND r.receipt_status != 'Cancelled'
            ) as prev_inv_amount
        FROM purchase_order i
        LEFT JOIN `supplier` o ON (i.company_id_supplier = o.supplier_id)
        WHERE i.company_id_supplier = {$supplier_id}
        AND (i.payment_status = 'Due' || i.payment_status = 'Partially Paid' || i.payment_status IS NULL)
        AND i.status != 'Cancelled'
        order by i.purchase_order_id
        ";
        $result = $db->sql_query($SQL);
        $numRows = $db->sql_numrows($result);

        if($numRows == 0) {
            return "<tr><td colspan='2'>Sorry no po is available or all the po are closed</td></tr>";
        }

        $count = 1;
        $po_amount = 0;
        $prev_inv_amount = 0;
        while ($row = $db->sql_fetchrow($result)) {
            $overall_discount = 0;

            $sqlQty = "
            SELECT SUM(pop.qty * pop.cost_price) AS po_amount
                  ,SUM(((pop.qty * pop.cost_price) * pop.gst) / 100) AS GST_Total
            FROM po_product pop WHERE pop.purchase_order_id = {$row['purchase_order_id']}
            ";
            $resultQty = $db->sql_query($sqlQty);
            $rowQty = $db->sql_fetchrow($resultQty);
            $po_amount   = $rowQty['po_amount'] + $rowQty['GST_Total'];
            //$po_amount = $rowQty['po_amount'];

            $SQLPartialPayment = "
            SELECT SUM(srh.amount) AS Po_partial_payment
            FROM supplier_receipt_history srh
            LEFT JOIN (purchase_order p) ON (srh.purchase_order_id = p.purchase_order_id)
            LEFT JOIN supplier_receipt sr ON (sr.supplier_receipt_id = srh.supplier_receipt_id)
            WHERE p.purchase_order_id = {$row['purchase_order_id']}
              AND sr.receipt_status != 'Cancelled'
            ";
            $resultPartialPayment = $db->sql_query($SQLPartialPayment);
            $rowPartialPayment    = $db->sql_fetchrow($resultPartialPayment);

            $paidAmountPrev = "";
            $prev_inv_amount = number_format($row['prev_inv_amount'], 2);
            if($row['prev_inv_amount'] > 0){
                $paidAmountPrev = "Paid: {$prev_inv_amount}";
            }
            $po_amount   = $po_amount - $rowPartialPayment['Po_partial_payment'];

            $po_amount = number_format(round($po_amount), 2);

            $inputRow = "
            <input type='checkbox' class='inputCheckboxForPurchaseOrder poCode' name='poCode[]' value='{$row['purchase_order_id']}' purchase_order_id='{$row['purchase_order_id']}'>
            <input type='hidden' class='inputSiteIdForPurchaseOrder siteId' name='siteId[]' value='{$row['site_id']}' purchase_order_id='{$row['purchase_order_id']}'>
            ";

            if($row['site_id'] == 2){
                $color =  'style=background-color:#DDEBF9';
            }
            else{
                $color =  'style=background-color:#FFFFFF';
            }

            $rows .= "
                <tr height='40px' {$color}>
                    <td>{$inputRow}</td>
                    <td>PO NO : {$row['po_code']}({$po_amount})</td>
                </tr>
            ";

            $count++;
        }

        $text="{$rows}";

        return $text;
    }

    /**
     *
     */
    function getNewSupplier(){
        $formObj = Zend_Registry::get('formObj');
        $fn = Zend_Registry::get('fn');
        $tv = Zend_Registry::get('tv');

        $formAction = "index.php?_spAction=addSupplier&lnkRoom=tradingsg_supplier&showHTML=0";
        $sqlCountry = getCPModelObj('common_geoCountry')->getCountryDDSQL();
        
        $text = "
        <form id='portalForm' class='yform columnar' method='post' action='{$formAction}'>
            <fieldset>
                {$formObj->getTBRow('Name', 'company_name')}
                {$formObj->getTBRow('Website', 'website')}
                {$formObj->getTBRow('Phone', 'phone')}
                {$formObj->getTBRow('Gst No', 'gst_no')}
                {$formObj->getTBRow('Office Address', 'address_flat')}
                {$formObj->getTBRow('Street Address', 'address_street')}
                {$formObj->getTBRow('District/ Town', 'address_town')}
                {$formObj->getTBRow('State/ Zip', 'address_state')}
                {$formObj->getDDRowBySQL('Country', 'address_country', $sqlCountry)}
            </fieldset>
            
        </form>
        ";

        return $text;
    }

    /**
     *
     */
    function getReceiptHistoryForSupplier() {
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');

        $purchase_order_id    = $fn->getReqParam('purchase_order_id');

        $rows = '';
        $errorText = '';

        $sqlClient = "
        SELECT sr.amount
              ,sr.date
              ,sr.mode_of_payment
              ,sr.remarks
              ,sr.receipt_status
              ,sr.supplier_receipt_id
              ,sr.supplier_id
              ,srh.purchase_order_id
        FROM supplier_receipt_history srh
        LEFT JOIN (supplier_receipt sr) ON (sr.supplier_receipt_id = srh.supplier_receipt_id)
        WHERE srh.purchase_order_id = {$purchase_order_id}
        ORDER BY srh.supplier_receipt_history_id
        ";

        $result     = $db->sql_query($sqlClient);
        $numRows    = $db->sql_numrows($result);

        if ($numRows == 0) {
            $clientRows =  "
            <table class='thinlist'>
                <td>Sorry, no previous Purchase History records for this Suppliers</td>
            </table>";
        }
        else{
            while ($row = $db->sql_fetchrow($result)) {
                $date = $fn->getCPDate($row['date'], 'd-m-Y');
                if ($row['receipt_status'] != 'Cancelled'){
                    $cancelSupplierReceipt = "<a href='#' class='cancelSupplierReceipt' supplier_receipt_id='{$row['supplier_receipt_id']}' purchase_order_id='{$row['purchase_order_id']}' supplier_id='{$row['supplier_id']}'><u>Cancel</u></a>";
                } else{
                    $cancelSupplierReceipt = 'Cancelled';                    
                }

                $rows .= "
                <tr>
                    <td>{$date}</td>
                    <td>{$row['amount']}</td>
                    <td>{$row['mode_of_payment']}</td>
                    <td>{$row['remarks']}</td>
                    <td>{$cancelSupplierReceipt}</td>
                </tr>
                ";
            }

            $clientRows = "
            <table class='thinlist'>
                <thead>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Mode of Payment</th>
                    <th>Notes</th>
                    <th></th>
                </thead>

                <tbody>
                    {$rows}
                </tbody>
            </table>
            ";
        }

        $text ="
        {$clientRows}
        ";

        return $text;
    }
}