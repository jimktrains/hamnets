@extends('base')

@section('title', 'Upcoming Nets')

@section('content')
    <div class="nav">
      <div><form>
          <select id="timezone" name="timezone">
            @foreach($timezones as $tz)
              <option value="{{$tz}}" {{($tz==$timezone) ? "selected=selected" : ""}}>{{$tz}}</option>
            @endforeach
          </select>  |
          <label for="gridsquare">Grid Square</label>
          <input type="text" id="gridsquare" name="gridsquare" value="{{$gridsquare}}" maxlength=6>
          <input type="button" onclick="getGridSquare()" value="Get Grid Square" />
        |
          <label for="hours_ahead">Hours to Look Ahead</label>
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
          <input type="submit" value="Update">
        </form>
      </div>

    </div>
    <div id="upcoming-nets">
      <h2>Upcoming</h2>
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

      <table>
        <thead>
          <tr>
            <!--<th>Id</th>-->
            <th>Name</th>
            <th>Band</th>
            <th>Frequency</th>
            <th>Start Time</th>
            <th>End Time</th>
          </tr>
        </thead>
        @foreach($NextNets as $Net)
          <tr>
            <!-- <td>{{$Net->net_id}}</td>-->
            <td>{{$Net->name}}
            @if (!empty($Net->url))
              <small>(<a href="{{$Net->url}}">www</a>)</small>
            @endif
            <small>(<a href="{{route('net', $Net->net_id)}}">hnd</a>)</small>
            </td>
            <td>{{$Net->band}}</td>
            <td class="frequency">{{$Net->format_primary_frequency()}}</td>
            <td>{{$Net->start_time}}</td>
            <td class="{{$Net->end_timestamp_is_estimated ? 'estimated' : ''}}" title="{{$Net->end_timestamp_is_estimated ? 'estimated end time' : ''}}">{{$Net->end_time}}</td>
          </tr>
        @endforeach
      </table>
    </div>
    <div id="current-nets">
      <h1>Current Nets</h1>
      <table>
        <thead>
          <tr>
            <!--<th>Id</th>-->
            <th>Name</th>
            <th>Band</th>
            <th>Frequency</th>
            <th>Start Time</th>
            <th>End Time</th>
          </tr>
        </thead>
        @foreach($NowNets as $Net)
          <tr>
           <!--<td>{{$Net->net_id}}</td>-->
            <td>{{$Net->name}}
            @if (!empty($Net->url))
              <small>(<a href="{{$Net->url}}">www</a>)</small>
            @endif
            <small>(<a href="{{route('net', $Net->net_id)}}">hnd</a>)</small>
            </td>
            <td>{{$Net->band}}</td>
            <td class="frequency">{{$Net->format_primary_frequency()}}</td>
            <td>{{$Net->start_time}}</td>
            <td class="{{$Net->end_timestamp_is_estimated ? 'estimated' : ''}}" title="{{$Net->end_timestamp_is_estimated ? 'estimated end time' : ''}}">{{$Net->end_time}}</td>
          </tr>
        @endforeach
      </table>
    </div>

    @if (!empty($CoverageNets))
    <div id="coverage-nets">
      <h2>Coverage Nets for {{$gridsquare}}</h2>
      <x-net-full-table :nets="$CoverageNets" />
    </div>
    @endif

    <div id="solar">
      <div>
        <h1>Solar Activity</h1>
        <center>
          <a href="http://www.hamqsl.com/solar.html" title="Click to add Solar-Terrestrial Data to your website!"><img src="http://www.hamqsl.com/solar101vhfpic.php"></a>
        </center>

        <center>
          <a href="http://www.hamqsl.com/solar.html" title="Click to add Solar-Terrestrial Data to your website!"><img src="http://www.hamqsl.com/solarmuf.php"></a>
        </center>
      </div>
    </div>
@endsection
