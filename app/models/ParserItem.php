<?php
abstract class ParserItem
{
	protected $ParserId = '';
	protected $MaxPagesDepth = 0;

	protected $CarMark;
	protected $CarModel;
	protected $Region;

	protected $SiteUrl = '';

	public function __construct()
	{

	}

	/**
	 * Метод устанавливает опции парсинга.
	 *
	 * @param array $_data Массив опций парсинга.
	 */
	public function setParseData($_data)
	{
		if(!empty($_data['region']))
		{
			$this->Region = $_data['region'];
		}

		if(!empty($_data['car_mark']))
		{
			$this->CarMark = CarMarks::getById(intval($_data['car_mark']));
		}

		if(!empty($_data['car_model']))
		{
			$this->CarModel = CarModels::getById(intval($_data['car_model']));
		}

		if(isset($_data['max_pages_depth']))
		{
			$this->MaxPagesDepth = intval($_data['max_pages_depth']);
		}
	}

	/**
	 * Метод возвращает регион для парсинга.
	 *
	 * @return string
	 */
	public function getRegion()
	{
		return $this->Region;
	}

	/**
	 * Метод возвращает марку автомобиля, который парсится.
	 *
	 * @return CarMarks
	 */
	public function getCarMark()
	{
		return $this->CarMark;
	}

	/**
	 * Метод возвращает модель автомобиля, который парсится.
	 *
	 * @return CarModels
	 */
	public function getCarModel()
	{
		return $this->CarModel;
	}

	/**
	 * Метод возвращает максимальное количество страниц для просмотра или false в случае, если глубина просмотра не ограничена.
	 *
	 * @return int
	 */
	public function getMaxPageDepth()
	{
		return $this->MaxPagesDepth;
	}

	/**
	 * Метод устанавливает максимальное количество страниц для просмотра.
	 *
	 * @param int|bool $_val Максимальное количество страниц для просмотра или false в случае, ограничивать глубину просмотра не требуется.
	 */
	public function setMaxPageDepth($_val)
	{
		$this->MaxPagesDepth = $_val;
	}

	/**
	 * Метод устанавливает идентификатор используемого парсера.
	 *
	 * @param $_parserName
	 */
	public function setParserId($_parserId)
	{
		$this->ParserId = $_parserId;
	}

	/**
	 * Метод возвращает идентификатор используемого парсера.
	 *
	 * @return string
	 */
	public function getParserId()
	{
		return $this->ParserId;
	}

	/**
	 * Метод выполняет запросы к заданной странице и получает содержимое этой страницы в виде массива.
	 *
	 * @return array
	 */
	public abstract function getCarsData();

	/**
	 * Метод выполняет парсинг списка моделей.
	 *
	 * @return array
	 */
	public abstract function getModelsData();
}
