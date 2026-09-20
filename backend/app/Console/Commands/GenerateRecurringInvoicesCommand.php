<?php

namespace App\Console\Commands;

use App\Modules\V1\RecurringInvoice\Services\Generate\DetailsService;
use Illuminate\Console\Command;

class GenerateRecurringInvoicesCommand extends Command
{
    protected $signature = 'recurring-invoices:generate {--dry-run : Preview what will happen without making any changes}';

    protected $description = 'Generate invoices for recurring invoice rules that are due';

    public function __construct(private readonly DetailsService $detailsService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $result = $this->detailsService->generateDue($dryRun);

        if ($result['locked']) {
            $this->warn('Skipped — a previous run is still in progress.');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            '%sDue: %d, Generated: %d, Failed: %d',
            $dryRun ? '[DRY RUN] ' : '',
            $result['due'],
            $result['generated'],
            $result['failed'],
        ));

        return self::SUCCESS;
    }
}
