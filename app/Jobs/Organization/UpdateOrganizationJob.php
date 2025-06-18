<?php

declare(strict_types=1);

namespace App\Jobs\Organization;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateOrganizationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Organization $organization,
        private array $data
    ) {}

    public function handle(): Organization
    {
        $this->organization->update($this->data);

        return $this->organization;
    }
}
