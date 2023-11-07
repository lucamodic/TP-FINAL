$(document).ready(function(){

    $.ajax({
        url: '/user/traerUsuariosPorPuntajeAjax',
        success: function (respuestaDelController) {
            mostrarEnHTML(respuestaDelController);
        }
    });

    function mostrarEnHTML(respuestaDelController) {
        let tabla = $("#tablaRanking");
        respuestaDelController.usuarios.forEach(function (usuario) {
            let fila = $("<tr>");
            let filaNombreDeUsuario = $("<td>").html('<a href="/user/mostrarPerfil?user=' + usuario.username + '">' + usuario.username + '</a>');
            let filaPuntajeUsuario = $("<td>").text(usuario.puntaje);
            fila.append(filaNombreDeUsuario);
            fila.append(filaPuntajeUsuario);
            tabla.append(fila);
        });
    }

});