<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepeaterbook extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<EOS
begin;

create or replace function latlon2maidenhead(lat numeric, lon numeric)
returns text
language 'plpgsql'
immutable
returns null on null input
as $$
-- Modified from view-source:https://www.qsl.net/k7jar/Links_files/latlon2grid_1.html
declare
  d0 text;
  d1 text;
  d2 text;
  d3 text;
  d4 text;
  d5 text;
begin


	lon = lon + 180;
	d0 := chr(65 + trunc(lon / 20)::int);	 -- number of 20deg blocks
	lon := lon % 20; -- degrees left after removing the 20deg blocks
	d2 := trunc(lon / 2)::int::text; -- number of 2deg blocks

	lon := lon % 2; -- degrees left after removing the 2deg blocks
	lon := lon * 60; -- convert them to minutes
	d4 := chr(97 + (lon / 5)::int);	-- number of 5min blocks; rounding error patch
	-- if (gSqrLen === 8)
	-- {
	-- 	lon %= 5;										//minutes left after removing the 5min blocks
	-- 	lon = ((lon * 60) / (30 - gOneTenth)) % 10;		//convert to seconds (1/10th second - filon rounding error)
	-- 	d6 = intRnd2Num(lon, 6);						//number of half second blocks
	-- }
	lat := lat + 90.0;										-- repeat the above using 10deg, 1deg, 2.5min
	d1 := chr(65 + trunc(lat / 10)::int);	--   and quarter second increments
	lat := lat % 10;
	d3 := lat::int::text;

	lat := lat % 1;
	lat := lat * 60;
	d5 = chr(97 + trunc(lat / 2.5)::int);
  -- if (gSqrLen === 8)
	-- {
	-- 	y %= 2.5;
	-- 	y = ((y * 60) / (15 - gOneTenth)) % 10;		// (1/10th second - fix rounding error)
	-- 	d7 = intRnd2Num(y, 8);
	-- }

  return d0 || d1 || d2 || d3 || d4 || d5;
end;
$$;

create table repeaterbook_raw (
            state int not null,
            id int not null,
            output_freq numeric,
            input_freq numeric,
            "offset" text,
            uplink_tone text,
            downlink_tone text,
            location text,
            county text,
            lat numeric,
            long numeric,
            call text,
            use text,
            op_status text,
            mode text,
            digital_access text,
            echolink text,
            irlp text,
            allstar text,
            coverage text,
            nets text,
            last_update date,

            output_freq_hz bigint generated always as (output_freq * 1000000) stored,
            input_freq_hz bigint generated always as (output_freq * 1000000) stored,
            gridsquare text generated always as (latlon2maidenhead(lat, long)) stored,
            url text generated always as (('https://www.repeaterbook.com/repeaters/details.php?state_id=' || state::text  || '&ID=' || id::text)) stored,

            primary key (state, id)
);

alter table net add constraint net_p_rb_fk foreign key (primary_repeaterbook_state_id, primary_repeaterbook_repeater_id) references repeaterbook_raw(state, id);
alter table net add constraint net_s_rb_fk foreign key (secondary_repeaterbook_state_id, secondary_repeaterbook_repeater_id) references repeaterbook_raw(state, id);

insert into band (frequencies, name, country) values ( '[902000000,928000001)', '33cm', 'US'), ('[1270000000,1295000001)', '23cm', 'US');

commit;
EOS
          );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec(<<<EOS
begin;
drop table repeaterbook_raw cascade;
drop function latlon2maidenhead;
commit;
EOS
        );
    }
}
