<?php

namespace Tests;

use App\Exceptions\UserException;
use App\Http\Requests\SearchObjects\BaseSearchObject;
use App\Http\Requests\SearchObjects\ProductSearchObject;
use App\Http\Requests\SearchObjects\RentalCarSearchObject;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\RentalCar;
use App\Models\Role;
use App\Models\User;
use App\Models\Variant;
use App\Services\BaseService;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Mockery;
use ReflectionMethod;

class BaseServiceTest extends TestCase
{
    use RefreshDatabase;

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testPageable()
    {
        $this->seedData();

        $searchObjectMock = Mockery::mock(ProductSearchObject::class);
        $searchObjectMock->shouldReceive('fill')->andReturnNull();
        $searchObjectMock->size = 10;

        $modelMock = Mockery::mock(Product::class);
        $modelMock->shouldReceive('query')->andReturnSelf();
        $modelMock->shouldReceive('paginate')->andReturn(new LengthAwarePaginator([], 0, 10, 1));

        App::shouldReceive('make')->with(ProductSearchObject::class)->andReturn($searchObjectMock);
        App::shouldReceive('make')->with(Product::class)->andReturn($modelMock);

        $productService = new ProductService();

        $result = $productService->getPageable();
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(10, $result->total());
    }

    public function testGetById()
    {
        $model = Product::factory()->create();
        $service = new ProductService();

        $result = $service->getById($model->id);
        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals($model->id, $result->id);

        $model->delete();
    }

    public function testGetByIdWithNonexistentId()
    {
        $nonexistentId = 999;
        $service = new ProductService();

        $this->expectException(UserException::class);
        $service->getById($nonexistentId);
    }

    public function testAdd()
    {
        $service = new ProductService();
        $requestData = [
            'name' => "test2000$",
	        'description' => "test21000$",
            'product_type_id' => 1,
            'validFrom' =>"2020-01-28 19:27:42.000",
            'validTo' => "2021-01-28 19:27:42.000"
        ];

        $result = $service->add($requestData);
        $this->assertInstanceOf(Product::class, $result);
        $this->assertDatabaseHas('products', $requestData);
        $result->delete();
    }

    public function testUpdate()
    {
        $this->seedData();

        $service = new ProductService();
        $product = Product::query()->first();

        $requestData = [
            'name' => 'testProductName',
            'description' => 'testProductDescription'
        ];


        $result = $service->update($requestData, $product->id);
        $this->assertDatabaseHas('products', $requestData);
        $this->assertEquals($result['name'], $requestData['name']);
        $this->assertEquals($result['description'], $requestData['description']);
    }

    public function testDelete()
    {
        $this->seedData();

        $service = new ProductService();
        $product = Product::query()->first();
        $service->remove($product->id);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function testAddFilter()
    {
        $service = new ProductService();
        $query = Product::query();
        $searchObject = new ProductSearchObject();
        $result = $service->addFilter($searchObject, $query);
        $this->assertSame($query, $result);
    }

    public function testGetSearchObject()
    {
        $service = new ProductService();
        $expectedSearchObj = new ProductSearchObject();
        $result = $service->getSearchObject();
        $this->assertEquals($expectedSearchObj, $result);
    }

    public function testIncludeRelation()
    {
        $service = new ProductService();
        $query = Product::query();
        $searchObject = new ProductSearchObject();
        $result = $service->includeRelation($searchObject, $query);
        $this->assertSame($query, $result);
    }

    public function testGetModelInstance()
    {
        $service = new ProductService();
        $expectedModelClass = new Product();
        $reflectionMethod = new ReflectionMethod($service, 'getModelInstance');
        $result = $reflectionMethod->invoke($service);
        $this->assertEquals($expectedModelClass, $result);
    }

    public function seedData()
    {
        ProductType::create([
            "name" => "Test1",
            "description" => "Test1"
        ]);

        Role::create([
            "name" => "admin",
        ]);

        User::factory(10)->create();
        Product::factory(10)->create();
        Variant::factory(10)->create();
    }
}
