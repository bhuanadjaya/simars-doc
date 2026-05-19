<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDocumentNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Document $document,
        private Collection $recipients,
        private string $type,
        private string $title,
        private string $message,
    ) {}

    public function handle(): void
    {
        $rows = $this->recipients->map(fn (User $user) => [
            'id'          => (string) \Illuminate\Support\Str::uuid(),
            'user_id'     => $user->id,
            'document_id' => $this->document->id,
            'title'       => $this->title,
            'message'     => $this->message,
            'type'        => $this->type,
            'is_read'     => false,
            'read_at'     => null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ])->all();

        // Insert in chunks to avoid hitting DB limits on large units
        foreach (array_chunk($rows, 100) as $chunk) {
            Notification::insert($chunk);
        }
    }
}
