<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('points:update-monthly')]
#[Description('Update monthly points for all users')]
class UpdateMonthlyPoints extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startOfMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfMonth = Carbon::now()->subMonth()->endOfMonth();

        User::chunk(100, function($users) use ($startOfMonth, $endOfMonth){
            foreach($users as $user){
                $spent = $user->orders()
                        ->where('status', 'delivered')
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                        ->sum('total_amount');

                $addPoints = $spent >= 5000 ? 500 : 10;
                $user->increment('points', $addPoints);
            }
        });
    }
}
