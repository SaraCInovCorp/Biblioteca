<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

//Schedule::command('app:atualizar-requisicoes-atrasadas')->everyTenSeconds();

//Schedule::command('enviar:lembretesrequisicoes')->dailyAt('8:00');

Schedule::command('app:atualizar-requisicoes-atrasadas')->dailyAt('00:05');

Schedule::command('app:enviar-lembretes-requisicoes')->dailyAt('08:00');
