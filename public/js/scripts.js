(function () {
	$(window).on('load', onWindowLoad);

	function onWindowLoad() {
		var curCarModel;

		$('.page-list .car-mark').on('change', function() {
			fillModelsList(true);
		});

		$('.page-parse .car-mark, .page-settings .setting-car-mark').on('change', function() {
			fillModelsList(false);
		});

		$('.car-mark').change();

		curCarModel = $('.cur-car-model').data('id');

		if(curCarModel != 0) {
			$('.car-model option[value="' + curCarModel + '"]').prop('selected', true);
		}

		$('.only-numbers').on('keyup', function() {
			checkForNum($(this));
		});

		$('.page-settings .btn-add-car-set').on('click', onAddParseCarSetClick);
		$('.page-settings .parse-exist-items').on('click', '.exist-item .glyphicon', deleteParseCarSet);

		$('.old-car-sets .old-car-set-item').each(function(_index, _elem) {
			var elem = $(_elem);

			addParseCarSet(elem.data('parser_id'), elem.data('frequency'), elem.data('region'), elem.data('car_mark'),
				elem.data('car_model'), elem.data('max_pages_depth'), elem.data('notifications'));
		});

		$('.parse-get-models-btn').on('click', onGetModelsBtnClick);

		$('.clickable').on('click', function() {
			var elem = $(this);
			var target = elem.data('target');

			if(!target) {
				target = '_self';
			}

			window.open(elem.data('url'), target);

		});

		$('.popup-sign').on('mouseover', function() {
			var elem = $(this);
			var popupElem = $('.info-popup');
			var orientation = elem.data('orientation');
			var posLeft;

			if(!orientation) {
				orientation = 'right';
			}

			if(orientation == 'right') {
				posLeft = elem.offset().left + 20 + 'px';
			} else {
				posLeft = elem.offset().left - 500 + 'px';
			}

			popupElem.removeClass('hide');
			popupElem.html(elem.data('text'));
			popupElem.css({
				left: posLeft,
				top: elem.offset().top - popupElem.height() - 15 + 'px'
			});
			elem.on('mouseleave', function() {
				$('.info-popup').addClass('hide');
			});
		});
	}

	function checkForNum(_elem) {
		_elem.val(_elem.val().replace(new RegExp('\\D', 'gi'), ''));
	}

	function fillModelsList(_showAllEntry) {
		var curMarkId = $('.car-mark').val();
		var selectData = '';
		var curElem;

		if(_showAllEntry) {
			selectData += '<option value="0">Все</option>';
		}

		$('.car-models .car-model').each(function(_index, _elem) {
			curElem = $(_elem);

			if(curElem.data('mark_id') == curMarkId) {
				selectData += '<option value="' + curElem.data('id') + '">' + curElem.data('title') + '</option>';
			}
		});

		$('.main-content .car-model').html(selectData);
	}

	function onAddParseCarSetClick() {
		var parserId = $('.setting-parser-id').val();
		var frequency = parseInt($('.setting-frequency').val());
		var region = $('.setting-region').val();
		var carMark = $('.setting-car-mark').val();
		var carModel = $('.setting-car-model').val();
		var maxPagesDepth = parseInt($('.setting-max-pages-depth').val());
		var notifications = parseInt($('.setting-notifications:checked').val());

		if(isNaN(frequency)) {
			frequency = 30;
		}

		if(frequency < 10) {
			frequency = 10;
		}

		if(frequency > 180) {
			frequency = 180;
		}

		if(isNaN(maxPagesDepth)) {
			maxPagesDepth = 1;
		}

		if(carModel != null) {
			addParseCarSet(parserId, frequency, region, carMark, carModel, maxPagesDepth, notifications);
		}
	}

	function addParseCarSet(_parserId, _frequency, _region, _carMark, _carModel, _maxPagesDepth, _notifications) {
		$('.page-settings .parse-exist-items .no-items').remove();

		$('.page-settings .parse-exist-items').append(
			'<div class="exist-item col-sm-12"' +
			'data-parser_id="' + _parserId + '"' +
			'data-frequency="' + _frequency + '"' +
			'data-region="' + _region + '"' +
			'data-car_mark="' + _carMark + '"' +
			'data-car_model="' + _carModel + '"' +
			'data-max_pages_depth="' + _maxPagesDepth + '"' +
			'data-notifications="' + _notifications + '"' +
			'>' +
			'<b>' + $('.parser-list .parser-list-item[data-id="' + _parserId + '"]').data('name') + '</b><br>' +
			'<b>' + $('.setting-region option[value="' + _region + '"]').text() +
			' - ' + $('.setting-car-mark option[value="' + _carMark + '"]').text() + ' ' +
			$('.car-models .car-model[data-id="' + _carModel + '"]').data('title') + '</b><br>' +
			'<b>Периодичность:</b> раз в ' + _frequency + ' минут<br>' +
			'<b>Глубина просмотра:</b> ' + (_maxPagesDepth == 0 ? '-' : _maxPagesDepth) + '<br>' +
			'<b>Уведомления:</b> ' + (_notifications == 0 ? 'выключены' : 'включены') +
			'<span class="glyphicon glyphicon-remove" title="Удалить"></span></div>'
		);

		compileSettings();
	}

	function deleteParseCarSet() {
		$(this).parents('.exist-item').remove();

		if($('.page-settings .parse-exist-items .exist-item').length == 0) {
			$('.page-settings .parse-exist-items').append('<label class="no-items control-label">Пусто</label>');
		}

		compileSettings();
	}

	function compileSettings() {
		var objectsArr = [ ], obj;

		$('.page-settings .parse-exist-items .exist-item').each(function(_index, _elem) {
			var elem = $(_elem);
			obj = {
				parser_id: elem.data('parser_id'),
				frequency: elem.data('frequency'),
				region: elem.data('region'),
				car_mark: elem.data('car_mark'),
				car_model: elem.data('car_model'),
				max_pages_depth: elem.data('max_pages_depth'),
				notifications: elem.data('notifications')
			};

			objectsArr.push(obj);
		});

		$('.settings-compiled').val(JSON.stringify(objectsArr));
	}

	function onGetModelsBtnClick() {
		location.href = '/parse/models/?car_mark=' + $('.car-mark').val() + '&parser_name=' + $('select[name=parser_name]').val();
	}
}());