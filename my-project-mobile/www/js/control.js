
$(function () {

    //akcja wylogowania
    $(document).on('click','#logout',function(){
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
    });

    
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

