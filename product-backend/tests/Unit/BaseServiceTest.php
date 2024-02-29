<?php

namespace Tests\Unit;

use App\Exceptions\UserException;
use App\Http\Requests\ProductInsertRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Requests\SearchObjects\ProductSearchObject;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Role;
use App\Models\User;
use App\Models\Variant;
use App\Services\ProductService;
use App\Services\VariantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Mockery;
use ReflectionMethod;
use Tests\TestCase;

class BaseServiceTest extends TestCase
{
    use RefreshDatabase;

    public ProductSearchObject $productSearchObject;
    public VariantService $variantService;

    public function setUp(): void
    {
        parent::setUp();
        $this->productSearchObject = new ProductSearchObject(['size' => 20]);
        $this->variantService = new VariantService();
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testPageable()
    {
        $this->seedData();

        $modelMock = Mockery::mock(Product::class);
        $modelMock->shouldReceive('query')->andReturnSelf();
        $modelMock->shouldReceive('paginate')->andReturn(new LengthAwarePaginator([], 0, 10, 1));

        App::shouldReceive('make')->with(Product::class)->andReturn($modelMock);

        $productService = new ProductService($this->variantService);

        $result = $productService->getPageable($this->productSearchObject);
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(10, $result->total());
    }

    public function testGetById()
    {
        $model = Product::factory()->create();
        $productService = new ProductService($this->variantService);

        $result = $productService->getById($model->id, $this->productSearchObject);
        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals($model->id, $result->id);

        $model->delete();
    }

    public function testGetByIdWithNonexistentId()
    {
        $nonexistentId = 999;
        $service = new ProductService($this->variantService);

        $this->expectException(UserException::class);
        $service->getById($nonexistentId, $this->productSearchObject);
    }

    public function testAdd()
    {
        $service = new ProductService($this->variantService);
        $requestData = [
            'name' => "test2000$",
	        'description' => "test21000$",
            'product_type_id' => 1,
            'validFrom' =>"2020-01-28 19:27:42.000",
            'validTo' => "2021-01-28 19:27:42.000"
        ];

        $request = new ProductInsertRequest($requestData);

        $result = $service->add($request);
        $this->assertInstanceOf(Product::class, $result);
        $this->assertDatabaseHas('products', $request->all());
        $result->delete();
    }

    public function testUpdate()
    {
        $this->seedData();

        $service = new ProductService($this->variantService);
        $product = Product::query()->first();

        $request = new ProductUpdateRequest([
            'name' => 'testProductName',
            'description' => 'testProductDescription'
        ]);

        $request->replace($request->all());

        $result = $service->update($request, $product->id);
        $this->assertDatabaseHas('products', $request->all());
        $this->assertEquals($result['name'], $request->all()['name']);
        $this->assertEquals($result['description'], $request->all()['description']);
    }

    public function testDelete()
    {
        $this->seedData();

        $service = new ProductService($this->variantService);
        $product = Product::query()->first();
        $service->remove($product->id);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function testAddFilter()
    {
        $service = new ProductService($this->variantService);
        $query = Product::query();
        $searchObject = new ProductSearchObject();
        $result = $service->addFilter($searchObject, $query);
        $this->assertSame($query, $result);
    }

    public function testIncludeRelation()
    {
        $service = new ProductService($this->variantService);
        $query = Product::query();
        $searchObject = new ProductSearchObject();
        $result = $service->includeRelation($searchObject, $query);
        $this->assertSame($query, $result);
    }

    public function testGetModelInstance()
    {
        $service = new ProductService($this->variantService);
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
