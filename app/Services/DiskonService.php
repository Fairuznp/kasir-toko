<?php

namespace App\Services;

use App\Repositories\DiskonRepository;
use Jackiedo\Cart\Facades\Cart;

class DiskonService
{
    protected $diskonRepository;

    public function __construct(DiskonRepository $diskonRepository)
    {
        $this->diskonRepository = $diskonRepository;
    }

    public function getAllDiskon($search = null)
    {
        $diskons = $this->diskonRepository->getAllDiskon($search);

        // foreach ($diskons as $diskon) {
        //     $diskon->updateStatus();
        // }

        if ($search) {
            $diskons->appends(['search' => $search]);
        }

        return $diskons;
    }

    public function getDataForCreate()
    {
        return [
            'kategoris' => $this->diskonRepository->getKategoriForSelect(),
            'produks' => $this->diskonRepository->getProdukForSelect()
        ];
    }

    public function createDiskon(array $data)
    {
        return $this->diskonRepository->createDiskon($data);
    }

    public function updateDiskon($diskon, array $data)
    {
        return $this->diskonRepository->updateDiskon($diskon, $data);
    }

    public function deleteDiskon($diskon)
    {
        return $this->diskonRepository->deleteDiskon($diskon);
    }

    public function terapkanDiskon($kodeDiskon, $userId)
    {
        $cart = Cart::name($userId);
        $cartDetails = $cart->getDetails();
        $subtotal = $cartDetails->get('subtotal');
        $items = $cartDetails->get('items');

        $diskon = $this->diskonRepository->getDiskonByKode($kodeDiskon);

        if (!$diskon) {
            throw new \Exception('Kode diskon tidak ditemukan');
        }

        // Perbarui status diskon sebelum validasi
        // $diskon->updateStatus();

        $validation = $diskon->isValid($subtotal, $items);

        if (!$validation['valid']) {
            throw new \Exception($validation['message']);
        }

        $nilaiDiskon = $diskon->hitungNilaiDiskon($subtotal, $items);

        // Tentukan info pada apa diskon diterapkan
        $infoDiterapkan = '';
        if ($diskon->produk_id) {
            $produk = \App\Models\Produk::find($diskon->produk_id);
            $infoDiterapkan = $produk ? 'produk ' . $produk->nama_produk : 'produk tertentu';
        } elseif ($diskon->kategori_id) {
            $kategori = \App\Models\Kategori::find($diskon->kategori_id);
            $infoDiterapkan = $kategori ? 'kategori ' . $kategori->nama_kategori : 'kategori tertentu';
        } else {
            $infoDiterapkan = 'semua produk';
        }

        // Simpan diskon ke cart extra info (mendukung multiple diskon)
        $extraInfo = $cart->getExtraInfo();
        if (!isset($extraInfo['diskons'])) {
            $extraInfo['diskons'] = [];
        }

        // Cek apakah diskon sudah diterapkan
        $sudahAda = false;
        foreach ($extraInfo['diskons'] as $existingDiskon) {
            if ($existingDiskon['id'] == $diskon->id) {
                $sudahAda = true;
                break;
            }
        }

        if ($sudahAda) {
            throw new \Exception('Diskon sudah diterapkan');
        }

        // Tambahkan diskon baru
        $extraInfo['diskons'][] = [
            'id' => $diskon->id,
            'kode_diskon' => $diskon->kode_diskon,
            'nilai_diskon' => $nilaiDiskon,
            'produk_id' => $diskon->produk_id,
            'kategori_id' => $diskon->kategori_id
        ];

        $cart->setExtraInfo($extraInfo);

        return [
            'message' => $validation['message'] . ' (Diterapkan pada ' . $infoDiterapkan . ')',
            'nilai_diskon' => $nilaiDiskon,
            'applied_discounts' => $extraInfo['diskons']
        ];
    }

    public function hapusDiskon($kodeDiskon, $userId)
    {
        $cart = Cart::name($userId);
        $extraInfo = $cart->getExtraInfo();

        if (!isset($extraInfo['diskons'])) {
            throw new \Exception('Tidak ada diskon yang diterapkan');
        }

        // Hapus diskon berdasarkan kode
        $diskons = $extraInfo['diskons'];
        $found = false;
        foreach ($diskons as $key => $diskon) {
            if ($diskon['kode_diskon'] == $kodeDiskon) {
                unset($diskons[$key]);
                $found = true;
                break;
            }
        }

        if (!$found) {
            throw new \Exception('Diskon tidak ditemukan');
        }

        // Update cart extra info
        $extraInfo['diskons'] = array_values($diskons); // Reset array keys
        $cart->setExtraInfo($extraInfo);

        return [
            'applied_discounts' => $extraInfo['diskons']
        ];
    }
}
