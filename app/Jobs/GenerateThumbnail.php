<?php

namespace App\Jobs;

use App\Models\Presentation;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Spatie\Browsershot\Browsershot;

class GenerateThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Presentation $presentation,
        public User $user,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tempPath = storage_path("temp/{$this->presentation->slug}-{$this->presentation->user->username}.jpg");

        $browsershot = Browsershot::url(route('presentations.show', [
            'user' => $this->presentation->user->username,
            'slug' => $this->presentation->slug,
        ]))
            ->waitUntilNetworkIdle()
            ->windowSize(1200, 630)
            ->newHeadless()
            ->setOption('args', ['--disable-web-security'])
            ->setScreenshotType('jpeg', 90)
            ->noSandbox()
            ->setOption('addStyleTag', json_encode([
                'content' => '.slide-view { height: 100vh !important; } '
                    .'.browsershot-hide { display: none !important; }',
            ]));

        if (! App::environment('local')) {
            $browsershot->setChromePath('/usr/bin/chromium-browser');
        }

        $browsershot->save($tempPath);

        $this->presentation->clearMediaCollection('thumbnail');
        $this->presentation->addMedia($tempPath)->toMediaCollection('thumbnail');

        // Note: If in the future we implement a websocket server,
        // then we can implement a completed notification like this.
        //
        // Notification::make()
        //     ->title('Thumbnail successfully generated. Refresh your page to view the new thumbnail.')
        //     ->broadcast($this->user)
        //     ->success()
        //     ->send();
    }
}
