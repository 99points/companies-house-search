<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\CompanyRegistry\Application\CompanyService;
use App\CompanyRegistry\Infrastructure\Providers\SingaporeCompanyProvider;
use App\CompanyRegistry\Infrastructure\Providers\MexicoCompanyProvider;
use App\CompanyRegistry\Infrastructure\Providers\UKCompanyProvider;

class CompanyModuleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(CompanyService::class, function($app) {
        $sg = $app->make(SingaporeCompanyProvider::class);
        $mx = $app->make(MexicoCompanyProvider::class);
        $uk = $app->make(UKCompanyProvider::class);
        
        return new CompanyService([
            'SG' => $sg,
            'MX' => $mx,
            'UK' => $uk,
        ]);
    });

    }

    public function boot() {}
}
