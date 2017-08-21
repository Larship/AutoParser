<?php
class Cities extends Eloquent
{
	protected $table = 'cities';
	
	/**
	 * Метод производит вставку городов, полученных из спарсенных автомобилей.
	 * 
	 * @param array $_cars Массив Cars, из которого требуется вставить города.
	 */
	public static function insertFromCars($_cars)
	{
		$cities = [ ];
		
		foreach($_cars as $car)
		{
			if(!in_array($car['city'], $cities))
			{
				$cities[] = $car['city'];
			}
		}
		
		$i = 0;
		$cities[] = null;
		$insertStr = '';
		
		foreach($cities as $city)
		{
			if(isset($city))
			{
				$insertStr .= '(NULL, "' . $city . '"),';
			}
			
			if(($i >= 50 || !isset($city)) && $i != 0)
			{
				DB::insert('
					INSERT IGNORE INTO `cities` (`id`, `title`)
					VALUES ' . trim($insertStr, ', ')
				);
				
				$insertStr = '';
				$i = 0;
			}
			
			$i++;
		}
	}

	/**
	 * Метод, выполняющий обновление массива автомобилей и установку соответствия строковым значениям городов соответствующих идентификаторов.
	 * 
	 * @param array $_cars Массив Cars, который требуется обновить.
	 * @return mixed
	 */
	public static function updateCarCities(&$_cars)
	{
		$citiesData = DB::select('
			SELECT
				`cities`.*
			FROM `cities`
		');
		
		foreach($_cars as &$car)
		{
			$curCity = Cities::getCityByTitle($citiesData, $car['city']);
			
			if(isset($curCity))
			{
				$car['city_id'] = $curCity->id;
			}
			else
			{
				$car['city_id'] = NULL;
			}
		}
		
		unset($car);
		
		return $_cars;
	}
	
	/**
	 * Метод возвращает город по его названию.
	 * 
	 * @param array $_haystack Массив городов, в котором требуется выполнить поиск.
	 * @param string $_title Название города, который требуется найти.
	 * @return mixed
	 */
	private static function getCityByTitle($_haystack, $_title)
	{
		$_title = preg_replace('#ё#i', 'е', $_title);
		
		foreach($_haystack as $city)
		{
			$cityTitle = preg_replace('#ё#i', 'е', $city->title);
			
			if($cityTitle == $_title)
			{
				return $city;
			}
		}
		
		return null;
	}
}
