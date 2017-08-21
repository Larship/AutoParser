<?php
class Cars extends Eloquent
{
	protected $table = 'cars';

	/**
	 * Метод возвращает путь к основному изображению автомобиля.
	 *
	 * @return string
	 */
	public function getImageURL()
	{
		return '/public/uploads/' . mb_convert_case($this->car_mark_title, MB_CASE_LOWER) . '/' . mb_convert_case($this->car_model_title, MB_CASE_LOWER) . '/' . $this->internal_id . '.jpg';
	}

	/**
	 * Метод получает список автомоблей, удовлетворяющих указанным критериям.
	 *
	 * @param array $_search Фильтр, на основе которого требуется сделать выборку.
	 * @return \Illuminate\Pagination\Paginator
	 */
	public static function getList($_search = array())
	{
		$bindings = array();

		if(empty($_search['car_mark']))
		{
			$_search['car_mark'] = 0;
		}

		if(empty($_search['car_model']))
		{
			$_search['car_model'] = 0;
		}

		if(empty($_search['city_id']))
		{
			$_search['city_id'] = 0;
		}

		if(empty($_search['content_raw']))
		{
			$_search['content_raw'] = '';
		}

		if(empty($_search['year_start']))
		{
			$_search['year_start'] = 0;
		}

		if(empty($_search['year_end']))
		{
			$_search['year_end'] = 0;
		}

		if(empty($_search['price_start']))
		{
			$_search['price_start'] = 0;
		}

		if(empty($_search['price_end']))
		{
			$_search['price_end'] = 0;
		}

		$bindings[] = $_search['car_mark'];
		$bindings[] = $_search['car_mark'];
		$bindings[] = $_search['car_model'];
		$bindings[] = $_search['car_model'];
		$bindings[] = $_search['city_id'];
		$bindings[] = $_search['city_id'];
		$bindings[] = $_search['content_raw'];
		$bindings[] = $_search['content_raw'];
		$bindings[] = $_search['year_start'];
		$bindings[] = $_search['year_start'];
		$bindings[] = $_search['year_end'];
		$bindings[] = $_search['year_end'];
		$bindings[] = $_search['price_start'];
		$bindings[] = $_search['price_start'];
		$bindings[] = $_search['price_end'];
		$bindings[] = $_search['price_end'];

		return self::
			selectRaw('
				`cars`.*,
				`car_marks`.`title` AS `car_mark_title`,
				`car_models`.`title` AS `car_model_title`,
				`cities`.`title` AS `city_title`,
				`parser`.`name` AS `parser_name`
			')->
			leftJoin('car_marks', 'car_marks.id', '=', 'cars.car_mark')->
			leftJoin('car_models', 'car_models.id', '=', 'cars.car_model')->
			leftJoin('cities', 'cities.id', '=', 'cars.city_id')->
			leftJoin('parser', 'parser.id', '=', 'cars.parser_id')->
			whereRaw('
				(? = 0 OR `cars`.`car_mark` = ?) AND
				(? = 0 OR `cars`.`car_model` = ?) AND
				(? = 0 OR `cars`.`city_id` = ?) AND
				(? = "" OR `cars`.`content_raw` LIKE CONCAT("%", ?, "%")) AND
				(? = 0 OR `cars`.`car_year` >= ?) AND
				(? = 0 OR `cars`.`car_year` <= ?) AND
				(? = 0 OR `cars`.`price` >= ?) AND
				(? = 0 OR `cars`.`price` <= ?)
			', $bindings)->
			orderByRaw('`cars`.`date` DESC, `cars`.`page_ord` ASC')->
			paginate(20);
	}

	/**
	 * Метод получает средние цены автомобилей и возвращает их в виде массива.
	 */
	public static function getAveragePrices()
	{
		$data = DB::select('
			SELECT
				`i`.*,
				COUNT(`i`.`internal_id`) AS `count`,
				CEIL(SUM(`i`.`price`) / COUNT(`i`.`internal_id`)) AS `average_price`
			FROM `cars` AS `i`
			WHERE `nodocs` = 0
			GROUP BY
				`i`.`car_mark`,
				`i`.`car_model`,
				`i`.`car_year`
		');

		$retPrices = [ ];
		foreach($data as $dataRow)
		{
			if(!isset($retPrices[$dataRow->car_mark]))
			{
				$retPrices[$dataRow->car_mark] = [ ];
			}

			if(!isset($retPrices[$dataRow->car_mark][$dataRow->car_model]))
			{
				$retPrices[$dataRow->car_mark][$dataRow->car_model] = [ ];
			}

			$retPrices[$dataRow->car_mark][$dataRow->car_model][$dataRow->car_year] = ceil($dataRow->average_price);
		}

		return $retPrices;
	}

	/**
	 * Метод производит вставку массива автомобилей в базу данных.
	 *
	 * @param array $_dataArr Массив автомобилей для вставки.
	 */
	public static function insertItems($_dataArr)
	{
		$_dataArr[] = null;
		$insertStr = '';

		$i = 0;

		foreach($_dataArr as $itemCol)
		{
			if(!empty($itemCol) && !is_dir(getcwd() . '/public/uploads/' . mb_convert_case($itemCol['car_mark']->title, MB_CASE_LOWER) . '/' . mb_convert_case($itemCol['car_model']->title, MB_CASE_LOWER) . '/'))
			{
				mkdir(getcwd() . '/public/uploads/' . mb_convert_case($itemCol['car_mark']->title, MB_CASE_LOWER) . '/' . mb_convert_case($itemCol['car_model']->title, MB_CASE_LOWER) . '/', 0700, true);
			}

			if(!empty($itemCol['image_url']))
			{
				$filedata = file_get_contents($itemCol['image_url']);
				$newFilename = $itemCol['internal_id'] . '.jpg';
				file_put_contents(getcwd() . '/public/uploads/' . mb_convert_case($itemCol['car_mark']->title, MB_CASE_LOWER) . '/' . mb_convert_case($itemCol['car_model']->title, MB_CASE_LOWER) . '/' . $newFilename, $filedata);
			}
			else
			{
				$newFilename = '';
			}

			if(isset($itemCol))
			{
				$insertStr .= '("' . $itemCol['parser_id'] . '", ' . $itemCol['internal_id'] . ',
					' . $itemCol['car_mark']->id . ', ' . $itemCol['car_model']->id . ', ' . $itemCol['car_year'] . ',
					"' . $itemCol['car_features'] . '", ' . $itemCol['car_mileage'] . ',
					' . $itemCol['price'] . ', ' . $itemCol['city_id'] . ', ' . $itemCol['is_sold'] . ',
					' . $itemCol['nodocs'] . ', ' . $itemCol['damaged'] . ',
					' . $itemCol['page_ord'] . ', ' . DB::connection()->getPdo()->quote($itemCol['content_raw']) . ', "' . $itemCol['date'] . '", NOW()),';
			}

			if(($i >= 50 || !isset($itemCol)) && $i != 0)
			{
				DB::insert('
					INSERT INTO `cars` (`parser_id`, `internal_id`, `car_mark`, `car_model`,
						`car_year`, `car_features`, `car_mileage`,
						`price`, `city_id`, `is_sold`, `nodocs`,
						`damaged`, `page_ord`, `content_raw`, `date`, `dti`)
					VALUES ' . trim($insertStr, ', ') . '
					ON DUPLICATE KEY UPDATE
						`price`=VALUES(`price`),
						`city_id`=VALUES(`city_id`),
						`is_sold`=VALUES(`is_sold`),
						`nodocs`=VALUES(`nodocs`),
						`damaged`=VALUES(`damaged`),
						`page_ord`=VALUES(`page_ord`),
						`content_raw`=VALUES(`content_raw`),
						`date`=VALUES(`date`),
						`dti`=NOW()
					'
				);

				$insertStr = '';
				$i = 0;
			}

			$i++;
		}
	}
}
