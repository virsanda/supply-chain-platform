<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','password','role','avatar','is_active'];
    protected $hidden   = ['password','remember_token'];
    protected $casts    = ['email_verified_at'=>'datetime','password'=>'hashed','is_active'=>'boolean'];

    public function watchlists()  { return $this->hasMany(Watchlist::class); }
    public function articles()    { return $this->hasMany(Article::class,'author_id'); }
    public function comparisons() { return $this->hasMany(ComparisonSnapshot::class); }

    public function isAdmin(): bool { return $this->role === 'admin'; }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) return asset('storage/'.$this->avatar);
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=0D6EFD&color=fff&size=64';
    }
}
