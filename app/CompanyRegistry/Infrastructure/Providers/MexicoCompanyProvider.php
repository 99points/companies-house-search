<?php
namespace App\CompanyRegistry\Infrastructure\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\CompanyRegistry\Domain\DTOs\CompanyDTO;

class MexicoCompanyProvider implements CompanyProviderInterface
{
    protected $connection = 'mysql_mx';
    public function countryCode(): string { return 'MX'; }

    public function searchCompanies(string $query, int $limit = 20, int $offset = 0): Collection
    {
        $rows = DB::connection($this->connection)
            ->table('companies')
            ->select('id', 'name', 'slug', 'address', 'state_id')
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
        // need company state_id then find report_state entries
        $company = DB::connection($this->connection)->table('companies')->where('id', $id)->first();
        if (! $company) return collect();

        $rows = DB::connection($this->connection)
            ->table('report_state as rs')
            ->join('reports as r', 'r.id', '=', 'rs.report_id')
            ->where('rs.state_id', $company->state_id)
            ->select('r.id as report_id', 'r.name', 'rs.amount')
            ->get();

        return $rows->map(fn($r) => collect([
            'report_id' => $r->report_id,
            'name' => $r->name,
            'price' => (float) $r->amount,
            'country' => $this->countryCode(),
        ]));
    }
}
