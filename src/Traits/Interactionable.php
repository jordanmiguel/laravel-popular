<?php

namespace JordanMiguel\LaravelPopular\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use JordanMiguel\LaravelPopular\Models\Interaction;

trait Interactionable
{
    /**
     * Registrates an interaction into the database if it does not exist on current day
     * (Registers unique interactions)
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function interaction($category = 'visit', $visitor = '')
    {
        if (empty($visitor)) {
            if (Auth::check()) {
                $visitor = 'user_' . Auth::id();
            } else {
                $visitor = request()->ip();
            }
        }

        return Interaction::firstOrCreate([
            'visitor' => $visitor,
            'date' => Carbon::now()->toDateString(),
            'interactionable_id' => $this->id,
            'interactionable_type' => (new \ReflectionClass($this))->getName(),
            'category' => $category
        ]);
    }

    /**
     * Setting relationship
     * @return mixed
     */
    public function interactions()
    {
        return $this->morphMany(Interaction::class, 'interactionable');
    }

    /**
     * Return count of the visits in the last day
     * @return mixed
     */
    public function interactionsDay()
    {
        return $this->visitsLast(1);
    }

    /**
     * Return count of the visits in the last 7 days
     * @return mixed
     */
    public function interactionsWeek()
    {
        return $this->visitsLast(7);
    }

    /**
     * Return count of the visits in the last 30 days
     * @return mixed
     */
    public function interactionsMonth()
    {
        return $this->visitsLast(30);
    }

    /**
     * Return the count of visits since system was installed
     * @return mixed
     */
    public function interactionsForever()
    {
        return $this->interactions()
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
     * Filter by popular in all time
     * @param $query
     * @return mixed
     */
    public function scopePopularAllTime($query)
    {
        return $query->withCount('interactions')->orderBy('interactions_count', 'desc');
    }

    /**
     * Return the visits of the model in the last ($days) days
     * @return mixed
     */
    public function interactionsLast($days)
    {
        return $this->interactions()
            ->where('date', '>=', Carbon::now()->subDays($days)->toDateString())
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
        return $query->withCount(['interactions' => function ($query) use ($days) {
            $query->where('date', '>=', Carbon::now()->subDays($days)->toDateString());
        }])->orderBy('interactions_count', 'desc');
    }
}
