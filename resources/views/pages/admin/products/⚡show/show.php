<?php

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app'), Title('Product Details')] class extends Component
{
    public Product $product;

    public function mount(Product $product): void
    {
        $this->ensureAdmin();

        $this->product = $product
            ->load([
                'partner',
                'location',
                'media' => fn ($query) => $query->orderBy('sort'),
                'units' => fn ($query) => $query->orderBy('name'),
                'ratePlans' => fn ($query) => $query->orderBy('name'),
            ])
            ->loadCount([
                'eventSeries',
                'events',
                'units',
                'ratePlans',
                'media',
                'eligibilityRules',
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

    public function visibilityColor(string $visibility): string
    {
        return match ($visibility) {
            'public' => 'green',
            'unlisted' => 'amber',
            'private' => 'red',
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
