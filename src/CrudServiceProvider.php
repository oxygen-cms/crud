<?php

namespace Oxygen\Crud;

use Illuminate\Support\ServiceProvider;

class CrudServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */

	public function boot() {
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'oxygen/crud');
		$this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'oxygen/crud');

		$this->publishes([
			__DIR__ . '/../resources/views' => base_path('resources/views/vendor/oxygen/crud'),
			__DIR__ . '/../resources/lang' => base_path('resources/lang/vendor/oxygen/crud'),
		]);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */

	public function register() {}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */

	public function provides() {
		return [];
	}

}
