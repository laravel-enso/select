<?php

use Faker\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use LaravelEnso\Select\Traits\OptionsBuilder;
use Tests\TestCase;

class SelectTest extends TestCase
{
    use OptionsBuilder;

    private $testModel;
    private $faker;
    protected $queryAttributes = ['email', 'relation.name'];
    protected $resource = null;
    protected $appends = null;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create();

        $this->createTestModelTable();
        $this->createRelationTable();

        $this->testModel = $this->createTestModel();

        $this->createRelation();
    }

    /** @test */
    public function can_get_options_without_filters()
    {
        $response = $this->requestResponse();

        $this->assertCount(SelectTestModel::count(), $response);
        $this->assertTrue($this->whithinResponse($response));
    }

    /** @test */
    public function can_get_empty_options_with_restricting_query()
    {
        $response = $this->requestResponse(['query' => 'NO_VALUE']);

        $this->assertCount(0, $response);
    }

    /** @test */
    public function can_get_empty_options_with_restricting_params()
    {
        $response = $this->requestResponse(['params' => ['id' => 0]]);

        $this->assertCount(0, $response);
    }

    /** @test */
    public function can_get_empty_options_with_restricting_pivot_params()
    {
        $response = $this->requestResponse([
            'pivotParams' => ['relation' => ['id' => 0]],
        ]);

        $this->assertCount(0, $response);
    }

    /** @test */
    public function can_get_selected_options_with_restricting_filter()
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
    public function can_get_options_with_param()
    {
        $response = $this->requestResponse([
            'params' => ['email' => $this->testModel->email],
        ]);

        $this->assertTrue($this->whithinResponse($response));
    }

    /** @test */
    public function can_get_options_with_pivot_params()
    {
        $response = $this->requestResponse([
            'pivotParams' => ['relation' => ['name' => $this->testModel->relation->name]],
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

    /** @test */
    public function can_use_resource()
    {
        $this->resource = SelectTestResource::class;
        $response = $this->requestResponse();

        $this->assertCount(SelectTestModel::count(), $response);

        $this->assertTrue(
            $response->first()->resolve()['resource'] === 'resource'
        );
    }

    /** @test */
    public function can_use_accessors()
    {
        $this->appends = ['custom'];
        $response = $this->requestResponse();

        $this->assertCount(SelectTestModel::count(), $response);
        $this->assertTrue(
            $response->first()->toArray()['custom'] === $this->testModel->custom
        );
    }

    private function whithinResponse($response)
    {
        return $response->pluck('email')
            ->contains($this->testModel->email);
    }

    private function requestResponse(array $params = [])
    {
        $request = new Request();

        Collection::wrap($params)->each(fn ($value, $key) => $request->merge([
            $key => is_array($value) ? json_encode($value) : $value,
        ]));

        return new Collection(
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

    public function getCustomAttribute()
    {
        return 'custom';
    }
}
class SelectRelation extends Model
{
    protected $fillable = ['name', 'parent_id'];
}

class SelectTestResource extends JsonResource
{
    public function toArray($request)
    {
        return ['resource' => 'resource'];
    }
}
