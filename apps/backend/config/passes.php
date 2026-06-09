<?php

return [
    /*
     | Prefix for generated pass serial numbers (e.g. "ETK-AB12CD34EF").
     */
    'serial_prefix' => env('ETICKET_SERIAL_PREFIX', 'ETK'),

    'nfc' => [
        /*
         | Base64 (DER) ECC P-256 public key embedded in the pass's NFC payload.
         | This is a PUBLIC key — safe to ship. If the env var is unset we fall
         | back to the committed asset at resources/passes/nfc_public_key.b64.
         | Generate a keypair with:
         |   openssl ecparam -name prime256v1 -genkey -noout -out nfc_private.pem
         |   openssl ec -in nfc_private.pem -pubout -outform DER | base64
         */
        'public_key' => env('ETICKET_NFC_PUBLIC_KEY'),
    ],
];
