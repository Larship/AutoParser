<?php
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ParseJobs extends Command
{
	protected $name = 'parse-jobs';
	protected $description = 'Starts automobile sites parsing';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
//		$curFrequency = $this->argument("frequency");

		$scheduleCarSets = ScheduleCarSets::getActual();

		if(!empty($scheduleCarSets))
		{
			Log::info('Запуск парсинга...');

			$carsAll = [ ];

			foreach($scheduleCarSets as $carSet)
			{
				Log::info('Парсим ' . $carSet->parser_name . ': ' . $carSet->car_mark_title . ' ' . $carSet->car_model_title . ' (глубина = ' . $carSet->max_pages_depth . ')');

				$parser = Parser::factory($carSet->parser_id);
				$parser->setParseData([
					'region' => $carSet->region,
					'car_mark' => $carSet->car_mark,
					'car_model' => $carSet->car_model,
					'max_pages_depth' => $carSet->max_pages_depth,
				]);
				$carData = $parser->getCarsData();

				$carsAll = array_merge($carsAll, $carData);

				ScheduleCarSets::updateLastStart([
					'parser_id' => $carSet->parser_id,
					'frequency' => $carSet->frequency,
					'region' => $carSet->region,
					'car_mark' => $carSet->car_mark,
					'car_model' => $carSet->car_model,
				]);

				if(!empty($carSet->notifications))
				{
					Notifier::perform($carData);
				}
			}

			Cities::insertFromCars($carsAll);
			Cities::updateCarCities($carsAll);
			Cars::insertItems($carsAll);

			Log::info('Конец парсинга.');
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
//			 array('frequency', InputArgument::REQUIRED, 'Тип периодичности для команды'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			// array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}
}