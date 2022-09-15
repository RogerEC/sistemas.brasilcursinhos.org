const SERVER_HOST = 'https://localhost:7070';

document.addEventListener('deviceready', onDeviceReady, false);

function onDeviceReady() {

    document.addEventListener("backbutton", onBackKeyDown, false);

    function onBackKeyDown() {
        // botão voltar
    }
    
    function hideAll() {
        $('#login').addClass('d-none');
        $('#home').addClass('d-none');
        $('#scan').addClass('d-none');
    }

    if(localStorage.getItem('userId') !== null && localStorage.getItem('accessToken') !== null) {
        hideAll();
        $('#home').removeClass('d-none');
        $("#btn-logout").removeClass('d-none');
    } else {
        hideAll();
        $('#login').removeClass('d-none');
        $("#btn-logout").addClass('d-none');
    }

    $("#btn-logout").on("click", function(){
        localStorage.clear();
        hideAll();
        $('#login').removeClass('d-none');
        $("#btn-logout").addClass('d-none');
    })
    
    $("#btn-login").on("click", function(event){
        event.preventDefault();
        const USER = $("#user").val();
        const PASSWORD = $("#password").val()
        $("#password").val('');
        cordova.plugin.http.post(SERVER_HOST + '/app/login', {
            user:USER,
            password:PASSWORD
            }, {}, function(response) {
                try {
                    data = JSON.parse(response.data);
                    if(data.error === false) {
                        hideAll();
                        $("#home").removeClass('d-none');
                        $("#btn-logout").removeClass('d-none');
                        localStorage.setItem('userId', data.userId);
                        localStorage.setItem('accessToken', data.token);
                    } else {
                        console.log('deu ruim');
                    }
                } catch(exception) {
                    console.log('deu ruim pior ainda');
                }
            }, function(response) {
            console.error(response.error);
        });
        
    });
}
