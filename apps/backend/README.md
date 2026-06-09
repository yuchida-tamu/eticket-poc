# eticket-poc — backend

Laravel service that issues signed Apple Wallet `.pkpass` files (with an NFC payload) via [`spatie/laravel-mobile-pass`](https://github.com/spatie/laravel-mobile-pass), backed by Postgres.

## Endpoints

| Method | Path | Description |
|--------|------|-------------|
| `GET`  | `/api/health` | Liveness check → `{"status":"ok"}` (no DB required). |
| `POST` | `/api/passes` | Issues a freshly signed `.pkpass` (event ticket) with a unique serial number and an NFC payload. Returns `application/vnd.apple.pkpass`; the serial is also echoed in the `X-Pass-Serial` header. |

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
./vendor/bin/pest      # tests
./vendor/bin/pint      # format
```

## Apple Wallet pass signing

Issuing a real pass needs a **Pass Type ID certificate** from your Apple Developer account.

1. **Place the certificate** outside the app, in the repo-root `.cert/` directory (gitignored, along with `*.p12`/`*.pem`/`*.pkpass`).

2. **Convert Apple's legacy `.p12`.** Apple exports `.p12` files with legacy RC2-40 encryption, which OpenSSL 3 / PHP's `openssl_pkcs12_read()` **cannot** read (`error:0308010C ... unsupported`). Re-encrypt it to AES-256 once:

   ```bash
   cd .cert
   openssl pkcs12 -in eticket_certificate.p12 -legacy -nodes -passin pass:YOURPASS -out _tmp.pem
   openssl pkcs12 -export -in _tmp.pem -out eticket_certificate_modern.p12 -passout pass:YOURPASS
   rm _tmp.pem
   ```

   Point `MOBILE_PASS_APPLE_CERTIFICATE_PATH` at the `_modern.p12`. The bundled Apple WWDR (G4) intermediate handles the rest of the chain.

3. **Set the Apple env vars** in `.env` (see `.env.example`):

   ```env
   MOBILE_PASS_APPLE_TYPE_IDENTIFIER=pass.com.your.passtype
   MOBILE_PASS_APPLE_TEAM_IDENTIFIER=YOURTEAMID
   MOBILE_PASS_APPLE_ORGANIZATION_NAME="Your Org"
   MOBILE_PASS_APPLE_CERTIFICATE_PATH=/abs/path/to/.cert/eticket_certificate_modern.p12
   MOBILE_PASS_APPLE_CERTIFICATE_PASSWORD=YOURPASS
   ```

4. **NFC payload.** Passes embed a base64 (DER) ECC P-256 **public** key in their `nfc` dictionary. A committed default lives at `resources/passes/nfc_public_key.b64`; override with `ETICKET_NFC_PUBLIC_KEY`. To mint your own keypair:

   ```bash
   openssl ecparam -name prime256v1 -genkey -noout -out nfc_private.pem
   openssl ec -in nfc_private.pem -pubout -outform DER | base64   # -> the public key
   ```

> The signed-pass test (`tests/Feature/PassGenerationTest.php`) is **skipped** automatically when no certificate is configured (e.g. in CI), so the suite stays green without secrets. The pass-assembly test always runs.

> Whether a third-party-issued NFC pass is actually accepted/read by Apple's ProximityReader on-device is the project's open risk (see `docs/PRD.md` → Open Questions); this backend produces a structurally valid, signed, NFC-bearing pass.
