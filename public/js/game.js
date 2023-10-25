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
    });
});