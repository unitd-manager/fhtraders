<?
class CPL_Admin_Modules_Tradingsg_DebitNote_Functions
{
    /**
     *
     */
    function setModuleArray($modules){

        $modObj = $modules->getModuleObj('tradingsg_debitNote');
        $modules->registerModule($modObj, array(
           'actBtnsDetail' => array()
          //,'actBtnsList' => array('printInvoicePDFList')
          ,'hasEditInList' => false
        ));
    }
}
