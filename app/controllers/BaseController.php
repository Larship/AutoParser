<?php
class BaseController extends Controller
{
	protected $IndexView;
	
	public function __construct()
	{
		$this->IndexView = View::make('index');
		
		$this->IndexView->with('stylesheets', array(
			'/' . $this->compileScss('public/styles/main.scss'),
			'/public/additional/TwitterBootstrap/css/bootstrap.min.css',
			'/public/additional/TwitterBootstrap/css/bootstrap-theme.min.css',
		));
		
		$this->IndexView->with('scripts', array(
			'/public/additional/jquery-1.11.0.min.js',
			'/public/additional/TwitterBootstrap/js/bootstrap.min.js',
			'/public/js/scripts.js',
		));

		$curRouteAction = Route::currentRouteAction();
		
		$this->IndexView->with('title', 'Парсер автомобилей');
		$this->IndexView->with('controllerName', substr($curRouteAction, 0, strpos($curRouteAction, '@')));
		$this->IndexView->with('carModels', CarModels::get());
	}
	
	/**
	 * Метод выполняет компилирование SCSS-файла стилей в CSS-файл стилей и возвращает путь к скомпилированному файлу.
	 * 
	 * @param string $_scssFile Путь к SCSS-файлу стилей.
	 * @return string Путь к скомпилированному файлу.
	 */
	private function compileScss($_scssFile)
	{
		$pathinfo = pathinfo($_scssFile);
		
		$scss = new Scssc();
		$scss->setFormatter('scss_formatter_compressed');
		$scssServer = new scss_server($pathinfo['dirname'], null, $scss);
		$scssServer->setInputFilename($pathinfo['basename']);
		$cssFilename = $scssServer->serveBackground();
		
		return str_replace('\\', '/', $cssFilename);
	}
}
