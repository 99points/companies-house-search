<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CompanyRegistry\Application\CompanyService;
use App\CompanyRegistry\Domain\DTOs\CompanyDTO;

class SearchController extends Controller
{
    protected CompanyService $svc;

    public function __construct(CompanyService $svc)
    {
        $this->svc = $svc;
    }

    public function index(Request $request)
    {
        $q       = trim($request->query('q', ''));
        $page    = (int) $request->query('page', 1);
        $perPage = 20;

        if ($q !== '') {
            $data = $this->svc->search($q, $page, $perPage);
        } else {
            $companiesDb1 = \DB::connection('mysql_mx')->table('companies')->get(); 
            $companiesDb2 = \DB::connection('mysql_sg')->table('companies')->get(); 
            $companiesDb3 = \DB::connection('mysql_uk')->table('companies')->get(); 

            $allCompanies = collect()
                
                ->merge(
                    $companiesDb2->map(fn($row) => new CompanyDTO('SG', $row)) // country = MX 
                )
                ->merge(
                    $companiesDb1->map(fn($row) => new CompanyDTO('MX', $row)) // country = SG 
                )                 
                ->merge($companiesDb3->map(fn($row) => new CompanyDTO('UK', $row))); // new
;

            $total = $allCompanies->count();

            //$allCompanies = $companiesDb1->merge($companiesDb2);
            //$total = $allCompanies->count();

            $hits = $allCompanies
                ->slice(($page - 1) * $perPage, $perPage)
                ->values(); 

            $data = [
                'hits'    => $hits,
                'total'   => $total,
                'page'    => $page,
                'perPage' => $perPage,
            ];
        }

        return view('search.index', [
            'q'       => $q,
            'results' => $data['hits'],
            'total'   => $data['total'],
            'page'    => $data['page'],
            'perPage' => $data['perPage'],
        ]);
    }

    public function __index(Request $request)
    {
        $q       = $request->query('q', '');
        $page    = (int) $request->query('page', 1);
        $perPage = 20;
        
        $data = $q
            ? $this->svc->search($q, $page, $perPage)
            : ['hits' => collect(), 'total' => 0, 'page' => $page, 'perPage' => $perPage];

        return view('search.index', [
            'q'       => $q,
            'results' => $data['hits'],
            'total'   => $data['total'],
            'page'    => $data['page'],
            'perPage' => $data['perPage'],
        ]);
    }

    public function autocomplete(Request $request)
    {
        $q = $request->query('q', '');
        if (!$q) {
            return response()->json([]);
        }

        $data = $this->svc->search($q, 1, 10);

        $suggestions = $data['hits']->map(fn($company) => [
            'id'      => $company->id,
            'name'    => $company->name,
            'country' => $company->country,
        ]);

        return response()->json($suggestions);
    }
}
