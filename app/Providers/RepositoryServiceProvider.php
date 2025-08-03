<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\TransaksiRepository;
use App\Repositories\ProdukRepository;
use App\Repositories\UserRepository;
use App\Repositories\KategoriRepository;
use App\Repositories\PelangganRepository;
use App\Repositories\StokRepository;
use App\Repositories\DiskonRepository;
use App\Services\TransaksiService;
use App\Services\ProdukService;
use App\Services\UserService;
use App\Services\KategoriService;
use App\Services\PelangganService;
use App\Services\StokService;
use App\Services\DiskonService;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register Repositories
        $this->app->bind(TransaksiRepository::class);
        $this->app->bind(ProdukRepository::class);
        $this->app->bind(UserRepository::class);
        $this->app->bind(KategoriRepository::class);
        $this->app->bind(PelangganRepository::class);
        $this->app->bind(StokRepository::class);
        $this->app->bind(DiskonRepository::class);

        // Register Services
        $this->app->bind(TransaksiService::class, function ($app) {
            return new TransaksiService(
                $app->make(TransaksiRepository::class),
                $app->make(ProdukRepository::class)
            );
        });

        $this->app->bind(ProdukService::class, function ($app) {
            return new ProdukService(
                $app->make(ProdukRepository::class),
                $app->make(KategoriRepository::class)
            );
        });

        $this->app->bind(UserService::class, function ($app) {
            return new UserService(
                $app->make(UserRepository::class)
            );
        });

        $this->app->bind(KategoriService::class, function ($app) {
            return new KategoriService(
                $app->make(KategoriRepository::class)
            );
        });

        $this->app->bind(PelangganService::class, function ($app) {
            return new PelangganService(
                $app->make(PelangganRepository::class)
            );
        });

        $this->app->bind(StokService::class, function ($app) {
            return new StokService(
                $app->make(StokRepository::class),
                $app->make(ProdukRepository::class)
            );
        });

        $this->app->bind(DiskonService::class, function ($app) {
            return new DiskonService(
                $app->make(DiskonRepository::class)
            );
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
