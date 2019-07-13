<?php

use Tests\TestCase;
use Illuminate\Http\Request;
use LaravelEnso\Select\app\Traits\OptionsBuilder;
use Faker\Factory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class OptionTest extends TestCase
{
    use OptionsBuilder;

    private $testModel;

    private $faker;

    protected $queryAttributes = ['email', 'child.name'];

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

        $this->faker = Factory::create();

        $this->createTables();

        $this->testModel = $this->createParentModels();

        $this->createChildModel($this->testModel);

        $this->createChildModel($this->createParentModels());
    }

    /** @test */
    public function can_get_options_without_query_or_params()
    {
        $response = $this->requestResponse();

        $this->assertCount(ParentModel::count(), $response);

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
            'query' => $this->testModel->child->name,
        ]);

        $this->assertTrue($this->whithinResponse($response));
    }

    /** @test */
    public function can_get_options_with_pivot_params()
    {
        $response = $this->requestResponse([
            'pivotParams' => ['child' => ['name' => $this->testModel->child->name]]
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

        collect($params)->each(function ($value, $key) use ($request) {
            $request->merge([
                $key => is_array($value) ? json_encode($value) : $value
            ]);
        });

        return collect(
            $this->__invoke($request)->toResponse($request)
        );
    }

    public function query()
    {
        return ParentModel::query();
    }


    private function createParentModels()
    {
        return ParentModel::create([
            'email' => $this->faker->email,
        ]);
    }

    private function createChildModel($parent = null)
    {
        return ChildModel::create([
            'name' => $this->faker->name,
            'parent_id' => $parent !== null ? $parent->id : null,
        ]);
    }

    private function createTables()
    {
        Schema::create('parent_models', function ($table) {
            $table->increments('id');
            $table->string('email');
            $table->timestamps();
        });
        
        Schema::create('child_models', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('parent_id')->nullable();
            $table->timestamps();
        });
    }

}

class ParentModel extends Model
{
    protected $fillable = ['email'];

    public function child()
    {
        return $this->hasOne(ChildModel::class, 'parent_id');
    }
}

class ChildModel extends Model
{
    protected $fillable = ['name', 'parent_id'];
}