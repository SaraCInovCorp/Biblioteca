<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\BookRequest;
use App\Models\BookReview;
use App\Models\LivroWaitingList;
use App\Models\Endereco;
use App\Models\Carrinho;
use App\Models\Encomenda;
use Laravel\Jetstream\HasTeams;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasTeams;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

   public function bookRequests()
    {
        return $this->hasMany(BookRequest::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isCidadao()
    {
        return $this->role === 'cidadao';
    }

    public function requisicoes()
    {
        return $this->hasMany(BookRequest::class, 'user_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(BookReview::class, 'user_id');
    }

    public function livrosAguardando()
    {
        return $this->hasMany(LivroWaitingList::class);
    }

    public function enderecos()
    {
        return $this->hasMany(Endereco::class, 'user_id');
    }

    public function carrinhos()
    {
        return $this->hasMany(Carrinho::class, 'user_id');
    }

    public function encomendas()
    {
        return $this->hasMany(Encomenda::class, 'user_id');
    }

    public function document()
    {
        return $this->hasOne(UserDocument::class);
    }

}
