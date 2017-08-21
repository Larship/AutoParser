<?php
class CarModels extends Eloquent
{
	protected $table = 'car_models';

	/**
	 * Метод получает модель автомобиля с указанным идентификатором.
	 * 
	 * @param int $_id Идентификатор модели, которую требуется получить.
	 * @return CarModels|null
	 */
	public static function getById($_id)
	{
		$item = self::select()->where('id', '=', $_id)->get();

		if(!empty($item) && !empty($item[0]))
		{
			return $item[0];
		}

		return null;
	}

	/**
	 * Метод производит вставку моделей автомобилей.
	 * 
	 * @param array $_dataArr Массив моделей автомобилей, который требуется вставить.
	 */
	public static function insertItems($_dataArr)
	{
		$_dataArr[] = null;
		$insertStr = "";
		
		$i = 0;
		
		foreach($_dataArr as $itemCol)
		{
			if(isset($itemCol))
			{
				$insertStr .= '(NULL, ' . $itemCol['mark_id'] . ', "' . $itemCol['title'] . '", "' . $itemCol['url'] . '"),';
			}

			if(($i >= 50 || !isset($itemCol)) && $i != 0)
			{
				DB::insert('
					INSERT IGNORE INTO `car_models` (`id`, `mark_id`, `title`, `url`)
					VALUES ' . trim($insertStr, ', ')
				);

				$insertStr = '';
				$i = 0;
			}

			$i++;
		}
	}
}
