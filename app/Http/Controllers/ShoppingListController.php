<?php

namespace App\Http\Controllers;

use App\Models\ShoppingListItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Illuminate\Http\Request;

use App\Repositories\ListRepository;

class ShoppingListController extends Controller
{
    public function showAll(ListRepository $listRepository): View
    {
        $shoppingList = $listRepository->getAll();

        return view('index', ['shoppingList' => $shoppingList]);
    }

    public function add(Request $request, ListRepository $listRepository): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $listRepository->add(
            new ShoppingListItem($validated['name'])
        );

        return redirect('/');
    }

    public function delete(ListRepository $listRepository, string $id): RedirectResponse
    {
        // if (!$listRepository->has($id)) {
            // TODO: 404 if not found
        // }

        $listRepository->remove($id);

        return redirect('/');
    }

    public function update(Request $request, ListRepository $listRepository, string $id): RedirectResponse
    {
        // if (!$listRepository->has($id)) {
            // TODO: 404 if not found
        // }

        $listRepository->buy($id);

        return redirect('/');
    }
}
