<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CompanyRegistry\Application\CompanyService;

class CompanyController extends Controller
{
    protected CompanyService $svc;
    public function __construct(CompanyService $svc) { $this->svc = $svc; }

    public function show($country, $id)
    {
        $company = $this->svc->getCompany($country, $id);
        if (! $company) abort(404, 'Company not found');
        $reports = $this->svc->getReportsForCompany($country, $id);
        return view('company.show', compact('company','reports'));
    }
}
