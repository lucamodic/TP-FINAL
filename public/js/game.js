$(document).ready(function(){
    $('#reportar').click(function(e){
        let  idPregunta = $('#id_pregunta').val();
        let url = '/game/reportarPregunta?id_pregunta=' + idPregunta;
        $.ajax({
            url: url,
            type: 'GET',
            success: function(){
            }
        });
        $('#reportar').hide();
    });

    setInterval(() => {
        $.ajax({
            url: '/game/calcularTiempoQueQueda',
            success: function (response) {
                let data = JSON.parse(response)["tiempo"];
                if (data <= 0) {
                    window.location.href = "/game/perdiste";
                }
                $('.tiempo').html(data);
            }
        });
    },900);



});