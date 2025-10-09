<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Cria a aplicação Laravel para os testes.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        // Carrega a aplicação Laravel
        $app = require __DIR__.'/../bootstrap/app.php';

        // Inicializa o kernel
        $app->make(Kernel::class)->bootstrap();

        // Retorna a instância da aplicação
        return $app;
    }
}
