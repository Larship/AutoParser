<?php
class Dates
{
	private static $monthNamesGen = [
		'января', 'февраля', 'марта', 'апреля',
		'мая', 'июня', 'июля', 'августа',
		'сентября', 'октября', 'ноября', 'декабря'
	];

	/**
	 * Метод возвращает порядковый номер месяца по его названию в родительном падеже.
	 *
	 * @param	$name		Название месяца в родительном падеже.
	 *
	 * @return	bool|mixed	Вернёт порядковый номер месяца, если его удалось получить по родительному падежу,
	 *                    	или false в ином случае.
	 */
	public static function monthNameGenIndex($name)
	{
		$index = array_search($name, Dates::$monthNamesGen);

		if ($index !== false) {
			return $index + 1;
		}

		return false;
	}
}
