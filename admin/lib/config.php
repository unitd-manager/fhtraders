<?
$cpCfg = array();
$cpCfg['cp.theme']               = 'Angle';
$cpCfg['cp.jqVersion']           = '1.8.2';
$cpCfg['cp.jqUiVersion']         = '1.9.2';
$cpCfg['cp.tradingLoginText']    = 1;
$cpCfg['cp.hasAccessModule']     = true;
$cpCfg['cp.hasMultiUniqueSites'] = true;
$cpCfg['cp.assetVersion']        = '100';
$cpCfg['cp.defaultPageCssClass'] = 'hidecol1';

$cpCfg['w.common_multiUniqueSite.ignoreModules'] = array(
     'common_site'
    ,'common_dashboard'
    ,'tradingsg_quote'
    ,'tradingsg_company'
    ,'tradingsg_contact'
    ,'tradingsg_supplierOrder'
    ,'tradingsg_product'
    ,'tradingsg_purchaseOrder'
    ,'tradingsg_supplier'
    ,'tradingin_inventory'
    ,'tradingsg_reports'
    ,'webBasic_content'
    ,'core_userGroup'
    ,'tradingsg_productGroup'
    ,'webBasic_category'
    ,'webBasic_subCategory'
    ,'webBasic_content'
    ,'core_valuelist'
    ,'core_translation'
);

$cpCfg['cp.topRooms'] = array(
   /*'dashboard' => array(
        'title' => 'Dashboard'
       ,'modules' => array(
             'common_dashboard'
       )
       ,'default' => 'common_dashboard'
    )

   ,'enquiry' => array(
        'title' => 'Enquiry'
       ,'modules' => array(
             'tradingsg_callRegistry'
            ,'tradingsg_enquiry'
       )
       ,'default' => 'tradingsg_enquiry'
    )*/

    'order' => array(
        'title' => 'Trade'
       ,'modules' => array(
             'common_dashboard'
            ,'tradingsg_quote'
            ,'tradingsg_company'
            ,'tradingsg_contact'
            ,'core_staff'
            ,'tradingsg_supplierOrder'
            
       )
       ,'default' => 'common_dashboard'
    )

   ,'inventory' => array(
        'title' => 'Inventory'
       ,'modules' => array(
             'tradingsg_pos'
            ,'tradingsg_product'
            ,'tradingsg_purchaseOrder'
            ,'tradingsg_supplier'
            ,'tradingsg_dailyTrack'
            ,'tradingin_inventory'
       )
       ,'default' => 'tradingsg_pos'
    )

   ,'finance' => array(
        'title' => 'Finance'
       ,'modules' => array(
             'tradingsg_order'
            ,'tradingsg_invoice'
            ,'tradingsg_creditNote'
            ,'tradingsg_debitNote'
            ,'tradingsg_expense'
            ,'tradingsg_expenseHead'
            ,'tradingsg_reports'
       )
       ,'default' => 'tradingsg_order'
    )

   /*,'pos' => array(
        'title' => 'POS'
       ,'modules' => array(
             'tradingsg_pos'
       )
       ,'default' => 'tradingsg_pos'
    )*/

    ,'admin' => array(
        'title' => 'Admin'
       ,'modules' => array(
             'core_userGroup'
            ,'tradingsg_productGroup'
            ,'tradingsg_staffAttendance'
       )
       ,'default' => 'tradingsg_productGroup'
    )

   ,'utils' => array(
        'title' => 'Utils'
       ,'modules' => array(
             'common_site'
            ,'webBasic_category'
            ,'webBasic_subCategory'
            ,'webBasic_content'
            ,'core_valuelist'
            ,'core_setting'
            ,'core_translation'
       )
       ,'default' => 'core_translation'
    )

    /*,'reports' => array(
        'title' => 'Reports'
       ,'modules' => array(
            'tradingsg_reports'
       )
       ,'default' => 'tradingsg_reports'
    )*/
);

$hiddenModules = array(
     'common_contactLink'
    ,'common_testRecipientLink'
    ,'common_interestLink'
    ,'ecommerce_orderItemLink'
    ,'tradingsg_companyLink'
    ,'tradingsg_productLink'
    ,'tradingsg_categoryLink'
    ,'tradingsg_purchaseOrderLink'
    ,'ecommerce_product'
    ,'tradingsg_contactLink'
    ,'tradingsg_quoteLink'
    ,'tradingsg_expenseLink'
    ,'tradingsg_discountLink'
    ,'tradingsg_batchHistoryLink'
    ,'tradingsg_productGroupLink'
 );

$tmpName = &$cpCfg['cp.topRooms'];
$cpCfg['cp.availableModules'] = array_merge(
	    $tmpName['order']['modules']
    //, $tmpName['dashboard']['modules']
    , $tmpName['finance']['modules']
    //, $tmpName['pos']['modules']
    , $tmpName['inventory']['modules']
    , $tmpName['admin']['modules']
    , $tmpName['utils']['modules']
    //, $tmpName['enquiry']['modules']
    //, $tmpName['reports']['modules']
    ,  $hiddenModules);

$cpCfg['cp.availableModGroups'] = array(
     'core'
    ,'common'
    ,'webBasic'
    ,'ecommerce'
    ,'tradingsg'
    ,'tradingin'
    ,'project'
);

$cpCfg['cp.availableWidgets'] = array(
     'tradingsg_invoiceSummary'
    ,'tradingsg_enquiryFollowUp'
    ,'tradingsg_quoteFollowUp'
    ,'tradingsg_leadFollowUp'
    ,'tradingsg_leadByStaff'
    ,'tradingsg_salesByMonthChart'
    ,'tradingsg_salesByYearChart'
    ,'tradingsg_invoiceChartByMonth'
    ,'tradingsg_invoiceSummary'
    ,'tradingsg_enquiryByMonthChart'
    ,'tradingsg_quoteValueByMonthChart'
    ,'tradingsg_salesByMonth'
    ,'tradingsg_salesByYear'
    ,'tradingsg_invoiceByMonth'
    ,'tradingsg_invoiceByYear'
    ,'tradingsg_profitByMonth'
    ,'tradingsg_profitByYear'
    ,'tradingsg_quoteByMonth'
    ,'tradingsg_quoteByYear'
    ,'tradingsg_salesByClient'
    ,'tradingsg_invoiceByClient'
    ,'tradingsg_enquiryByMonth'
    ,'tradingsg_enquiryByYear'
    ,'tradingsg_enquiryByStaff'
    ,'tradingsg_enquiryActivityByStaff'
    ,'tradingsg_salesSummaryByProduct'
    ,'tradingsg_quoteByStaff'
    ,'tradingsg_detailInvoiceByMonth'
    ,'tradingsg_salesSummaryByProductGroup'
    ,'tradingsg_invoiceSummaryByProductGroup'
    ,'tradingsg_stockReport'
    ,'tradingsg_detailSummaryByClient'
    ,'tradingsg_quoteByStaffChart'
    ,'tradingsg_purchaseOrderReport'
    ,'tradingsg_dailyCollectionReport'
    ,'tradingsg_salesSummaryByProduct'
    ,'tradingsg_priceTrackReport'
    ,'tradingsg_stockReport'
    ,'tradingsg_profitLossReport'
    ,'common_multiUniqueSite'
);

$cpCfg['cp.availablePlugins'] = array(
     'common_comment'
    ,'common_media'
    ,'common_login'
);

return $cpCfg;