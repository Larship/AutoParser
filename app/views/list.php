<div class="page-list">
	<div>
		<legend>Просмотр автомобилей</legend>
	</div>
	<div class="list-filter">
		<form action="/list/" method="GET" class="form-horizontal">
			<div class="form-group">
				<label class="col-sm-3 col-md-2 control-label">Марка авто:</label>
				<div class="col-sm-5">
					<select class="car-mark form-control" name="car_mark">
						<option value="0">Все</option>
						<? foreach($carMarks as $mark): ?>
							<option value="<?=$mark->id?>"<?=(!empty($filter["car_mark"]) && $filter["car_mark"] == $mark->id ? " selected='selected'" : "")?>><?=$mark->title?></option>
						<? endforeach; ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 col-md-2 control-label">Модель авто:</label>
				<div class="col-sm-5">
					<select class="car-model form-control" name="car_model">
						<option value="0">Все</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 col-md-2 control-label">Город:</label>
				<div class="col-sm-5">
					<select class="form-control" name="city_id">
						<option value="0">Все</option>
						<? foreach($cities as $city): ?>
							<option value="<?=$city->id?>"<?=(!empty($filter["city_id"]) && $filter["city_id"] == $city->id ? " selected='selected'" : "")?>><?=$city->title?></option>
						<? endforeach; ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 col-md-2 control-label">Описание:</label>
				<div class="col-sm-5">
					<input name="content_raw" class="form-control" value="<?=(!empty($filter["content_raw"]) ? $filter["content_raw"] : "")?>"/>
				</div>
			</div>
			<?
				$yearCur = intval(date("Y"));
				$yearMin = $yearCur - 100;
				$yearStart = (!empty($filter["year_start"]) ? $filter["year_start"] : $yearMin);
				$yearEnd = (!empty($filter["year_end"]) ? $filter["year_end"] : $yearCur);
			?>
			<div class="form-group">
				<label class="col-sm-3 col-md-2 control-label">Год выпуска:</label>
				<div class="col-sm-2">
					<select class="form-control" name="year_start">
						<? for($i = $yearMin; $i <= $yearCur; $i++): ?>
							<option value="<?=$i?>"<?=($i == $yearStart ? " selected='selected'" : "")?>><?=$i?></option>
						<? endfor; ?>
					</select>
				</div>
				<label class="text-center col-sm-1 control-label">-</label>
				<div class="col-sm-2">
					<select class="form-control" name="year_end">
						<? for($i = $yearMin; $i <= $yearCur; $i++): ?>
							<option value="<?=$i?>"<?=($i == $yearEnd ? " selected='selected'" : "")?>><?=$i?></option>
						<? endfor; ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 col-md-2 control-label">Цена:</label>
				<div class="col-sm-2">
					<input name="price_start" class="form-control" value="<?=(!empty($filter["price_start"]) ? $filter["price_start"] : "")?>"/>
				</div>
				<label class="text-center col-sm-1 control-label">-</label>
				<div class="col-sm-2">
					<input name="price_end" class="form-control" value="<?=(!empty($filter["price_end"]) ? $filter["price_end"] : "")?>"/>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-5 col-sm-offset-3 col-md-offset-2">
					<button class="filter-btn btn btn-default">Фильтровать</button>
				</div>
			</div>
			<div class="cur-car-model" data-id="<?=(!empty($filter["car_model"]) ? $filter["car_model"] : 0)?>"></div>
		</form>
	</div>
	<? if($carData->count() > 0): ?>
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
						<th></th>
						<th></th>
					</tr>
					<? foreach($carData as $carItem): ?>
						<?
							$url = '';

							switch($carItem->parser_id)
							{
								case 'Drom':
									$url = 'http://' . $carItem->internal_id . '.drom.ru';
									break;

								case 'Farpost':
									$url = 'http://farpost.ru/' . $carItem->internal_id;
									break;
							}
						?>
						<tr class="items-list-row clickable <?=lcfirst($carItem->parser_id)?>"
							data-url="<?=$url?>"
							data-target="_blank">
							<td class="items-list-cell cell-item-id">
								<b><?=$carItem->parser_name?></b><br>
								<?=$carItem->internal_id?>
							</td>
							<td class="items-list-cell">
								<?=$carItem->date?>
							</td>
							<td class="items-list-cell">
								<? $imageUrl = $carItem->getImageURL(); ?>
								<? if(file_exists(getcwd() . $imageUrl)): ?>
									<img src="<?=$imageUrl?>" width="140">
								<? else: ?>
									<img src="/public/assets/no_img.jpg" width="140">
								<? endif; ?>
							</td>
							<td class="items-list-cell">
								<span<?=($carItem->is_sold ? ' class="sold-item"' : '')?>>
									<?=mb_convert_case($carItem->car_mark_title, MB_CASE_TITLE)?> <?=mb_convert_case($carItem->car_model_title, MB_CASE_TITLE)?>
								</span>
							</td>
							<td class="items-list-cell">
								<?=$carItem->car_year?>
							</td>
							<td class="items-list-cell">
								<?=str_replace(', ', '<br>', $carItem->car_features)?>
                <? if(!empty($carItem->car_mileage)): ?>
                  <br><?=$carItem->car_mileage?> тыс. км.
                <? endif; ?>
                <? if(!empty($carItem->nodocs) || !empty($carItem->damaged)): ?>
                  <br>
                  <? if(!empty($carItem->nodocs)): ?>
                    <img src="/public/assets/nodocs.png" title="Без документов">
                  <? endif; ?>
                  <? if(!empty($carItem->damaged)): ?>
                    <img src="/public/assets/damaged.png" title="Битый или не на ходу">
                  <? endif; ?>
                <? endif; ?>
							</td>
							<td class="items-list-cell">
								<? $colorClass = ''; ?>
								<?
									$averagePrice = 0;

									if(isset($averagePrices[$carItem->car_mark][$carItem->car_model][$carItem->car_year]))
									{
										$averagePrice = $averagePrices[$carItem->car_mark][$carItem->car_model][$carItem->car_year];
									}
								?>
								<? if($carItem->price < $averagePrice && $averagePrice != 0): ?>
									<? $colorClass = ' green'; ?>
								<? elseif($carItem->price > $averagePrice && $averagePrice != 0): ?>
									<? $colorClass = ' red'; ?>
								<? endif; ?>

								<span class="car-price<?=$colorClass?>"><?=number_format($carItem->price, 0, '.', ' ')?> р.</span><br>
								<? if($averagePrice != 0): ?>
									<span class="car-price-average"><?=number_format($averagePrice, 0, '.', ' ')?> р.</span><br>
								<? endif; ?>
								<b><?=$carItem->city_title?></b>
							</td>
							<td width="7%" class="items-list-cell">
								<?
									$content = preg_replace('#<span.*?>(.*?)</span>#ims', '<br><br><span class="car-additional-content">$1</span>', $carItem->content_raw);
									$content = str_replace('<br><br><br>', '<br><br>', $content);
									$content = preg_replace('#<br><br>#ism', '', $content, 1);
									$content = preg_replace('#<p>(.*?)</p>#ism', '$1', $content, 1);
									$content = htmlentities($content);
									$content = trim($content, "'");
								?>
								<? if(!empty($content)): ?>
									<span class="popup-sign" data-text="<?=$content?>" data-orientation="left">?</span>
								<? endif; ?>
							</td>
						</tr>
					<? endforeach; ?>
				</table>
			</div>
		</div>
		<?=$carData->appends(Request::all())->links()?>
	<? else: ?>
		<b>Нет автомобилей для отображения.</b>
	<? endif; ?>
</div>