<?php
class ListController extends BaseController
{
	/**
	 * Метод, отображающий список автомобилей.
	 * 
	 * @return \Illuminate\View\View
	 */
	public function getIndex()
	{
		$filter = array();
		$carMark = intval(Input::get('car_mark'));
		$carModel = intval(Input::get('car_model'));
		$cityId = intval(Input::get('city_id'));
		$contentRaw = Input::get('content_raw');
		$yearStart = intval(Input::get('year_start'));
		$yearEnd = intval(Input::get('year_end'));
		$priceStart = intval(Input::get('price_start'));
		$priceEnd = intval(Input::get('price_end'));
		
		if(!empty($carMark))
		{
			$filter['car_mark'] = $carMark;
		}
		
		if(!empty($carModel))
		{
			$filter['car_model'] = $carModel;
		}
		
		if(!empty($cityId))
		{
			$filter['city_id'] = $cityId;
		}
		
		if(!empty($contentRaw))
		{
			$filter['content_raw'] = $contentRaw;
		}
		
		if(!empty($yearStart))
		{
			$filter['year_start'] = $yearStart;
		}
		
		if(!empty($yearEnd))
		{
			$filter['year_end'] = $yearEnd;
		}

		if(!empty($priceStart))
		{
			$filter['price_start'] = $priceStart;
		}

		if(!empty($priceEnd))
		{
			$filter['price_end'] = $priceEnd;
		}
		
		$carData = Cars::getList($filter);
		$cities = Cities::get();
		
		$listView = View::make('list');
		$listView->with('carData', $carData);
		$listView->with('cities', $cities);
		$listView->with('carMarks', CarMarks::get());
		$listView->with('averagePrices', Cars::getAveragePrices());
		$listView->with('filter', $filter);
		
		$this->IndexView->with('content', $listView);
		
		return $this->IndexView;
	}
}
