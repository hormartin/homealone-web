toastr.options = {
    "closeButton": true,
    "debug": true,
    "newestOnTop": true,
    "progressBar": false,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "2000",
    "hideDuration": "1000",
    "timeOut": "2000",
    "extendedTimeOut": "0",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  }

$( document ).ready(function() {
    spoilerHandler();

    $("#logout").click(function() {
        logout();
    });

    $(document).click(function(event) {
        var target = $(event.target);

        var isBtn = false;
        if(target.hasClass("dropdown") || target.hasClass("dropbtn") || target.hasClass("fa-use")) isBtn = true;
        
        if(isBtn == false) {
            $(".dropdown").children(".dropdown-content").fadeOut("fast");
            $(".dropdown").children(".dropbtn").removeClass("active");
        }
    });
    
    $(".dropdown").click(function() {
        $(this).children(".dropdown-content").fadeToggle("fast");
        $(this).children(".dropbtn").toggleClass("active");
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

            if(isNaN(value)) return false;
            value = value * 1;

            if(value < min) return false;
            if(value > max) return false;
            if(value % 1 != 0) return false;
        }
    }

    var writtenOut = false;

    for(var i = 0; i < inputs.length; i++) {
        input = inputs[i];
        var name = input.getAttribute("name");
        value = input.value.trim();

        if(value == "") continue;

        setconfig(formelm.getAttribute("sensor"), name, value, function(s, text, d, name, newvalue) {
            //d - device name
            if(s && text == "success") {
                var spoiler = getDivElementBySensor(d);

                if(spoiler) {
                    var div = spoiler.getElementsByClassName("content")[0];

                    //Tizedes jegy eltávolítása
                    if(isNaN(newvalue) == false) newvalue = Math.trunc(newvalue);

                    var update_input = $($(div).children("form").children("div").children("input[name="+name+"]"));
                    $(update_input).attr("placeholder", newvalue);
                    $(update_input).val("");

                    if(name == "display") {
                        spoiler.getElementsByTagName("p")[0].innerHTML = ""+newvalue+" ("+d+")";
                    }

                    if(!writtenOut) toastr["info"]("Sikeresen frissítetted az adatokat! ("+d+")", "Információ");
                    writtenOut = true
                }    
                else return false;
            }else{
                if(text == "error")
                    toastr["error"]("Ilyen adatmező nem létezik. ("+name+")", "Hiba!");
                else if(text == "wrong-data")
                    toastr["error"]("Adatként nem adható meg speciális karakter. ("+name+")", "Hiba!");
                else if(text == "non-numeric")
                    toastr["error"]("Az adatnak számnak kell lennie. ("+name+")", "Hiba!");
                else if(text == "max-value")
                    toastr["error"]("Túl nagy számot adtál meg. ("+name+")", "Hiba!");
                else if(text == "min-value")
                    toastr["error"]("Túl alacsony számot adtál meg. ("+name+")", "Hiba!");
                else if(text == "non-whole-num")
                    toastr["error"]("A számnak egész számnak kell lennie. ("+name+")", "Hiba!");
                else
                    toastr["error"]("Ismeretlen hiba történt! ("+text+")", "Hiba!");
            }
        });

    }
}