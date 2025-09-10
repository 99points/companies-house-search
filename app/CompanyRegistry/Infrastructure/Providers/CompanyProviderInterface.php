<?php
namespace App\CompanyRegistry\Infrastructure\Providers;

use Illuminate\Support\Collection;
use App\CompanyRegistry\Domain\DTOs\CompanyDTO;

interface CompanyProviderInterface
{
    public function searchCompanies(string $query, int $limit = 20, int $offset = 0): Collection;
    public function getCompanyById(int|string $id): ?CompanyDTO;
    public function getReportsForCompany(int|string $id): Collection; // each item: ['report_id','name','price','country']
    public function countryCode(): string; // e.g. 'SG' or 'MX'
}
