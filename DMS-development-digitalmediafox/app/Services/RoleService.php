<?php

namespace App\Services;

use App\Repositories\RoleRepository;

class RoleService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected RoleRepository $roleRepository)
    {
        //
    }

    public function create(array $data)
    {
        return $this->roleRepository->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->roleRepository->update($data, $id);
    }

    public function delete($id)
    {
        return $this->roleRepository->delete($id);
    }

    public function all()
    {
        return $this->roleRepository->all();
    }

    public function find($id)
    {
        return $this->roleRepository->find($id);
    }

}
