<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    const WINNER_DRAWN = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'client',
        'entry_fields',
        'promotion_mechanic',
        'winning_moment_time',
        'winner_drawn',
        'entry_count'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * @return string
     */
    public function getMechanism()
    {
        return $this->attributes['promotion_mechanic'];
    }

    /**
     * @return string
     */
    public function getWinningMomentTime()
    {
        return $this->attributes['winning_moment_time'];
    }

    /**
     * @return int
     */
    public function getWinnerDrawn()
    {
        return $this->attributes['winner_drawn'];
    }

    /**
     * @return void
     */
    public function setWinnerDrawn()
    {
        $this->attributes['winner_drawn'] = self::WINNER_DRAWN;
        $this->save();
    }

    /**
     * @return int
     */
    public function getEntryCount()
    {
        return $this->attributes['entry_count'];
    }

    /**
     * @return void
     */
    public function incrementEntryCount()
    {
        $this->attributes['entry_count']++;
        $this->save();
    }
}
