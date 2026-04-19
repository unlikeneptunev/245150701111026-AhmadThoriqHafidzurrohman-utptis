<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[
    OA\Info(
        title: "UTP Teknologi Integrasi Sistem - TI A",
        version: "1.0.0",
        description: "Ecommerce-like Backend API menggunakan Laravel dengan mock data JSON<br>Nama: Ahmad Thoriq Hafidzurrohman<br>NIM: 245150701111026",
    ),
]
#[
    OA\Server(
        url: "http://localhost:8000",
        description: "Local Development Server",
    ),
]
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

    #[
        OA\Get(
            path: "/api/items",
            summary: "Tampilkan semua item",
            tags: ["Items"],
            responses: [
                new OA\Response(
                    response: 200,
                    description: "Daftar semua item berhasil ditampilkan",
                ),
            ],
        ),
    ]
    public function index()
    {
        $items = $this->readData();
        return response()->json($items, 200);
    }

    #[
        OA\Get(
            path: "/api/items/{id}",
            summary: "Tampilkan item berdasarkan ID",
            tags: ["Items"],
            parameters: [
                new OA\Parameter(
                    name: "id",
                    in: "path",
                    required: true,
                    schema: new OA\Schema(type: "integer"),
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: "Item berhasil ditemukan",
                ),
                new OA\Response(
                    response: 404,
                    description: "Item tidak ditemukan",
                ),
            ],
        ),
    ]
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

    #[
        OA\Post(
            path: "/api/items",
            summary: "Tambah item baru",
            tags: ["Items"],
            requestBody: new OA\RequestBody(
                required: true,
                content: new OA\JsonContent(
                    required: ["name", "price"],
                    properties: [
                        new OA\Property(
                            property: "name",
                            type: "string",
                            example: "Google Pixel 10",
                        ),
                        new OA\Property(
                            property: "price",
                            type: "number",
                            example: 13500000,
                        ),
                    ],
                ),
            ),
            responses: [
                new OA\Response(
                    response: 201,
                    description: "Item berhasil ditambahkan",
                ),
                new OA\Response(response: 422, description: "Validasi gagal"),
            ],
        ),
    ]
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

    #[
        OA\Put(
            path: "/api/items/{id}",
            summary: "Update seluruh data item",
            tags: ["Items"],
            parameters: [
                new OA\Parameter(
                    name: "id",
                    in: "path",
                    required: true,
                    schema: new OA\Schema(type: "integer"),
                ),
            ],
            requestBody: new OA\RequestBody(
                required: true,
                content: new OA\JsonContent(
                    required: ["name", "price"],
                    properties: [
                        new OA\Property(
                            property: "name",
                            type: "string",
                            example: "Sepatu Adidas",
                        ),
                        new OA\Property(
                            property: "price",
                            type: "number",
                            example: 600000,
                        ),
                    ],
                ),
            ),
            responses: [
                new OA\Response(
                    response: 200,
                    description: "Item berhasil diupdate",
                ),
                new OA\Response(
                    response: 404,
                    description: "Item tidak ditemukan",
                ),
            ],
        ),
    ]
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

    #[
        OA\Patch(
            path: "/api/items/{id}",
            summary: "Update sebagian data item",
            tags: ["Items"],
            parameters: [
                new OA\Parameter(
                    name: "id",
                    in: "path",
                    required: true,
                    schema: new OA\Schema(type: "integer"),
                ),
            ],
            requestBody: new OA\RequestBody(
                required: true,
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "name",
                            type: "string",
                            example: "Sepatu Puma",
                        ),
                        new OA\Property(
                            property: "price",
                            type: "number",
                            example: 750000,
                        ),
                    ],
                ),
            ),
            responses: [
                new OA\Response(
                    response: 200,
                    description: "Item berhasil diupdate sebagian",
                ),
                new OA\Response(
                    response: 404,
                    description: "Item tidak ditemukan",
                ),
            ],
        ),
    ]
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

    #[
        OA\Delete(
            path: "/api/items/{id}",
            summary: "Hapus item",
            tags: ["Items"],
            parameters: [
                new OA\Parameter(
                    name: "id",
                    in: "path",
                    required: true,
                    schema: new OA\Schema(type: "integer"),
                ),
            ],
            responses: [
                new OA\Response(
                    response: 200,
                    description: "Item berhasil dihapus",
                ),
                new OA\Response(
                    response: 404,
                    description: "Item tidak ditemukan",
                ),
            ],
        ),
    ]
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
