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
     * Return count of the interactions in the last day
     * @return mixed
     */
    public function interactionsDay($category = null)
    {
        return $this->interactionsLast(1, $category);
    }

    /**
     * Return count of the interactions in the last 7 days
     * @return mixed
     */
    public function interactionsWeek($category = null)
    {
        return $this->interactionsLast(7, $category);
    }

    /**
     * Return count of the interactions in the last 30 days
     * @return mixed
     */
    public function interactionsMonth($category = null)
    {
        return $this->interactionsLast(30, $category);
    }

    /**
     * Return the count of interactions since system was installed
     * @return mixed
     */
    public function interactionsForever($category = null)
    {
        $query = $this->interactions();

        if ($category) {
            $query->where('category', '=', $category);
        }

        return $query->count();
    }

    /**
     * Filter by popular in the last $days days
     * @param $query
     * @param $days
     * @return mixed
     */
    public function scopePopularLast($query, $days, $category = null)
    {
        return $this->queryPopularLast($query, $days, $category);
    }

    /**
     * Filter by popular in the last day
     * @param $query
     * @return mixed
     */
    public function scopePopularDay($query, $category = null)
    {
        return $this->queryPopularLast($query, 1, $category);
    }

    /**
     * Filter by popular in the last 7 days
     * @param $query
     * @return mixed
     */
    public function scopePopularWeek($query, $category = null)
    {
        return $this->queryPopularLast($query, 7, $category);
    }

    /**
     * Filter by popular in the last 30 days
     * @param $query
     * @return mixed
     */
    public function scopePopularMonth($query, $category = null)
    {
        return $this->queryPopularLast($query, 30, $category);
    }

    /**
     * Filter by popular in the last 365 days
     * @param $query
     * @return mixed
     */
    public function scopePopularYear($query, $category = null)
    {
        return $this->queryPopularLast($query, 365, $category);
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
     * Return the interactions of the model in the last ($days) days
     * @return mixed
     */
    public function interactionsLast($days, $category = null)
    {
        $query = $this->interactions()
            ->where('date', '>=', Carbon::now()->subDays($days)->toDateString());

        if ($category) {
            $query->where('category', '=', $category);
        }

        return $query->count();
    }

    /**
     * Returns a Query Builder with Model ordered by popularity in the Last ($days) days
     * @param $query
     * @param $days
     * @return mixed
     */
    public function queryPopularLast($query, $days, $category = null)
    {
        return $query->withCount(['interactions' => function ($query) use ($days, $category) {
            $query->where('date', '>=', Carbon::now()->subDays($days)->toDateString());

            if ($category) {
                $query->where('category', '=', $category);
            }

        }])->orderBy('interactions_count', 'desc');
    }
}
