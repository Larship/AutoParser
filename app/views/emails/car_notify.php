<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		Список полученных автомобилей:<br/><br/>
		<? foreach($notifyCars as $carItem): ?>
			Авто <a href="<?=htmlentities($carItem["internal_id"])?>.drom.ru"><?=htmlentities($carItem["internal_id"])?></a>:<br/>
			Название: <?=htmlentities($carItem["car_mark"]->title)?> <?=htmlentities($carItem["car_model"]->title)?><br/>
			Год: <?=intval($carItem["car_year"])?><br/>
			Цена: <?=intval($carItem["price"])?> р., средняя: <?=intval($averagePrices[$carItem["car_mark"]->id][$carItem["car_model"]->id][$carItem["car_year"]])?> р.<br/>
			<br/>===<br/><br/>
		<? endforeach; ?>
		С уважением, парсер Drom.ru
	</body>
</html>