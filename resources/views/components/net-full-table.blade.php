<div>
  <table class="nets">
    <thead>
      <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Band</th>
        <th>Frequency</th>
        <th>Start Time</th>
        <th>End Time</th>
        <th>Timezone</th>
        <th colspan=7>Operating Days</th>
      </tr>
    </thead>
    <tbody>
      @foreach($Nets as $Net)
        <tr class="{{$Net->active ? "" : "inactive"}}" title="{{$Net->active ? "" : "inactive"}}">
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
              <small class="national_traffic_affiliated"><abbr title="National Traffic System">NTS</abbr></small>
            @endif
            <td>{{$Net->band}}
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
              <td>{{$Net->start_time}}</td>
              <td>{{$Net->end_time}}</td>
              <td>{{$Net->timezone}}</td>
              <td>{!!$Net->sunday     ? '<abbr title="Sunday">S</abbr>' : "" !!} </td>
              <td>{!!$Net->monday     ? '<abbr title="Monday">M</abbr>' : "" !!} </td>
              <td>{!!$Net->tuesday    ? '<abbr title="Tuesday">T</abbr>' : "" !!} </td>
              <td>{!!$Net->wednesday  ? '<abbr title="Wednesday">W</abbr>' : "" !!} </td>
              <td>{!!$Net->thursday   ? '<abbr title="Thursday">H</abbr>' : "" !!} </td>
              <td>{!!$Net->friday     ? '<abbr title="Friday">F</abbr>' : "" !!} </td>
              <td>{!!$Net->saturday   ? '<abbr title="Saturday">A</abbr>' : "" !!} </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
