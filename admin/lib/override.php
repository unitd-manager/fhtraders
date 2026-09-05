<?
$cpCfg = Zend_Registry::get('cpCfg');
$fn    = Zend_Registry::get('fn');
$modulesArr = Zend_Registry::get('modulesArr');

$dashboard = getCPModuleObj('common_dashboard')->model;

$themePath = CP_THEMES_PATH_LOCAL_ALIAS . $cpCfg['cp.theme'] . '/';

$modulesArr['tradingsg_callRegistry']['title'] = 'Lead';

$arr = array();
$userGroupType = $fn->getSessionParam('userGroupType');

$arr[] = $dashboard->getDasboardObj('tradingsg_quoteByStaffChart', array('subClass' => 'subcr p0 mr0'));
if ($userGroupType != "User") {
	$arr[] = $dashboard->getDasboardObj('tradingsg_invoiceChartByMonth', array('subClass' => 'subcr p0 mr0'));
    $arr[] = $dashboard->getDasboardObj('tradingsg_salesByMonthChart');
    $arr[] = $dashboard->getDasboardObj('tradingsg_salesByYearChart', array('subClass' => 'subcr p0 mr0'));
    $arr[] = $dashboard->getDasboardObj('tradingsg_invoiceSummary');
}
#$arr[] = $dashboard->getDasboardObj('project_projectSummary');
#$arr[] = $dashboard->getDasboardObj('project_invoiceSummary', array('subClass' => 'subcr p0 mr0'));
$arr[] = $dashboard->getDasboardObj('tradingsg_leadFollowUp');
$arr[] = $dashboard->getDasboardObj('tradingsg_enquiryFollowUp', array('subClass' => 'subcr p0 mr0'));
$arr[] = $dashboard->getDasboardObj('tradingsg_quoteFollowUp');
$arr[] = $dashboard->getDasboardObj('tradingsg_enquiryByMonthChart', array('subClass' => 'subcr p0 mr0'));

#$arr[] = $dashboard->getDasboardObj('tradingsg_quoteValueByMonthChart');

$cpCfg['cp.dashboardArr'] = $arr;

$cssFilesArr = array();
$cssFilesArr[] = 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,200,200italic,300,300italic,400italic,600,600italic,700italic,700,900,900italic';
$cssFilesArr[] = 'https://fonts.googleapis.com/css?family=Titillium+Web:400,200,200italic,300,300italic,400italic,600,600italic,700,700italic,900';

$cssFilesArr[] = $themePath.'css/bootstrap.min.css';
$cssFilesArr[] = $themePath.'css/bootstrap-theme.min.css';
$jsFilesArr = array();
$jsFilesArr[] = $themePath.'js/bootstrap-modal.js';
$jsFilesArr[] = $themePath.'js/jquery.min.js';
$jssKeys = array('fontAwesome-4.3.0');

CP_Common_Lib_Registry::arrayMerge('jsFilesArr', $jsFilesArr);
CP_Common_Lib_Registry::arrayMerge('jssKeys', $jssKeys);
CP_Common_Lib_Registry::arrayMerge('cssFilesArr', $cssFilesArr);
CP_Common_Lib_Registry::arrayMerge('cpCfg', $cpCfg);
CP_Common_Lib_Registry::arrayMerge('modulesArr', $modulesArr);
