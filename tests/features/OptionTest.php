<?php

use Illuminate\Http\Request;
use LaravelEnso\Forms\app\TestTraits\DestroyForm;
use LaravelEnso\Forms\app\TestTraits\EditForm;
use LaravelEnso\Select\app\Traits\OptionsBuilder;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use LaravelEnso\Core\app\Models\User;
use LaravelEnso\Core\app\Models\UserGroup;
use LaravelEnso\DataImport\app\Enums\Statuses;
use LaravelEnso\DataImport\app\Models\DataImport;
use LaravelEnso\DataImport\app\Services\Template;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelEnso\Tables\app\Traits\Tests\Datatable;

class OptionTest extends TestCase
{
    use OptionsBuilder, RefreshDatabase;

    private $testModel;
    private $queryAttributes=["id"];

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed()
            ->actingAs(User::first());

        $this->testModel = factory(User::class)
            ->create();
    }

    public function tearDown(): void
    {
//        $this->cleanUp();
        parent::tearDown();
    }

    /** @test */
    public function select_without_condition()
    {
        $response = $this->sendRequest([]);
        $this->assertCount(User::get()->count(),$response);
        $this->assertTrue($response->pluck("id")->contains($this->testModel->id));
    }

    /** @test */
    public function select_with_empty_result()
    {
        $response = $this->sendRequest([
            "params"=>json_encode(["id"=>null])
        ]);
        $this->assertCount(0,$response);
    }

    /** @test */
    public function select_with_select_and_no_query()
    {
        $response = $this->sendRequest([
            "value"=> $this->testModel->id,
            "query"=>"NO_NAME",
        ]);
        $this->assertCount(1,$response);
    }

    /** @test */
    public function select_with_query_and_no_select()
    {
        $response = $this->sendRequest([
            "query"=> $this->testModel->id,
            "trackBy"=>"id"
        ]);
        $this->assertCount(User::whereId($this->testModel->id)->get()->count(),$response);
    }

    /** @test */
    public function select_with_nested_query()
    {
        $this->queryAttributes = ["person.id"];
        $response = $this->sendRequest([
            "query"=> $this->testModel->person_id,
            "trackBy"=>"id"
        ]);
        $this->assertEqualsResults(User::wherePersonId($this->testModel->person_id),$response);
    }
    /** @test */
    public function select_with_pivot()
    {
        $this->queryAttributes = ["person.id"];
        $response = $this->sendRequest([
            "query"=> $this->testModel->person_id,
            "pivotParams"=>json_encode([
                    "person"=>[
                        "id"=>$this->testModel->person_id,
                    ]
                ]
            )        ]);
        $this->assertEqualsResults(User::wherePersonId($this->testModel->person_id),$response);
    }

    /** @test */
    public function select_with_order()
    {
        $this->testModel->created_at = "2012-12-12";
        $this->testModel->save();
        $this->queryAttributes = ["created_at"];
        $response = $this->sendRequest([]);
        $this->assertEquals(
            User::orderBy("created_at")->get()->pluck("id")->values()->toArray(),
            $response->pluck("id")->values()->toArray()
        );
    }


    /** @test */
    public function select_with_limit()
    {
        $response = $this->sendRequest([
            "paginate"=>1
        ]);
        $this->assertCount(1, $response);
    }




    public function query(Request $request)
    {
        return User::query();
    }

    /**
     * @param array $params
     * @return \Illuminate\Support\Collection
     */
    private function sendRequest(array $params =[])
    {
        $request = $this->createRequest($params);
        $response = collect(json_decode($this->__invoke($request)->toResponse($request), true));
        return $response;
    }

    private function createRequest(
        $content,
        $method = "post",
        $uri = '/test',
        $server = ['CONTENT_TYPE' => 'application/json'],
        $parameters = [],
        $cookies = [],
        $files = []
    ) {
        return Request::createFromBase(\Symfony\Component\HttpFoundation\Request::create($uri, $method, $parameters, $cookies, $files, $server, json_encode($content)));
    }

    private function assertEqualsResults($query, $response)
    {
        $this->assertEquals(
            $query->get()->pluck("id")->values()->toArray(),
            $response->pluck("id")->values()->toArray()
        );
    }

}
