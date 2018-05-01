$(document).ready(function(){
    eventoPedido();
    eventoPedidoMultiple();
});

function eventoPedidoMultiple(){
    $('#pedidoMult').on('click', function(){
        var nueva = window.open('/js/ventana.html','Pedido',"width=300px, height=400px, menubar=no")
    });
}

function eventoPedido(){
    $('.pedido').on('click',function(){
        var id  =  $(this).attr('id');
        var boton = $(this);
        $.ajax({
            url: urlCantidad,
            type: 'GET',
            data: {'id':id},
            success: function(data){
                var input = '<div class="pedido-container"><input type="number" id="'+id+'" value="1" min="1" max="'+data+'"><button class="glyphicon glyphicon-ok aceptar-pedido"></button></div>'
                boton.replaceWith(input);
                $('.aceptar-pedido').on('click', function(){
                    var cantidadPedida = $('input[id='+id+']').val();
                    $.ajax({
                        url: urlPedido,
                        type: 'GET',
                        data: {'id':id, 'cantidadPedida':cantidadPedida},
                        // success: function() {
                        //     var boton = '<button type="button" id="'+id+'" class="btn btn-success pedido">Hacer pedido</button>'
                        //     $('.pedido-container').replaceWith(boton);
                        //     eventoPedido();
                        // }
                    });
                });
            },
        });
    });
}
