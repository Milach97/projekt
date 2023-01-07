
$(function () {


    //App.GetObjectsForMainPage();
    //App.GetCities(); 


    // $(":mobile-pagecontainer").on("pagecontainerbeforetransition", function (event, ui) {
    //     //dodanie headera i footera do storny ?
    //     $("#header").prependTo(ui.toPage);
    //     $("#footer").appendTo(ui.toPage);
    // });
    
    

    //brak przycisku logowania -> funkcja nie potrzebna
    // if (App.IsLoggedIn())
    // {
    //     $('#loginhref').html('Wyloguj');
    //     $('#loginhref').attr('href','#');
    //     $('#loginhref').addClass('logout');
    // }
    // else
    // {
    //     $('#loginhref').html('Logowanie');
    //     $('#loginhref').attr('href','#loginpage');
    //     $('#loginhref').removeClass('logout');
    // }
    
    

    //akcja wylogowania
    $(document).on('click','#logout',function(){
        console.log('wyloguj');
        localStorage.removeItem('loginData');
        $(':mobile-pagecontainer').pagecontainer('change', '#loginpage');
    });
    
    
    
    //akcja logowania
    $('#submit-login').on('click', function () {
        var email = $("[name=email]").val();
        var password = $("[name=password]").val();

        App.Login(email,password);
    });




    //wyswietlenie storny mojezdrowie
    $(document).on("pagebeforeshow", "#myhealth", function () {

        if (!App.IsLoggedIn())
        {
            $(':mobile-pagecontainer').pagecontainer('change', '#loginpage');
            return;
        }

        console.log('Uzytkownik zalogowany - ');
        //App.GetUserReservations(); 
        // $('#header-text').html('Panel użytkownika');
    });


    // $(document).on("pagebeforeshow", "#loginpage", function () {
    //     $('#header-text').html('Panel logowania');
    // });
    
    
    //puls
    $(document).on("pagebeforeshow", "#pulse", function () {
        if (!App.IsLoggedIn())
        {
            $(':mobile-pagecontainer').pagecontainer('change', '#loginpage');
            return;
        }

        App.GetProtegePulses(sessionStorage.getItem('protege_id'), $('#pulses-list').data('page'));
    });

    //poprzednia strona
    $('#pulsePrevPageNav').on('click', function () {
        $('#pulses-list').data('page', $('#pulses-list').data('page')-1);
        App.GetProtegePulses(sessionStorage.getItem('protege_id'), $('#pulses-list').data('page'));
    });

    //nastepna strona pulsu
    $('#pulseNextPageNav').on('click', function () {
        $('#pulses-list').data('page', $('#pulses-list').data('page')+1);
        console.log($('#pulses-list').data('page'));
        App.GetProtegePulses(sessionStorage.getItem('protege_id'), $('#pulses-list').data('page'));
    });
    
    

    //opiekun
    $(document).on("pagebeforeshow", "#protector", function () {
        if (!App.IsLoggedIn())
        {
            $(':mobile-pagecontainer').pagecontainer('change', '#loginpage');
            return;
        }

        App.GetProtectors(sessionStorage.getItem('protege_id'));
    });
    
    

    //moje konto
    $(document).on("pagebeforeshow", "#account", function () {
        if (!App.IsLoggedIn())
        {
            $(':mobile-pagecontainer').pagecontainer('change', '#loginpage');
            return;
        }

        App.GetUserData(sessionStorage.getItem('protege_id'));
    });


});

