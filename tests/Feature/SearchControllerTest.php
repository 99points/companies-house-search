<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;
use App\CompanyRegistry\Application\CompanyService;
use App\CompanyRegistry\Domain\DTOs\CompanyDTO;

class SearchControllerTest extends TestCase
{
    use RefreshDatabase;

    public function it_returns_search_results_when_query_provided()
    {
        // Fake the service
        $mock = Mockery::mock(CompanyService::class);
        $mock->shouldReceive('search')
            ->once()
            ->with('test', 1, 20)
            ->andReturn([
                'hits' => collect([ (object)['name' => 'Fake Company'] ]),
                'total' => 1,
                'page' => 1,
                'perPage' => 20,
            ]);

        $this->app->instance(CompanyService::class, $mock);

        $response = $this->get('/?q=test');

        $response->assertStatus(200);
        $response->assertViewHas('results', function ($results) {
            return $results->first()->name === 'Fake Company';
        });
    }

    public function it_merges_results_from_two_dbs_when_no_query()
    {
        // Fake DB connections
        DB::shouldReceive('connection->table->get')
            ->once()
            ->andReturn(collect([(object)['id' => 1, 'name' => 'From MX']]));
        DB::shouldReceive('connection->table->get')
            ->once()
            ->andReturn(collect([(object)['id' => 2, 'name' => 'From SG']]));

        $mock = Mockery::mock(CompanyService::class);
        $this->app->instance(CompanyService::class, $mock);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('results', function ($results) {
            // Both from MX and SG present as DTOs
            return $results->count() === 2 &&
                   $results[0] instanceof CompanyDTO &&
                   $results[1] instanceof CompanyDTO;
        });
    }

    public function pagination_slices_results_correctly()
    {
        // Fake DB returning more than 20 items
        $fakeRows = collect(range(1, 40))->map(function ($i) {
            return (object)['id' => $i, 'name' => 'Company '.$i];
        });

        // Each DB returns 20
        DB::shouldReceive('connection->table->get')
            ->once()
            ->andReturn($fakeRows->slice(0, 20)->values());
        DB::shouldReceive('connection->table->get')
            ->once()
            ->andReturn($fakeRows->slice(20, 20)->values());

        $mock = Mockery::mock(CompanyService::class);
        $this->app->instance(CompanyService::class, $mock);

        // Page 2
        $response = $this->get('/?page=2');

        $response->assertStatus(200);
        $response->assertViewHas('results', function ($results) {
            // Should have second slice of results
            return $results->count() === 20;
        });
    }
}
