<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    private function isAdmin(?User $user): bool
    {
        return ($user->role ?? 'CUSTOMER') === 'ADMIN';
    }

    public function viewAny(User $user): bool { return $this->isAdmin($user); }
    public function view(User $user, Category $model): bool { return $this->isAdmin($user); }
    public function create(User $user): bool { return $this->isAdmin($user); }
    public function update(User $user, Category $model): bool { return $this->isAdmin($user); }
    public function delete(User $user, Category $model): bool { return $this->isAdmin($user); }
}

