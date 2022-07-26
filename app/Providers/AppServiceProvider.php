<?php

namespace App\Providers;
use App\Project;
use View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    
    public function boot()
    {
        $projects = Project::all();
        View::share('projects',$projects);
    }
}
