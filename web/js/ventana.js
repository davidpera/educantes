$(document).ready(function(){
    eventoVentana();
});

function eventoVentana(){
    $('#anadir-pedido').on('click',function(){
        $.ajax({
            url:"/index.php?r=uniformes/externos",
            type:'GET',
            dataType: 'json',
            success: function(data){
                var pedido = '<div class="pedido-container"><select>';
                for (unif of data) {
                    pedido += '<option value='+unif.id+'>'+unif.descripcion+'</option>'
                }
                pedido += '</select></div>';
                $('.pedidos').append(pedido);
                var id =  $('select').val();
                console.log(id);
                $('.pedido-container').attr('id',id);
                $.ajax({
                    url: "/index.php?r=uniformes/cantidad",
                    type: 'GET',
                    data: {'id':id},
                    success: function(data){
                        var num = '<input type="number" value="1" min="1" max="'+data+'"></input>';
                        $('.pedido-container[id='+id+']').last().append(num);
                        var equis = '<a href="#" class="borrar">X</a>'
                        $('.pedido-container[id='+id+']').last().append(equis);
                        eventoBorrar();
                    },
                });
                $('select').on('change', function(){
                    var id =  $(this).val();
                    var este =  $(this);
                    console.log(id);
                    $.ajax({
                        url: "/index.php?r=uniformes/cantidad",
                        type: 'GET',
                        data: {'id':id},
                        success: function(data){
                            var num = '<input type="number" value="1" min="1" max="'+data+'"></input>';
                            este.next().replaceWith(num);
                        },
                    });
                });
            }
        });
    });
}

function eventoBorrar(){
    $('.borrar').on('click',function(e){
        e.preventDefault();
        $(this).closest('.pedido-container').remove();
    });
}
