<?
class CPL_Admin_Modules_Tradingsg_DailyTrack_Model extends CP_Common_Lib_ModuleModelAbstract
{
   function getSQL() {
        $SQL = "
        SELECT v.* 	
            ,CONCAT_WS(' ', s.first_name, s.last_name) AS staff_name  
        FROM daily_track v
        LEFT JOIN staff s ON (s.staff_id = v.staff_id)

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
        $searchVar->mainTableAlias = 'v';

        $daily_track_id = $fn->getReqParam('daily_track_id');
       
        if ($daily_track_id != "") {
            $searchVar->sqlSearchVar[] = "v.daily_track_id = '{$daily_track_id}'";
        } else if ($tv['record_id'] != '') {
            $searchVar->sqlSearchVar[] = "v.daily_track_id = '{$tv['record_id']}'";
        }  
             if ($tv['keyword'] != "") {
                $searchVar->sqlSearchVar[] = "(
                    s.first_name     LIKE '{$tv['keyword']}%'
                    OR v.area        LIKE '%{$tv['keyword']}%'
                    OR v.party       LIKE '{$tv['keyword']}%'
                   
                )";
            }
    }

    /**
     *
     */
     function getNewValidate() {
       $db = Zend_Registry::get('db');
        $validate = Zend_Registry::get('validate');
        $fn = Zend_Registry::get('fn');

        $validate->resetErrorArray();

        $validate->validateData('date', 'Please enter the Date');
      
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

       // $validate->validateData('project_id', 'Please enter the title');

        if (count($validate->errorArray) == 0) {
            return true;
        } else {
            return false;
        }
      
    }

     //function getEditPortalValidate() {
       // return $this->getNewValidate();
   // }

    function getSave(){
        $fn = Zend_Registry::get('fn');
        $validate = Zend_Registry::get('validate');

         if (!$this->getEditValidate()){
            return $validate->getErrorMessageXML();
        }

        $fa = $this->getFields();
       
        $id = $fn->saveRecord($fa);
       $fn->returnAfterNewSave($id);
    }
    /**
     *
     */
    function getFields(){
        $fn = Zend_Registry::get('fn');

        $fa = array();

       
        $fa = $fn->addToFieldsArray($fa, 'daily_track_id');
        $fa = $fn->addToFieldsArray($fa, 'driver');
        $fa = $fn->addToFieldsArray($fa, 'name');
        $fa = $fn->addToFieldsArray($fa, 'date');
        $fa = $fn->addToFieldsArray($fa, 'party');
        $fa = $fn->addToFieldsArray($fa, 'area');
        $fa = $fn->addToFieldsArray($fa, 'material');
        $fa = $fn->addToFieldsArray($fa, 'weight');
        $fa = $fn->addToFieldsArray($fa, 'unloading');
        $fa = $fn->addToFieldsArray($fa, 'diesel');
        $fa = $fn->addToFieldsArray($fa, 'shortage');
        $fa = $fn->addToFieldsArray($fa, 'advance_amount');
        $fa = $fn->addToFieldsArray($fa, 'other_expense');
        $fa = $fn->addToFieldsArray($fa, 'staff_id');
        $fa = $fn->addToFieldsArray($fa, 'vehicle_no');

        return $fa;
    }

  

	

}
