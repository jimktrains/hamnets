<div>
      <table>
        <thead>
          <tr>
            <!--<th>Id</th>-->
            <th>{{trans('common.name')}}</th>
            <th>{{trans('common.band')}}</th>
            <th>{{trans('common.frequency')}}</th>
            <th>{{trans('common.start_time')}}</th>
            <th>{{trans('common.end_time')}}</th>
          </tr>
        </thead>
        @foreach($Nets as $Net)
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
            <td class="{{$Net->end_timestamp_is_estimated ? 'estimated' : ''}}" title="{{$Net->end_timestamp_is_estimated ? trans('common.estimated_end_time') : ''}}">{{$Net->end_time}}</td>
          </tr>
        @endforeach
      </table>
</div>
