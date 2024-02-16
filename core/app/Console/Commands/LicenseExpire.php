<?php

namespace App\Console\Commands;

use App\Notifications\LicenseExpireNotify;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class LicenseExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'license:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'License expire before 7 it will execute';

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
     * @return int
     */
    public function handle()
    {
        $users = User::whereNotNull('license_expire_date')
            ->whereDate('license_expire_notify_date', '<=', now()->format('Y-m-d'))
            ->where('license_expire_notify', 'no')
            ->get();

        foreach ($users as $user) {
            if (Carbon::parse($user->license_expire_notify_date)->format('Y-m-d') <= now()->format('Y-m-d')) {
                $user->notify(new LicenseExpireNotify());
                $user->update(['license_expire_notify' => 'yes']);
            }
        }
    }
}
