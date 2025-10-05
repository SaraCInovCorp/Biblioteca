<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Carrinho;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\CarrinhoAbandonadoMail;
use Carbon\Carbon;

class EnviarLembretesCarrinhosAbandonados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:enviar-lembretes-carrinhos-abandonados';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia lembrete por e-mail para carrinhos abandonados há mais de 1 hora';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limite = Carbon::now()->subHour();

        $carrinhos = Carrinho::with(['user', 'items.livro'])
            ->where('status', 'ativo')
            ->where('updated_at', '<=', $limite)
            ->whereNull('lembrete_enviado_em')  // Evita lembrete duplicado
            ->get();

        Log::info("Carrinhos encontrados", $carrinhos->map(function($c) {
            return [
                'id' => $c->id,
                'user_email' => $c->user->email ?? null,
                'items_count' => $c->items->count(),
                'updated_at' => $c->updated_at,
            ];
        })->toArray());

        $enviados = 0;

        foreach ($carrinhos as $carrinho) {
            Log::info("Debug Carrinho:", [
                'carrinho_id' => $carrinho->id,
                'user_email' => $carrinho->user->email ?? 'sem email',
                'items_count' => $carrinho->items->count(),
            ]);

            if ($carrinho->user && $carrinho->items->isNotEmpty()) {
                try {
                    Mail::to($carrinho->user->email)
                        ->send(new CarrinhoAbandonadoMail($carrinho));

                    $carrinho->update([
                        'lembrete_enviado_em' => now(),
                        'lembrete_enviado_para' => $carrinho->user->email,
                    ]);

                    Log::info("Lembrete carrinho enviado", ['carrinho_id' => $carrinho->id]);
                    $enviados++;
                } catch (\Exception $e) {
                    Log::error("Erro ao enviar lembrete para carrinho_id={$carrinho->id}", [
                        'erro' => $e->getMessage(),
                    ]);
                }
            }
        }

        if ($enviados === 0) {
            Log::info("Nenhum lembrete enviado para carrinhos abandonados.");
        }

        $this->info("Lembretes enviados para {$enviados} carrinhos.");
    }
}
