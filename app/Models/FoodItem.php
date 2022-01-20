<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'api_id',
        'name',
        'total_weight',
        'quantity',
        'quantity_measure',
        'calories',
        'carbs',
        //'sugar',
        //'fiber',
        'proteins',
        'fat',
        //'saturated_fat'
    ];

    // /**
    //  * The default values of the attributes
    //  *
    //  * @var array
    //  */
    // protected $attributes = [
    //     'calories' => 2000,
    //     'carbs' => 100,
    //     'proteins' => 100,
    //     'fat' => 100
    // ];

    //     /**
    //  * The attributes excluded from the model's JSON form.
    //  *
    //  * @var array
    //  */
    // protected $hidden = [
    //     'created_at', 'updated_at'
    // ];

    /**
     * Get the user that owns the goal.
     */
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
}
