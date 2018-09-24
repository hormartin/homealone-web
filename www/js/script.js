$( document ).ready(function() {
    spoilerHandler();
});

function spoilerHandler() {
    $(".spoiler p").click(function() {
        $(this).next(".content").toggle(200);
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