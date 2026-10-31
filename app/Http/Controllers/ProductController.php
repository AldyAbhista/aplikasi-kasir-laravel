<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;
use App\Models\Product;
use App\Models\ActivityLog;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->get(); 
        return view('products.index', compact('products')); 
    }

    /**
     * Generate the first available product code from active products.
     * Format: P001, P002, P003, dst.
     */
    private function generateKodeBarang(bool $includeTrashed = false): string
    {
        $products = $includeTrashed ? Product::withTrashed() : Product::query();
        $usedNumbers = $products
            ->where('kode_barang', 'like', 'P%')
            ->pluck('kode_barang')
            ->map(static function (string $code): ?int {
                if (preg_match('/^P(\d+)$/', $code, $matches) !== 1) {
                    return null;
                }

                return (int) $matches[1];
            })
            ->filter(static fn (?int $number): bool => $number !== null)
            ->all();

        $usedNumbers = array_fill_keys($usedNumbers, true);
        $nextNumber = 1;

        while (isset($usedNumbers[$nextNumber])) {
            $nextNumber++;
        }

        return 'P' . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function create()
    {
        $nextKode = $this->generateKodeBarang();
        return view('products.create', compact('nextKode'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ], [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'harga_beli.required' => 'Harga modal/beli wajib diisi.',
            'harga_jual.required' => 'Harga jual wajib diisi.',
            'stok.required' => 'Jumlah stok awal wajib diisi.',
            'stok.min' => 'Stok tidak boleh bernilai negatif.',
        ]);

        // Auto-generate kode barang
        $validatedData['kode_barang'] = $this->generateKodeBarang();

        try {
            $product = Product::create($validatedData);
        } catch (QueryException $exception) {
            if (! $this->isDuplicateProductCodeException($exception)) {
                throw $exception;
            }

            $validatedData['kode_barang'] = $this->generateKodeBarang(true);
            $product = Product::create($validatedData);
        }

        ActivityLog::record(
            action: 'create_product',
            description: "Admin " . auth()->user()->name . " menambahkan produk baru: {$product->nama_barang} ({$product->kode_barang}) dengan stok {$product->stok}.",
            model: $product
        );

        return redirect()->route('products.index')->with('success', 'Barang berhasil ditambahkan dengan kode: ' . $validatedData['kode_barang']);
    }

    private function isDuplicateProductCodeException(QueryException $exception): bool
    {
        return $exception->getCode() === '23000'
            && str_contains($exception->getMessage(), 'kode_barang');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validatedData = $request->validate([
            'kode_barang' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'kode_barang')
                    ->ignore($product->id)
                    ->whereNull('deleted_at'),
            ],
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ], [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'harga_beli.required' => 'Harga beli wajib diisi.',
            'harga_jual.required' => 'Harga jual wajib diisi.',
            'stok.min' => 'Stok tidak boleh negatif.',
        ]);

        $oldValues = $product->only(['kode_barang', 'nama_barang', 'kategori', 'harga_beli', 'harga_jual', 'stok']);

        $product->update($validatedData);

        ActivityLog::record(
            action: 'update_product',
            description: "Admin " . auth()->user()->name . " memperbarui data produk {$product->nama_barang} ({$product->kode_barang}).",
            model: $product,
            oldValues: $oldValues,
            newValues: $product->only(['kode_barang', 'nama_barang', 'kategori', 'harga_beli', 'harga_jual', 'stok'])
        );

        return redirect()->route('products.index')->with('success', 'Data barang berhasil diubah!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        ActivityLog::record(
            action: 'delete_product',
            description: "Admin " . auth()->user()->name . " menghapus produk {$product->nama_barang} ({$product->kode_barang}).",
            model: $product
        );

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Barang berhasil dihapus!');
    }
}