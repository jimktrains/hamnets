<div>
      <table>
        <thead>
          <tr>
            <!--<th>Id</th>-->
            <th>{{trans('common.name')}}</th>
            <th>{{trans('common.band')}}</th>
            <th>{{trans('common.frequency')}}</th>
            <th>{{trans('common.mode')}}</th>
            <th>{{trans('common.start_time')}}</th>
            <th>{{trans('common.end_time')}}</th>
          </tr>
        </thead>
        @foreach($Nets as $Net)
          <tr>
            <!-- <td>{{$Net->net_id}}</td>-->
            <td>{{$Net->name}}
            @if (!empty($Net->url))
              <small>(<a href="{{$Net->url}}" title="Link to net website">www</a>)</small>
            @else
              <small>(
                <a href="https://google.com?q={{$Net->name}}" title="Google Search for the net name">G</a>
                |
                <a href="https://duckduckgo.com?q={{$Net->name}}" title="DuckDuckGo Search for the net name">D</a>
              )</small>
            @endif
            @if (!empty($Net->net_id))
              <small>(<a href="{{route('net', $Net->net_id)}}" title="HamNet Database Link">hnd</a>)</small>
            @endif
            @if (!empty($Net->Frequency_raw))
              <small>(<abbr title="via NetLogger">NL</abbr>)</small>
            @endif
            </td>
            <td>{{$Net->primary_band}}</td>
            <td class="frequency">{{$Net->format_primary_frequency()}}</td>
            <td>{{$Net->mode}}</td>
            <td>{{$Net->local_start_time}}</td>
            @if (!empty($Net->local_end_time))
              <td class="{{$Net->end_timestamp_is_estimated ? 'estimated' : ''}}" title="{{$Net->end_timestamp_is_estimated ? trans('common.estimated_end_time') : ''}}">{{$Net->local_end_time}}</td>
            @endif
          </tr>
        @endforeach
      </table>
</div>
