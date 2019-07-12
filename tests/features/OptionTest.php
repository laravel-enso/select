<?php
use Tests\TestCase;
use Illuminate\Http\Request;
use LaravelEnso\Core\app\Models\User;
use LaravelEnso\Select\app\Traits\OptionsBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OptionTest extends TestCase
{
    use OptionsBuilder, RefreshDatabase;

    private $testModel;

    protected $queryAttributes = ['email', 'person.name'];

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

        $this->seed()
            ->actingAs(User::first());

        $this->testModel = factory(User::class)
            ->create();
    }

    /** @test */
    public function can_get_options_without_query_or_params()
    {
        $response = $this->requestResponse([]);

        $this->assertCount(User::count(), $response);

        $this->assertTrue($response->pluck('id')->contains($this->testModel->id));
    }

    /** @test */
    public function can_get_empty_options_with_restricting_params()
    {
        $response = $this->requestResponse(['params' => ['id' => 0]]);

        $this->assertCount(0, $response);
    }
    
    /** @test */
    public function can_get_empty_options_with_restricting_query()
    {
        $response = $this->requestResponse(['query' => 'NO_NAME']);

        $this->assertCount(0, $response);
    }

    /** @test */
    public function can_get_selected_options_with_restricting_query()
    {
        $response = $this->requestResponse([
            'value' => $this->testModel->id,
            'query' => 'NO_NAME',
        ]);

        $this->assertCount(1, $response);

        $this->assertTrue($this->whithinResponse($response));
    }

    /** @test */
    public function can_get_filtered_options()
    {
        $response = $this->requestResponse([
            'query' => $this->testModel->email,
        ]);

        $this->assertTrue($this->whithinResponse($response));
    }

    /** @test */
    public function can_get_filtered_on_nested_attrs_options()
    {
        $response = $this->requestResponse([
            'query' => $this->testModel->person->name,
        ]);

        $this->assertTrue($this->whithinResponse($response));
    }

    /** @test */
    public function can_get_options_with_pivot_params()
    {
        $response = $this->requestResponse([
            'pivotParams' => ['role' => ['name' => $this->testModel->role->name]]
        ]);

        $this->assertTrue($this->whithinResponse($response));
    }

    /** @test */
    public function can_paginate()
    {
        $paginate = 0;

        $response = $this->requestResponse(['paginate' => $paginate]);

        $this->assertCount($paginate, $response);
    }

    private function whithinResponse($response)
    {
        return $response->pluck('email')
            ->contains($this->testModel->email);
    }

    private function requestResponse(array $params = [])
    {
        $request = new Request();

        collect($params)->each(function($value, $key) use ($request) {
            $request->merge([
                $key => is_array($value) ? json_encode($value) : $value
            ]);
        });

        return collect(
            $this->__invoke($request)->toResponse($request)
        );
    }

    public function query(Request $request)
    {
        return User::query();
    }
}
