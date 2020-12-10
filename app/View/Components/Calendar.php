<?php

namespace App\View\Components;

use Illuminate\View\Component;
use DateTime;
use DateTimeZone;
use DateInterval;

class Calendar extends Component
{
    public $events;
    public $year;
    public $month;
    public $today;
    public $showInactive;

    private $counter_start;

    public function __construct($events, $year = null, $month = null, $timezone = null, $incMonth = null, $showInactive = true)
    {
      $this->showInactive = $showInactive;
      $dt = new DateTime();
      $tz = new DateTimeZone($timezone ?: 'America/New_York');
      $dt->setTimezone($tz);

      $this->today = $dt->format('Y-m-d');

      if (!empty($incMonth)) {
        $dt = $dt->add(new DateInterval('P1M'));
      }

      $this->events = $events;
      $this->year = $year ?: $dt->format('Y');
      $this->month = $month ?: $dt->format('m');

      $this->counter = 0;
      $this->counter_start = strtotime('first sunday of this month', $dt->getTimestamp());
      if (date('d', $this->counter_start) != 1) {
        $this->counter_start = strtotime('-7 days', $this->counter_start);
      }
    }

    public function strmonth() {
      return date('M', strtotime("{$this->year}-{$this->month}-01"));
    }

    public function year() {
      return $this->year;
    }

    public function cells() {
      $event_i = 0;
      for ($i = 0; $i < 42; $i++) {
        $d = strtotime("$i days", $this->counter_start);
        $nd = strtotime("$i days + 1 days", $this->counter_start);

        $hasEvent = false;
        $hasEventLong = false;
        $hasEventLongStart = false;
        $hasEventLongEnd = false;

        for (; $event_i < count($this->events); $event_i++) {
          $e = $this->events[$event_i];
          $ed = strtotime($e->start_timestamp);

          if ($ed >= $nd) {
            break;
          }
          if (date('d', $ed) == date('d', $d) && date('m', $ed) == date('m', $d)) {
            $hasEvent = true;
            $hasEventLong = $e->prev_day || $e->next_day;
            $hasEventLongStart = (!$e->prev_day) && $e->next_day;
            $hasEventLongEnd = $e->prev_day && (!$e->next_day);
          }
        }

        $day      = date('d', $d);
        $inactive = date('m', $d) != $this->month ? "inactive" : "";
        $sunday   = date('N', $d) == 7;
        $saturday = date('N', $d) == 6;
        $event    = $hasEvent ? "event" : "";
        $long     = $hasEventLong ? "long" : "";
        $start    = $hasEventLongStart ? "start" : "";
        $end      = $hasEventLongEnd ? "end" : "";
        $today    = date('Y-m-d', $d) == $this->today ? "today" : "";

        if ($inactive && !$this->showInactive) {
          $day      = '';
          $sunday   = date('N', $d) == 7;
          $saturday = date('N', $d) == 6;
          $event    = '';
          $long     = '';
          $start    = '';
          $end      = '';
          $today    = '';
        }

        yield compact('day', 'inactive', 'sunday', 'saturday', 'long', 'start', 'end', 'today', 'event');

        if ($saturday and $inactive) {
          break;
        }
      }
    }

    public function render()
    {
        return view('components.calendar');
    }
}
