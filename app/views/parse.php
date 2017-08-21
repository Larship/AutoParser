<div class="page-parse">
	<div>
		<legend>Режим парсинга</legend>
	</div>
	<div class="parse-filter">
		<form action="/parse/start/" method="GET" class="form-horizontal">
			<div class="form-group">
				<label class="col-sm-3 col-md-2 control-label">Сайт для парсинга:</label>
				<div class="col-sm-5">
					<select class="form-control" name="parser_name">
						<? foreach($parserList as $parser): ?>
							<option value="<?=$parser['id']?>"<?=(!empty($filter['parser_name']) && $filter['parser_name'] == $parser['id'] ? ' selected="selected"': '')?>><?=$parser['name']?></option>
						<? endforeach; ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 col-md-2 control-label">Регион:</label>
				<div class="col-sm-5">
					<select class="form-control" name="region">
						<option value="region25"<?=(!empty($filter['region']) && $filter['region'] == 'region25' ? ' selected="selected"': '')?>>Приморский край</option>
						<option value="vladivostok"<?=(!empty($filter['region']) && $filter['region'] == 'vladivostok' ? ' selected="selected"': '')?>>Владивосток</option>
						<option value="nakhodka"<?=(!empty($filter['region']) && $filter['region'] == 'nakhodka' ? ' selected="selected"': '')?>>Находка</option>
						<option value="ussuriisk"<?=(!empty($filter['region']) && $filter['region'] == 'ussuriisk' ? ' selected="selected"': '')?>>Уссурийск</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 col-md-2 control-label">Марка авто:</label>
				<div class="col-sm-5">
					<select class="car-mark form-control" name="car_mark">
						<? foreach($carMarks as $mark): ?>
							<option value="<?=$mark->id?>"<?=(!empty($filter['car_mark']) && $filter['car_mark'] == $mark->id ? ' selected="selected"': '')?>><?=$mark->title?></option>
						<? endforeach; ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 col-md-2 control-label">Модель авто:</label>
				<div class="col-sm-5">
					<select class="car-model form-control" name="car_model"></select>
				</div>
				<div class="col-sm-4 col-md-3 col-lg-2">
					<button type="button" class="parse-get-models-btn btn btn-default btn-block">Получить модели</button>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 col-md-2 control-label">Глубина просмотра:</label>
				<div class="col-sm-5">
					<input class="form-control" value="<?=(isset($filter["max-pages-depth"]) ? intval($filter["max-pages-depth"]) : 1)?>" name="max-pages-depth"/>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-5 col-sm-offset-3 col-md-offset-2">
					<button class="parse-btn btn btn-danger">Выполнить</button>
				</div>
			</div>
			<? if(!empty($carsParseStatus) || !empty($modelsParseStatus)): ?>
				<div class="form-group">
					<div class="col-sm-5 col-sm-offset-3 col-md-offset-2">
						<? if($carsParseStatus == "success"): ?>
							<div class="alert alert-success">
								Парсинг автомобилей успешно произведен!
							</div>
						<? elseif($modelsParseStatus == "success"): ?>
							<div class="alert alert-success">
								Парсинг моделей успешно произведен!
							</div>
						<? elseif($carsParseStatus == "error" || $modelsParseStatus == "error"): ?>
							<div class="alert alert-danger">
								При попытке запуска парсинга произошла неизвестная ошибка!
							</div>
						<? endif; ?>
					</div>
				</div>
			<? endif; ?>
			<div class="cur-car-model" data-id="<?=(!empty($filter["car_model"]) ? $filter["car_model"] : 0)?>"></div>
		</form>
	</div>
	<? if(!empty($carData)): ?>
		<div class="items-list">
			<div class="items-list-title">Выгруженные автомобили</div>
			<div class="items-list-body">
				<table class="table table-hover">
					<tr>
						<th>Код</th>
						<th>Дата</th>
						<th></th>
						<th>Авто</th>
						<th>Год выпуска</th>
						<th>Характеристики</th>
						<th>Пробег, тыс. км.</th>
						<th></th>
					</tr>
					<? foreach($carData as $carItem): ?>
						<tr>
							<td class="items-list-table-cell">
								<a href="http://<?=$carItem->internal_id?>.drom.ru" target="_blank">
									<?=$carItem->internal_id?>
								</a>
							</td>
							<td class="items-list-table-cell">
								<?=$carItem->date?>
							</td>
							<td class="items-list-table-cell">
								<img src="<?=$carItem->image_url?>" width="140"/>
							</td>
							<td class="items-list-table-cell">
								<?=$carItem->car_mark?> <?=$carItem->car_model?>
							</td>
							<td class="items-list-table-cell">
								<?=$carItem->car_year?>
							</td>
							<td class="items-list-table-cell">
								<?=str_replace(", ", "<br/>", $carItem->car_features)?>
							</td>
							<td class="items-list-table-cell">
								<?=(!empty($carItem->car_mileage) ? $carItem->car_mileage : "")?>
							</td>
							<td class="items-list-table-cell">
								<?=$carItem->price?><br/>
								<?=$carItem->city?>
							</td>
						</tr>
					<? endforeach; ?>
				</table>
			</div>
		</div>
	<? endif; ?>
</div>