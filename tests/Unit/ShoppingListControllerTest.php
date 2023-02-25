<?php

namespace Tests\Unit;

use App\Models\ShoppingListItem;
use App\Repositories\ListRepository;
use Tests\TestCase;
use Mockery;
use Mockery\MockInterface;

class ShoppingListControllerTest extends TestCase
{
    public function test_controller_returns_populated_shopping_list(): void
    {
        $list = [new ShoppingListItem('Cherries')];
        $this->instance(
            ListRepository::class,
            Mockery::mock(ListRepository::class, function (MockInterface $mock) use ($list) {
                $mock->shouldReceive('getAll')->once()->andReturn($list);
            })
        );

        $this->call('GET', '/')
            ->assertViewHas('shoppingList', $list);
    }

    public function test_controller_adds_item_to_shopping_list_and_redirects_home(): void
    {
        $this->instance(
            ListRepository::class,
            Mockery::mock(ListRepository::class, function (MockInterface $mock) {
                $mock->shouldReceive('add')->once()->withArgs(function(ShoppingListItem $item) {
                    return $item->name === 'Sugar';
                });
                $mock->shouldReceive('getAll')->andReturns(
                    [new ShoppingListItem('Sugar')]
                );
            })
        );

        $this->call('POST', '/v1/item', ['name' => 'Sugar'])
            ->assertRedirect('/');
    }

    public function test_controller_deletes_items_from_shopping_list_and_redirects_home(): void
    {
        $sherbert = new ShoppingListItem('Sherbert');
        $this->instance(
            ListRepository::class,
            Mockery::mock(ListRepository::class, function (MockInterface $mock) use ($sherbert) {
                $mock->shouldReceive('remove')->withArgs([$sherbert->id]);
            })
        );

        $this->call('DELETE', '/v1/item/' . $sherbert->id)
            ->assertRedirect('/');
    }

    public function test_controller_marks_item_as_bought_and_redirects_home(): void
    {
        $candyfloss = new ShoppingListItem('Candyfloss');
        $this->instance(
            ListRepository::class,
            Mockery::mock(ListRepository::class, function (MockInterface $mock) use ($candyfloss) {
                $mock->shouldReceive('buy')->withArgs([$candyfloss->id]);
            })
        );

        $this->call('PATCH', '/v1/item/' . $candyfloss->id)
            ->assertRedirect('/');
    }
}
