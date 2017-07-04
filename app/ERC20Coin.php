<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ERC20Coin
 *
 * @property int $id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $user_id
 * @property string $name
 * @property string $ticker
 * @property int $decimals
 * @property string $address
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ERC20Coin whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ERC20Coin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ERC20Coin whereDecimals($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ERC20Coin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ERC20Coin whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ERC20Coin whereTicker($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ERC20Coin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ERC20Coin whereUserId($value)
 * @mixin \Eloquent
 */
class ERC20Coin extends Model
{
    public $fillable = ['user_id', 'address'];
}
