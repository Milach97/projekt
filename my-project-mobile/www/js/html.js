var Html = {

    PulsesList: function(pulses) {
        var list = '';
        $.each(pulses, function(index, pulse){
            list += '<li><a><img src="img/pulse.png"><h2>Wartość: <small>'+pulse.value+'</small></h2><p>'+pulse.datetime.date+'</p></a></li>';
        });

        return list;
    },

    SaturationsList: function(saturations) {
        var list = '';
        $.each(saturations, function(index, saturation){
            //console.log(saturation.value);
            list += '<li><a><img src="img/saturation.png"><h2>Wartość: <small>'+saturation.value+'</small></h2><p>'+saturation.datetime.date+'</p></a></li>';
        });

        return list;
    },

    WightsList: function(weights) {
        var list = '';
        $.each(weights, function(index, weight){
            //console.log(weight.value);
            list += '<li><a><img src="img/weight.png"><h2>Wartość: <small>'+weight.value+'</small></h2><p>'+weight.datetime.date+'</p></a></li>';
        });

        return list;
    },


    PressuresList: function(pressures) {
        var list = '';
        $.each(pressures, function(index, pressure){
            //console.log(weight.value);
            list += '<li><a><img src="img/pressure.png"><h2>Wartość 1: <small>'+pressure.value1+'</small> Wartość 2: <small>'+pressure.value2+'</small></h2><p>'+pressure.datetime.date+'</p></a></li>';
        });

        return list;
    },



    ProtectorsList: function(protectors) {
        var list = '<li data-role="list-divider">Lista opiekunów</li>';
        $.each(protectors, function(index, protector){
            index += 1;
            list += '<li><h2>Opiekun '+index+':</h2><p>Imię: '+protector.name+'</p><p>Nazwisko: '+protector.last_name+'</p><p>Adres e-mail: '+protector.email+'</p><p>Nr telefonu: '+protector.phone_number+'</p></li>';
        });

        return list;
    },

};
