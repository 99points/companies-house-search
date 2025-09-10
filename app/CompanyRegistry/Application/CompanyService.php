<?php

namespace App\CompanyRegistry\Application;

use Illuminate\Support\Collection;
use App\CompanyRegistry\Infrastructure\Providers\CompanyProviderInterface;
use Meilisearch\Client;

class CompanyService
{
    protected array $providers = [];
    protected ?Client $meili = null;
    protected string $meiliIndex = 'companies';

    public function __construct(array $providers = [])
    {
        $this->providers = is_array($providers) ? $providers : iterator_to_array($providers);

        if (config('services.meili.host')) {
            $this->meili = new Client(
                config('services.meili.host'),
                config('services.meili.key')
            );
        }
    }

    public function registerProvider(CompanyProviderInterface $p): void
    {
        $this->providers[$p->countryCode()] = $p;
    }

    /**
     * Search companies via Meilisearch (preferred) or  fallback.
     *
     * @param string $query
     * @param int $page Current page number (1-based)
     * @param int $perPage Results per page
     *
     */
    public function search(string $query, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;

        // 1. Try Meilisearch
        if ($this->meili && $query !== '') {
            try {
                $result = $this->meili
                    ->index($this->meiliIndex)
                    ->search($query, [
                        'limit' => $perPage,
                        'offset' => $offset,
                    ]);

                $hits = collect($result->getHits());

                \Log::info('Meili hits:', $hits->toArray());

                $mapped = $hits->map(function ($hit) {
                    return new \App\CompanyRegistry\Domain\DTOs\CompanyDTO(
                        $hit['country'],
                        (object) [
                            'id' => $hit['company_id'],
                            'name' => $hit['name'] ?? '',
                            'slug' => $hit['slug'] ?? null,
                            'registration_number' => $hit['registration_number'] ?? null,
                            'address' => $hit['address'] ?? null,
                            'state_id' => $hit['state_id'] ?? null,
                        ]
                    );
                });

                return [
                    'hits'    => $mapped,
                    'total'   => $result->getEstimatedTotalHits() ?? $mapped->count(),
                    'page'    => $page,
                    'perPage' => $perPage,
                ];
            } catch (\Throwable $e) {
                \Log::warning('Meilisearch failed: ' . $e->getMessage());
            }
        }

        // if milie search failed lets do Provider fallback 
        $results = collect();
        foreach ($this->providers as $provider) {
            $results = $results->merge($provider->searchCompanies($query, $perPage, $offset));
        }

        return [
            'hits'    => $results->values(),
            'total'   => $results->count(), 
            'page'    => $page,
            'perPage' => $perPage,
        ];
    }

    public function getProvider(string $country): ?CompanyProviderInterface
    {
        return $this->providers[$country] ?? null;
    }

    public function getCompany(string $country, $id)
    {
        return $this->getProvider($country)?->getCompanyById($id);
    }

    public function getReportsForCompany(string $country, $id)
    {
        return $this->getProvider($country)?->getReportsForCompany($id) ?? collect();
    }

    public function getAllProviders(): array
    {
        return $this->providers;
    }
}
