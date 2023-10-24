$(document).ready(function(){
    $('#reportar').click(function(e){
        $.ajax({
            url:'http://localhost/game/reportarPregunta?id_pregunta=' +  $('#id_pregunta').val(),
            type:'GET',
            success: function(){
                console.log("BIEN");
            }
        })
    })
})