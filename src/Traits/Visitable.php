<?php

namespace JordanMiguel\LaravelPopular\Traits;

use JordanMiguel\LaravelPopular\Models\Visit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait Visitable
{
    /**
     * Registrates a visit into the database if it does not exist on current day
     * (Registers unique visitors)
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function visit($ip = '')
    {
        if(empty($ip)){
            $ip = request()->ip();
        }

        return $this->visits()->updateOrCreate(
            [
                'ip' => $ip,
                'date' => Carbon::now()->toDateString(),
                'visitable_id' => $this->id,
                'visitable_type' => (new \ReflectionClass($this))->getName(),
            ]
        );
    }

    /**
     * Setting relationship
     * @return mixed
     */
    public function visits()
    {
        return $this->morphMany(Visit::class, 'visitable');
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
     * Filter by popular in the last $days days
     * @param $query
     * @param $days
     * @return mixed
     */
    public function scopePopularLast($query, $days)
    {
        return $this->queryPopularLast($query, $days);
    }

    /**
     * Filter by popular in the last day
     * @param $query
     * @return mixed
     */
    public function scopePopularDay($query)
    {
        return $this->queryPopularLast($query, 1);
    }

    /**
     * Filter by popular in the last 7 days
     * @param $query
     * @return mixed
     */
    public function scopePopularWeek($query)
    {
        return $this->queryPopularLast($query, 7);
    }

    /**
     * Filter by popular in the last 30 days
     * @param $query
     * @return mixed
     */
    public function scopePopularMonth($query)
    {
        return $this->queryPopularLast($query, 30);
    }

    /**
     * Filter by popular in the last 365 days
     * @param $query
     * @return mixed
     */
    public function scopePopularYear($query)
    {
        return $this->queryPopularLast($query, 365);
    }

    /**
     * Filter by popular in a given interval date
     * @param $query
     * @return mixed
     */
    public function scopePopularBetween($query, $from, $to)
    {
        return $query->queryPopularBetween($query, $from, $to);
    }

    /**
     * Filter by popular in all time
     * @param $query
     * @return mixed
     */
    public function scopePopularAllTime($query)
    {
        return $query->withCount('visits')->orderBy('visits_count', 'desc');
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

    /**
     * Return the visits of the model in a given interval date
     * @return mixed
     */
    public function visitsBetween($from, $to)
    {
        return $this->visits()
            ->whereBetween('date', [$from, $to])
            ->count();
    }

    /**
     * Returns a Query Builder with Model ordered by popularity in the Last ($days) days
     * @param $query
     * @param $days
     * @return mixed
     */
    public function queryPopularLast($query, $days)
    {
        return $query->withCount(['visits' => function ($query) use ($days) {
            $query->where('date', '>=', Carbon::now()->subDays($days)->toDateString());
        }])->orderBy('visits_count', 'desc');
    }

    /**
     * Returns a Query Builder with Model ordered by popularity in a given interval date
     * @param $query
     * @param $days
     * @return mixed
     */
    public function queryPopularBetween($query, $from, $to)
    {
        return $query->withCount(['visits' => function ($query) use ($from, $to) {
            $query->whereBetween('date', [$from, $to]);
        }])->orderBy('visits_count', 'desc');
    }
}
