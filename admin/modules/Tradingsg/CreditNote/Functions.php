<?
class CPL_Admin_Modules_Tradingsg_CreditNote_Functions
{
    /**
     *
     */
    function setModuleArray($modules){

        $modObj = $modules->getModuleObj('tradingsg_creditNote');
        $modules->registerModule($modObj, array(
           'actBtnsDetail' => array()
          //,'actBtnsList' => array('printInvoicePDFList')
          ,'hasEditInList' => false
        ));
    }
}
