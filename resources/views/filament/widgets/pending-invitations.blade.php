<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Pending Invitations
        </x-slot>

        <div class="space-y-3">
            @foreach($this->getViewData()['pendingInvitations'] as $invitation)
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100">
                            {{ $invitation->presentation->title }}
                        </h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Invited by {{ $invitation->presentation->user->name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500">
                            {{ $invitation->invited_at->diffForHumans() }}
                        </p>
                    </div>
                    <div class="flex">
                        <x-filament::button
                            style="margin-right: .5rem;"
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
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
