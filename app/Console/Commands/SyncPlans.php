<?php

namespace App\Console\Commands;

use App\Plan;
use Braintree_Plan;
use Illuminate\Console\Command;

class SyncPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'braintree:sync-plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincronización con planes en línea en Braintree';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Plan::truncate();

        tap(Braintree_Plan::all(), function ($planes) {
            foreach ($planes as $plan) {
                Plan::create([
                    'name' => $plan->name,
                    'braintree_plan' => $plan->id,
                    'price' => $plan->price,
                    'trialDuration' => $plan->trialDuration,
                    'description' => $plan->description,
                ]);
            }
        });

    }
}
