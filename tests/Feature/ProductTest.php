<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    //use RefreshDatabase;

    /**
     *  Test if products index returns the expected static list
     *
    public function test_can_list_products()
    {
        $response = $this->get(route('products'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'products' => [
                    ['id', 'name', 'origin'],
                ],
            ]);
            /*->assertJsonFragment(['id' => 1, 'name' => 'matooke', 'origin' => 'uganda'])
            ->assertJsonFragment(['id' => 2, 'name' => 'rice', 'origin' => 'india'])
            ->assertJsonFragment(['id' => 3, 'name' => 'beans', 'origin' => 'burundi']);*

        // Ensure exactly 3 products are returned
        $data = $response->json();
        $this->assertArrayHasKey('products', $data);
        $this->assertCount(3, $data['products']);
    }

    /**
        Test if add product form shows
     *
    public function test_can_show_add_product_form() {
        $response = $this->get(route('add-product-form'));
        $response->assertStatus(200);

    }
    /**
     * Test if a product can be added
     *
    public function test_can_add_product()
    {

        $response = $this->post(route('add-product'), [
            'name' => 'carrot', 'origin' => 'Whales'
        ]);

        $response->assertStatus(200)
            ->assertSessionHasNoErrors()
            ->assertJsonStructure([
                'product' => ['id', 'name', 'origin'],
                'message'
            ]);
    }

    /**
     *  Test if an image can be uploaded
     *
    public function test_can_upload_image()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->post(route('add_product_photo'),[
            'image' => $file
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'file_path', 'message', 'url'
            ]);
    }

    /**
     *  Test if a product name is required
     *
    public function test_product_name_is_required()
    {
        $response = $this->post(route('add-product'),[
            'origin' => 'Tz'
        ]);

        $response->assertStatus(302)
            ->assertSessionHasNoErrors()
            ->assertJsonValidationErrors(['name']);
    }
     * */

    /**
     * Test if edit product form shows
     *
    public function test_can_show_edit_product_form()
    {
        $id = random_int(1,4);
        $response = $this->get(route('edit-product-form-id',$id));
        $response->assertStatus(200);
    }
      */
}
