<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    /**
     * Flag to indicate that a winner has been drawn
     */
    const WINNER_DRAWN = 1;

    /**
     * The expected name of the Winning Moment mechanic
     */
    const WINNING_MOMENT_MECHANISM_NAME = 'winning-moment';

    /**
     * The expected name of the Chance mechanic
     */
    const CHANCE_MECHANISM_NAME = 'chance';

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
