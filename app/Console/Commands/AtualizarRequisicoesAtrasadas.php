<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\BookRequestItem;

class AtualizarRequisicoesAtrasadas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:atualizar-requisicoes-atrasadas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza status de itens de requisição atrasados para "não entregue" quando passou da data prevista de entrega';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hoje = Carbon::today();

        $itensParaAtualizar = BookRequestItem::where('status', 'realizada')
            ->whereHas('bookRequest', function ($q) use ($hoje) {
                $q->whereDate('data_fim', '<', $hoje);
            })
            ->get();

        if ($itensParaAtualizar->isEmpty()) {
            Log::info('Nenhum item atrasado para atualizar no momento.');
            $this->info('Nenhum item atrasado para atualizar no momento.');
            return;
        }

        $ids = $itensParaAtualizar->pluck('id')->toArray();
        Log::info('Atualizando itens com IDs: ' . implode(', ', $ids));

        $alterados = BookRequestItem::whereIn('id', $ids)
            ->update(['status' => 'nao_entregue']);

        Log::info("Itens de requisições atrasadas marcados como não entregues: {$alterados}");
        $this->info("Itens atualizados: {$alterados}");
    }

}
