@extends('base')

@section('title', trans('common.upcoming_nets'))

@section('content')
    <div class="nav">
      <div><form>
          <select id="timezone" name="timezone">
            @foreach($timezones as $tz)
              <option value="{{$tz}}" {{($tz==$timezone) ? "selected=selected" : ""}}>{{$tz}}</option>
            @endforeach
          </select>  |
          <label for="gridsquare">{{trans('common.grid_square')}}</label>
          <input type="text" id="gridsquare" name="gridsquare" value="{{$gridsquare}}" maxlength=6>
          <input type="button" onclick="getGridSquare()" value="Get Grid Square" />
        |
          <label for="hours_ahead">{{trans('common.hours_look_ahead')}}</label>
          <select id="hours_ahead" name="hours_ahead">
            @for($h = 1; $h < 24; $h++)
              <option value="{{$h}}" {{($h==$hoursAhead) ? "selected=selected" : ""}}>{{$h}}</option>
            @endfor
          </select> <!--|
          <select id="license_class" name="license_class">
            @foreach($licenseClasses as $lc)
              <option value="{{$lc}}" {{($lc==$licenseClass) ? "selected=selected" : ""}}>{{$lc}}</option>
            @endforeach
          </select> --> |
          @foreach($bands as $band)
            <input type="checkbox" id="band_{{$band}}" name="bands[]" value="{{$band}}" {{in_array($band, $selectedBands) ? "checked=checked" : ""}}>
            <label for="band_{{$band}}">{{$band}}</label>&nbsp;
          @endforeach
          <input type="submit" value="{{trans('common.search')}}">
        </form>
      </div>

    </div>
    <div id="upcoming-nets">
      <h2>{{trans('common.upcoming')}}</h2>
      <!--
      <div class="freq-bar">
        @foreach($NextNets as $Net)
          @if(7125000 <= $Net->primary_frequency and $Net->primary_frequency <= 7300000)
            <span class="freq" style="left: {{ (($Net->primary_frequency - 7125000)/(7300000 - 7125000))*800 }}px">{{$Net->format_primary_frequency()}}</span>
          @endif
        @endforeach
      </div>
      <div style="height:10px; border: 1px black solid;" class="freq-bar">
        @foreach($NextNets as $Net)
          @if(7125000 <= $Net->primary_frequency and $Net->primary_frequency <= 7300000)
            <span class="freq freq-viz" style="left: {{ (($Net->primary_frequency - 7125000)/(7300000 - 7125000))*800 }}px">&nbsp;</span>
          @endif
        @endforeach
      </div>
      -->

      <x-net-compact-table :nets="$NextNets" />
    </div>
    <div id="current-nets">
      <h1>{{trans('common.current')}}</h1>
      <x-net-compact-table :nets="$NowNets" />
    </div>

    @if (!empty($CoverageNets))
    <div id="coverage-nets">
      <h2>{{trans('common.coverage_for', ['gridsquare' => $gridsquare])}}</h2>
      <x-net-full-table :nets="$CoverageNets" />
    </div>
    @endif

    <div id="solar">
      <div>
        <h1>{{trans('common.solar_activity')}}</h1>
        <center>
          <a href="http://www.hamqsl.com/solar.html" title="Click to add Solar-Terrestrial Data to your website!"><img src="http://www.hamqsl.com/solar101vhfpic.php"></a>
        </center>

        <center>
          <a href="http://www.hamqsl.com/solar.html" title="Click to add Solar-Terrestrial Data to your website!"><img src="http://www.hamqsl.com/solarmuf.php"></a>
        </center>
      </div>
    </div>
@endsection
