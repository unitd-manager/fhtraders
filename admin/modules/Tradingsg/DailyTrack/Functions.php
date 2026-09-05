<?
class CPL_Admin_Modules_Tradingsg_DailyTrack_Functions 
{
    //==================================================================//
   function setModuleArray($modules){

       
	$cpCfg = Zend_Registry::get('cpCfg');
        $modObj = $modules->getModuleObj('tradingsg_dailyTrack');
        $modObj['tableName'] = 'daily_track';
        $modObj['keyField']  = 'daily_track_id';
        $modules->registerModule($modObj, array(
            'hasMultiLang'  => 1
           ,'hasFlagInList' => 0
           ,'actBtnsList'   => array('new')
           ,'actBtnsDetail' => array('edit', 'delete', 'duplicate')
           ,'actBtnsEdit'   => array('save', 'apply', 'delete')
		   ,'relatedTables' => array('media')
       ,'title'         => 'Daily Track'
        ));
    }


    /**
     *
     */
    function setMediaArray($mediaArr) {

        //------------------------------------------------------------------------------//
        $mediaObj = $mediaArr->getMediaObj('tradingsg_dailyTrack', 'attachment', 'attachment');

        $mediaArr->registerMedia($mediaObj, array(
        ));
    }
	
    

    
    /**
     *
     */
   
}