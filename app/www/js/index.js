const SERVER_HOST = 'http://192.168.0.107:7373';

document.addEventListener('deviceready', onDeviceReady, false);

function onDeviceReady() {

    /*let canvas = document.getElementById("canvas");
    window.plugin.CanvasCamera.initialize(canvas);

    $("#btn-scan").on("click", function(){
        let options = {
            quality: 75,
            destinationType: 0,
            encodingType: 0,
            width: 640,
            height: 480
        };
        window.plugin.CanvasCamera.start(options);
    })*/
    console.log(navigator.camera);

    localStorage.setItem('userId', 'data.userId');
    localStorage.setItem('accessToken', 'data.token');
    localStorage.setItem('currentPage', 'home');

    document.addEventListener("backbutton", onBackKeyDown, false);

    function onBackKeyDown() {
        let page = localStorage.getItem('currentPage');
        if(page === 'login') {
            navigator.app.exitApp();
        } else if (page === 'home') {
            navigator.app.exitApp();
        } else if (page === 'activities' || page === 'scan') {
            hideAll();
            $("#home").removeClass('d-none');
        }
    }
    
    function hideAll() {
        $('#login').addClass('d-none');
        $('#home').addClass('d-none');
        $('#scan').addClass('d-none');
        $('#activities').addClass('d-none');
    }

    if(localStorage.getItem('userId') !== null && localStorage.getItem('accessToken') !== null) {
        hideAll();
        $('#home').removeClass('d-none');
        $("#btn-logout").removeClass('d-none');
        localStorage.setItem('currentPage', 'home');
    } else {
        hideAll();
        $('#login').removeClass('d-none');
        $("#btn-logout").addClass('d-none');
        localStorage.setItem('currentPage', 'login');
    }

    $("#btn-logout").on("click", function(){
        localStorage.clear();
        hideAll();
        $('#login').removeClass('d-none');
        $("#btn-logout").addClass('d-none');
        localStorage.setItem('currentPage', 'login');
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
                        localStorage.setItem('currentPage', 'home');
                    } else {
                        console.log('deu ruim');
                        localStorage.setItem('currentPage', 'login');
                        $("#msg").text('teste 1');
                    }
                } catch(exception) {
                    console.log('deu ruim pior ainda');
                    localStorage.setItem('currentPage', 'login');
                    $("#msg").text('teste 2');
                }
            }, function(response) {
            console.error(response.error);
            $("#msg").text(response.error);
        });
        
    });

    $(".btn-select-option").on("click", function(){
        hideAll();
        localStorage.setItem('option', $(this).val());

        if(localStorage.getItem('option') === 'check') {
            localStorage.setItem('currentPage', 'scan');
            $("#scan").removeClass('d-none');
        } else if(localStorage.getItem('option') === 'register') {
            localStorage.setItem('currentPage', 'scan');
            $("#scan").removeClass('d-none');
        } else if(localStorage.getItem('option') === 'presence') {
            localStorage.setItem('currentPage', 'activities');
            $("#activities").removeClass('d-none');
        }
    });

    $(".btn-activity").on("click", function(){
        console.log('clicou');
        if(localStorage.getItem('option') === 'presence' && localStorage.getItem('currentPage') === 'activities') {
            console.log('entrou');
            localStorage.setItem('currentPage', 'scan');
            localStorage.setItem('activityId', $(this).val());
            hideAll();
            $("#scan").removeClass('d-none');
        } else {
            console.log('nao entrou');
        }
    });

    
}


