var App = {

    BaseUrl:  'http://127.0.0.1:8000/api/v1/',
    ImageUrl:  '',
    UserRole: null,
    Md5Hash: null,

    Login: function(email, password) {
        timestamp = Math.floor(Date.now() / 1000);
        salt = '4gmHNIl7RHx0e6TGDQcXsLDZ4mb7tPTj2Tj23t8UBDTicRUCGvX58E4TM56lEucx';
        hash = SparkMD5.hash(timestamp+salt+password);   

        App.Md5Hash = hash;
        email = email.replace('.', '%2E');

        Ajax.login('GET', 'account/login/'+encodeURIComponent(email), 
        {"timestamp": timestamp, "password":hash}, 'SuccessLogin', null, 'CompleteLogin', 'ErrorLogin');
    },

    SuccessLogin: function(response) {
        //ponownie zakodowany hash jako sessionId
        sessionId = SparkMD5.hash(App.Md5Hash);
        App.Md5Hash = null;
        
        App.UserRole = 'ROLE_PROTEGE';

        //dane logowania zapisane w apce
        var loginData = {"sessionId": sessionId, "timestamp": Math.floor(Date.now() / 1000)};
        localStorage.setItem("loginData", JSON.stringify(loginData));
        
        sessionStorage.setItem('protege_id', response.protegeId);
        console.log('zostales zalogowany');

        //zmiana strony
        $(':mobile-pagecontainer').pagecontainer('change', '#myhealth');
    },
 
 
    ErrorLogin: function(response = null) {
        if(response){
            try{
                alert(response.responseJSON['error']);
            }
            catch(e){
                alert('Błąd. Zrócona odpowiedź z serwera nie mogła zostać poprawnie przetworzona.')
            }
        }
        else{
            alert('Wystąpił problem z połączeniem.');
        }
    },


    CompleteLogin: function() {
        //zmiana widoku na panel uzytkownika jezeli zalogowano
        if(App.IsLoggedIn()){
            $(':mobile-pagecontainer').pagecontainer('change', '#myhealth');
        }
        else{
            alert('Błąd. Użytkownik nie został zalogowany.');
        }
    },


    //uzytkownik zalogowany przez godzine
    IsLoggedIn: function() {
        var status = JSON.parse(localStorage.getItem("loginData"));

        if(status)
        {
            var now = Math.floor(Date.now() / 1000);
            if( (now - status.timestamp) > 3600) return false;
            else{
                return true;
            }
        }
        else
        return false;
    },


    
    // pobranie id sesji - md5(zakodowaneHaslo)
    GetSessionId: function() {
        var session = JSON.parse(localStorage.getItem("loginData"));

        if(session)
        return session.sessionId;
        else
        return false;
    },
    

    // powiadomienie 
    Alert: function() {
        if(window.cordova)
        navigator.notification.beep(1);
    },


    // ekran startowy
    ShowSplashScreen: function() {
        navigator.splashscreen.show();
    },
    



    //pobranie zapisanych pulsow podopiecznego
    GetProtegePulses: function(id, page) {
        Ajax.go('GET', 'protege/'+id+'/pulses/'+page, {"sessionId": App.GetSessionId()}, 'SuccessGetProtegePulses');
    },
    SuccessGetProtegePulses: function(response) {

        $('#pulses-list').html('');
        $("#pulses-list").append(Html.PulsesList(response.page));
        $("#pulses-list").listview("refresh");
        $("#pulses-list").listview("refresh");

        //aktualny numer strony
        $('#pulseActivePageNumber').html($('#pulses-list').data('page'));

        //jezeli jest inna strona niz 1
        if($('#pulses-list').data('page') > 1){
            $('#pulsePrevPageNav').css("display", "block");
        }
        else{
            $('#pulsePrevPageNav').css("display", "none");
        }

        //jezeli jest kolejna strona
        if(response.isNextPage){
            //dodaj guzik
            $('#pulseNextPageNav').css("display", "block");
        }
        else{
            $('#pulseNextPageNav').css("display", "none");
        }

    },




    //pobranie opiekunow podopiecznego
    GetProtectors: function(id) {
        Ajax.go('GET', 'protege/'+id+'/protectors', {"sessionId": App.GetSessionId()}, 'SuccessGetProtectors');
    },
    SuccessGetProtectors: function(response) {
        $('#protectors-list').html('');
        $('#protectors-list').append(Html.ProtectorsList(response));
        $("#protectors-list").listview("refresh");
        $("#protectors-list").listview("refresh");
    },





    //pobranie danych uzytkownika
    GetUserData: function(id) {
        Ajax.go('GET', 'user/data/'+id, {"sessionId": App.GetSessionId()}, 'SuccessGetUserData');
    },
    SuccessGetUserData: function(response) {
        $('#nameRowVal').html('-');
        $('#lastNameRowVal').html('-');
        $('#emailRowVal').html('-');
        $('#phoneRowVal').html('-');

        $('#nameRowVal').html(response.name);
        $('#lastNameRowVal').html(response.last_name);
        $('#emailRowVal').html(response.email);
        $('#phoneRowVal').html(response.phone_number);
    },



    // pobranie danych zalogowanego klienta
    GetClientData: function(number) {
        Ajax.go('GET', 'clientData/account/'+number, {"sessionId": App.GetSessionId()}, 'SuccessGetClientData');
    },
    SuccessGetClientData: function(response) {
        console.log(response.objects);
    },
    
    
};

