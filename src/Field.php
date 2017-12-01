<?php
namespace HdCustomMeta;

class Field
{
    public function fieldProtection()
    {
        return [];
    }

    public static function protectedSave($protection, $value)
    {
        switch($protection){
            case 'multiple_select':
                if (is_array($value)) {
                    $value = json_encode($value);
                } else {
                    $value = json_encode([$value]);
                }
                break;
            case 'date':
                $value = strtotime($value);
                break;
        }

        return $value;
    }
}