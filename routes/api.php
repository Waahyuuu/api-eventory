<?php

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Transaksi;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/barang', function (Request $request) {
    $data = Barang::get();

    return response()->json($data);
});

Route::post('/barang', function (Request $request) {

    $request->validate([
        'images' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120'
    ]);

    $barang = new Barang();
    $barang->namaBarang = $request->input('namaBarang');
    $barang->kategori = $request->input('kategori');
    $barang->stok = $request->input('stok');
    $barang->deskripsi = $request->input('deskripsi');

    if ($request->hasFile('images')) {
        $file = $request->file('images');
        $filename = time() . '_' . $file->getClientOriginalName();

        $file->move(public_path('storage/images'), $filename);

        $barang->images = $filename;
    }

    $barang->save();
    return response()->json($barang);
});

Route::get('/barang/{id}', function ($id) {
    $barang = Barang::find($id);

    if ($barang) {
        return response()->json($barang);
    } else {
        return response()->json(['message' => 'Barang tidak ditemukan'], 404);
    }
});

Route::put('/barang/{id}', function (Request $request, $id) {

    $request->validate([
        'images' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120'
    ]);

    $barang = Barang::find($id);

    if (!$barang) {
        return response()->json(['message' => 'Barang yang ingin diubah tidak ditemukan'], 404);
    }

    $barang->namaBarang = $request->input('namaBarang');
    $barang->kategori = $request->input('kategori');
    $barang->stok = $request->input('stok');
    $barang->deskripsi = $request->input('deskripsi');

    if ($request->hasFile('images')) {

        if ($barang->images && file_exists(public_path('storage/images/' . $barang->images))) {
            unlink(public_path('storage/images/' . $barang->images));
        }

        $file = $request->file('images');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('storage/images'), $filename);

        $barang->images = $filename;
    }

    $barang->save();

    return response()->json($barang);
});

Route::delete('/barang/{id}', function ($id) {
    $barang = Barang::find($id);

    if (!$barang) {
        return response()->json(['message' => 'Barang yang ingin dihapus tidak ditemukan'], 404);
    }

    if ($barang->images && file_exists(public_path('storage/images/' . $barang->images))) {
        unlink(public_path('storage/images/' . $barang->images));
    }

    $barang->delete();

    return response()->json(['message' => 'Barang dan gambar berhasil dihapus']);
});

// Bagian Transaksi Barang Guys

Route::post('/transaksi', function (Request $request) {

    $request->validate([
        'barang_id' => 'required|exists:barang,id',
        'qty' => 'required|integer|min:1',
        'jenis_transaksi' => 'required|in:masuk,keluar',
        'catatan' => 'nullable|string'
    ]);

    $barang = Barang::find($request->barang_id);

    $stokSebelum = $barang->stok;

    if ($request->jenis_transaksi === 'keluar') {
        if ($barang->stok < $request->qty) {
            return response()->json(['message' => 'Stok tidak cukup'], 400);
        }
        $barang->stok -= $request->qty;
    } else {
        $barang->stok += $request->qty;
    }

    $stokSesudah = $barang->stok;
    $barang->save();

    $transaksi = Transaksi::create([
        'barang_id' => $barang->id,
        'qty' => $request->qty,
        'jenis_transaksi' => $request->jenis_transaksi,
        'stok_sebelum' => $stokSebelum,
        'stok_sesudah' => $stokSesudah,
        'catatan' => $request->catatan
    ]);

    return response()->json($transaksi);
});

Route::get('/transaksi', function () {

    return response()->json(
        Transaksi::with('barang')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'nama_barang' => $t->barang?->namaBarang ?? 'Barang telah dihapus',
                    'qty' => $t->qty,
                    'jenis_transaksi' => $t->jenis_transaksi,
                    'stok_sebelum' => $t->stok_sebelum,
                    'stok_sesudah' => $t->stok_sesudah,
                    'catatan' => $t->catatan,
                    'tanggal' => $t->created_at
                ];
            })
    );
});
