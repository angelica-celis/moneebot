<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\PendingTransaction
 *
 * @property int $id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $username
 * @property float $value
 * @property string $tx
 * @property string|null $phone
 * @property string|null $telegram_id
 * @property string|null $coin
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\PendingTransaction onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PendingTransaction whereCoin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PendingTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PendingTransaction whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PendingTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PendingTransaction wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PendingTransaction whereTelegramId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PendingTransaction whereTx($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PendingTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PendingTransaction whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PendingTransaction whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PendingTransaction withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\PendingTransaction withoutTrashed()
 * @mixin \Eloquent
 */
class PendingTransaction extends Model
{
    use SoftDeletes;
}
