<?php

namespace App\Traits;

use App\Enums\FieldsTypes;
use Illuminate\Support\Facades\DB;
use App\Models\Field;

trait FieldTrait
{
    public function storeFields()
    {
        DB::beginTransaction();
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Field::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            Field::insert($this->getFields());
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    public function getFields()
{
    $fields = [
        [
            'name' => 'Total Orders',
            'type' => FieldsTypes::INTEGER->value,
            'required' => true,
            'is_default' => true
        ],
        [
            'name' => 'Bonus',
            'type' => FieldsTypes::INTEGER->value,
            'required' => false,
            'is_default' => true
        ],
        [
            'name' => 'Tip',
            'type' => FieldsTypes::INTEGER->value,
            'required' => false,
            'is_default' => true
        ],
        [
            'name' => 'Other Tip',
            'type' => FieldsTypes::INTEGER->value,
            'required' => false,
            'is_default' => true
        ],
        [
            'name' => 'Upload Driver Documents',
            'type' => FieldsTypes::DOCUMENT->value,
            'required' => false,
            'is_default' => true
        ],
        [
            'name' => 'Cash Paid at Restaurant',
            'type' => FieldsTypes::INTEGER->value,
            'required' => false,
            'is_default' => false
        ],
        [
            'name' => 'Cash Collected by Driver',
            'type' => FieldsTypes::INTEGER->value,
            'required' => false,
            'is_default' => false
        ],
        [
            'name' => 'Net Cash Received at Branch',
            'type' => FieldsTypes::INTEGER->value,
            'required' => false,
            'is_default' => false
        ],
        [
            'name' => 'Balance In Wallet',
            'type' => FieldsTypes::INTEGER->value,
            'required' => false,
            'is_default' => false
        ],
        [
            'name' => 'Daily Mileage',
            'type' => FieldsTypes::INTEGER->value,
            'required' => false,
            'is_default' => false
        ],
        [
            'name' => 'Kilometer Driven',
            'type' => FieldsTypes::INTEGER->value,
            'required' => false,
            'is_default' => false
        ],
        [
            'name' => 'Fuel Amount',
            'type' => FieldsTypes::INTEGER->value,
            'required' => false,
            'is_default' => false
        ],
        [
            'name' => 'Remarks',
            'type' => FieldsTypes::TEXT->value,
            'required' => false,
            'is_default' => false
        ],
        [
            'name' => 'Penalties',
            'type' => FieldsTypes::INTEGER->value,
            'required' => false,
            'is_default' => false
        ],
        [
            'name' => 'Compensation',
            'type' => FieldsTypes::INTEGER->value,
            'required' => false,
            'is_default' => false
        ],
        [
            'name' => 'Delivered',
            'type' => FieldsTypes::INTEGER->value,
            'required' => false,
            'is_default' => false
        ],
        [
            'name' => 'Returned to Store',
            'type' => FieldsTypes::INTEGER->value,
            'required' => false,
            'is_default' => false
        ],
        [
            'name' => 'Cash Received',
            'type' => FieldsTypes::INTEGER->value,
            'required' => false,
            'is_default' => false
        ],
        [
            'name' => 'POS Amount',
            'type' => FieldsTypes::INTEGER->value,
            'required' => false,
            'is_default' => false
        ],
    ];

    // Generate short_name dynamically
    return array_map(function ($field) {
        $field['short_name'] = strtolower(str_replace(' ', '_', $field['name']));
        return $field;
    }, $fields);
}

}
