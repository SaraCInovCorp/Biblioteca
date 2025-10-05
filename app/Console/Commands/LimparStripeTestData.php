<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\Charge;
use Stripe\Refund;

class LimparStripeTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:limpar-testdata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpa dados de teste (clientes, pagamentos, cobranças) na conta Stripe';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $this->info('Iniciando limpeza de clientes e pagamentos Stripe...');

        // Cancelar todos PaymentIntents
        $paymentIntents = PaymentIntent::all(['limit' => 100]);
        foreach ($paymentIntents->autoPagingIterator() as $pi) {
            try {
                $this->info("Tentando cancelar PaymentIntent: {$pi->id}");
                $paymentIntent = PaymentIntent::retrieve($pi->id);
                $paymentIntent->cancel();
            } catch (\Exception $e) {
                $this->error("Erro ao cancelar PaymentIntent {$pi->id}: " . $e->getMessage());
            }
        }

        // Reembolsar cobranças
        $charges = Charge::all(['limit' => 100]);
        foreach ($charges->autoPagingIterator() as $charge) {
            try {
                $this->info("Reembolsando Charge: {$charge->id}");
                \Stripe\Refund::create(['charge' => $charge->id]);
            } catch (\Exception $e) {
                $this->error("Erro ao reembolsar Charge {$charge->id}: " . $e->getMessage());
            }
        }

        // Deletar clientes
        $customers = Customer::all(['limit' => 100]);
        foreach ($customers->autoPagingIterator() as $customer) {
            try {
                $this->info("Deletando cliente: {$customer->id}");
                $customerInstance = Customer::retrieve($customer->id);
                $customerInstance->delete();
            } catch (\Exception $e) {
                $this->error("Erro ao deletar cliente {$customer->id}: " . $e->getMessage());
            }
        }

        $this->info('Limpeza do Stripe finalizada.');
    }
}
