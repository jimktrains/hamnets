@extends('base')

@section('title', $Net->name)

@push('scripts')
  <script src="/openlayers.github.io@master/en/v6.4.3/build/ol.js"></script>
  <link rel="stylesheet" href="/openlayers.github.io@master/en/v6.4.3/css/ol.css" />

  <style>
    #map {
      width: 800px;
      height: 600px;
    }

    #description {
      margin-top: 2em;
    }

    ul .frequencies {
      list-style-type: upper-alpha;
    }
  </style>
@endpush

@section('content')
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
              {{trans('common.sunday')}}
            </li>
          @endif
          @if ($Net->monday)
            <li>
              {{trans('common.monday')}}
            </li>
          @endif
          @if ($Net->tuesday)
            <li>
              {{trans('common.tuesday')}}
            </li>
          @endif
          @if ($Net->wednesday)
            <li>
              {{trans('common.wednesday')}}
            </li>
          @endif
          @if ($Net->thursday)
            <li>
              {{trans('common.thursday')}}
            </li>
          @endif
          @if ($Net->friday)
            <li>
              {{trans('common.friday')}}
            </li>
          @endif
          @if ($Net->saturday)
            <li>
              {{trans('common.saturday')}}
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
        <h2>{{trans('common.intended_coverage')}}</h2>
        <ul>
          @foreach ($Net->Coverage()->get() as $Gadm)
            <li>{{$Gadm->name_0}} > {{$Gadm->name_1}}</li>
          @endforeach
        </ul>
      @endif
      </div>
      <div id='map'></div>
      <div><small>{{trans('common.coverage_disclaimer')}}</small></div>
      <div><a href="mailto:info@hamnets.org?subject=Problem Report for #{{$Net->net_id}}: {{$Net->name}}">{{trans('common.report_inaccuracy')}}</a></div>
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
@endsection
