<?php
try {
	if(preg_match('/(?i)msie [1-6]/',$_SERVER['HTTP_USER_AGENT'])) {
		header('Location: /ie6/ie6.html');
	}
	header('Content-Type: text/html; charset=utf-8', true);
	define('ROOT',$_SERVER['DOCUMENT_ROOT']);
	define('LIBS',ROOT.'/=libs/');
	error_reporting(E_ALL);

	require_once LIBS.'cm/utilites.php';

	require_once 'cm/Registry.php';
	require_once 'cm/Controller/Front.php';
	require_once 'cm/Controller/Router/XML.php';
	require_once 'cm/Controller/Router/XML/PageResolver/Acl.php';
	require_once 'mods/Auth/Acl.php';
	require_once 'mods/Auth/Acl/Provider/Simple.php';
        require_once 'Zend/Mail.php';
        require_once 'Zend/Mail/Transport/Sendmail.php';
        require_once 'Zend/Session.php';
	//require_once 'kupon/Helper.php';
	
	cm_Registry::getConfig()
		->set('debug', true)
                ->set('payment', array('pwd1'=>'ghbvregjy1','pwd2'=>'ghbvregjy2','logs'=>'_logs/'))
                ->set('log','_glogs/')
		->set('db', array(
			'driver' => 'Pdo_Mysql',
			'host' => '80.92.162.212',
			'dbname' => 'primkupon',
			'username' => 'primkupon',
			'password' => 'vtufcrblrb',
			'driver_options' => array(
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES cp1251'
			)
		));

//	cm_Registry::set('superaccess', array(
//		'login' => 'admin',
//		'password' => 'qopqop'
//	));

        $tr = new Zend_Mail_Transport_Sendmail('-fno-replay@primkupon.ru');
        Zend_Mail::setDefaultTransport($tr);

	$acl = new mods_Auth_Acl();
	$acl->setupRoles(new mods_Auth_Acl_Provider_Simple);

	$router = new cm_Controller_Router_XML('structure.xml');
	$router->setPageResolver(new cm_Controller_Router_XML_PageResolver_Acl($acl));
        //Zend_Session::regenerateId();
	$app = new cm_Controller_Front;
	$app->setRouter($router);
	$app->run();

} catch (Exception $e) {
	echo $e->getMessage();
}
