{{--
    Filament 4 no longer ships raw Tailwind utilities in its compiled panel CSS
    (they moved into @apply rules), and this app's own Tailwind build is only
    loaded on the Inertia frontend, never inside the panel. So all styling here
    comes from Filament's own components, and the only inline styles are layout,
    which needs no light/dark handling. A custom Filament theme would be the
    tidier answer; that needs Tailwind 4.
--}}
<x-filament-widgets::widget>
    <x-filament::section heading="Pending Invitations" divided compact>
        @foreach($this->getViewData()['pendingInvitations'] as $invitation)
            <x-filament::section
                :heading="$invitation->presentation->title"
                :description="'Invited by '.$invitation->presentation->user->name.' — '.$invitation->invited_at->diffForHumans()"
                aside
                compact
            >
                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                    <x-filament::button
                        color="success"
                        size="sm"
                        wire:click="acceptInvitation({{ $invitation->id }})"
                    >
                        Accept
                    </x-filament::button>
                    <x-filament::button
                        color="gray"
                        size="sm"
                        wire:click="rejectInvitation({{ $invitation->id }})"
                    >
                        Reject
                    </x-filament::button>
                </div>
            </x-filament::section>
        @endforeach
    </x-filament::section>
</x-filament-widgets::widget>
