<div class="page-settings">
	<legend>Настройки расписания</legend>
	<form action="/settings/save/" method="POST" class="form-horizontal">
		<div class="form-group">
			<label class="col-sm-4 col-md-3 control-label">Сайт для парсинга:</label>
			<div class="col-sm-7 col-lg-5">
				<select class="form-control setting-parser-id">
					<? foreach($parserList as $parser): ?>
						<option value="<?=$parser['id']?>"><?=$parser['name']?></option>
					<? endforeach; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 col-md-3 control-label">Периодичность:</label>
			<div class="col-sm-7 col-lg-5">
				<div class="input-group">
					<span class="input-group-addon">Один раз в</span>
					<input type="text" class="form-control only-numbers setting-frequency" value="30">
					<span class="input-group-addon">минут</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 col-md-3 control-label">Регион:</label>
			<div class="col-sm-7 col-lg-5">
				<select class="form-control setting-region">
					<option value="region25">Приморский край</option>
					<option value="vladivostok">Владивосток</option>
					<option value="nakhodka">Находка</option>
					<option value="ussuriisk">Уссурийск</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 col-md-3 control-label">Марка авто:</label>
			<div class="col-sm-7 col-lg-5">
				<select class="setting-car-mark car-mark form-control">
					<? foreach($carMarks as $mark): ?>
						<option value="<?=$mark->id?>"><?=$mark->title?></option>
					<? endforeach; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 col-md-3 control-label">Модель авто:</label>
			<div class="col-sm-7 col-lg-5">
				<select class="setting-car-model car-model form-control"></select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 col-md-3 control-label">Глубина просмотра:</label>
			<div class="col-sm-7 col-lg-5">
				<input class="setting-max-pages-depth form-control only-numbers" value="1"/>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 col-md-3 control-label">Уведомления:</label>
			<div class="col-sm-7 col-lg-5">
				<div data-toggle="buttons" class="radio-group">
					<div class="btn btn-default col-xs-12 col-sm-6 active">
						<input checked="checked" name="setting-notifications" class="setting-notifications" value="1" id="1" type="radio" data-title="Включены">Включены
					</div>
					<div class="btn btn-default col-xs-12 col-sm-6">
						<input name="setting-notifications" class="setting-notifications" value="0" id="2" type="radio" data-title="Отключены">Отключены
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-4 col-md-offset-3 col-sm-5">
				<input type="button" class="btn-add-car-set btn btn-default" value="Добавить"/>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 col-md-3 control-label">Добавленное:</label>
			<div class="col-sm-7 col-lg-5">
				<div class="parse-exist-items">
					<label class="no-items control-label">Пусто</label>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-8 col-md-offset-7 col-lg-offset-6 col-sm-3 col-lg-2">
				<input type="submit" class="btn btn-danger btn-block" value="Сохранить"/>
			</div>
		</div>
		<? if(!empty($status) && $status == "schedule-success"): ?>
			<div class="form-group">
				<div class="col-sm-offset-3 col-md-offset-2 col-sm-6">
					<div class="alert alert-success">
						Сохранение настроек расписания выполнено успешно!
					</div>
				</div>
			</div>
		<? endif; ?>
		<input type="hidden" name="settings-compiled" class="settings-compiled" value=""/>
		<input type="hidden" name="settings-category" value="schedule"/>
	</form>
	<legend>Настройки уведомлений</legend>
	<form action="/settings/save/" method="POST" class="form-horizontal">
		<div class="form-group">
			<label class="col-sm-4 col-md-3  control-label">E-mail:</label>
			<div class="col-sm-7 col-lg-5">
				<input class="form-control" name="notification-email" value="<?=(isset($settings["notification-email"]) ? $settings["notification-email"] : "")?>"/>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 col-md-3 control-label">Изменение цены: <span class="popup-sign" data-text="Данная настройка показывает, на сколько процентов полученная цена на автомобиль должна быть ниже средней цены для того, чтобы она попала в список для уведомления.">?</span></label>
			<div class="col-sm-7 col-lg-5">
				<div class="input-group">
					<input class="form-control" name="notification-percent" value="<?=(isset($settings["notification-percent"]) ? $settings["notification-percent"] : "")?>"/>
					<span class="input-group-addon">%</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-8 col-md-offset-7 col-lg-offset-6 col-sm-3 col-lg-2">
				<input type="submit" class="btn btn-danger btn-block" value="Сохранить"/>
			</div>
		</div>
		<? if(!empty($status) && $status == "notification-success"): ?>
			<div class="form-group">
				<div class="col-sm-offset-3 col-md-offset-2 col-sm-6">
					<div class="alert alert-success">
						Сохранение настроек уведомлений выполнено успешно!
					</div>
				</div>
			</div>
		<? endif; ?>
		<input type="hidden" name="settings-category" value="notification"/>
	</form>
	<legend>Настройки прокси</legend>
	<form action="/settings/save/" method="POST" class="form-horizontal">
		<div class="form-group">
			<label class="col-sm-3 col-md-2 control-label">Префикс адреса:</label>
			<div class="col-sm-8 col-lg-6">
				<input name="proxy-prefix" class="form-control" value="<?=(isset($settings["proxy-prefix"]) ? $settings["proxy-prefix"] : "")?>"/>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 col-md-2 control-label">URL (или IP):</label>
			<div class="col-sm-8 col-lg-6">
				<input name="proxy-url" class="form-control" value="<?=(isset($settings["proxy-url"]) ? $settings["proxy-url"] : "")?>"/>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 col-md-2 control-label">Логин:</label>
			<div class="col-sm-8 col-lg-6">
				<input name="proxy-login" class="form-control" value="<?=(isset($settings["proxy-login"]) ? $settings["proxy-login"] : "")?>"/>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 col-md-2 control-label">Пароль:</label>
			<div class="col-sm-8 col-lg-6">
				<input name="proxy-password" class="form-control" type="password" value="<?=(isset($settings["proxy-password"]) ? $settings["proxy-password"] : "")?>"/>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-8 col-md-offset-7 col-lg-offset-6 col-sm-3 col-lg-2">
				<input type="submit" class="btn btn-danger btn-block" value="Сохранить"/>
			</div>
		</div>
		<? if(!empty($status) && $status == "proxy-success"): ?>
			<div class="form-group">
				<div class="col-sm-offset-3 col-md-offset-2 col-sm-6">
					<div class="alert alert-success">
						Сохранение настроек прокси выполнено успешно!
					</div>
				</div>
			</div>
		<? endif; ?>
		<input type="hidden" name="settings-category" value="proxy"/>
	</form>
	<div class="old-car-sets">
		<? foreach($scheduleCarSets as $carSet): ?>
			<div class="old-car-set-item" 
				data-parser_id="<?=$carSet->parser_id?>" 
				data-frequency="<?=$carSet->frequency?>" 
				data-region="<?=$carSet->region?>" 
				data-car_mark="<?=$carSet->car_mark?>" 
				data-car_model="<?=$carSet->car_model?>" 
				data-max_pages_depth="<?=$carSet->max_pages_depth?>"
				data-notifications="<?=$carSet->notifications?>">
			</div>
		<? endforeach; ?>
	</div>
	<div class="parser-list">
		<? foreach($parserList as $parserItem): ?>
			<div class="parser-list-item"
				data-id="<?=$parserItem['id']?>"
				data-name="<?=$parserItem['name']?>">
			</div>
		<? endforeach; ?>
	</div>
</div>