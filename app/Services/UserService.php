<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'student',
        ]);
    }

    public function updateUser(int $id, array $data): User
    {
        $user = User::findOrFail($id);
        
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        $user->update($data);
        
        return $user->fresh();
    }

    public function deleteUser(int $id): bool
    {
        return User::findOrFail($id)->delete();
    }

    public function changeRole(int $id, string $role): User
    {
        $user = User::findOrFail($id);
        $user->update(['role' => $role]);
        
        return $user;
    }

    public function deactivateUser(int $id): User
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => false]);
        
        return $user;
    }

    public function activateUser(int $id): User
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => true]);
        
        return $user;
    }
}
