<?php
class CarMarks extends Eloquent
{
	protected $table = 'car_marks';

	/**
	 * Метод возвращает марку автомобиля по ее идентификатору.
	 * 
	 * @param int $_id Идентификатор марки автомобиля. 
	 * @return CarMarks|null
	 */
	public static function getById($_id)
	{
		$item = self::select()->where("id", "=", $_id)->get();
		
		if(!empty($item) && !empty($item[0]))
		{
			return $item[0];
		}
		
		return null;
	}
}
