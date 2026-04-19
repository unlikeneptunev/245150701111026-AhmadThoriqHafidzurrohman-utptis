<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItemController extends Controller
{
    private string $filePath;

    public function __construct()
    {
        $this->filePath = storage_path("app/data/items.json");
    }

    private function readData(): array
    {
        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, "[]");
        }
        return json_decode(file_get_contents($this->filePath), true) ?? [];
    }

    private function writeData(array $data): void
    {
        file_put_contents(
            $this->filePath,
            json_encode(array_values($data), JSON_PRETTY_PRINT),
        );
    }

    public function index()
    {
        $items = $this->readData();
        return response()->json($items, 200);
    }

    public function show($id)
    {
        $items = $this->readData();
        $item = collect($items)->firstWhere("id", (int) $id);

        if (!$item) {
            return response()->json(
                ["message" => "Item dengan ID $id tidak ditemukan"],
                404,
            );
        }

        return response()->json($item, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "price" => "required|numeric|min:0",
        ]);

        $items = $this->readData();

        $newItem = [
            "id" => count($items) > 0 ? max(array_column($items, "id")) + 1 : 1,
            "name" => $validated["name"],
            "price" => $validated["price"],
        ];

        $items[] = $newItem;
        $this->writeData($items);

        return response()->json($newItem, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "price" => "required|numeric|min:0",
        ]);

        $items = $this->readData();
        $index = collect($items)->search(
            fn($item) => $item["id"] === (int) $id,
        );

        if ($index === false) {
            return response()->json(
                ["message" => "Item dengan ID $id tidak ditemukan"],
                404,
            );
        }

        $items[$index] = [
            "id" => (int) $id,
            "name" => $validated["name"],
            "price" => $validated["price"],
        ];

        $this->writeData($items);
        return response()->json($items[$index], 200);
    }

    public function partialUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            "name" => "sometimes|string|max:255",
            "price" => "sometimes|numeric|min:0",
        ]);

        $items = $this->readData();
        $index = collect($items)->search(
            fn($item) => $item["id"] === (int) $id,
        );

        if ($index === false) {
            return response()->json(
                ["message" => "Item dengan ID $id tidak ditemukan"],
                404,
            );
        }

        $items[$index] = array_merge($items[$index], $validated);
        $this->writeData($items);
        return response()->json($items[$index], 200);
    }

    public function destroy($id)
    {
        $items = $this->readData();
        $index = collect($items)->search(
            fn($item) => $item["id"] === (int) $id,
        );

        if ($index === false) {
            return response()->json(
                ["message" => "Item dengan ID $id tidak ditemukan"],
                404,
            );
        }

        unset($items[$index]);
        $this->writeData($items);
        return response()->json(
            ["message" => "Item dengan ID $id berhasil dihapus"],
            200,
        );
    }
}
