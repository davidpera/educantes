$(document).ready(function(){
    eventoColegio();
    var cont = 0;
});
var cont = 0;
function eventoColegio(){
    $('#anadir-colegio').on('click', function(){
        $.ajax({
            url:"/index.php?r=colegios/lista",
            type:'GET',
            datatype: 'json',
            success: function(data){
                var colegios = '<div class="colegios-container"><select>';
                for (col of data) {
                    console.log(col.nombre.split(" ").join(""));
                    colegios += '<option value='+col.nombre.split(" ").join("")+'>'+col.nombre+'</option>'
                }
                colegios += '</select><button id="aceptar-colegio" class="btn btn-success glyphicon glyphicon-ok"></button></div>';
                $('.pedidos').append(colegios);
                $('select').on('change', function(){
                    var id =  $(this).val().replace(/([a-z](?=[A-Z]))/g, '$1 ');
                    $(this).closest('.colegios-container').attr('id',id);
                });
                var id =  $('select').last().val().replace(/([a-z](?=[A-Z]))/g, '$1 ');
                console.log(id);
                $('.colegios-container').attr('id',id);
                eventoAceptarColegio();
            }
        })
    });
}

function eventoAceptarColegio(){
    $('#aceptar-colegio').on('click', function(){
        var select = $(this).prev();
        var contenedor = $(this).closest('.colegios-container');
        if (!$('fieldset[id='+select.val()+']').length) {
            cont++;
            contenedor.empty();
            var nomb = select.val().replace(/([a-z](?=[A-Z]))/g, '$1 ');
            var field = '<fieldset id="'+select.val()+'"><legend>'+nomb+'</legend>'+
            '<button id="anadir-pedido'+cont+'" type="button" name="button" class="btn btn-info">Añadir Articulo</button></fieldset>';
            contenedor.append(field);
            eventoPedido(cont);
        }
    });
}

function eventoPedido(cont){
    $('#anadir-pedido'+cont).on('click',function(){
        var boton = $(this);
        var colegio = boton.closest('.colegios-container').attr('id');
        console.log(colegio);
        $.ajax({
            url:"/index.php?r=uniformes/externos",
            type:'GET',
            data: {'nombre' : colegio },
            dataType: 'json',
            success: function(data){
                console.log(data);
                var pedido = '<div class="pedido-container"><select>';
                for (unif of data) {
                    pedido += '<option value='+unif.id+'>'+unif.descripcion+'</option>'
                }
                pedido += '</select></div>';
                boton.closest('.colegios-container').children('fieldset').append(pedido);
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
                        var equis = '<a href="#" class="borrar glyphicon glyphicon-remove"></a>'
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
            },
            error: function(xhr, ajaxOptions, thrownError){
                console.log(xhr.responseText);
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
