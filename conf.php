 <?
$config['cp.TimeZone'] = 'Asia/Kolkata';
date_default_timezone_set($config['cp.TimeZone']);

define('CP_HOST', $_SERVER['HTTP_HOST']);
$docRoot = $_SERVER['DOCUMENT_ROOT'];

//================================================================//
if (CP_HOST == "fhtraders.cubosale.in") {
    define('CP_ENV', 'production');
    define('CP_CORE_PATH', $docRoot . '/cmspilotv30/');

} else if (CP_HOST == "fhtraders.smartprosoft.com") {
    define('CP_ENV', 'testing');
    define('CP_CORE_PATH', $docRoot . '/cmspilotv30/');

} else if (CP_HOST == "fhtraders.localhost") {
    $docRoot = $_SERVER['DOCUMENT_ROOT'];
    $rootFolder = substr($docRoot, 0, stripos($docRoot, '/fhtraders/'));

    define('CP_ENV', 'local');
    define('CP_CORE_PATH', $docRoot . '/cmspilotv30/');
}

define('CP_PATH', CP_CORE_PATH . 'CP/');
//================================================================//
require_once(CP_PATH . 'common/lib/inc_path.php');

/*** Local Server **/
$config['local'] = array(
     'db' => array(
          'host'     => 'localhost'
         ,'username' => 'root'
         ,'password' => ''
         ,'dbname'   => 'fhtraders'
     )
    ,'display_errors' => true
    ,'paymentGatewayMode' => 'test'
);

/*** Development Server **/
$config['development'] = $config['local'];
$config['development']['db']['username'] = 'fhtraders';
$config['development']['db']['password'] = 't1r2a6d4i5ngdemo';
$config['development']['display_errors'] = false;

/*** Testing Server **/
$config['testing'] = $config['development'];
$config['testing']['db']['dbname']   = 'fhtraders';
$config['testing']['db']['username'] = 'fhtraders';
$config['testing']['db']['password'] = 'm3U6T6GC4SK79K';
$config['testing']['display_errors'] = true;

/*** Production Server **/
$config['production'] = $config['testing'];
$config['production']['db']['dbname']   = 'fhtraders';
$config['production']['db']['username'] = 'fhtraders';
$config['production']['db']['password'] = 'm3U6T6GC4SK79K';
$config['production']['display_errors'] = true;
$config['production']['paymentGatewayMode'] = 'live';
//================================================================//
require_once(CP_PATH . 'common/lib/Registry.php');
$cfgCommon = require_once(CP_PATH . 'common/lib/config.php');
$cfgMast = require_once($cfgCommon['cp.masterPath'] . 'lib/config.php');
$cfgLoc  = require_once($cfgCommon['cp.localPath'] . 'lib/config.php');
$cpCfg = array_merge($config, $cfgCommon, $cfgMast, $cfgLoc);
Zend_Registry::set('cpCfg',$cpCfg);
//================================================================//
