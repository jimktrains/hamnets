<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">

    <title>{{$Net->name}} -- Ham Net Database</title>

    <style>
#header {
  margin-bottom: 20px;
  border-bottom: 1px black solid;
}

      #description {
        margin-top: 2em;
      }

      .frequencies {
        list-style-type: upper-alpha;
      }
    </style>
  </head>
  <body>
    <div id="header">
      <a href="{{route('home')}}">Home</a>
    </div>
    <div id="net">
      <h1>{{$Net->name}}</h1>

      <ol class="frequencies">
        <li>{{$Net->format_primary_frequency()}} Hz
          @if (!empty($Net->primary_frequency_repeaterbook_url()))
            (<a href="{{$Net->primary_frequency_repeaterbook_url()}}"><abbr title="RepeaterBook">RB</abbr></a>)
          @endif
        </li>
        <li>{{$Net->format_secondary_frequency()}} Hz
          @if (!empty($Net->secondary_frequency_repeaterbook_url()))
            (<a href="{{$Net->secondary_frequency_repeaterbook_url()}}"><abbr title="RepeaterBook">RB</abbr></a>)
          @endif
        </li>
      </ol>

      @if (!empty($Net->url))
        <div><a href="{{$Net->url}}">{{$Net->url}}</a></div>
      @endif


      @if (!empty($Net->start_time))
        <div>When: {{$Net->start_time}}
          @if (!empty($Net->end_time))
            to {{$Net->end_time}}
          @endif
          {{$Net->timezone}}</div>
        <div>
          <ul>
            @if ($Net->sunday)
              <li>
                Sunday
              </li>
            @endif
            @if ($Net->monday)
              <li>
                Monday
              </li>
            @endif
            @if ($Net->tuesday)
              <li>
                Tuesday
              </li>
            @endif
            @if ($Net->wednesday)
              <li>
                Wednesday
              </li>
            @endif
            @if ($Net->thursday)
              <li>
                Thursday
              </li>
            @endif
            @if ($Net->friday)
              <li>
                Friday
              </li>
            @endif
            @if ($Net->saturday)
              <li>
                Saturday
              </li>
            @endif
          </ul>
        @endif

        @if (!empty($Net->description))
          <div id="description">{{$Net->description}}</div>
        @endif

        <hr>
        @if (!empty($Net->arrl_net_id))
          <div>ARRL Net Id: <a href="http://www.arrl.org/webroot/resources/nets/client/netdetail.html?mfind={{$Net->arrl_net_id}}">{{$Net->arrl_net_id}}</a></div>
        @endif
        @if (!empty($Net->arrl_state))
          <div>ARRL State: {{$Net->arrl_state}}</div>
        @endif
        @if (!empty($Net->arrl_area))
          <div>ARRL Area: {{$Net->arrl_area}}</div>
        @endif
        @if (!empty($Net->arrl_region))
          <div>ARRL Region: {{$Net->arrl_region}}</div>
        @endif
        @if (!empty($Net->arrl_section))
          <div>ARRL Section: {{$Net->arrl_section}}</div>
        @endif
        @if (!empty($Net->arrl_traffic_handling))
          <div>ARRL Traffic Handling: {{$Net->arrl_traffic_handling}}</div>
        @endif
        @if (!empty($Net->arrl_national_traffic_affiliated))
          <div>ARRL National Traffic Affiliated: {{$Net->arrl_national_traffic_affiliated}}</div>
        @endif


        @if (0 != $Net->Coverage()->count())
          <h2>Intended Coverage</h2>
          <ul>
            @foreach ($Net->Coverage()->get() as $Gadm)
              <li>{{$Gadm->name_0}} > {{$Gadm->name_1}}</li>
            @endforeach
          </ul>
        @endif
        </div>
    </div>
  </body>
</html>
