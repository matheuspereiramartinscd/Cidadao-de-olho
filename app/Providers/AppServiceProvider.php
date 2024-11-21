<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registre os serviços da aplicação.
     */
    public function register(): void
    {
        // Exemplo: registrando uma interface e sua implementação
        // $this->app->bind(Interface::class, Implementation::class);
    }

    /**
     * Inicialize os serviços da aplicação.
     */
    public function boot(): void
    {
        // Exemplo: compartilhar uma variável com todas as views
        View::share('appName', config('app.name'));

        // Exemplo: Definir o local para o Carbon (para datas)
        Carbon::setLocale('pt_BR');
    }
}
