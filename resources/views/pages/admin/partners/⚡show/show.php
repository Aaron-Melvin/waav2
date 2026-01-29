<?php

use App\Concerns\ApiClientValidationRules;
use App\Models\Partner;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app'), Title('Partner Details')] class extends Component
{
    use ApiClientValidationRules;

    public Partner $partner;

    public string $clientId = '';

    public string $clientSecret = '';

    public string $status = 'active';

    public string $scopes = '';

    public ?string $issuedSecret = null;

    public function mount(Partner $partner): void
    {
        $this->ensureAdmin();

        $this->partner = $partner
            ->load([
                'apiClients' => fn ($query) => $query->latest(),
            ])
            ->loadCount([
                'locations',
                'products',
                'bookings',
                'users',
                'apiClients',
            ]);
    }

    public function issueApiClient(): void
    {
        $this->ensureAdmin();

        $payload = $this->apiClientPayload();

        $validated = Validator::make(
            $payload,
            $this->apiClientRules(),
            $this->apiClientMessages()
        )->validate();

        $plainSecret = $validated['client_secret'] ?? Str::random(40);

        $this->partner->apiClients()->create([
            'client_id' => $validated['client_id'],
            'client_secret_hash' => Hash::make($plainSecret),
            'scopes' => $validated['scopes'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ]);

        $this->issuedSecret = $plainSecret;
        $this->clientId = '';
        $this->clientSecret = '';
        $this->scopes = '';
        $this->status = 'active';

        $this->partner
            ->load([
                'apiClients' => fn ($query) => $query->latest(),
            ])
            ->loadCount([
                'apiClients',
            ]);

        $this->resetValidation();
    }

    public function statusColor(string $status): string
    {
        return match ($status) {
            'active' => 'green',
            'inactive' => 'red',
            default => 'amber',
        };
    }

    protected function apiClientPayload(): array
    {
        $clientId = trim($this->clientId);
        $clientSecret = trim($this->clientSecret);
        $scopes = $this->parseScopes($this->scopes);

        return [
            'client_id' => $clientId,
            'client_secret' => $clientSecret !== '' ? $clientSecret : null,
            'scopes' => $scopes !== [] ? $scopes : null,
            'status' => $this->status !== '' ? $this->status : null,
        ];
    }

    protected function parseScopes(string $scopes): array
    {
        return collect(explode(',', $scopes))
            ->map(fn (string $scope) => trim($scope))
            ->filter()
            ->values()
            ->all();
    }

    protected function ensureAdmin(): void
    {
        if (! auth()->user()?->hasRole('super-admin')) {
            abort(403);
        }
    }
};
