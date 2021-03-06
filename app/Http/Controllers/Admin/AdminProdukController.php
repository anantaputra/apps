<?php

namespace App\Http\Controllers\Admin;

use App\Models\Produk;
use App\Models\Paket;
use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GambarPaket;
use App\Models\PaketDetail;
use Carbon\Carbon;

class AdminProdukController extends Controller
{
    public function index()
    {
        $produk = Produk::all();
        $pakets = Paket::all();

        return view('admin.produk.index', compact('pakets', 'produk'));
    }

    public function tambahProduk()
    {
        return view('admin.produk.tambah-produk');
    }

    public function editProduk($id)
    {
        $produk = Produk::find($id);

        return view('admin.produk.tambah-produk', compact('produk'));
    }

    public function tambahPaket()
    {
        $kategoris = Kategori::all();

        $produks = Produk::all();

        return view('admin.produk.tambah-paket', compact('kategoris', 'produks'));
    }

    public function editPaket($id)
    {
        $paket= Paket::find($id);

        $produks = Produk::all();

        $kategoris = Kategori::all();

        return view('admin.produk.tambah-paket', compact('paket', 'produks', 'kategoris'));
    }

    public function storePaket(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'jml_tamu' => 'required',
            'harga' => 'required|numeric',
            'kategori' => 'required',
            'gambar_paket' => 'required',
        ]);

        if($request->file('gambar_paket')){
            $paket = new Paket();
            $paket->id_kategori = $request->kategori;
            $paket->nama = $request->nama;
            $paket->harga = $request->harga;
            $paket->jml_tamu = $request->jml_tamu;
            $paket->save();

            $paket_detail = new PaketDetail();
            $paket_detail->id_paket = $paket->id;
            $paket_detail->isi_paket = $request->detail;
            $paket_detail->save();

                foreach($request->file('gambar_paket') as $img){
                    $filename = $img->getClientOriginalName();
                    $img->move(public_path(). '/paket/detail', $filename);
                    $gambar = new GambarPaket();
                    $gambar->id_paket = $paket->id;
                    $gambar->img = $filename;
                    $gambar->save();
                }
            return redirect()->route('admin.produk');
        }
    }

    public function updatePaket(Request $request)
    {
        $paket = Paket::find($request->id);

        $paket->nama = $request->nama;
        $paket->harga = $request->harga;
        $paket->jml_tamu = $request->jml_tamu;
        $paket->id_kategori = $request->kategori;
        $paket->save();

        return redirect()->route('admin.produk');
    }

    public function DeletePaket($id)
    {
        $paket = Paket::find($id);
        $paket->delete();

        return redirect()->route('admin.produk');
    }

    public function storeProduk(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'harga' => 'required|numeric',
        ]);

        $produk = new Produk();
        // jika ada gambarnya baru bisa masuk ke database
        if($request->file('gambar')){
            $file = $request->file('gambar');
            $filename = date('YmdHi').$file->getClientOriginalName(); //buat nama filenya
            $file-> move(public_path('public/produk'), $filename); //simpan fotonya ke folder public/produkk

            $produk->img = $filename;
        }
        // simpan ke databasenya
        // sesuai nama kolom table databse =  sesuai nama form input
        $produk->nama_produk = $request->nama;
        $produk->harga = $request->harga;
        $produk->save();
        
        return redirect()->route('admin.produk');
    }

    public function updateProduk(Request $request)
    {
        $produk = Produk::find($request->id);

        $produk->nama_produk = $request->nama;
        $produk->harga = $request->harga;
        $produk->save();

        return redirect()->route('admin.produk');
    }

    public function DeleteProduk($id)
    {
        $produks = Produk::find($id);
        $produks->delete();

        return redirect()->route('admin.produk');
    }
}
