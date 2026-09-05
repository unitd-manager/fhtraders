<?
class CPL_Admin_Modules_Tradingsg_DailyTrack_View extends CP_Common_Lib_ModuleViewAbstract
{


    /**
     *
     */
    function getList($dataArray){
        $listObj = Zend_Registry::get('listObj');
        $fn = Zend_Registry::get('fn');

        $count   = 0;
        $rows    = '';

        foreach ($dataArray as $row){		
         $employeeRec = $fn->getRecordRowByID('staff', 'staff_id', $row['staff_id']);
          $date = $fn->getCPDate($row['date'], 'd-m-Y');
	            			
            $rows .= "
            {$listObj->getListRowHeader($row, $count)}            
            {$listObj->getListDataCell($date)}   
             {$listObj->getListDataCell($row['staff_name'])}  
             {$listObj->getListDataCell($row['party'])}    
             {$listObj->getListDataCell($row['area'])}      
             {$listObj->getListDataCell($row['material'])}     
             {$listObj->getListDataCell($row['weight'])}  
             {$listObj->getListDataCell($row['unloading'])}    
             {$listObj->getListDataCell($row['advance_amount'])}      
             {$listObj->getListDataCell($row['diesel'])}    
             {$listObj->getListDataCell($row['other_expense'])}   
             {$listObj->getListDataCell($row['shortage'])}    
             {$listObj->getListDataCell($row['vehicle_no'])}      
             
            ";
            $count++;
        }
        $rows = $listObj->getDisplayListRows($rows);

        $text = "
        {$listObj->getListHeader()}
		
        
        {$listObj->getListHeaderCell('Date', 'v.date')}
        {$listObj->getListHeaderCell('Driver Name', 'v.staff_name')} 
         {$listObj->getListHeaderCell('Party', 'v.party')}
         {$listObj->getListHeaderCell('Area', 'v.area')}
        {$listObj->getListHeaderCell('Material', 'v.material')}
        {$listObj->getListHeaderCell('Weight', 'v.weight')}
         {$listObj->getListHeaderCell('Unloading', 'v.unloading')}
        {$listObj->getListHeaderCell('Advance Amount', 'v.advance_amount')}
         {$listObj->getListHeaderCell('Diesel', 'v.diesel')}
          {$listObj->getListHeaderCell('Other Expense', 'v.other_expense')}
           {$listObj->getListHeaderCell('Shortage', 'v.shortage')}
            {$listObj->getListHeaderCell('Vehicle No', 'v.vehicle_no')}
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
		 $fn = Zend_Registry::get('fn');
        $tv = Zend_Registry::get('tv');

     

		        
        $fielset="
        {$formObj->getDATERow('Date', 'date')}
        ";

        $text = "
        {$formObj->getFieldSetWrapped('Key Details', $fielset)}
        ";
        return $text;
    }

    /**
     *
     */
    function getEdit($row){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
        $formObj = Zend_Registry::get('formObj');
		$tv = Zend_Registry::get('tv');
        $db = Zend_Registry::get('db');

        $expNoEdit = array('isEditable' => 0);

         $formObj->mode = $tv['action'];

          $daily_track_id = $fn->getReqParam('daily_track_id');
           

        
        $creation_date = $fn->getCPDate($row['creation_date'], 'd-m-Y-H-i-s');
        $modification_date = $fn->getCPDate($row['modification_date'], 'd-m-Y-H-i-s');
        
        
        $expCode = array('isEditable' => 0);
        $sqlPM = $fn->getDDSql('core_staff', array('condn' => "position = 'Driver'"));
        $expVl   = array('sqlType' => 'OneField');
        
        $text = "
        <div class='linkPortalWrapper'>
            <div expanded='1' class='header'>
                <div class='floatbox'>
                    <div class='float_left'>Details</div>
                    <div class='toggle'></div>
                    <div class='float_right'>Creation : {$row['created_by']} on {$creation_date} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Modified : {$row['modified_by']} {$modification_date}</div>
                </div>
            </div>
            <div>
                <div class='linkPortalDataWrapper'>
                    <table class='thinlist'>
                        <tbody>
                            <tr>        	
                                <td>{$formObj->getDATERow('Date', 'date', $row['date'])}</td>	
                                <td>{$formObj->getDDRowBySQL('Driver Name', 'staff_id', $sqlPM, $row['staff_id'])}</td>
                                <td>{$formObj->getTBRow('Party', 'party', $row['party'])}</td>
                                <td> {$formObj->getTBRow('Area', 'area',  $row['area'])}</td>                                
                        	</tr>   
                            <tr>               
                                <td>{$formObj->getTBRow('Material', 'material', $row['material'])}</td>
                                <td>{$formObj->getTBRow('Weight', 'weight', $row['weight'])}</td>
                                <td>{$formObj->getTBRow('Unloading', 'unloading', $row['unloading'])}</td>
                                <td>{$formObj->getTBRow('Advance Amount', 'advance_amount', $row['advance_amount'])}</td>

                                
                            </tr>
                            <tr>               
                                <td> {$formObj->getTBRow('Diesel', 'diesel',  $row['diesel'])}</td>
                                <td>{$formObj->getTBRow('Other Expense', 'other_expense', $row['other_expense'])}</td>
                                 <td>{$formObj->getTBRow('Shortage', 'shortage', $row['shortage'])}</td>
                                 <td>{$formObj->getTBRow('Vehicle No', 'vehicle_no', $row['vehicle_no'])}</td>
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
    function getRightPanel($row){
        $cpCfg = Zend_Registry::get('cpCfg');
        $fn = Zend_Registry::get('fn');
       $db = Zend_Registry::get('db');
        $displayLinkData = Zend_Registry::get('displayLinkData');
        $media = Zend_Registry::get('media');

 

        $text = "
        {$media->getRightPanelMediaDisplay('Attachments', 'tradingsg_dailyTrack', 'attachment', $row)}
        

        ";


        return $text;
    }

   
    /**
     *
     */
    function getQuickSearch() {
        $cpUtil = Zend_Registry::get('cpUtil');
        $tv = Zend_Registry::get('tv');
        $fn = Zend_Registry::get('fn');
        $db = Zend_Registry::get('db');
        $dbUtil = Zend_Registry::get('dbUtil');

       
        $type     = $fn->getReqParam('type');

        //==================================================================//
        

        $statusArr = array(
            "Machine"
           ,"Equipment"
        );

        $text = "
  
       
        ";

        return $text;
    }

}