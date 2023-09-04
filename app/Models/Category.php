<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    public function root()
    {
        return $this->hasOne(Category::class, 'id', 'root_id');
    }

    public function childs()
    {
        return $this->hasMany(Category::class, 'root_id', 'id');
    }

    public function childsId($root = null)
    {
        if (!$root)
            $root = $this->childs;
        $array = array();
        foreach ($root as $ctg) {
            array_push($array, $ctg->id);
            if (count($ctg->childs))
                $array = array_merge($array, $this->childsId($ctg->childs));
        }
        return $array;
    }

    public function products()
    {
        $childsIds = $this->childsId();
        return $this->hasMany(Product::class, 'ctg_id', 'id')->orWhereIn('ctg_id', $childsIds);
    }
}