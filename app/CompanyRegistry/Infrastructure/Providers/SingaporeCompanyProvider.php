<?php
namespace App\CompanyRegistry\Infrastructure\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\CompanyRegistry\Domain\DTOs\CompanyDTO;

class SingaporeCompanyProvider implements CompanyProviderInterface
{
    protected $connection = 'mysql_sg';

    public function countryCode(): string { return 'SG'; }

    public function searchCompanies(string $query, int $limit = 20, int $offset = 0): Collection
    {
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
