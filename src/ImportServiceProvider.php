<?php

namespace hiriz\import;
use Illuminate\Support\ServiceProvider;

class ImportServiceProvider extends ServiceProvider{


	public function boot()
	{
		$this->loadRoutesFrom(__DIR__.'/routes/web.php');
		$this->loadViewsFrom(__DIR__.'/views','import');
	}

	public function register()
	{
		# code...
	}
}