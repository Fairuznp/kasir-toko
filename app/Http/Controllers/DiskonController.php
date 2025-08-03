<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diskon;
use App\Services\DiskonService;

class DiskonController extends Controller
{
    protected $diskonService;

    public function __construct(DiskonService $diskonService)
    {
        $this->diskonService = $diskonService;
    }

    public function index(Request $request)
    {
        $diskons = $this->diskonService->getAllDiskon($request->search);

        return view('diskon.index', compact('diskons'));
    }

    public function create()
    {
        $data = $this->diskonService->getDataForCreate();

        return view('diskon.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_diskon' => 'required|unique:diskons,kode_diskon',
            'jenis_diskon' => 'required|in:persen,nominal',
            'jumlah_diskon' => 'required|integer|min:1',
            'minimal_pembelian' => 'nullable|integer|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'kategori_id' => 'nullable|exists:kategoris,id',
            'produk_id' => 'nullable|exists:produks,id',
        ]);

        $this->diskonService->createDiskon($request->all());

        return redirect()->route('diskon.index')->with('store', 'success');
    }

    public function edit(Diskon $diskon)
    {
        $data = $this->diskonService->getDataForCreate();
        $data['diskon'] = $diskon;

        return view('diskon.edit', $data);
    }

    public function update(Request $request, Diskon $diskon)
    {
        $request->validate([
            'kode_diskon' => 'required|unique:diskons,kode_diskon,' . $diskon->id,
            'jenis_diskon' => 'required|in:persen,nominal',
            'jumlah_diskon' => 'required|integer|min:1',
            'minimal_pembelian' => 'nullable|integer|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status' => 'boolean',
            'kategori_id' => 'nullable|exists:kategoris,id',
            'produk_id' => 'nullable|exists:produks,id',
        ]);

        $this->diskonService->updateDiskon($diskon, $request->all());

        return redirect()->route('diskon.index')->with('update', 'success');
    }

    public function destroy(Diskon $diskon)
    {
        $this->diskonService->deleteDiskon($diskon);

        return back()->with('destroy', 'success');
    }

    public function terapkanDiskon(Request $request)
    {
        $request->validate([
            'kode_diskon' => 'required|exists:diskons,kode_diskon'
        ]);

        try {
            $result = $this->diskonService->terapkanDiskon(
                $request->kode_diskon,
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'nilai_diskon' => $result['nilai_diskon']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
