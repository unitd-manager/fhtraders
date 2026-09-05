<?
class CPL_Admin_Modules_Tradingsg_Supplier_Controller extends CP_Common_Lib_ModuleControllerAbstract
{
    function getCreateLoginForm() {
        return $this->view->getCreateLoginForm();
    }

    function getCreateLoginFormSubmit() {
        return $this->model->getCreateLoginFormSubmit();
    }

}