$(document).ready(function(){
    $('#reportar').click(function(e){
        var idPregunta = $('#id_pregunta').val();
        var url = '/game/reportarPregunta?id_pregunta=' + idPregunta;
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
                var data = JSON.parse(response)["tiempo"];
                if (data <= -1000000) {
                    window.location.href = "/game/perdiste";
                }
                $('.tiempo').html(data);
            }
        });
    },900);



});