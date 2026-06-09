<?php

namespace App\Services;

use Illuminate\Support\Str;
use Spatie\LaravelMobilePass\Builders\Apple\EventTicketPassBuilder;
use Spatie\LaravelMobilePass\Enums\BarcodeType;

/**
 * Builds signed Apple Wallet event-ticket passes that carry an NFC payload.
 *
 * The pass's serial number is echoed into the NFC message and the QR barcode so
 * the reader app (issue #4) can surface it after a tap.
 */
class EticketPassFactory
{
    /**
     * Generate a fresh, unique serial number for a pass.
     */
    public function newSerialNumber(): string
    {
        $prefix = (string) config('passes.serial_prefix', 'ETK');

        return $prefix.'-'.Str::upper(Str::random(10));
    }

    /**
     * The base64 (DER) P-256 public key embedded in the NFC payload.
     * Prefers the ETICKET_NFC_PUBLIC_KEY env var, falling back to the
     * committed asset.
     */
    public function nfcPublicKey(): string
    {
        $configured = (string) config('passes.nfc.public_key', '');

        if ($configured !== '') {
            return $configured;
        }

        return trim((string) file_get_contents(resource_path('passes/nfc_public_key.b64')));
    }

    /**
     * Configure an event-ticket pass builder for the given serial number.
     */
    public function builder(string $serial): EventTicketPassBuilder
    {
        $organizationName = (string) config('mobile-pass.apple.organization_name') ?: 'eticket-poc';

        return EventTicketPassBuilder::make()
            ->setSerialNumber($serial)
            ->setDescription('eticket POC event ticket')
            ->setOrganizationName($organizationName)
            ->setIconImage(resource_path('passes/icon.png'))
            ->setBarcode(BarcodeType::Qr, $serial)
            ->setNfc($serial, $this->nfcPublicKey());
    }

    /**
     * Build and sign a `.pkpass` for the given serial number.
     * Requires the Apple Wallet signing certificate to be configured.
     */
    public function generate(string $serial): string
    {
        return $this->builder($serial)->generate();
    }
}
