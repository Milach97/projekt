var App = {

    BaseUrl:  'http://127.0.0.1:8000/api/v1/',
    ImageUrl:  '',
    UserRole: null,
    Md5Hash: null,


    Login: function(email, password) {
        timestamp = Math.floor(Date.now() / 1000);
        salt = '4gmHNIl7RHx0e6TGDQcXsLDZ4mb7tPTj2Tj23t8UBDTicRUCGvX58E4TM56lEucx';
        hash = SparkMD5.hash(timestamp+salt+password);   

        //localStorage.setItem("hash", hash);
        App.Md5Hash = hash;

        Ajax.login('GET', 'account/login/'+email, {"timestamp": timestamp, "password":hash}, 'SuccessLogin', null, 'CompleteLogin', 'ErrorLogin');
    },
    

    SuccessLogin: function() {
        //ponownie zakodowany hash jako sessionId
        sessionId = SparkMD5.hash(App.Md5Hash);
        App.Md5Hash = null;
        
        //poki co tylko jedna rola - nie ma potrzeby odbierania roli uzytkownika z API
        App.UserRole = 'ROLE_PROTEGE';

        //dane logowania zapisane w apce
        var loginData = {"sessionId": sessionId, "timestamp": Math.floor(Date.now() / 1000)};
        localStorage.setItem("loginData", JSON.stringify(loginData));
        
        //TODO: zapisac id podopiecznego do sesji
        sessionStorage.setItem('protege_id', "1");
 

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

    
    // pobranie danych dla zalogowanego uzytkownika ...
    GetUserReservations: function() { 
        Ajax.go('GET','admin',null,'SuccessGetUserReservations');
    },
    SuccessGetUserReservations: function(response) {
        $('#user-reservations > ul').html('');
        $("#user-reservations > ul").append(Html.ObjectsWithReservations(response.objects));
        $("#user-reservations > ul").listview("refresh");
    },
    
    

    GetObjectsForMainPage: function() {
        Ajax.go('GET','',null,'SuccessGetObjectsForMainPage');
    },

    SuccessGetObjectsForMainPage: function(response) {
        $('#objectsForMainPage > ul').html('');
        $("#objectsForMainPage > ul").append( Html.ObjectsForMainPage(response.objects) );
        $("#objectsForMainPage > ul").listview("refresh");
    },
    
    

    GetObjectDetails: function(object_id) {
        Ajax.go('GET','object/'+object_id,null,'SuccessGetObjectDetails',null,'CompleteGetObjectDetails');
    },
    
    SuccessGetObjectDetails: function(response) {
        $('#objectdata').html('');
        $("#objectdata").append( Html.ObjectDetails(response.object) );
    },
    
    

    CompleteGetObjectDetails: function() {
        $("#objectdata ul").listview().listview("refresh");
    },

    GetRoomDetails: function(room_id) {
        Ajax.go('GET','room/'+room_id,null,'SuccessGetRoomDetails');
    },



    SuccessGetRoomDetails: function(response) {
        $('#roomdata > ul').html('');
        $("#roomdata > ul").append( Html.RoomDetails(response.room) );
        $("#roomdata > ul").listview("refresh");
    },
    
    GetCities: function() {
        Ajax.go('GET','cities',null,'SuccessGetCities');
    },

    SuccessGetCities: function(response) {
        $('#citysearchresults').html('');
        $("#citysearchresults").append( Html.CitiesForSearching(response) );
        $("#citysearchresults").listview("refresh");        

    },
    
    
    SearchRooms: function(city,dayin,dayout,room_size) {
        Ajax.go('POST','roomsearch',{city:city,check_in:dayin,check_out:dayout,room_size:room_size},'SuccessSearchRooms',null,'CompleteSearchRooms');
    },



    SuccessSearchRooms: function(response) {
        $('#objectsForMainPage').html('');
        $("#objectsForMainPage").append( Html.SearchResults(response.city) );

    },
    
    

    CompleteSearchRooms: function() {
        $("#objectsForMainPage ul").listview().listview("refresh");
    },
    
    
    DeleteReservation: function(id) {
        Ajax.go('GET','admin/deleteReservation/'+id,null,null,null,'CompleteDeleteReservation');
    },


    CompleteDeleteReservation: function() {
       App.GetUserReservations();
    },


    ConfirmReservation: function(id) {
        Ajax.go('GET','admin/confirmReservation/'+id,null,null,null,'CompleteConfirmReservation');
    },



    CompleteConfirmReservation: function() {
       App.GetUserReservations();
    },

    

    SetReadNotifications: function(id) {
        Ajax.go('POST','admin/setReadNotifications',{id:id});
    },


    MakeReservation: function(day_in,day_out) {
        var room_id = sessionStorage.getItem('room_id');
        var city_id = sessionStorage.getItem('city_id');

        Ajax.go('POST','makeReservation/'+room_id+'/'+city_id,{checkin:day_in,checkout:day_out},'SuccessMakeReservation');
    },


    SuccessMakeReservation: function(response) {
       if(response.reservation === false)
       alert('Date taken. Try another one.');
       else
       alert('The reservation has been made.');
    }
    
    
};

