<?php
class SettingsController extends BaseController
{
	/**
	 * Метод, отображающий страницу настроек.
	 * 
	 * @return \Illuminate\View\View
	 */
	public function getIndex()
	{
		$settingsView = View::make('settings');
		
		$status = Session::get('status');
		
		if(!empty($status))
		{
			$settingsView->with('status', $status);
		}

		$settingsView->with('parserList', Parser::getAll());
		$settingsView->with('carMarks', CarMarks::get());
		$settingsView->with('scheduleCarSets', ScheduleCarSets::get());
		$settingsView->with('settings', Settings::getAll());
		$this->IndexView->with('content', $settingsView);
		
		return $this->IndexView;
	}
	
	/**
	 * Метод, сохраняющий настройки.
	 * 
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function postSave()
	{
		switch(Input::get('settings-category'))
		{
			case 'schedule':
				$settingsCompiled = Input::get('settings-compiled');

				ScheduleCarSets::insertByJSON($settingsCompiled);

				$status = 'schedule-success';
			break;
			
			case 'notification':
				Settings::setValues([
					'notification-email' => Input::get('notification-email'),
					'notification-percent' => Input::get('notification-percent'),
				]);
				
				$status = 'notification-success';
			break;
			
			case 'proxy':
				Settings::setValues([
					'proxy-prefix' => Input::get('proxy-prefix'),
					'proxy-url' => Input::get('proxy-url'),
					'proxy-login' => Input::get('proxy-login'),
					'proxy-password' => Input::get('proxy-password'),
				]);

				$status = 'proxy-success';
			break;
		}
		
		return Redirect::to('/settings/')->with('status', $status);
	}
}
