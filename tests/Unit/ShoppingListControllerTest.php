<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\ShoppingListItem;

class ShoppingListControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_controller_returns_populated_shopping_list(): void
    {
        ShoppingListItem::create(['name' => 'Cherries']);

        $items = ShoppingListItem::all();

        $this->call('GET', '/')
            ->assertViewHas('shoppingList', $items);
    }

    public function test_controller_returns_total_cost(): void
    {
        ShoppingListItem::create(['name' => 'Swede', 'price' => 0.17]);
        ShoppingListItem::create(['name' => 'Carrot', 'price' => 0.11]);

        $this->call('GET', '/')
            ->assertViewHas('totalCost', 0.28);
    }

    public function test_controller_adds_item_to_shopping_list_and_redirects_home(): void
    {
        $this->call(
            'POST',
            '/v1/item',
            ['name' => 'Sugar', 'price' => '0.95']
        )->assertRedirect('/');

        $this->assertDatabaseHas(
            'shoppinglistitems',
            ['name' => 'Sugar', 'price' => '0.95']
        );
    }

    public function test_controller_deletes_items_from_shopping_list_and_redirects_home(): void
    {
        $sherbert = ShoppingListItem::create(['name' => 'Sherbert']);

        $this->call('DELETE', '/v1/item/' . $sherbert->id)
            ->assertRedirect('/');

        $this->assertDatabaseMissing('shoppinglistitems', [
            'id' => $sherbert->id,
            'deleted_at' => null, // required because of soft deletes
        ]);
    }

    public function test_controller_marks_item_as_bought_and_redirects_home(): void
    {
        $candyfloss = ShoppingListItem::create(['name' => 'Candyfloss', 'isBought' => false ]);

        $this->call('POST', '/v1/item/' . $candyfloss->id . '/bought')
            ->assertRedirect('/');

        $this->assertDatabaseHas('shoppinglistitems', [
            'id' => $candyfloss->id,
            'isBought' => true,
        ]);
    }

    public function test_controller_updates_item_ordering_and_redirects_home(): void
    {
        $cucumber = ShoppingListItem::create(['name' => 'Cucumber', 'order' => 0 ]);
        $cress = ShoppingListItem::create(['name' => 'Cress', 'order' => 1 ]);

        $this->call('PATCH', '/v1/list', [
            'order' => [ $cress->id, $cucumber->id ],
        ])
            ->assertRedirect('/');

        $this->assertDatabaseHas('shoppinglistitems', [
            'id' => $cress->id,
            'order' => 0,
        ]);
        $this->assertDatabaseHas('shoppinglistitems', [
            'id' => $cucumber->id,
            'order' => 1,
        ]);
    }
}
