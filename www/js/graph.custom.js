function getLinedConfig(chartData) {

    var lindedConfig = {
        type: 'line',
        data: {
            labels: ["1", "2", "3", "4"],
            datasets: [{
                label: '',
                backgroundColor: 'rgba(255, 255, 255, 0.1)',
                borderColor: 'rgba(255, 255, 255, 0.3)',
                data: chartData,
                fill: true,
            },
            ]
        },
        options: {
            responsive: true,
            responsiveAnimationDuration: 0,
            title: {
                display: false
            },
            legend: {
                display: false
            },
            tooltips: {
                mode: 'index',
                intersect: false
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            scales: {
                xAxes: [{
                    display: false,
                    scaleLabel: {
                        display: false,
                        labelString: ''
                    }
                }],
                yAxes: [{
                    display: false,
                    scaleLabel: {
                        display: false,
                        labelString: ''
                    }
                }]
            }
        }
    };

    return lindedConfig;
}

function generateCardGraphs() {
    //in-temp
    getlastvalue("C9", function(s, t) {
        var cData = [];

        for(var i = 0; i < t.length; i++) cData[3 - i] = t[i];

        var graph = document.getElementById('in-temp');
        var ctx = graph.getContext('2d');
        new Chart(ctx, getLinedConfig(cData));

        $("#in-temp-label").html(cData[cData.length - 1]+" °C");
    });

    //out-temp
    getlastvalue("C8", function(s, t) {
        var cData = [];

        for(var i = 0; i < t.length; i++) cData[3 - i] = t[i];
        
        var graph = document.getElementById('out-temp');
        var ctx = graph.getContext('2d');
        new Chart(ctx, getLinedConfig(cData));

        $("#out-temp-label").html(cData[cData.length - 1]+" °C");
    });

    //boiler
    getlastvalue("XC", function(s, t) {
        var cData = [];

        for(var i = 0; i < t.length; i++) cData[3 - i] = t[i];
        
        var graph = document.getElementById('boiler');
        var ctx = graph.getContext('2d');
        new Chart(ctx, getLinedConfig(cData));

        $("#boiler-label").html(cData[cData.length - 1]+" °C");
    });

    //garage
    getlastvalue("X5", function(s, t) {
        var cData = [];

        for(var i = 0; i < t.length; i++) cData[3 - i] = t[i];
        
        var graph = document.getElementById('garage');
        var ctx = graph.getContext('2d');
        new Chart(ctx, getLinedConfig(cData));

        var state = "Nyitva";
        if(cData[cData.length - 1] == 1) state = "Zárva";

        $("#garage-label").html(state);
    });

    
}