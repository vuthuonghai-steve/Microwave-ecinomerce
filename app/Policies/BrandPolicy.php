<?php

namespace App\Policies;

use App\Models\Brand;
use App\Models\User;

class BrandPolicy
{
    private function isAdmin(?User $user): bool
    {
        return ($user->role ?? 'CUSTOMER') === 'ADMIN';
    }

    public function viewAny(User $user): bool { return $this->isAdmin($user); }
    public function view(User $user, Brand $model): bool { return $this->isAdmin($user); }
    public function create(User $user): bool { return $this->isAdmin($user); }
    public function update(User $user, Brand $model): bool { return $this->isAdmin($user); }
    public function delete(User $user, Brand $model): bool { return $this->isAdmin($user); }
}

