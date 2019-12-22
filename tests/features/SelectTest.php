<?php

use Faker\Factory;
use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use LaravelEnso\Select\app\Traits\OptionsBuilder;

class SelectTest extends TestCase
{
    use OptionsBuilder;

    private $testModel;
    private $faker;

    protected $queryAttributes = ['email', 'relation.name'];

    public function setUp(): void
    {
        parent::setUp();

        // $this->withoutExceptionHandling();

        $this->faker = Factory::create();

        $this->createTestModelTable();
        $this->createRelationTable();

        $this->testModel = $this->createTestModel();
        $this->createRelation();
    }

    /** @test */
    public function can_get_options_without_query_or_params()
    {
        $response = $this->requestResponse();

        $this->assertCount(SelectTestModel::count(), $response);

        $this->assertTrue($this->whithinResponse($response));
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
        $response = $this->requestResponse(['query' => 'NO_VALUE']);

        $this->assertCount(0, $response);
    }

    /** @test */
    public function can_get_selected_options_with_restricting_query()
    {
        $response = $this->requestResponse([
            'value' => $this->testModel->id,
            'query' => 'NO_VALUE',
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
            'query' => $this->testModel->relation->name,
        ]);
        $this->assertTrue($this->whithinResponse($response));
    }

    /** @test */
    public function can_get_options_with_pivot_params()
    {
        $response = $this->requestResponse([
            'pivotParams' => [
                'relation' => ['name' => $this->testModel->relation->name]
            ]
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

        collect($params)->each(fn($value, $key) => (
            $request->merge([
                $key => is_array($value) ? json_encode($value) : $value
            ])
        ));

        return collect(
            $this->__invoke($request)->toResponse($request)
        );
    }

    public function query()
    {
        return SelectTestModel::query();
    }

    private function createTestModel()
    {
        return SelectTestModel::create([
            'email' => $this->faker->email,
        ]);
    }

    private function createRelation()
    {
        return SelectRelation::create([
            'name' => $this->faker->name,
            'parent_id' => $this->testModel->id,
        ]);
    }

    private function createTestModelTable()
    {
        Schema::create('select_test_models', function ($table) {
            $table->increments('id');
            $table->string('email');
            $table->timestamps();
        });
    }

    private function createRelationTable()
    {
        Schema::create('select_relations', function ($table) {
            $table->increments('id');
            $table->integer('parent_id');
            $table->foreign('parent_id')->references('id')->on('select_test_models');
            $table->string('name');
            $table->timestamps();
        });
    }
}

class SelectTestModel extends Model
{
    protected $fillable = ['email'];

    public function relation()
    {
        return $this->hasOne(SelectRelation::class, 'parent_id');
    }
}

class SelectRelation extends Model
{
    protected $fillable = ['name', 'parent_id'];
}
