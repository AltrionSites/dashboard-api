<?php 
namespace App\Traits;

use Illuminate\Support\Str;

trait File
{
    public function generateFileUniqueName($model, $column)
    {
        $uuid = Str::uuid()->toString();
        while(!is_null($model::where($column, '=', $uuid)->first())){
            $uuid = Str::uuid()->toString();
        }
        return $uuid;
    }
}
?>