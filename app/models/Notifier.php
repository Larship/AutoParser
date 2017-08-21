<?php
class Notifier
{
	/**
	 * Метод выполняет рассылку уведомлений о новых автомобилях.
	 * 
	 * @param array $_carsData Массив автомобилей, из которых требуется выбрать автомобили для уведомления.
	 */
	public static function perform($_carsData)
	{
		$settings = Settings::getAll();
		$averagePrices = Cars::getAveragePrices();
		$notifyCars = [ ];
		
		if(empty($settings['notification-email'])) return;
		
		foreach($_carsData as $carItem)
		{
			$averagePrice = 0;
			
			if(isset($averagePrices[$carItem['car_mark']->id][$carItem['car_model']->id][$carItem['car_year']]))
			{
				$averagePrice = $averagePrices[$carItem['car_mark']->id][$carItem['car_model']->id][$carItem['car_year']];
			}
			
			if(empty($settings['notification-percent']) ||
				($averagePrice != 0 && $carItem['price'] <= $averagePrice * ((100 - intval($settings['notification-percent'])) / 100)))
			{
				$notifyCars[] = $carItem;
			}
		}
		
		Mail::send('emails.car_notify', [ 'notifyCars' => $notifyCars, 'averagePrices' => $averagePrices ], function($_message) use ($settings) {
			$_message->subject('Уведомления об авто');
			$_message->to($settings['notification-email']);
			$_message->from('no-reply@drom-parser.ru');
		});
	}
}
