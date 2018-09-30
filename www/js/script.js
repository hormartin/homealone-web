$( document ).ready(function() {
    spoilerHandler();

    $("#logout").click(function() {
        logout();
    });
});

function spoilerHandler() {
    $(".spoiler p").click(function() {
        $(this).next(".content").slideToggle(200);
        $(this).toggleClass("active");
    });
}

function getDivElementBySensor(sensor) {
    var divs = document.getElementsByClassName("spoiler");

    for(var i = 0; i < divs.length; i++) {
        var divSensor = divs[i].getAttribute("sensor");
        if(divSensor) {
            if(divSensor == sensor) return divs[i];
        }    
    }

    return false;
}

/*
var configInts = [
    //["name"] = [min, max(0 infinity), length]
    ["enable"] =                [0, 1, 1],
    ["write"] =                 [0, 1, 1],
    ["default_value"] =         [0, 0, 0],
    ["min"] =                   [-100, 0, 0],
    ["max"] =                   [-100, 0, 0],
    ["precision"] =             [1, 0, 0],
    ["monostab"] =              [1, 0, 0],
    ["distance"] =              [1, 0, 0],
    ["alarm.set"] =             [0, 1, 0],
    ["alarm.minvalue"] =        [1, 0, 0],
    ["alarm.maxvalue"] =        [1, 0, 0],
    ["sensibility.percent"] =   [1, 100, 0],
]*/