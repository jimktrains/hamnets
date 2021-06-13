<?php

namespace App\Console\Commands\RepeaterBook;

use Illuminate\Console\Command;
use DB;

class ProcessNets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repeaterbook:process-nets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      DB::transaction(function () {
        $repeatersWithNets = DB::table('repeaterbook_raw')
          ->whereNotNull('nets')
          ->leftJoin('tz_us', fn ($q) => $q->whereRaw('st_contains(geom, st_point(long, lat))'))
          ->get();
        $didntParse = [];
        $parsedCount = 0;
        foreach ($repeatersWithNets as $repeaterWithNets) {
          $nets = array_filter(
            array_map(fn($x) => str_replace(["\r", "\n"], "", $x),
            array_map('trim',
            preg_split('~<br */?>~', $repeaterWithNets->nets))));
          $timezones = [
            'eastern',
            'eastern time',
            '\(eastern time, standard or daylight savings\)',
            'et',
            'est',
            'edt',

            'central',
            'ct',
            'cst',
            'cdt',

            'mountain',
            'mountain time',
            'mt',
            'mst',
            'mdt',

            'pacific',
            'pacific time',
            'pt',
            'pst',
            'pdt',
          ];
          $timezones = join('|', array_map(fn ($x) => "($x)", $timezones));
          $regex = "(?<name>.+)(:|(--?))?" .
            " *((?<every>every)|((the)?(?<first>first)|(?<second>second)|(?<third>third)|(?<fourth>fourth)))? *".
            " *(and ((?<first2>first)|(?<second2>second)|(?<third2>third)|(?<fourth2>fourth)))? *".
            "(" .
            "(?<sunday>sun(day)?)|".
            "(?<monday>mon(day)?)|".
            "(?<tuesday>tues?(day)?)|".
            "(?<wednesday>wed(nesday)?)|".
            "(?<thursday>thur?s?(day)?)|".
            "(?<friday>fri(day)?)|".
            "(?<saturday>sat(urday)?)".
            ")s?".
            " *((evening)|(night)|(morning)|(of ((the)|(each)) month))? *" .
            " +((at)|@)? *".
            "(?<time>[0-9]+:?[0-9]{2}( *([ap]m)|([ap] ))?)( *hrs)?" .
            "( *- *(?<endtime>[0-9]+:?[0-9]{2}( *([ap]m)|([ap] ))?)( *hrs)?)?" .
            "( *(except holidays))?".
            "(?<timezone>(?<localtime>((local time)|(local)|(\(local\))))|{$timezones})?\.?";
          DB::table('net')->where('primary_repeaterbook_state_id', '=', $repeaterWithNets->state)
                          ->where('primary_repeaterbook_repeater_id', '=', $repeaterWithNets->id)
                          ->delete();
          foreach ($nets as $net) {
            if (preg_match("~{$regex}~i", $net, $matches)) {
              $parsedCount++;
              $matches = array_map('trim', $matches);
              if (!empty($matches['localtime'])) {
                $matches['timezone'] = $repeaterWithNets->tzid;
              } elseif(!array_key_exists('timezone', $matches)) {
                $matches['timezone'] = $repeaterWithNets->tzid;
              } elseif (in_array(strtolower($matches['timezone']), ['eastern time', 'eastern', 'et', '(eastern time, standard or daylight savings)'])) {
                $matches['timezone'] = 'America/New_York';
              } elseif (in_array(strtolower($matches['timezone']), ['central time', 'central', 'ct'])) {
                $matches['timezone'] = 'America/Chicago';
              } elseif (in_array(strtolower($matches['timezone']), ['mountain time', 'mountain', 'mt'])) {
                $matches['timezone'] = 'America/Denver';
              } elseif (in_array(strtolower($matches['timezone']), ['pacific time', 'pacific', 'pt', 'pst'])) {
                $matches['timezone'] = 'America/Los_Angeles';
              } elseif (empty($matches['timezone'])) {
                $matches['timezone'] = $repeaterWithNets->tzid;
              } elseif (preg_match('/\d/', $matches['timezone'])) {
                var_dump($net);
                var_dump($matches['timezone']);
                $matches['timezone'] = $repeaterWithNets->tzid;
              }

              if (preg_match('/(\d{2}):(\d{2}) *[ap]m?/i', $matches['time'], $timematch)) {
                if (intval($timematch[1]) > 12) {
                  $matches['time'] = $timematch[1] . ':' .  $timematch[2];
                }
              }
              if (preg_match('/(\d{1,2})(\d{2}) *([ap]m?)/i', $matches['time'], $timematch)) {
                if (strlen($timematch[3]) == 1) {
                  $timematch[3] .= 'm';
                }
                if (intval($timematch[1]) > 12) {
                  $timematch[3] = '';
                } else {
                  $timematch[3] = ' ' . $timematch[3];
                }
                $matches['time'] = $timematch[1] . ':' .  $timematch[2] . $timematch[3];
              }
              $row = [
                'created_at' => DB::raw('now()'),
                'updated_at' => DB::raw('now()'),
                'name' => $matches['name'],
                'description' => $net,
                'url' => null,
                'primary_frequency' => $repeaterWithNets->output_freq_hz,
                'start_time' => $matches['time'],
                'end_time' => null,
                'timezone' => $matches['timezone'],
                'active' => true,
                'sunday' => !empty($matches['sunday']),
                'monday' => !empty($matches['monday']),
                'tuesday' => !empty($matches['tuesday']),
                'wednesday' => !empty($matches['wednesday']),
                'thursday' => !empty($matches['thursday']),
                'friday' => !empty($matches['friday']),
                'saturday' => !empty($matches['saturday']),
                'primary_frequency_url' => $repeaterWithNets->url,
                'primary_frequency_is_repeater' => true,
                'mode' => "FM", // Not all of these are FM. Need to tease out the digital ones.
                'repeater_gridsquare' => $repeaterWithNets->gridsquare,
                'repeaterbook_primary_url' => $repeaterWithNets->url,
                'primary_repeater_gridsquare' => $repeaterWithNets->gridsquare,
                'primary_repeaterbook_state_id' => $repeaterWithNets->state,
                'primary_repeaterbook_repeater_id' => $repeaterWithNets->id,
              ];
              DB::table('net')->insert($row);

            } else {
              $didntParse[] = [$repeaterWithNets, $net];
            }
          }
        }
        $this->info("Parsed {$parsedCount} nets");
        $this->info("Couldn't parse " . count($didntParse) . " nets");
        DB::statement('refresh materialized view netschedule');
      });
      return 0;
    }
}
