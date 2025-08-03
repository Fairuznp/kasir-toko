<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Services\PelangganService;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    protected $pelangganService;

    public function __construct(PelangganService $pelangganService)
    {
        $this->pelangganService = $pelangganService;
    }

    public function index(Request $request)
    {
        $pelanggans = $this->pelangganService->getAllPelanggan($request->search);

        return view('pelanggan.index', [
            'pelanggans' => $pelanggans
        ]);
    }

    public function create()
    {
        return view('pelanggan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'max:100'],
            'alamat' => ['nullable', 'max:500'],
            'nomor_tlp' => ['nullable', 'max:14']
        ]);

        $this->pelangganService->createPelanggan($request->all());

        return redirect()->route('pelanggan.index')->with('store', 'success');
    }

    public function show(Pelanggan $pelanggan)
    {
        abort(404);
    }

    public function edit(Pelanggan $pelanggan)
    {
        return view('pelanggan.edit', [
            'pelanggan' => $pelanggan
        ]);
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $request->validate([
            'nama' => ['required', 'max:100'],
            'alamat' => ['nullable', 'max:500'],
            'nomor_tlp' => ['nullable', 'max:14']
        ]);

        $this->pelangganService->updatePelanggan($pelanggan, $request->all());

        return redirect()->route('pelanggan.index')->with('update', 'success');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        $this->pelangganService->deletePelanggan($pelanggan);

        return back()->with('destroy', 'success');
    }
}
