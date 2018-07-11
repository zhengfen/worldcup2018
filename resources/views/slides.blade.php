<!doctype html>
<html lang="en" prefix="og: http://ogp.me/ns#">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- SEO -->
    <title>Worldcup 2018 - Russia</title>
    <meta name="description" content="">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,700,700i%7CMaitree:200,300,400,600,700&amp;subset=latin-ext" rel="stylesheet">
    <!-- CSS Base -->
    <link rel="stylesheet" type='text/css' media='all' href="{{ asset('css/webslides.css') }}">    
    <link rel="stylesheet" type='text/css' media='all' href="{{ asset('css/flag-icon.css') }}">
    <!-- Optional - CSS SVG Icons (Font Awesome) -->
    <link rel="stylesheet" type="text/css" media="all" href="{{ asset('css/svg-icons.css') }}">
    <link rel="stylesheet" type='text/css' media='all' href="{{ asset('css/matches.css') }}">  
    <!-- Favicon -->
    <meta name="msapplication-TileColor" content="#ffffff" />
    <meta name="msapplication-TileImage" content="{{ asset('images/favicon/football-soccer-ball-144-183228.png') }}">
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="{{ asset('images/favicon/football-soccer-ball-152-183228.png') }}">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{ asset('images/favicon/football-soccer-ball-144-183228.png') }}">
    <link rel="apple-touch-icon-precomposed" sizes="120x120" href="{{ asset('images/favicon/football-soccer-ball-120-183228.png') }}">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{ asset('images/favicon/football-soccer-ball-114-183228.png') }}">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{ asset('images/favicon/football-soccer-ball-72-183228.png') }}">
    <link rel="apple-touch-icon-precomposed" href="{{ asset('images/favicon/football-soccer-ball-57-183228.png') }}">
    <link rel="icon" href="{{ asset('images/favicon/football-soccer-ball-32-183228.png') }}" sizes="32x32">
    
    <style>
        .table-vsm td, .table-vsm th {
            padding: 0.2rem;
            font-size: 1.2rem;
            white-space: nowrap;
        }

        .table-nowrap td, .table-nowrap th {
            white-space: nowrap;
        }
        input{
            width: 28px;
        }
        .text-center{
            text-align : center;
        }
        .text-right{
            text-align : right;
        }
        .text-left{
            text-align : left;
        }
    </style>

    </head>
  <body>    
    <main role="main">
      <article id="webslides">
        <!-- Quick Guide
          - Each parent <section> in the <article id="webslides"> element is an individual slide.
          - Vertical sliding = <article id="webslides" class="vertical">
          - <div class="wrap"> = container 90% / <div class="wrap size-50"> = 45%;
        -->
        <!-- ranking -->
        <section class="bg-white aligncenter">                
          <div class="wrap">
            <div class="grid vertical-align">
                <div class="column" style="width:60%">
                  <p class="pageTitle">Classification</p>
                  <div class="chart-container" style="position: relative; ">
                    <canvas id="myChart"></canvas>
                  </div>
                </div><!-- .end .column -->
                <div class="column">
                    <table class="table-vsm" style="border-spacing: 2px;">
                        <tr>
                            <th>Nom</th>
                            <th>Point 1e tour</th> 
                            <th>Point</th>
                        </tr>
                        @forelse($dataset as $record )
                            <tr>
                              <td>{{ $record['label'] }}</td>
                              <td>{{ sizeof($record['data'])>47 ? $record['data'][48]:end($record['data']) }}</td> 
                              <td>{{ end($record['data']) }}</td>
                            </tr>
                        @empty
                        @endforelse
                    </table>
                </div><!-- .end .column -->
            </div>
          </div>          
          <!-- .end .wrap -->
        </section>
        
        <!-- last 5 matches and next 5 matches -->
        <section class="bg-white aligncenter">                
          <div class="wrap">
            <div class="grid vertical-align">
                <div class="column">
                  <h3><strong>Derniers Matchs</strong></h3>
                  <table class="table-nowrap">
                    <tbody>
                    @forelse($matches_p as $match)
                      @include('matches._match')
                    @empty
                    @endforelse
                    </tbody>
                  </table>                  
                  
                </div><!-- .end .column -->
                <div class="column">
                  <h3><strong>Prochains Matchs</strong></h3>  
                  <table class="table-nowrap">
                    <tbody>
                    @forelse($matches_n as $match)
                      @include('matches._match')
                    @empty
                    @endforelse
                    </tbody>
                  </table>
                </div><!-- .end .column -->
            </div>
          </div><!-- .end .wrap -->
        </section>
        
        <!-- points of last $num matches -->
        <section class="bg-white aligncenter">                
          <div class="wrap">
            <div class="grid vertical-align">
                <div class="column">
                  <h3><strong>Classification</strong></h3>
                  <table class="table-vsm" style="border-spacing: 2px;">
                        <tr>
                            <th>Nom</th>
                            <th>Points</th>
                        </tr>
                        @forelse($dataset as $record )
                            <tr>
                              <td>{{ $record['label'] }}</td>
                              <td>{{ end($record['data']) }}</td>
                            </tr>
                        @empty
                        @endforelse
                  </table>
                </div>                
                <div class="column">
                  <h3><strong>Derniers {{ env('DELTA_MATCH_NUM')}} Matchs</strong></h3>
                  <table class="table-vsm" style="border-spacing: 2px;">
                        <tr>
                            <th>Nom</th>
                            <th>Delta</th>
                        </tr>
                        @forelse($dataset_delta as $record )
                            <tr>
                              <td>{{ $record['label'] }}</td>
                              <td>{{ $record['point'] }}</td>
                            </tr>
                        @empty
                        @endforelse
                  </table>
                </div>                
              </div>
            </div>
          </div><!-- .end .wrap -->
        </section>
        
        <!-- team group match standings -->
        <section style="background: #a6a2a3;" id="slide_standings">                
          <div class="container-fluid"  style="margin-top: -110px;">
              <div class="groups">
                @include('matches._group')
              </div>              
              <div class="knockouts">
                @include('matches._knockout')
              </div>
          </div><!-- .end .wrap -->
        </section>

      </article>
    </main>
    <!--main-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js"></script>
    <script>
        $.getJSON("/ranking", function (result) {
            // console.log(result);
            // set label
            var labels = [];
            for (var i = 1; i <= result[0]['data'].length; i++) {
                labels.push(i);
            }

            var ctx = document.getElementById("myChart").getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: result
                },
                options: {
                    animation: {
                        duration: 0, // general animation time
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    },
                    elements: {
                        line: {
                            tension: 0, // disables bezier curves
                        }
                    }
                }
            });


        });
    </script>
    
    <!-- Required -->
    <script src=" {{ asset('js/webslides.js') }} "></script>
    <script>
        $(function(){ 
            window.ws = new WebSlides({ autoslide: 30000 });
            setTimeout(function() {
                location.reload();
            }, 900000);
        });
    </script>
    <!-- OPTIONAL - svg-icons.js (fontastic.me - Font Awesome as svg icons) -->
    <script defer src="{{ asset('js/svg-icons.js') }}"></script>
  </body>
</html>







