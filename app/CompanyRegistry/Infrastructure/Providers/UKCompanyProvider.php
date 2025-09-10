<?php

namespace App\CompanyRegistry\Infrastructure\Providers;

use App\CompanyRegistry\Domain\DTOs\CompanyDTO;
use Illuminate\Support\Collection;


class UKCompanyProvider implements CompanyProviderInterface
{
    protected $connection = 'mysql_uk';
    public function countryCode(): string { return 'UK'; }

    public function searchCompanies(string $query, int $limit = 20, int $offset = 0): Collection
    {
        //App\\CompanyRegistry\\Infrastructure\\Providers\\UKCompanyProvider')); echo PHP_EOL;"

        // Simple LIKE search — real app should index to Meilisearch.
        $rows = DB::connection($this->connection)
            ->table('companies')
            ->select('id', 'name', 'slug', 'registration_number', 'address')
            ->where('name', 'like', "%$query%")
            ->limit($limit)
            ->offset($offset)
            ->get();

        return $rows->map(fn($r) => new CompanyDTO($this->countryCode(), $r));
    }

    public function getCompanyById($id): ?CompanyDTO
    {
        $r = DB::connection($this->connection)
            ->table('companies')->where('id', $id)->first();

        return $r ? new CompanyDTO($this->countryCode(), $r) : null;
    }

    public function getReportsForCompany($id): Collection
    {
        // business rule: all reports from reports table apply in SG
        $rows = DB::connection($this->connection)
            ->table('reports')->select('id','name','amount')->get();

        return $rows->map(fn($r) => collect([
            'report_id' => $r->id,
            'name' => $r->name,
            'price' => (float) $r->amount,
            'country' => $this->countryCode(),
        ]));
    }
}
