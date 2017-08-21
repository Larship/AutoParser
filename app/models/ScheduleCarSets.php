<?php
class ScheduleCarSets extends Eloquent
{
	protected $table = "schedule_car_sets";

	/**
	 * Метод возвращает список заданий в расписании для указанной периодичности.
	 * 
	 * @param string $_frequency Периодичность, для которой требуется получить список заданий.
	 * @return array
	 */
	public static function getForFrequency($_frequency)
	{
		return DB::select('
			SELECT
				`schedule_car_sets`.*,
				`car_marks`.`title` AS `car_mark_title`,
				`car_models`.`title` AS `car_model_title`
			FROM
				`schedule_car_sets`
			LEFT JOIN
				`car_marks` ON `car_marks`.`id` = `schedule_car_sets`.`car_mark`
			LEFT JOIN
				`car_models` ON `car_models`.`id` = `schedule_car_sets`.`car_model`
			WHERE
				`schedule_car_sets`.`frequency` = ?
		', [ $_frequency ]);
	}

	/**
	 * Метод возвращает список всех заданий в расписании.
	 *
	 * @return array
	 */
	public static function getActual()
	{
		return DB::select('
			SELECT
				`schedule_car_sets`.*,
				`car_marks`.`title` AS `car_mark_title`,
				`car_models`.`title` AS `car_model_title`,
				`parser`.`name` AS `parser_name`
			FROM
				`schedule_car_sets`
			LEFT JOIN
				`car_marks` ON `car_marks`.`id` = `schedule_car_sets`.`car_mark`
			LEFT JOIN
				`car_models` ON `car_models`.`id` = `schedule_car_sets`.`car_model`
			LEFT JOIN
				`parser` ON `parser`.`id` = `schedule_car_sets`.`parser_id`
			WHERE DATE_ADD(`last_start`, INTERVAL `frequency` MINUTE) <= NOW()
		');
	}

	/**
	 * Метод выполняет обновление времени последнего запуска у задания.
	 * 
	 * @param int $_carSetParams Массив, содержащий набор уникальных ключей, по которому можно идентифицировать задание.
	 */
	public static function updateLastStart($_carSetParams)
	{
		DB::update('
			UPDATE `schedule_car_sets`
			SET `last_start`=NOW()
			WHERE `parser_id`=? AND `frequency`=? AND `region`=? AND `car_mark`=? AND `car_model`=?', [
				$_carSetParams['parser_id'],
				$_carSetParams['frequency'],
				$_carSetParams['region'],
				$_carSetParams['car_mark'],
				$_carSetParams['car_model'],
			]
		);
	}

	/**
	 * Метод производит вставку заданий на основе JSON-строки.
	 * 
	 * @param string $_jsonStr JSON-строка с заданиями для расписания.
	 */
	public static function insertByJSON($_jsonStr)
	{
		$valuesArr = json_decode($_jsonStr);

		DB::statement('TRUNCATE TABLE `schedule_car_sets`');
		
		if(!empty($valuesArr))
		{
			$insertStr = '';
			
			foreach($valuesArr as $val)
			{
				$insertStr .= '("' . $val->parser_id . '", "' . $val->frequency . '", "' . $val->region . '", "' . $val->car_mark . '",' .
					'"' . $val->car_model . '", "' . $val->max_pages_depth . '", "' . $val->notifications . '", NOW()),';
			}
	
			$insertStr = trim($insertStr, ',');
				
			DB::insert('
				INSERT IGNORE INTO `schedule_car_sets`
				VALUES ' . $insertStr);
		}
	}
}
