<?php

namespace JordanMiguel\LaravelPopular\Traits;

use Carbon\Carbon;
use JordanMiguel\LaravelPopular\Models\Interaction;

/**
 * Include this trait for backward compatibility.
 */

trait Visitable
{
    /**
     * Registrates a visit into the database if it does not exist on current day
     * (Registers unique visitors)
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function visit($visitor = '')
    {
        return $this->interaction('visit', $visitor);
    }

    /**
     * Setting relationship
     * @return mixed
     */
    public function visits()
    {
        return $this->morphMany(Interaction::class, 'interactionable');
    }

    /**
     * Return count of the visits in the last day
     * @return mixed
     */
    public function visitsDay()
    {
        return $this->visitsLast(1);
    }

    /**
     * Return count of the visits in the last 7 days
     * @return mixed
     */
    public function visitsWeek()
    {
        return $this->visitsLast(7);
    }

    /**
     * Return count of the visits in the last 30 days
     * @return mixed
     */
    public function visitsMonth()
    {
        return $this->visitsLast(30);
    }

    /**
     * Return the count of visits since system was installed
     * @return mixed
     */
    public function visitsForever()
    {
        return $this->visits()
            ->count();
    }

    /**
     * Return the visits of the model in the last ($days) days
     * @return mixed
     */
    public function visitsLast($days)
    {
        return $this->visits()
            ->where('date', '>=', Carbon::now()->subDays($days)->toDateString())
            ->count();
    }
}
