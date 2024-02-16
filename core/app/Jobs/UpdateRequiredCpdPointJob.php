<?php

namespace App\Jobs;

use App\BasicSetting;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class UpdateRequiredCpdPointJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $year = date('Y');

        $users = User::query()
            ->with('cpd_required', fn ($query) =>
                $query->latest('year')->limit(1)
            )
            ->whereDoesntHave('cpd_required', fn(Builder $query) =>
                $query->where('year', '=', $year)
            )
            ->get(['id'])
            ->makeHidden('full_name');

        $bs = BasicSetting::first(['def_required_cpd_point']);

        foreach ($users as $user) {
            $cpdRequired = $user->cpd_required->first();
            $cpdPoint = $cpdRequired?->required_points ?? $bs->def_required_cpd_point;

            $user->cpd_required()
                ->firstOrCreate(
                    ['year' => $year],
                    ['required_points' => $cpdPoint],
                );
        }

        Log::info("CPD Updated user count: " . count($users));
    }
}
