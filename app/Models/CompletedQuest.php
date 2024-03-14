<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompletedQuest extends Model
{
    use HasFactory;

    protected $table = 'completed_quests';

    public $timestamps = false;

    protected $fillable = ['user_id', 'quest_id', 'completed_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quest()
    {
        return $this->belongsTo(Quest::class);
    }
}
