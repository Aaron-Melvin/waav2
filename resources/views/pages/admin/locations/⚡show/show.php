<?php

use App\Models\Location;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app'), Title('Location Details')] class extends Component
{
    public Location $location;

    public function mount(Location $location): void
    {
        $this->ensureAdmin();

        $this->location = $location
            ->load([
                'partner',
                'products' => fn ($query) => $query->orderBy('name'),
                'eventBlackouts' => fn ($query) => $query
                    ->with('product')
                    ->latest(),
            ])
            ->loadCount([
                'products',
                'eventBlackouts',
            ]);
    }

    public function statusColor(string $status): string
    {
        return match ($status) {
            'active' => 'green',
            'inactive' => 'red',
            default => 'zinc',
        };
    }

    protected function ensureAdmin(): void
    {
        if (! auth()->user()?->hasRole('super-admin')) {
            abort(403);
        }
    }
};
