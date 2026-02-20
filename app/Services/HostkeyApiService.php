<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class HostkeyApiService
{
    private const string BASE_URL = 'https://invapi.hostkey.com';

    private const array LOCATIONS = [
        'NL' => 'Netherlands',
        'US' => 'USA',
        'FI' => 'Finland',
        'DE' => 'Germany',
        'IS' => 'Iceland',
        'TR' => 'Turkey',
        'UK' => 'United Kingdom',
        'ES' => 'Spain',
        'IT' => 'Italy',
        'PL' => 'Poland',
        'CH' => 'Switzerland',
        'FR' => 'France',
    ];

    private ?string $sessionToken = null;

    /**
     * Get all available locations.
     *
     * @return array<string, string>
     */
    public function getLocations(): array
    {
        return self::LOCATIONS;
    }

    /**
     * Authenticate with API key and get session token.
     */
    public function authenticate(): bool
    {
        $apiKey = config('services.hostkey.api_key');

        if (empty($apiKey)) {
            Log::error('Hostkey API key not configured');

            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(30)
                ->post(self::BASE_URL.'/auth.php', [
                    'action' => 'login',
                    'token' => $apiKey,
                ]);

            if (! $response->successful()) {
                Log::error('Hostkey authentication failed', [
                    'status' => $response->status(),
                ]);

                return false;
            }

            $data = $response->json();

            if (($data['result'] ?? null) === 'OK' && isset($data['token'])) {
                $this->sessionToken = $data['token'];

                return true;
            }

            Log::error('Hostkey authentication failed', ['response' => $data]);

            return false;
        } catch (ConnectionException $e) {
            Log::error('Hostkey API connection error', ['message' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Get the current session token, authenticating if needed.
     */
    private function getToken(): ?string
    {
        if ($this->sessionToken === null) {
            $this->authenticate();
        }

        return $this->sessionToken;
    }

    /**
     * Get available presets for a location.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPresets(string $location): array
    {
        try {
            $response = Http::asForm()
                ->timeout(30)
                ->post(self::BASE_URL.'/presets.php', [
                    'action' => 'list',
                    'location' => $location,
                ]);

            if (! $response->successful()) {
                return [];
            }

            $data = $response->json();

            return $data['presets'] ?? [];
        } catch (ConnectionException $e) {
            Log::error('Failed to get presets', ['message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * Get available operating systems for a preset.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getOperatingSystems(int $instanceId): array
    {
        try {
            $response = Http::asForm()
                ->timeout(30)
                ->post(self::BASE_URL.'/os.php', [
                    'action' => 'list',
                    'instance_id' => $instanceId,
                ]);

            if (! $response->successful()) {
                return [];
            }

            $data = $response->json();

            return $data['os_list'] ?? [];
        } catch (ConnectionException $e) {
            Log::error('Failed to get operating systems', ['message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * Get available software for a preset.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getSoftware(string $location, int $instanceId): array
    {
        try {
            $response = Http::asForm()
                ->timeout(30)
                ->post(self::BASE_URL.'/software.php', [
                    'action' => 'list',
                    'location' => $location,
                    'instance_id' => $instanceId,
                ]);

            if (! $response->successful()) {
                return [];
            }

            $data = $response->json();

            return $data['software'] ?? [];
        } catch (ConnectionException $e) {
            Log::error('Failed to get software', ['message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * Get available traffic plans for a preset.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getTrafficPlans(string $location, int $instanceId): array
    {
        try {
            $response = Http::asForm()
                ->timeout(30)
                ->post(self::BASE_URL.'/traffic_plans.php', [
                    'action' => 'list',
                    'location' => $location,
                    'instance' => $instanceId,
                ]);

            if (! $response->successful()) {
                return [];
            }

            $data = $response->json();

            return $data['traffic_plans'] ?? [];
        } catch (ConnectionException $e) {
            Log::error('Failed to get traffic plans', ['message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * Order a server instance using a recipe.
     *
     * @param  array<string, mixed>  $recipe
     * @return array{success: bool, invoice_id?: int, error?: string}
     */
    public function orderInstance(array $recipe, string $rootPassword): array
    {
        $token = $this->getToken();

        if ($token === null) {
            return ['success' => false, 'error' => 'Authentication failed'];
        }

        $params = [
            'action' => 'order_instance',
            'token' => $token,
            'deploy_period' => $recipe['deploy_period'] ?? 'monthly',
            'deploy_notify' => 'true',
            'preset' => $recipe['preset_id'],
            'location_name' => $recipe['location'],
            'os_id' => $recipe['os_id'],
            'traffic_plan' => $recipe['traffic_plan_id'],
            'root_pass' => $rootPassword,
        ];

        if (! empty($recipe['software_id'])) {
            $params['soft_id'] = $recipe['software_id'];
        }

        if (! empty($recipe['ssh_key'])) {
            $params['ssh_key'] = $recipe['ssh_key'];
        }

        if (! empty($recipe['post_install_script'])) {
            $params['post_install_script'] = $recipe['post_install_script'];
        }

        if (! empty($recipe['post_install_callback'])) {
            $params['post_install_callback'] = $recipe['post_install_callback'];
        }

        try {
            $response = Http::asForm()
                ->timeout(60)
                ->post(self::BASE_URL.'/eq.php', $params);

            if (! $response->successful()) {
                return ['success' => false, 'error' => 'API request failed'];
            }

            $data = $response->json();

            if (($data['result'] ?? null) === 'OK') {
                return [
                    'success' => true,
                    'invoice_id' => $data['invoice'] ?? null,
                    'status' => $data['status'] ?? null,
                ];
            }

            return [
                'success' => false,
                'error' => $data['error'] ?? 'Unknown error',
            ];
        } catch (ConnectionException $e) {
            Log::error('Failed to order instance', ['message' => $e->getMessage()]);

            return ['success' => false, 'error' => 'Connection error'];
        }
    }
}
