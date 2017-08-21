<?php
class HTTP
{
	/**
	 * Метод выполняет загрузку указанный страницы.
	 * 
	 * @param string $_url URL страницы, которую требуется загрузить.
	 * @param string $_ref URL источника запроса.
	 * @param string $_cookieStr Cookie, которые требуется установить для запроса.
	 * 
	 * @return boolean|string Содержимое страницы либо false в случае ошибки при выполнении запроса.
	 */
	public static function curlGET($_url, $_ref = "", $_cookieStr = "")
	{
		$agentBrowser = array('Firefox', 'Safari', 'Opera', 'Flock', 'Internet Explorer',
			'Ephifany', 'AOL Explorer', 'Seamonkey', 'Konqueror', 'GoogleBot');
		$agentOS = array('Windows 2000', 'Windows NT', 'Windows XP', 'Windows Vista', 'Windows 7',
			'Redhat Linux', 'Ubuntu', 'Fedora', 'FreeBSD', 'OpenBSD', 'OS 10.5');
		$userAgent = $agentBrowser[rand(0, 9)] . '/' . rand(1, 8) . '.' . rand(0, 9) . ' (' . $agentOS[rand(0, 10)] . ' ' . rand(1, 7) . '.' . rand(0, 9) . '; en-US;)';
		
		$settings = Settings::getAll();
		
		$curlObj = curl_init();
		curl_setopt($curlObj, CURLOPT_URL, $_url);
		curl_setopt($curlObj, CURLOPT_REFERER, $_ref);
		curl_setopt($curlObj, CURLOPT_HEADER, false);
		curl_setopt($curlObj, CURLOPT_HTTPGET, true);
		curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curlObj, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curlObj, CURLOPT_MAXREDIRS, 3);
		curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlObj, CURLOPT_TIMEOUT, 30);
		curl_setopt($curlObj, CURLOPT_USERAGENT, $userAgent);
		
		if(!empty($settings["proxy-url"]))
		{
			curl_setopt($curlObj, CURLOPT_PROXY, $settings["proxy-url"]);

			if(!empty($settings["proxy-login"]) && !empty($settings["proxy-password"]))
			{
				curl_setopt($curlObj, CURLOPT_PROXYUSERPWD, $settings["proxy-login"] . ":" . $settings["proxy-password"]);
			}
		}
		
		curl_setopt($curlObj, CURLOPT_COOKIE, $_cookieStr); // Формат "cookie1=1;cookie2=2"
		
		$page = curl_exec($curlObj);
		$err = curl_error($curlObj);
		curl_close($curlObj);

		if(strlen($err) > 0)
		{
			return false;
		}
		
		return $page;
	}
}
