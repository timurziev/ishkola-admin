<?php

namespace App\Models\Auth\User;

use App\Models\Auth\User\Traits\Ables\Protectable;
use App\Models\Auth\User\Traits\Attributes\UserAttributes;
use App\Models\Schedule;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Auth\User\Traits\Ables\Rolable;
use App\Models\Auth\User\Traits\Scopes\UserScopes;
use App\Models\Auth\User\Traits\Relations\UserRelations;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Lang;
use App\Models\Lesson;
use App\Models\Rate;
use App\Models\Discount;

/**
 * App\Models\Auth\User\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property bool $active
 * @property string $confirmation_code
 * @property bool $confirmed
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property-read mixed $avatar
 * @property-read mixed $licensee_name
 * @property-read mixed $licensee_number
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Protection\ProtectionShopToken[] $protectionShopTokens
 * @property-read \App\Models\Protection\ProtectionValidation $protectionValidation
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Auth\User\SocialAccount[] $providers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Auth\Role\Role[] $roles
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\User\User sortable($defaultSortParameters = null)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\User\User whereActive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\User\User whereConfirmationCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\User\User whereConfirmed($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\User\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\User\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\User\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\User\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\User\User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\User\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\User\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\User\User whereRole($role)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Auth\User\User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Rolable,
        UserAttributes,
        UserScopes,
        UserRelations,
        Notifiable,
        SoftDeletes,
        Sortable,
        Protectable;

    public $sortable = ['name', 'email', 'notes', 'phone', 'miraID', 'created_at', 'updated_at'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'notes', 'phone', 'miraID', 'password', 'active', 'confirmation_code', 'confirmed'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function langs()
    {
        return $this->belongsToMany(Lang::class);
    }

    public function rates()
    {
        return $this->hasMany(Rate::class);
    }

    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }

    public function avatar()
    {
        return $this->hasOne(Avatar::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public static function userIdByRole($ids, $arg)
    {
        $user = User::whereIn('id', $ids)->whereHas('roles', function ($q) use ($arg) {
            $q->where('name', $arg);
        })->first();

        return $user->id;
    }

    public static function sendPaymentInfo($userId, $mirapolis)
    {
        $schedule = Schedule::whereHas('payments', function ($q) use ($userId) {
            $q->where('paid', 0)->where('user_id', $userId);
        })->first();

        if (isset($schedule)) {
            $schedule = $schedule->schedule->subDays(2)->format('d.m.Y');

            $user = User::whereId($userId)->first();
            $id = $user->miraID;

            $phone = ["personworktel" => "Следующую оплату необходимо сделать до $schedule"];


            $service_url = $mirapolis->miraURL('persons', null, $id);
            $mirapolis->sendRequest($service_url, $phone, "PUT");
        }
    }
}
