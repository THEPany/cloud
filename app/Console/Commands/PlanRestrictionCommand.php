<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\{Restriction, Plan, Organization};

class PlanRestrictionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:restriction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create plan restriction';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Restriction::truncate();

        foreach (Plan::all('id') as $plan) {
            Restriction::create([
                    'plan_id' => $plan->id,
                    'restriction_key' => Organization::RESTRICTION_ORGANIZATION,
                    'restriction_limit' => 1
            ]);
            Restriction::create([
                'plan_id' => $plan->id,
                'restriction_key' => Organization::RESTRICTION_CONTRIBUTOR,
                'restriction_limit' => 1
            ]);
        }
    }
}
