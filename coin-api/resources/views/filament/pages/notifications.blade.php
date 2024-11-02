<x-filament-panels::page>
    <form wire:submit="sendGeneralNotification">
        {{ $this->generalNotificationForm }}

        <div class="flex justify-end mt-4">
            <x-filament::button type="sendGeneralNotification">
                Send Notification
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
