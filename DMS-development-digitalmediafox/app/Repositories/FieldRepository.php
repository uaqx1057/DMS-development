<?php

namespace App\Repositories;

use App\Interfaces\FieldInterface;
use App\Models\Field;

class FieldRepository implements FieldInterface
{
    public function all()
    {
        return Field::with('businesses')->get();
    }

    public function create(array $data)
    {
        $field = Field::create($data);
        return $field;
    }

    public function update(array $data, $id)
    {
        $field = Field::findOrFail($id);
        $field->update($data);
        return $field;
    }

    public function delete($id)
    {
        $field = Field::findOrFail($id);
        $field->delete();
    }

    public function find($id)
    {
        return Field::findOrFail($id);
    }
}
