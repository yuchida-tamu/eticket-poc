<?php

namespace App\Http\Controllers;

use App\Services\EticketPassFactory;
use Symfony\Component\HttpFoundation\Response;

class PassController extends Controller
{
    /**
     * Issue a freshly signed Apple Wallet `.pkpass` containing an NFC payload
     * and a unique serial number.
     */
    public function store(EticketPassFactory $factory): Response
    {
        $serial = $factory->newSerialNumber();
        $binary = $factory->generate($serial);

        return response($binary, 200, [
            'Content-Type' => 'application/vnd.apple.pkpass',
            'Content-Disposition' => 'attachment; filename="eticket-'.$serial.'.pkpass"',
            'X-Pass-Serial' => $serial,
        ]);
    }
}
