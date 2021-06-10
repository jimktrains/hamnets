<div>
  <table class="nets">
    <thead>
      <tr>
        <th>{{trans('common.id')}}</th>
        <th>{{trans('common.name')}}</th>
        <th>{{trans('common.band')}}</th>
        <th>{{trans('common.frequency')}}</th>
        <th>{{trans('common.mode')}}</th>
        <th>{{trans('common.start_time')}}</th>
        <th>{{trans('common.end_time')}}</th>
        <th>{{trans('common.timezone')}}</th>
        <th colspan=7>{{trans('common.operating_days')}}</th>
      </tr>
    </thead>
    <tbody>
      @foreach($Nets as $Net)
        <tr class="{{$Net->active ? "" : "inactive"}}" title="{{$Net->active ? "" : trans('common.inactive')}}">
          <td class="net_id"><a href="{{route('net', $Net->net_id)}}">{{$Net->net_id}}</a></td>
          <td>{{$Net->name}}
            @if(!empty($Net->description))
              <br>
              <small class="description">{{$Net->description}}</small>
            @endif
            @if(!empty($Net->url) || !empty($Net->arrl_net_id))
              <br>
              @if(!empty($Net->url))
                <small class="url"><a href="{{$Net->url}}">{{$Net->url}}</a></small>
              @endif
              @if(!empty($Net->arrl_net_id))
                <small class="url"><a href="http://www.arrl.org/webroot/resources/nets/client/netdetail.html?mfind={{$Net->arrl_net_id}}">(ARRL)</a></small>
              @endif
            @endif
            @if($Net->arrl_national_traffic_affiliated))
              <br>
              <small class="national_traffic_affiliated"><a href="http://www.arrl.org/nts"><abbr title="{{trans('common.nts')}}">NTS</abbr></a></small>
            @endif
            <td class="band">{{$Net->primary_band}}
              @if ($Net->secondary_band)
                <br>{{$Net->secondary_band}}
              @endif</td>
              <td class="frequency">{{$Net->format_primary_frequency()}}
                @if (!empty($Net->primary_frequency_repeaterbook_url()))
                  (<a href="{{$Net->primary_frequency_repeaterbook_url()}}"><abbr title="RepeaterBook">RB</abbr></a>)
                @endif
                @if(!empty($Net->secondary_frequency))
                  <br>
                  {{$Net->format_secondary_frequency()}}
                  @if (!empty($Net->secondary_frequency_repeaterbook_url()))
                    (<a href="{{$Net->secondary_frequency_repeaterbook_url()}}"><abbr title="RepeaterBook">RB</abbr></a>)
                  @endif
                @endif </td>
              <td>{{$Net->mode}}</td>
              <td class="time">{{$Net->start_time ? date('H:i', strtotime($Net->start_time)) : ""}}</td>
              <td class="time">{{$Net->end_time ? date('H:i', strtotime($Net->end_time)) : ""}}</td>
              <td>{{$Net->timezone}}</td>
              <td>{!!$Net->sunday     ? '<abbr title="'.trans('common.sunday').'">'.trans('common.sunday_short').'</abbr>' : "" !!} </td>
              <td>{!!$Net->monday     ? '<abbr title="'.trans('common.monday').'">'.trans('common.monday_short').'</abbr>' : "" !!} </td>
              <td>{!!$Net->tuesday    ? '<abbr title="'.trans('common.tuesday').'">'.trans('common.tuesday_short').'</abbr>' : "" !!} </td>
              <td>{!!$Net->wednesday  ? '<abbr title="'.trans('common.wednesday').'">'.trans('common.wednesday_short').'</abbr>' : "" !!} </td>
              <td>{!!$Net->thursday   ? '<abbr title="'.trans('common.thursday').'">'.trans('common.thursday_short').'</abbr>' : "" !!} </td>
              <td>{!!$Net->friday     ? '<abbr title="'.trans('common.friday').'">'.trans('common.friday_short').'</abbr>' : "" !!} </td>
              <td>{!!$Net->saturday   ? '<abbr title="'.trans('common.saturday').'">'.trans('common.saturday_short').'</abbr>' : "" !!} </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
