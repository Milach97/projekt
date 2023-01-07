var Html = {

    PulsesList: function(pulses) {
        var list = '';
        $.each(pulses, function(index, pulse){
            //console.log(pulse.value);
            list += '<li><a><img src="img/pulse.png"><h2>Wartość: <small>'+pulse.value+'</small></h2><p>'+pulse.datetime.date+'</p></a></li>';
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


    /*
    ObjectsForMainPage: function(objects) {
        var ObjectsForMainPage = '';

        $.each(objects, function(index, object){
            ObjectsForMainPage += '<li><a href="#objectdetails" onclick="sessionStorage.setItem(\'object_id\', '+object.id+')" data-transition="none">' +
            '<img  alt="" src="'+App.ImageUrl+object.photos[0].path+'">' +
            '<h2>'+object.name+' <small>'+object.city.name+'</small></h2>' +
            '<p>'+object.description.substring(0,100)+'...</p></a>';
            '</li>';

        });

        return ObjectsForMainPage;
    },


    ObjectsWithReservations: function(objects) {

        var ObjectsWithReservations = '';

        $.each(objects, function(oi,object){
            ObjectsWithReservations += '<li data-role="list-divider">Object '+object.name+'</li>';

                // rooms
                $.each(object.rooms, function(ri,room){
                ObjectsWithReservations += '<li>';

                ObjectsWithReservations += '<h2>Room number '+room.room_number+'</h2>';

                        // reservations
                        ObjectsWithReservations += Html.RoomReservations(room.reservations) ;

                ObjectsWithReservations += "</li>";
                });

         });


        return ObjectsWithReservations;
    },

    RoomReservations: function(reservations) {
        var RoomReservations = '';

            $.each(reservations, function(index,reservation){

                var reservationStatus = 'Unconfirmed ';


                if(reservation.status === 1)
                {
                    reservationStatus = "Confirmed ";
                }
                else
                {
                    if(App.UserRole === 'owner' || App.UserRole === 'admin')
                    reservationStatus = '<a class="confirmReservation" data-id="'+reservation.id+'" href="#">Confirm </a>';
                }


            RoomReservations += '<p>Check in: '+reservation.day_in+'</p>' +
                '<p>Check out: '+reservation.day_out+'</p>' +
                '<p>Guest: '+reservation.user.name + " " + reservation.user.surname+'</p>' +
                '<p>'+reservationStatus+' <a class="deleteReservation ui-btn ui-shadow ui-corner-all ui-icon-delete ui-btn-icon-notext ui-btn-inline" data-id="'+reservation.id+'">Delete</a></p>';

            });


        return RoomReservations;
    },


    ObjectDetails: function(object) {


        var objectAddress = object.city.name + " " + object.address.street + " " + object.address.number;

        var objectDetails = '<h1>Object '+object.name+' <small>'+objectAddress+'</small></h1>' +
        '<p>'+object.description+'</p>' +
        '<p>Available rooms in the object:</p><ul data-role="listview" data-split-icon="gear" data-split-theme="a" data-inset="true">';

        $.each(object.rooms, function(index, room) {

            if( typeof room.photos[0] !== 'undefined'  )
                var photo = room.photos[0].path;
            else if( typeof object.photos[0] !== 'undefined' )
                var photo = object.photos[0].path;
            else
                var photo = null;

            objectDetails += '<li><a href="#room" onclick="sessionStorage.setItem(\'room_id\','+room.id+');sessionStorage.setItem(\'city_id\','+room.object.city.id+')" data-transition="none">' +
            '<img alt="" src="'+App.ImageUrl+photo+'">' +
            '<h2>Room number '+room.room_number+' <small>'+object.city.name+'</small></h2>' +
            '<p>'+room.description.substring(0,100)+'...</p>' +
            '</a>' +
            '</li>';

        });

        objectDetails += '</ul>';

        return objectDetails;
    },


    RoomDetails: function(room) {
        var roomHtml = '<li data-role="list-divider">Room number '+room.room_number+' in the object: '+room.object.name+'</li>' +
        '<li>' +
        '<h2>Room size: '+room.room_size+', price  '+room.price+'$</h2>' +
        '<p><strong>ul. '+room.object.address.street+' nr '+room.object.address.number+'</strong></p>' +
        '<p>'+room.description+'</p>' +
        '</li>';

        return roomHtml;
    },


    CitiesForSearching: function(cities) {
        var html = '';

        $.each(cities, function(index,city){
            html += '<li><a href="#">'+city.name+'</a></li>';
        });

        return html;
    },

    SearchResults: function(city) {
        var SearchResults = '<ul data-role="listview" data-split-icon="gear" data-split-theme="a" data-inset="true">';

        $.each(city.rooms, function(index, room) {

            if( typeof room.photos[0] !== 'undefined'  )
                var photo = room.photos[0].path;
            else if( typeof room.object.photos[0] !== 'undefined' )
                var photo = room.object.photos[0].path;
            else
                var photo = null;

            SearchResults += '<li><a href="#room" onclick="sessionStorage.setItem(\'room_id\','+room.id+');sessionStorage.setItem(\'city_id\','+city.id+')" data-transition="none">' +
            '<img alt="" src="'+App.ImageUrl+photo+'">' +
            '<h2>Room number '+room.room_number+' <small>'+city.name+'</small></h2>' +
            '<p>'+room.description.substring(0,100)+'...</p>' +
            '</a>' +
            '</li>';

        });

        SearchResults += '</ul>';
        return SearchResults;
    },

    
    Notifications: function(notifications) {
        var html = '';

        $.each(notifications, function(index,notification){
            html += '<li><a class="notification_data" data-id="'+notification.id+'">'+notification.content+'</a></li>';
        });

        return html;
    }
    */

};
