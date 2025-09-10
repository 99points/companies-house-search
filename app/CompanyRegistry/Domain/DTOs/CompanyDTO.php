<?php
namespace App\CompanyRegistry\Domain\DTOs;

class CompanyDTO
{
    public string $country;
    public int|string $id;
    public string $name;
    public ?string $slug;
    public ?string $registration_number;
    public ?string $address;
    public ?int $state_id;
    public bool $has_reports = false;
    
    public function __construct(string $country, $row, array $meta = [])
    {
        $this->country = $country;
        $this->id = $row->id;
        $this->name = $row->name ?? '';
        $this->slug = $row->slug ?? null;
        $this->registration_number = $row->registration_number ?? null;
        $this->address = $row->address ?? null;
        $this->state_id = $row->state_id ?? null;

            $this->has_reports = (bool)($meta['has_reports'] ?? false);

    }
}
