<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function update(User $user, Document $document): bool
    {
        return $this->ownsDocument($user, $document);
    }

    public function publish(User $user, Document $document): bool
    {
        return $this->ownsDocument($user, $document);
    }

    private function ownsDocument(User $user, Document $document): bool
    {
        if ($user->role->name === 'super_admin') {
            return true;
        }

        if ($user->role->name === 'admin_unit') {
            return $document->owner_unit_id === $user->unit_id;
        }

        return false;
    }
}
