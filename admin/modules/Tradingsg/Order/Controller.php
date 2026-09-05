<?
class CPL_Admin_Modules_Tradingsg_Order_Controller extends CP_Common_Lib_ModuleControllerAbstract
{
    /**
     *
     */
    function getUploadToDHL() {
        return $this->model->getUploadToDHL();
    }

    function getGenerateDHLLabel() {
        return $this->model->getGenerateDHLLabel();
    }

    function getAttachDHLLabelToOrder() {
        return $this->model->getAttachDHLLabelToOrder();
    }

    function getPrintDeliveryOrder() {
        return $this->view->getPrintDeliveryOrder();
    }

    function getPrintDeliveryOrderByProductGroup() {
        return $this->view->getPrintDeliveryOrderByProductGroup();
    }

    function getPrintOrderSummary() {
        return $this->view->getPrintOrderSummary();
    }

    function getPrintCaptainCopy() {
        return $this->view->getPrintCaptainCopy();
    }

    function getPrintCaptainCopy1() {
        return $this->view->getPrintCaptainCopy1();
    }

    function getCreditPortalDisplay() {
        return $this->view->getCreditPortalDisplay();
    }

     function getGenerateCreditFormSubmit() {
        return $this->model->getGenerateCreditFormSubmit();
    }

     function getGenerateCreditNoteForm() {
        return $this->view->getGenerateCreditNoteForm();
    }

    function getPrintCreditRecord() {
        return $this->view->getPrintCreditRecord();
    }


    function getDebitPortalDisplay() {
        return $this->view->getDebitPortalDisplay();
    }

     function getGenerateDebitFormSubmit() {
        return $this->model->getGenerateDebitFormSubmit();
    }

     function getGenerateDebitNoteForm() {
        return $this->view->getGenerateDebitNoteForm();
    }

    function getPrintDebitRecord() {
        return $this->view->getPrintDebitRecord();
    }


    function getGenerateInvoiceForm() {
        $modObj = getCPModuleObj('tradingsg_invoice');
        return $modObj->view->getGenerateInvoiceForm();
    }

    function getGenerateInvoiceFormSubmit() {
        $modObj = getCPModuleObj('tradingsg_invoice');
        return $modObj->model->getGenerateInvoiceFormSubmit();
    }

    function getEditInvoiceFormSubmit() {
        $modObj = getCPModuleObj('tradingsg_invoice');
        return $modObj->model->getEditInvoiceFormSubmit();
    }

    function getGenerateReceiptForm() {
        $modObj = getCPModuleObj('tradingsg_receipt');
        return $modObj->view->getGenerateReceiptForm();
    }

    function getGenerateReceiptFormSubmit() {
        $modObj = getCPModuleObj('tradingsg_receipt');
        return $modObj->model->getGenerateReceiptFormSubmit();
    }

    function getPopulateReceiptAmount() {
        return $this->model->getPopulateReceiptAmount();
    }

    function getPopulateInvoiceAmount() {
        return $this->model->getPopulateInvoiceAmount();
    }

    function getCancelInvoice() {
        return $this->model->getCancelInvoice();
    }

    function getCancelCreditNote() {
        return $this->model->getCancelCreditNote();
    }

    function getCancelDebitNote() {
        return $this->model->getCancelDebitNote();
    }

    function getCancelReceipt() {
        return $this->model->getCancelReceipt();
    }

    function getPrintInvoiceRecord() {
        return $this->view->getPrintInvoiceRecord();
    }

    function getPrintInvoiceRecordForPurchaseOrder() {
        return $this->view->getPrintInvoiceRecordForPurchaseOrder();
    }

    function getPrintReceipt() {
        return $this->view->getPrintReceipt();
    }

    function getTransporterInvoiceRecord() {
        return $this->view->getTransporterInvoiceRecord();
    }

    function getExtraInvoiceRecord() {
        return $this->view->getExtraInvoiceRecord();
    }

    function getEditInvoiceForm() {
        $modObj = getCPModuleObj('tradingsg_invoice');
        return $modObj->view->getEditInvoiceForm();
    }

    function getPrintProformaOrderItemInvoiceRecord() {
        return $this->view->getPrintProformaOrderItemInvoiceRecord();
    }

    function getSummaryInOrder () {
        return $this->view->getSummaryInOrder();
    }

    function getDeleteInvoice () {
        return $this->model->getDeleteInvoice();
    }

    function getDeleteReceipt () {
        return $this->model->getDeleteReceipt();
    }

}