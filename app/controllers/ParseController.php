<?php
class ParseController extends BaseController
{
	/**
	 * Метод, отображающий страницу запуска парсинга автомобилей.
	 *
	 * @return \Illuminate\View\View
	 */
	public function getIndex()
	{
		$parseView = View::make('parse');
		$carsParseStatus = Session::get('cars_parse_status');
		$modelsParseStatus = Session::get('models_parse_status');

		$parseView->with('parserList', Parser::getAll());
		$parseView->with('carsParseStatus', !empty($carsParseStatus) ? $carsParseStatus : '');
		$parseView->with('modelsParseStatus', !empty($modelsParseStatus) ? $modelsParseStatus : '');
		$parseView->with('carMarks', CarMarks::get());
		$parseView->with('filter', Input::all());

		$this->IndexView->with('content', $parseView);

		return $this->IndexView;
	}

	/**
	 * Метод, запускающий парсинг автомобилей.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function getStart()
	{
		$carModel = Input::get('car_model');

		if(empty($carModel))
		{
			return Redirect::to('/parse/?' . Input::getQueryString())->with('status', 'error');
		}

		$parserName = Input::get('parser_name');

		$parser = Parser::factory($parserName);
		$parser->setParseData([
			'region' => Input::get('region'),
			'car_mark' => Input::get('car_mark'),
			'car_model' => Input::get('car_model'),
			'max_pages_depth' => intval(Input::get('max-pages-depth')),
		]);
		$carData = $parser->getCarsData();

		Cities::insertFromCars($carData);
		Cities::updateCarCities($carData);

		Cars::insertItems($carData);

		return Redirect::to('/parse/?' . Input::getQueryString())->with('cars_parse_status', 'success');
	}

	/**
	 * Метод, запускающий парсинг моделей автомобилей.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function getModels()
	{
		$carMark = intval(Input::get('car_mark'));

		if(!empty($carMark))
		{
			$parserName = Input::get('parser_name');

			$parser = Parser::factory($parserName);
			$parser->setParseData([
				'car_mark' => $carMark,
				'parse_models' => true,
			]);
			$carModels = $parser->getModelsData();

			CarModels::insertItems($carModels);

			return Redirect::to('/parse/?' . Input::getQueryString())->with('models_parse_status', 'success');
		}

		return Redirect::to('/parse/?' . Input::getQueryString())->with('models_parse_status', 'error');
	}

	/**
	 * Метод-заглушка для выполнения КРОН-задач через веб-интерфейс.
	 */
	public function getCron()
	{
		Artisan::add(new ParseJobs());
//		Artisan::call('parse-jobs', array('frequency' => $type));
		Artisan::call('parse-jobs');
	}
}
