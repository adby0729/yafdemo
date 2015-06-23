<?php
/**
* 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
* 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
* 调用的次序, 和申明的次序相同
*/
class Bootstrap extends Yaf_Bootstrap_Abstract{

    public function _initConfig( Yaf_Dispatcher $dispatcher ) {

        $config = Yaf_Application::app()->getConfig();

        Yaf_Registry::set("config", $config);

		define("UPLOAD_DIR",  $config['application']['upload']['dir']);

		define("VIEWS_PATH", $config['application']['view']['dir']);

		define("PUBLIC_LIB_PATH", $config['application']['public_library']['dir']);

		define("URL", $config['application']['url']);

		define("STATIC_URL", $config['application']['static']['dir']);

		ini_set('error_reporting', $config['application']['config']['error_reporting']);

		ini_set('display_errors', $config['application']['display_errors']);

		ini_set('date.timezone', $config['application']['config']['timezone']);

		define('DOWN_URL', $config['application']['config']['download_url']);

		define('SITE_NAME', $config['application']['config']['system_name']);

		$dispatcher->setDefaultModule($config['application']['dispatcher']['defaultModule'])->setDefaultController($config['application']['dispatcher']['defaultController'])->setDefaultAction($config['application']['dispatcher']['defaultAction']);
    }


	/**
    * 注册一个插件
    * 插件的目录是在application_directory/plugins
    */
    public function _initPlugin( Yaf_Dispatcher $dispatcher ) {

        $user = new UserPlugin();
        $dispatcher->registerPlugin($user);

		include PUBLIC_LIB_PATH . 'YlmfCookie.php';

		include PUBLIC_LIB_PATH . 'YlmfMcrypt.php';

		$cookie = new YlmfCookie( $config['application']['config']['cookie']['path'], $config['application']['config']['cookie']['domain'], $config['application']['config']['cookie']['expire']);

		Yaf_Registry::set("cookie", $cookie);

    }

    public function _initGlobalVaribals(){

		session_start();

		$cookie = Yaf_Registry::get("cookie");

        $userinfo = $cookie->get( YlmfCookie::AUTH_KEY_NAME );

        if($userinfo) Yaf_Registry::set('userinfo', array('username' => $userinfo) );

	}
}