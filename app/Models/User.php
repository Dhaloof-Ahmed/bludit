<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'location'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getName() {
        if ($this->first_name && $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        }
        if ($this->first_name) {
            return $this->first_name;
        }
        return null;
    }

    public function getNameOrUsername(){
        return $this->getName() ?: $this->username;
    }

    public function getFirstNameOrUsername(){
        return $this->first_name ?: $this->username;
    }

    public function getAvatarUrl(){
        return "https://www.gravatar.com/avatar/{{ md5($this->email) }}?d=mm&s=40";
    }

    public function statuses(){
        return $this->hasMany('App\Models\Status', 'user_id');
    }

    public function likes() {
        return $this->hasMany('App\Models\Like', 'user_id');
    }

    public function friendsOfMine(){
        return $this->belongsToMany('App\Models\User', 'friends', 'user_id', 'friend_id');
    }

    public function friendOf(){
        return $this->belongsToMany('App\Models\User', 'friends', 'friend_id', 'user_id');
    }

    public function friends(){
        return $this->friendsOfMine()->wherePivot('accepted', true)->get()->merge($this->friendOf()->wherePivot('accepted', true)->get());
    }

    public function friendRequests(){
        return $this->friendsOfMine()->wherePivot('accepted', false)->get();  
    }

    public function friendRequestsPending(){
        return $this->friendOf()->wherePivot('accepted', false)->get();  

    }

    public function hasFriendRequestPending(User $user){
        return (bool) $this->friendRequestsPending()->where('id', $user->id)->count();
    }

    public function hasFriendRequestReceived(User $user){
        return (bool) $this->friendRequests()->where('id', $user->id)->count();
    }

    public function addFriend(User $user){
        $this->friendOf()->attach($user->id);
    }

    public function acceptFriendRequest(User $user){
        $this->friendRequests()->where('id', $user->id)->first()->pivot->
        update([
            'accepted' => true,
        ]);
    }

    public function isFriendsWith(User $user){
        return (bool) $this->friends()->where('id', $user->id)->count();
    }

    public function hasLikedStatus(Status $status) {
        return (bool) $status->likes
            ->where('likeable_id', $status->id)
            ->where('likeable_type', get_class($status))
            ->where('user_id', $this->id)
            ->count();
    }
}
