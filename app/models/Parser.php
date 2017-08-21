<?php
class Parser extends Eloquent
{
	protected $table = 'parser';
	
	/**
	 * Метод возвращает экземпляр класса парсера с указанным индетификатором.
	 * @param string $_parserId Идентификатор парсера.
	 * @return ParserItem
	 */
	public static function factory($_parserId)
	{
		$className = 'Parser_' . $_parserId;
		
		$parserItem = new $className;
		$parserItem->setParserId($_parserId);
		
		return $parserItem;
	}

	/**
	 * Метод выполняет получение всех доступных парсеров.
	 *
	 * @return array Ассоциативный массив парсеров.
	 */
	public static function getAll()
	{
		$data = self::get();
		$retArr = [ ];

		foreach($data as $row)
		{
			$retArr[$row['id']] = $row;
		}

		return $retArr;
	}
}
