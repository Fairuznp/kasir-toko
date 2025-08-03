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

        $validation = $diskon->isValid($subtotal, $items);

        if (!$validation['valid']) {
            throw new \Exception($validation['message']);
        }

        $nilaiDiskon = $diskon->hitungNilaiDiskon($subtotal, $items);

        // Simpan diskon ke cart extra info
        $extraInfo = $cart->getExtraInfo();
        $extraInfo['diskon'] = [
            'id' => $diskon->id,
            'kode_diskon' => $diskon->kode_diskon,
            'nilai_diskon' => $nilaiDiskon
        ];

        $cart->setExtraInfo($extraInfo);

        return [
            'message' => $validation['message'],
            'nilai_diskon' => $nilaiDiskon
        ];
    }
}
