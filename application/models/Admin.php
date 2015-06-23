<?php
class Admin{
	const TABLE_NAME = 'yzb_admin';
	const COLUM_ADMIN = 'admin';
	const COLUM_PASSWORD = 'password';
	const COLUM_IS_ACTIVE = 'active';
	const COLUM_REG_TIME = 'reg_time';
	const IS_ACTIVE = true;
	const PASSWORD_SALT = 'ylmf_salt';
	const MSG_REGISTER_SUCC = '注册成功';
	const MSG_UPDATE_SUCC = '更新成功';
	const MSG_REGISTER_FAIL = '插入数据失败';
	const MSG_UPDATE_FAIL = '更新数据失败';
	const MSG_LOGIN_SUCC = '登陆成功';
	const MSG_LOGIN_FAIL = '用户名或密码错误';
	const MSG_LOGOUT_SUCC = '退出成功';
	const MSG_NEED_LOGIN = '请先登陆';
	const URL_REGISTER_SUCC = '/Index/index/AdminList/';
	const URL_UPDATE_SUCC = '/Index/index/AdminList/';
	const URL_LOGIN_SUCC = '/Index/index/index/';
	const URL_LOGIN_FAIL = '/Index/index/login/';
	const URL_LOGOUT_SUCC = '/Index/index/login/';
	const URL_LOGOUT_FAIL = '/Index/index/index/';
	const URL_NEED_LOGIN = '/Index/index/login/';

	public static function adminAdd( $username, $password ){
		$data = array( self::COLUM_ADMIN => $username, self::COLUM_PASSWORD => self::passwordHandle( $password ), self::COLUM_IS_ACTIVE => self::IS_ACTIVE, self::COLUM_REG_TIME => time() );
		if(!Mg::insert(self::TABLE_NAME, $data )){
			PublicFun::toErrorBack(self::MSG_REGISTER_FAIL);
		}
		PublicFun::toUrl( self::URL_REGISTER_SUCC, self::MSG_REGISTER_SUCC );
	}

	public static function adminList(){
		return Mg::find_all( self::TABLE_NAME, array( self::COLUM_REG_TIME => -1 ), '', array(), array(self::COLUM_ADMIN, self::COLUM_IS_ACTIVE, self::COLUM_REG_TIME ) );
	}

	private static function passwordHandle( $password ){
		return md5(self::PASSWORD_SALT . md5($password));
	}

	public static function adminUpdate( $username, $password, $active ){
		$data = array( self::COLUM_ADMIN => $username, self::COLUM_IS_ACTIVE => $active );
		if( isset($password) && !empty($password) ){ $data[self::COLUM_PASSWORD] = self::passwordHandle( $password ); }
		if(!Mg::update_one(self::TABLE_NAME, array(self::COLUM_ADMIN => $username), $data)){
			PublicFun::toErrorBack(self::MSG_UPDATE_FAIL);
		}
		PublicFun::toUrl( self::URL_UPDATE_SUCC, self::MSG_UPDATE_SUCC );
	}

	public static function adminLogin( $username, $password ){

		$data = array(self::COLUM_ADMIN => $username, self::COLUM_PASSWORD => self::passwordHandle($password), self::COLUM_IS_ACTIVE => self::IS_ACTIVE );

		if(Mg::find_count( self::TABLE_NAME, $data )){
			$cookie = Yaf_Registry::get("cookie");
			$cookie->set( YlmfCookie::AUTH_KEY_NAME, $username );
			PublicFun::toUrl( self::URL_LOGIN_SUCC, self::MSG_LOGIN_SUCC );
		}
		else{
			PublicFun::toErrorBack( self::MSG_LOGIN_FAIL );
		}
	}
	public static function adminLogout(){
		$cookie = Yaf_Registry::get("cookie");
		if($cookie->del( YlmfCookie::AUTH_KEY_NAME )){
			PublicFun::toUrl( self::URL_LOGOUT_SUCC, self::MSG_LOGOUT_SUCC );
		}
		else{
        	PublicFun::toErrorBack( self::URL_LOGOUT_FAIL );
    	}
	}
	public static function checkLogin(){

		$cookie = Yaf_Registry::get("cookie");

		if(!$cookie->get( YlmfCookie::AUTH_KEY_NAME )){
			PublicFun::toUrl( self::URL_NEED_LOGIN, self::MSG_NEED_LOGIN);
		}
	}
}