<?php

namespace App\Repositories;

use App\Interfaces\EmployeeInterface;
use App\Models\User;

class EmployeeRepository implements EmployeeInterface
{
    public function all()
    {
        return User::with('role')->get();
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update(array $data, $id)
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
    }

    public function find($id)
    {
        return User::findOrFail($id);
    }
}
