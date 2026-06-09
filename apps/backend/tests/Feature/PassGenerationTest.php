<?php

use App\Services\EticketPassFactory;

/*
| The first test exercises pass *assembly* (serial + NFC + identifiers) without
| signing, so it runs everywhere including CI (no certificate required). The
| second test exercises the full signed endpoint and is skipped unless a real
| Apple Wallet signing certificate is configured (i.e. on a developer machine).
*/

it('assembles pass data with a serial number and NFC payload', function () {
    // Make the test independent of real Apple credentials.
    config([
        'mobile-pass.apple.type_identifier' => 'pass.com.example.test',
        'mobile-pass.apple.team_identifier' => 'TEAMID1234',
        'mobile-pass.apple.organization_name' => 'eticket-poc test',
    ]);

    $factory = app(EticketPassFactory::class);
    $serial = $factory->newSerialNumber();

    $data = $factory->builder($serial)->data();

    expect($serial)->toStartWith('ETK-');
    expect($data['serialNumber'])->toBe($serial);
    expect($data['passTypeIdentifier'])->toBe('pass.com.example.test');
    expect($data['teamIdentifier'])->toBe('TEAMID1234');
    expect($data['nfc']['message'])->toBe($serial);
    expect($data['nfc']['encryptionPublicKey'])->not->toBe('');
});

it('issues a signed .pkpass over the API', function () {
    $certPath = (string) config('mobile-pass.apple.certificate_path');
    $certInline = (string) config('mobile-pass.apple.certificate');

    if ($certInline === '' && ($certPath === '' || ! is_readable($certPath))) {
        test()->markTestSkipped('Apple Wallet signing certificate not configured.');
    }

    $response = $this->postJson('/api/passes');

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/vnd.apple.pkpass');

    $serial = $response->headers->get('X-Pass-Serial');
    expect($serial)->toStartWith('ETK-');

    // The body must be a valid .pkpass zip whose pass.json carries our serial + NFC.
    $tmp = tempnam(sys_get_temp_dir(), 'pkpass').'.pkpass';
    file_put_contents($tmp, $response->getContent());

    $zip = new ZipArchive;
    expect($zip->open($tmp))->toBeTrue();

    $names = [];
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $names[] = $zip->getNameIndex($i);
    }
    expect($names)->toContain('pass.json', 'manifest.json', 'signature', 'icon.png');

    $passJson = json_decode($zip->getFromName('pass.json'), true);
    $zip->close();
    unlink($tmp);

    expect($passJson['serialNumber'])->toBe($serial);
    expect($passJson['nfc']['message'])->toBe($serial);
});
