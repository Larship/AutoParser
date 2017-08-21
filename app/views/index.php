<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=$title?></title>
		<meta charset="utf-8">
		<meta name="description" content=""/>
		<meta name="keywords" content=""/>
		<meta name="robots" content="noindex, nofollow"/>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		
		<? foreach($stylesheets as $styleFile): ?>
			<link type="text/css" href="<?=$styleFile?>" rel="stylesheet"/>
		<? endforeach; ?>
		
		<? foreach($scripts as $scriptFile): ?>
			<script type="text/javascript" src="<?=$scriptFile?>"></script>
		<? endforeach; ?>
		
		<link rel="icon" type="image/x-icon" href="/favicon.ico"/>
	</head>
	<body>
		<div class="page-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="page-navigation col-sm-3 col-lg-2">
						<ul class="nav nav-pills nav-stacked">
							<li<?=($controllerName == "ListController" ? ' class="active"' : "")?>>
								<a href="/list/">Просмотр авто</a>
							</li>
							<li<?=($controllerName == "ParseController" ? ' class="active"' : "")?>>
								<a href="/parse/">Режим парсинга</a>
							</li>
							<li<?=($controllerName == "SettingsController" ? ' class="active"' : "")?>>
								<a href="/settings/">Настройки</a>
							</li>
						</ul>
					</div>
					<div class="main-content col-sm-9 col-lg-10">
						<?=$content?>
					</div>
				</div>
			</div>
		</div>
		<div class="car-models hide">
			<? foreach($carModels as $model): ?>
				<div class="car-model"
					 data-id="<?=$model->id?>"
					 data-mark_id="<?=$model->mark_id?>"
					 data-title="<?=htmlentities($model->title)?>"
					></div>
			<? endforeach; ?>
		</div>
		<div class="info-popup hide"></div>
	</body>
</html>