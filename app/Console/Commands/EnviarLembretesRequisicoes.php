<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BookRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\PedidoLembreteMail;
use Carbon\Carbon;

class EnviarLembretesRequisicoes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:enviar-lembretes-requisicoes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia lembrete por e-mail no dia anterior à data de entrega das requisições';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $amanha = Carbon::tomorrow()->toDateString();

        $requisicoes = BookRequest::with(['user', 'items.livro'])
            ->whereDate('data_fim', $amanha)
            ->where(function ($query) {
                $query->whereNull('lembrete_enviado_em')
                      ->orWhereDate('lembrete_enviado_em', '<', now()->toDateString());
            })
            ->get();

        $enviados = 0;

        foreach ($requisicoes as $req) {
            $itensNaoEntregues = $req->items->whereNotIn(
                'status', 
                ['entregue_ok', 'entregue_obs', 'nao_entregue', 'cancelada']
            );

            if ($itensNaoEntregues->isNotEmpty()) {
                try {
                    Mail::to($req->user->email)->send(new PedidoLembreteMail($req));

                    $req->update([
                        'lembrete_enviado_em' => now(),
                        'lembrete_enviado_para' => $req->user->email,
                    ]);

                    $itensCount = $itensNaoEntregues->count();

                    Log::info("Lembrete enviado", [
                        'requisicao_id' => $req->id,
                        'user_nome' => $req->user->name,
                        'user_email' => $req->user->email,
                        'quantidade_itens' => $itensCount,
                        'data_envio' => now()->toDateTimeString(),
                    ]);

                    $enviados++;
                } catch (\Exception $e) {
                    Log::error("Erro ao enviar lembrete para requisicao_id={$req->id}, email={$req->user->email}", [
                        'erro' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }
        }

        if ($enviados === 0) {
            Log::info("Nenhum lembrete enviado para requisições com entrega em {$amanha}");
        }

        $this->info("Lembretes enviados para {$enviados} requisições.");
    }
}
