<?php
namespace App\Search;

use Meilisearch\Client;
use App\CompanyRegistry\Application\CompanyService;

class MeiliIndexer
{
    protected Client $client;
    protected CompanyService $companyService;
    protected $indexName = 'companies';

    public function __construct(CompanyService $svc)
    {
        $this->client = new Client(config('services.meili.host'), config('services.meili.key'));
        $this->companyService = $svc;

        // Ensure index exists with searchable attributes
        $index = $this->client->index($this->indexName);
        $index->updateSearchableAttributes(['name', 'registration_number', 'country']);
        $index->updateSortableAttributes(['name']);
    }

    /**
     * Rebuild the entire index from all providers.
     */
    public function reindexAll(): void
    {
        $allDocs = [];
        foreach ($this->companyService->getAllProviders() as $provider) {
            $country = $provider->countryCode();
            $offset = 0;
            $batch = 500;
            while (true) {
                $companies = $provider->searchCompanies('', $batch, $offset);
                if ($companies->isEmpty()) {
                    break;
                }
                foreach ($companies as $c) {

                    $reports = $provider->getReportsForCompany($c->id);
                    $hasReports = $reports->isNotEmpty();

                    $allDocs[] = [
                        'id' => $country . '_' . $c->id,
                        'company_id' => $c->id,
                        'country' => $country,
                        'name' => $c->name,
                        'registration_number' => $c->registration_number,
                        'address' => $c->address,
                        'state_id' => $c->state_id,
                        'has_reports' => $hasReports,
                    ];
                }
                $offset += $batch;
            }
        }
        $this->client->index($this->indexName)->addDocuments($allDocs, 'id');
    }
    
    public function indexAll()
    {
        foreach ($this->companyService->getAllProviders() as $country => $provider) {
            // fetch data in pages; convert each company to canonical doc
            // push to meili index
        }
    }
}
