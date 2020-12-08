<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">

    <title>{{$Net->name}} -- Ham Net Database</title>

<script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.4.3/build/ol.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.4.3/css/ol.css">


    <style>
#header {
  margin-bottom: 20px;
  border-bottom: 1px black solid;
}
      #footer {
        margin-top: 20px;
        border-top: 1px black solid;
      }
      #map {
			width: 800px;
			height: 600px;
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
        @if (!empty($Net->secondary_frequency))
        <li>{{$Net->format_secondary_frequency()}} Hz
          @if (!empty($Net->secondary_frequency_repeaterbook_url()))
            (<a href="{{$Net->secondary_frequency_repeaterbook_url()}}"><abbr title="RepeaterBook">RB</abbr></a>)
          @endif
        </li>
        @endif
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
        @if (!empty($Net->arrl_coverage))
          <div>ARRL Coverage: {{$Net->arrl_coverage}}</div>
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
        <div id='map'></div>
	<div><small>Coverage is only estimated. Exact coverage depends on conditions, transmitter power, antenna parameters, &amp;c.</small></div>
  <script>
    var map = new ol.Map({
      target: 'map',
      layers: [
        new ol.layer.Tile({
          source: new ol.source.OSM()
        }),
        new ol.layer.VectorTile({
          source: new ol.source.VectorTile({
            format: new ol.format.MVT(),
            url: "/net/{{$Net->net_id}}/tiles/{z}/{x}/{y}",
          }),
          style: function(feature, res) {
            return new ol.style.Style({
              fill: new ol.style.Fill({
                color: 'rgba(0, 102, 204, 0.2)'
              }),
              stroke: new ol.style.Stroke({
                width: 2,
                color: 'rgba(0, 102, 204)'
              })
            });
          },
        })
      ],
      view: new ol.View({
        center: ol.proj.fromLonLat([-90, 40]),
        zoom: 4
      })
    });
  </script>
    </div>
    <div id="footer">
      <div><a href="mailto:info@hamnets.org">Contact</a></div>
      <div>Please reach out to update or add nets, or to suggest features.</a>
      <hr>
      <div>
        <h1>Other Resources</h1>
        <ul>
          <li><a href="https://docs.google.com/spreadsheets/d/1cpaIUPJOG9Kdb0Xo-hyzhcVKcyvOr37vrGIF1mIETHs/edit#gid=906307814">N1YZ HF NET_LIST</a></li>
          <li><a href="http://www.arrl.org/resources/nets/">ARRL ONLINE NET DIRECTORY</a></li>
          <li><a href="http://repeaterbook.com">RepeaterBook</a></li>
        </ul>
    </div>
  </body>
</html>
