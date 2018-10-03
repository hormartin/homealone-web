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

function submitConfigForm(formelm) {
    var inputs = formelm.getElementsByTagName("input");

    var input;
    var value;

    for(var i = 0; i < inputs.length; i++) {
        input = inputs[i];
        value = input.value.trim();
        
        if(value == "") continue;

        if(input.classList.contains("number") == true) {
            var min = $(input).attr("min");
            var max = $(input).attr("max");

            if(isNaN(value)) {
                return false;
            }

            if(value * 1 < min) {
                return false;
            }
                
            if(value * 1 > max) {
                return false;
            }

        }
    }


    for(var i = 0; i < inputs.length; i++) {
        input = inputs[i];
        var name = input.getAttribute("name");
        value = input.value.trim();

        if(value == "") continue;

        setconfig(formelm.getAttribute("sensor"), name, value, function(s, t, d, n, newvalue) {
            if(s && t == "success") {
                var spoiler = getDivElementBySensor(d);

                if(spoiler) {
                    var div = spoiler.getElementsByClassName("content")[0];

                    //console.log($($(div).children("form").children("div").children("input[name="+n+"]")));
                    var update_input = $($(div).children("form").children("div").children("input[name="+n+"]"));
                    $(update_input).attr("placeholder", newvalue);
                    $(update_input).val("");

                    if(n == "display") {
                        spoiler.getElementsByTagName("p")[0].innerHTML = ""+newvalue+" ("+d+")";
                    }

                    console.log("Sikeres frissítés.");
                }    
                else return false;
            }else{
                alert("Valami hiba történt, az adatok frissítése során. ("+t+")");
            }
        });

    }
}

/*
var configInts = {
    //["name"] = []
    ["enable"]
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
}*/