<?php

namespace App\Services;

use App\Models\Launchpad;
use App\Models\Holder;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class TokenHolderService
{
    protected $apiKey;
    protected $baseUrl = 'https://rpc.ankr.com/multichain';

    public function __construct()
    {
        $this->apiKey = config('evm.ankr_key');
    }

    /**
     * Update holders for all active launchpads
     */
    public function updateAllHolders()
    {
        try {
            $launchpads = Launchpad::query()
                // ->whereNull('pool')
                ->whereNotNull('token')
                ->get();
            foreach ($launchpads as $launchpad) {
                $this->updateHolders($launchpad);
            }
        } catch (Exception $e) {
            Log::error('Failed to update all holders: ' . $e->getMessage());
        }
    }

    /**
     * Update holders for a specific launchpad
     */
    public function updateHolders(Launchpad $launchpad)
    {
        try {
            $response = $this->fetchHolders($launchpad->token, $launchpad->chainId);

            if (!isset($response['holders'])) {
                Log::error("Invalid response for token {$launchpad->token}");
                return;
            }
            $users = User::query()->pluck('id', 'address');
            foreach ($response['holders'] as $holder) {
                Holder::updateOrCreate(
                    [
                        'launchpad_id' => $launchpad->id,
                        'address' => $holder['holderAddress']
                    ],
                    [
                        'qty' => $holder['balance'],
                        'user_id' => $users[$holder['holderAddress']] ?? $users[Util::toChecksumAddress($holder['holderAddress'])] ?? null
                    ]
                );
            }
        } catch (Exception $e) {
            Log::error("Failed to update holders for launchpad {$launchpad->id}: " . $e->getMessage());
        }
    }

    /**
     * Fetch holders from Ankr API
     */
    protected function fetchHolders(string $tokenAddress, string $chainId, int $page = 1): array
    {

        $response = Http::post($this->baseUrl . '/' . $this->apiKey, [
            'jsonrpc' => '2.0',
            'method' => 'ankr_getTokenHolders',
            'params' => [
                'blockchain' => $this->getBlockchainName($chainId),
                'contractAddress' => $tokenAddress,
                'pageSize' => 10000,
                'pageNumber' => $page
            ],
            'id' => 1
        ]);
        if ($response->failed()) {
            throw new Exception('Ankr API request failed: ' . $response->body());
        }

        return $response->json()['result'] ?? [];
    }

    /**
     * Convert chainId to blockchain name for Ankr API
     */
    protected function getBlockchainName(string $chainId): string
    {
        return match ($chainId) {
            '1' => 'eth',
            '5' => 'eth_holesky',
            '10' => 'optimism',
            '11155111' => 'eth_sepolia',
            '56' => 'bsc',
            '100' => 'gnosis',
            '137' => 'polygon',
            '420' => 'optimism_testnet',
            '8453' => 'base',
            '84532' => 'base_sepolia',
            '42161' => 'arbitrum',
            '43114' => 'avalanche',
            '43113' => 'avalanche_fuji',
            '250' => 'fantom',
            '1101' => 'polygon_zkevm',
            '59144' => 'linea',
            '534352' => 'scroll',
            '57' => 'syscoin',
            '40' => 'telos',
            '14' => 'flare',
            '570' => 'rollux',
            '660279' => 'xai',
            default => throw new Exception("Unsupported chainId: {$chainId}")
        };
    }

    /**
     * Find user ID by address
     */
    protected function findUserId(string $address): ?int
    {
        $user = \App\Models\User::where('address', $address)->first();
        return $user?->id;
    }
}