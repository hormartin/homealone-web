
//
var lindedConfig = {
    type: 'line',
    data: {
        labels: ["1", "2", "3", "4"],
        datasets: [{
            label: '',
            backgroundColor: 'rgba(255, 255, 255, 0.2)',
            borderColor: 'rgba(255, 255, 255, 0.8)',
            data: [
                10,
                20,
                10,
                20,
                10
            ],
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

function generateCardGraphs() {
    //card1
    var graphs = document.getElementsByClassName('canvas');
    for(var i = 0; i < graphs.length; i++) {
        var ctx = graphs[i].getContext('2d');
        new Chart(ctx, lindedConfig);
    }
}