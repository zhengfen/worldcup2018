@extends('layouts.app')

@section('content')
<div class="container" style="background-color:#fff;">
    <p class="pageTitle">Classification</p>
    <div class="chart-container" style="position: relative; ">
        <canvas id="myChart"></canvas>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js"></script>
<script>
    $.getJSON("/ranking_json", function (result) {
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
                }
            }
        });


    });
</script>
@endsection