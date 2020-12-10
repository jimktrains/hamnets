<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class NextNet extends Net
{
  protected $table = "net";

  /**
   * The "booting" method of the model.
   *
   * @return void
   */
  protected static function boot()
  {
    parent::boot();


    static::addGlobalScope('schedule', function (Builder $builder) {
      return $builder->addSelect('net.*')
                     ->addSelect('netschedule.start_timestamp')
                     ->addSelect('netschedule.end_timestamp')
                     ->addSelect('netschedule.end_timestamp_is_estimated')
                     ->join('netschedule', 'netschedule.net_id', '=', 'net.net_id');
    });
  }

  public function scopeUpcoming($query, $hours = 1)
  {
    return $query->where(function ($query) use ($hours) {
      return $query->whereRaw("current_timestamp between start_timestamp and end_timestamp")
            ->orWhereRaw("start_timestamp between current_timestamp and current_timestamp + make_interval(hours := ?::integer)", $hours);
    });
  }

  public function scopeOngoing($query)
  {
    return $query->whereRaw("(current_timestamp between start_timestamp and end_timestamp)");
  }

  public function scopeForTz($query, $timezone)
  {
    return $query->selectRaw("
to_char(start_timestamp  at time zone ?, 'HH24:MI') as local_start_time,
to_char(end_timestamp at time zone ?, 'HH24:MI') as local_end_time,
start_timestamp at time zone ?  as local_start_timestamp,
end_timestamp at time zone ?    as local_end_timestamp
", [$timezone, $timezone, $timezone, $timezone]);
  }
}
