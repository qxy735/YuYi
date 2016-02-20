<?php 
 
 class Session
 {
 	/**
	 * 设置 Session
	 */
 	public static function set($key, $value = null, $remember = false, $expire = 3600)
	{
		$_SESSION[$key] = $value;
		
		if($remember){
			$expire += time();

			setcookie($key, $value, $expire, '/');
		}
	}
	
	/**
	 * 获取 Session
	 */
	public static function get($key = null)
	{
		if(is_null($key)){
			return null;
		}
		
		if(isset($_SESSION[$key])){
			return $_SESSION[$key];
		}
		
		if(isset($_COOKIE[$key])){
			return $_COOKIE[$key];
		}
		
		return null;
	}
	
	/**
	 * 检测 Session
	 */
	public static function has($key)
	{
		if(isset($_SESSION[$key]) || isset($_COOKIE[$key])){
			return true;
		}
		
		return false;
	}
	
	/**
	 * 清除 Session
	 */
	public static function clear()
	{
		session_destroy();
		
		session_unset();
		
		setcookie(session_name(), '', time() - 1, '/');
		setcookie('islogin', '', time() - 1, '/');
		setcookie('username', '', time() - 1, '/');
		setcookie('_sign', '', time() - 1, '/');
		setcookie('avatar', '', time() - 1, '/');
		setcookie('uid', 0, time() - 1, '/');
		setcookie('lastoperate', 0, time() - 1, '/');
		
		unset($_SESSION);
	}
 }