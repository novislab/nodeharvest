<?php

use App\Services\HostkeyApiService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->service = new HostkeyApiService;
});

describe('getLocations', function () {
    it('returns all available locations', function () {
        $locations = $this->service->getLocations();

        expect($locations)
            ->toBeArray()
            ->toHaveKeys(['NL', 'US', 'FI', 'DE', 'IS', 'TR', 'UK', 'ES', 'IT', 'PL', 'CH', 'FR'])
            ->and($locations['NL'])->toBe('Netherlands')
            ->and($locations['FR'])->toBe('France');
    });
});

describe('authenticate', function () {
    it('returns false when api key is not configured', function () {
        config(['services.hostkey.api_key' => null]);

        $result = $this->service->authenticate();

        expect($result)->toBeFalse();
    });

    it('returns true on successful authentication', function () {
        config(['services.hostkey.api_key' => 'test-api-key']);

        Http::fake([
            'invapi.hostkey.com/auth.php' => Http::response([
                'result' => 'OK',
                'token' => 'session-token-123',
            ]),
        ]);

        $result = $this->service->authenticate();

        expect($result)->toBeTrue();
    });

    it('returns false on authentication failure', function () {
        config(['services.hostkey.api_key' => 'invalid-key']);

        Http::fake([
            'invapi.hostkey.com/auth.php' => Http::response([
                'result' => 'ERROR',
                'error' => 'Invalid API key',
            ]),
        ]);

        $result = $this->service->authenticate();

        expect($result)->toBeFalse();
    });
});

describe('getPresets', function () {
    it('returns presets for a location', function () {
        Http::fake([
            'invapi.hostkey.com/presets.php' => Http::response([
                'result' => 'OK',
                'presets' => [
                    ['id' => 108, 'name' => 'vm.pico', 'description' => 'VPS 1/1/15 SSD'],
                    ['id' => 109, 'name' => 'vm.nano', 'description' => 'VPS 2/2/30 SSD'],
                ],
            ]),
        ]);

        $presets = $this->service->getPresets('NL');

        expect($presets)
            ->toBeArray()
            ->toHaveCount(2)
            ->and($presets[0]['id'])->toBe(108)
            ->and($presets[1]['name'])->toBe('vm.nano');
    });

    it('returns empty array on error', function () {
        Http::fake([
            'invapi.hostkey.com/presets.php' => Http::response(null, 500),
        ]);

        $presets = $this->service->getPresets('NL');

        expect($presets)->toBeArray()->toBeEmpty();
    });
});

describe('getOperatingSystems', function () {
    it('returns operating systems for an instance', function () {
        Http::fake([
            'invapi.hostkey.com/os.php' => Http::response([
                'result' => 'OK',
                'os_list' => [
                    ['id' => 183, 'name' => 'RockyLinux 8', 'active' => 1],
                    ['id' => 187, 'name' => 'Ubuntu 22.04', 'active' => 1],
                ],
            ]),
        ]);

        $osList = $this->service->getOperatingSystems(108);

        expect($osList)
            ->toBeArray()
            ->toHaveCount(2)
            ->and($osList[0]['name'])->toBe('RockyLinux 8');
    });
});

describe('getSoftware', function () {
    it('returns software list for an instance', function () {
        Http::fake([
            'invapi.hostkey.com/software.php' => Http::response([
                'result' => 'OK',
                'software' => [
                    ['id' => 20, 'name' => 'WordPress', 'active' => 1],
                    ['id' => 26, 'name' => '3X-UI VPN', 'active' => 1],
                ],
            ]),
        ]);

        $software = $this->service->getSoftware('NL', 108);

        expect($software)
            ->toBeArray()
            ->toHaveCount(2)
            ->and($software[0]['name'])->toBe('WordPress');
    });
});

describe('getTrafficPlans', function () {
    it('returns traffic plans for an instance', function () {
        Http::fake([
            'invapi.hostkey.com/traffic_plans.php' => Http::response([
                'result' => 'OK',
                'traffic_plans' => [
                    ['id' => 25, 'name' => '3Tb traffic (1Gbps) VM'],
                    ['id' => 37, 'name' => 'Unlimited traffic'],
                ],
            ]),
        ]);

        $plans = $this->service->getTrafficPlans('NL', 108);

        expect($plans)
            ->toBeArray()
            ->toHaveCount(2)
            ->and($plans[0]['name'])->toBe('3Tb traffic (1Gbps) VM');
    });
});

describe('orderInstance', function () {
    it('returns error when not authenticated', function () {
        config(['services.hostkey.api_key' => null]);

        $result = $this->service->orderInstance([
            'preset_id' => '108',
            'location' => 'NL',
            'os_id' => 187,
            'traffic_plan_id' => 25,
            'deploy_period' => 'monthly',
        ], 'SecureP@ss123');

        expect($result)
            ->toBeArray()
            ->toHaveKey('success', false)
            ->toHaveKey('error', 'Authentication failed');
    });

    it('orders a server successfully', function () {
        config(['services.hostkey.api_key' => 'test-api-key']);

        Http::fake([
            'invapi.hostkey.com/auth.php' => Http::response([
                'result' => 'OK',
                'token' => 'session-token-123',
            ]),
            'invapi.hostkey.com/eq.php' => Http::response([
                'result' => 'OK',
                'invoice' => 24016,
                'status' => 'Paid',
            ]),
        ]);

        $result = $this->service->orderInstance([
            'preset_id' => '108',
            'location' => 'NL',
            'os_id' => 187,
            'traffic_plan_id' => 25,
            'deploy_period' => 'monthly',
        ], 'SecureP@ss123');

        expect($result)
            ->toBeArray()
            ->toHaveKey('success', true)
            ->toHaveKey('invoice_id', 24016)
            ->toHaveKey('status', 'Paid');
    });

    it('returns error on order failure', function () {
        config(['services.hostkey.api_key' => 'test-api-key']);

        Http::fake([
            'invapi.hostkey.com/auth.php' => Http::response([
                'result' => 'OK',
                'token' => 'session-token-123',
            ]),
            'invapi.hostkey.com/eq.php' => Http::response([
                'result' => -1,
                'error' => 'OS 5 is not compatible with server eq:12345',
            ]),
        ]);

        $result = $this->service->orderInstance([
            'preset_id' => '108',
            'location' => 'NL',
            'os_id' => 5,
            'traffic_plan_id' => 25,
            'deploy_period' => 'monthly',
        ], 'SecureP@ss123');

        expect($result)
            ->toBeArray()
            ->toHaveKey('success', false)
            ->toHaveKey('error', 'OS 5 is not compatible with server eq:12345');
    });
});
