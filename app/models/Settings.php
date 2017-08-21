<?php
class Settings extends Eloquent
{
	protected $table = 'settings';

	/**
	 * Метод выполняет получение всех настроек.
	 * 
	 * @return array Ассоциативный массив настроек.
	 */
	public static function getAll()
	{
		$data = self::get();
		$retArr = [ ];
		
		foreach($data as $row)
		{
			$retArr[$row['title']] = $row['value'];
		}
		
		return $retArr;
	}
	
	/**
	 * Метод получает значение настройки по ее названию.
	 * 
	 * @param string $_title Название настройки, значение которой требуется получить.
	 * @return string|null Значение настройки.
	 */
	public static function getValue($_title)
	{
		$item = self::select()->where('title', '=', $_title)->get();
		
		if(!empty($item) && !empty($item[0]) && !empty($item[0]['value']))
		{
			return $item[0]['value'];
		}
		
		return null;
	}

	/**
	 * Метод устанавливает значение настройки.
	 * 
	 * @param string $_title Название настройки, для которой требуется установить значение.
	 * @param string $_value Значение настройки, которое требуется установить.
	 */
	public static function setValue($_title, $_value)
	{
		DB::update('
			UPDATE
				`settings`
			SET `value` = ?
			WHERE `title` = ?
		', [ $_value, $_title ]);
	}

	/**
	 * Метод устанавливает значения для массива настрок.
	 * 
	 * @param array $_values Ассоциативный массив названий и значений для настроек.
	 */
	public static function setValues($_values)
	{
		$insertStr = '';
		
		foreach($_values as $key => $val)
		{
			$insertStr .= '("' . $key . '", "' . $val . '"),';
		}
		
		$insertStr = trim($insertStr, ',');
		
		DB::insert('
			INSERT INTO `settings`
			VALUES
				' . $insertStr . '
			ON DUPLICATE KEY UPDATE
				`value` = VALUES(`value`)
		');
	}
}
