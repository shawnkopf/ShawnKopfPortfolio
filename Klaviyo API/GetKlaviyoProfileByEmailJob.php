<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\KlaviyoService;
use Illuminate\Support\Facades\Log;

class GetKlaviyoProfileByEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $email;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(KlaviyoService $klaviyoService)
    {
        $profileId = $klaviyoService->getKlaviyoProfileIdByEmail($this->email);

        if ($profileId) {
            Log::info("Successfully retrieved Klaviyo profile ID for email {$this->email}: {$profileId}");
        } else {
            Log::warning("No Klaviyo profile found for email {$this->email}.");
        }
    }
}
