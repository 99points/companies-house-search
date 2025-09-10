<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Search\MeiliIndexer;

class ReindexCompanies extends Command
{
    protected $signature = 'companies:reindex';
    protected $description = 'Reindex all companies into Meilisearch';

    public function handle(MeiliIndexer $indexer)
    {
        $this->info('Reindexing companies...');
        $indexer->reindexAll();
        $this->info('Done.');
    }
}
