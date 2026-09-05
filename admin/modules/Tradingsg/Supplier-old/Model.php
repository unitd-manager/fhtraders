<?
class CPL_Admin_Modules_Tradingsg_Supplier_Model extends CP_Common_Lib_ModuleModelAbstract
{
    /**
     *
     */
    function getSQL() {

        $SQL = "
        SELECT s.*
              ,gc.name AS country_name
        FROM supplier s
        LEFT JOIN (geo_country gc) ON (s.address_country = gc.country_code)
        ";

        return $SQL;
    }

    /**
     *
     */
    function setSearchVar($linkRecType = '') {
        $tv = Zend_Registry::get('tv');
        $fn = Zend_Registry::get('fn');
        $searchVar = Zend_Registry::get('searchVar');
        $searchVar->mainTableAlias = 's';

        $status       = $fn->getReqParam('status');
        $supplier_id   = $fn->getReqParam('supplier_id');
        $company_name = $fn->getReqParam('company_name');

        if ($supplier_id != "") {
            $searchVar->sqlSearchVar[] = "s.supplier_id = '{$supplier_id}'";
        } else if ($tv['record_id'] != '') {
            $searchVar->sqlSearchVar[] = "s.supplier_id = '{$tv['record_id']}'";
        } else {
            $fn->setSearchVarForLinkData($searchVar, $linkRecType, 's.supplier_id');


            if ($status != "") {
                $searchVar->sqlSearchVar[] = "s.status = '{$status}'";
            }

            if ($company_name != "") {
                $searchVar->sqlSearchVar[] = "s.company_name LIKE '%{$company_name}%'";
            }

            if ($_SESSION['userGroupName'] == "Supplier") {
                $searchVar->sqlSearchVar[] = "s.supplier_id = '{$_SESSION['supplier_id']}'";
            }

            if ($tv['keyword'] != "") {
                $searchVar->sqlSearchVar[] = "(
                    s.company_name  LIKE '%{$tv['keyword']}%'
                    OR s.email      LIKE '%{$tv['keyword']}%'
                )";
            }

            //------------------------------------------------------------------------//
            if ($tv['special_search'] == "Flagged") {
                $searchVar->sqlSearchVar[] = "s.flag = 1";
            }

            if ($tv['special_search'] == "Not-Flagged") {
                $searchVar->sqlSearchVar[] = "(s.flag != 1 OR s.flag IS null)";
            }

            $searchVar->sortOrder = "s.company_name";
        }
    }

    /**
     *
     */
    function getNewValidate() {
        $validate = Zend_Registry::get('validate');

        $validate->resetErrorArray();
        $validate->validateData('company_name', 'Please enter the company name');

        if (count($validate->errorArray) == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     */
    function getAdd(){
        $fn = Zend_Registry::get('fn');
        $validate = Zend_Registry::get('validate');

        if (!$this->getNewValidate()){
            return $validate->getErrorMessageXML();
        }

        $fa = $this->getFields();
        $id = $fn->addRecord($fa);
        $fn->returnAfterNewSave($id);
    }

    /**
     *
     */
    function getEditValidate() {
        $validate = Zend_Registry::get('validate');

        $validate->resetErrorArray();

        if (count($validate->errorArray) == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     */
    function getSave(){
        $fn = Zend_Registry::get('fn');
        $validate = Zend_Registry::get('validate');
        $cpCfg = Zend_Registry::get('cpCfg');

        if (!$this->getEditValidate()){
            return $validate->getErrorMessageXML();
        }

        $fa = $this->getFields();
        $id = $fn->saveRecord($fa);
        //$fn->returnAfterNewSave($id, $cpCfg['cp.pagetoReturnAfterSave']);
        $fn->returnAfterNewSave($id);
    }

    /**
     *
     */
    function getSaveList(){
        $fn = Zend_Registry::get('fn');
        $fn->getSaveList();
    }

    /**
     *
     */
    function getFields(){
        $fn = Zend_Registry::get('fn');

        $fa = array();
        $fa = $fn->addToFieldsArray($fa, 'company_name');
        $fa = $fn->addToFieldsArray($fa, 'code');
        $fa = $fn->addToFieldsArray($fa, 'website');
        $fa = $fn->addToFieldsArray($fa, 'company_size');
        $fa = $fn->addToFieldsArray($fa, 'industry');
        $fa = $fn->addToFieldsArray($fa, 'source');
        $fa = $fn->addToFieldsArray($fa, 'address_flat');
        $fa = $fn->addToFieldsArray($fa, 'address_street');
        $fa = $fn->addToFieldsArray($fa, 'address_town');
        $fa = $fn->addToFieldsArray($fa, 'address_state');
        $fa = $fn->addToFieldsArray($fa, 'address_country');
        $fa = $fn->addToFieldsArray($fa, 'address_po_code');
        $fa = $fn->addToFieldsArray($fa, 'return_address_flat');
        $fa = $fn->addToFieldsArray($fa, 'return_address_street');
        $fa = $fn->addToFieldsArray($fa, 'return_address_town');
        $fa = $fn->addToFieldsArray($fa, 'return_address_state');
        $fa = $fn->addToFieldsArray($fa, 'return_address_country');
        $fa = $fn->addToFieldsArray($fa, 'phone');
        $fa = $fn->addToFieldsArray($fa, 'fax');
        $fa = $fn->addToFieldsArray($fa, 'group_name');
        $fa = $fn->addToFieldsArray($fa, 'status');
        $fa = $fn->addToFieldsArray($fa, 'category');
        $fa = $fn->addToFieldsArray($fa, 'source');
        $fa = $fn->addToFieldsArray($fa, 'industry');
        $fa = $fn->addToFieldsArray($fa, 'company_size');
        $fa = $fn->addToFieldsArray($fa, 'supplier_type');
        $fa = $fn->addToFieldsArray($fa, 'customer_type');
        $fa = $fn->addToFieldsArray($fa, 'mark_up_percentage');
        $fa = $fn->addToFieldsArray($fa, 'cst_no');
        $fa = $fn->addToFieldsArray($fa, 'tin_no');
        $fa = $fn->addToFieldsArray($fa, 'notification_email');
        $fa = $fn->addToFieldsArray($fa, 'email');
        $fa = $fn->addToFieldsArray($fa, 'tin_no');
        $fa = $fn->addToFieldsArray($fa, 'cst_no');

        return $fa;
    }

    /**
     *
     */
    function getCreateLoginFormSubmit() {
        $fn = Zend_Registry::get('fn');
        $validate = Zend_Registry::get('validate');
        $db = Zend_Registry::get('db');
        $dbUtil = Zend_Registry::get('dbUtil');
        $cpCfg = Zend_Registry::get('cpCfg');

        if (!$this->getCreateLoginFormValidate()){
            return $validate->getErrorMessageXML();
        }

        $email  = $fn->getPostParam('email');
        $pass_word    = $fn->getPostParam('pass_word');
        $supplier_id  = $fn->getPostParam('supplier_id');
        $first_name  = $fn->getPostParam('first_name');
        $last_name  = $fn->getPostParam('last_name');

        $fa = array();
        $fa['user_group_id']   = 10;
        $fa['email']   = $email;
        $fa['published'] = 1;
        $fa['creation_date']     = date("Y-m-d H:i:s");
        $fa['pass_word']   = $pass_word;
        $fa['first_name']   = $first_name;
        $fa['last_name']   = $last_name;
        $fa['supplier_id']   = $supplier_id;
        $fa['status']   = 'Current';

        $staff_id = $fn->addRecord($fa, 'staff');

        return $validate->getSuccessMessageXML();
    }

    /**
     *
     */
    function getCreateLoginFormValidate() {
        $validate = Zend_Registry::get('validate');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');

        $validate->resetErrorArray();

        if (count($validate->errorArray) == 0) {
            return true;
        } else {
            return false;
        }
    }
}
