
//
var lindedConfig = {
    type: 'line',
    data: {
        labels: [],
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
    var ctx = document.getElementById('canvas').getContext('2d');
    window.myLine = new Chart(ctx, lindedConfig);
}