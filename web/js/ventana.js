$(document).ready(function(){
    eventoColegio();
    confirmar();
    var cont = 0;
});
var cont = 0;
function eventoColegio(){
    $('#anadir-colegio').on('click', function(){
        $.ajax({
            url:"/index.php/colegios/lista",
            type:'GET',
            datatype: 'json',
            success: function(data){
                var colegios = '<div class="colegios-container "><select>';
                // console.log(data);
                for (col of data) {
                    colegios += '<option value='+col.nombre.split(" ").join("")+'>'+col.nombre+'</option>'
                }
                colegios += '</select><button id="aceptar-colegio" class="btn btn-success glyphicon glyphicon-ok"></button></div>';
                $('.pedidos').append(colegios);
                $('select').on('change', function(){
                    var id =  $(this).val().replace(/([a-z](?=[A-Z]))/g, '$1 ');
                    $(this).closest('.colegios-container').attr('id',id);
                });
                var id =  $('select').last().val().replace(/([a-z](?=[A-Z]))/g, '$1 ');
                $('.colegios-container').attr('id',id);
                eventoAceptarColegio();
            }
        })
    });
}

function confirmar(){

    $('#confirmar').on('click',function(){
        var todos = new Object();
        var pedidos = [];
        // var todos = [];
        // var cont = 0;
        for (f of $('fieldset')) {
            var ar = [];
            var id = f['id'].replace(/([a-z](?=[A-Z]))/g, '$1 ');
            ar.push(id);
            for (var i = 2; i <= f.childNodes.length - 1; i++) {
                var sel = [];
                var ped = [];
                var inp = [];
                sel.push(f.childNodes[i].childNodes[0]['value']);
                inp.push(f.childNodes[i].childNodes[1]['value']);

                for (var j = 0; j < sel.length; j++) {
                    ped.push([sel[j],inp[j]])
                }
                ar.push(ped);
            }
            var inc = [];
            for (a of ar) {
                var inda = ar.indexOf(a);
                // console.log(a);
                for (b of ar) {
                    var indb = ar.indexOf(b);
                    if (inda != indb) {
                        if (a[0][0] === b[0][0]) {
                            // console.log(a[0][0], b[0][0]);
                            if (!inc.includes(indb)) {
                                inc.push(indb);
                            }

                            var sum = parseInt(a[0][1]) + parseInt(b[0][1]);
                            a[0][1] = sum;
                            // ar.splice(ind);
                        }
                    }
                }
            }
            for (var i = 0; i < inc.length; i++) {
                if (i != inc.length - 1) {
                    ar.splice(inc[i]);
                    // console.log(ar[inc[i]]);
                }
            }
            pedidos.push(ar);
            // console.log(pedidos);
        }
        todos.pedidos = pedidos;
        var json = JSON.stringify(todos);
        $.ajax({
            url:"/index.php/uniformes/multiple",
            type:"GET",
            data: {'pedido':json},
            datatype: 'json',
            contentType: "application/json",
            success: function(data){
                // console.log(data);
                window.opener.location.href="/index.php/uniformes%2Findex"
                window.close();
            },
            error: function(xhr, status, error) {
                // var err = eval("(" + xhr.responseText + ")");
                console.log(xhr.responseText);
            },
        });
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
        $.ajax({
            url:"/index.php/uniformes/externos",
            type:'GET',
            data: {'nombre' : colegio },
            dataType: 'json',
            success: function(data){
                var pedido = '<div class="pedido-container"><select>';
                for (unif of data) {
                    pedido += '<option value='+unif.id+'>'+unif.codigo+'</option>'
                }
                pedido += '</select></div>';
                boton.closest('.colegios-container').children('fieldset').append(pedido);
                var id =  $('select').val();
                $('.pedido-container').attr('id',id);
                $.ajax({
                    url: "/index.php/uniformes/cantidad",
                    type: 'GET',
                    data: {'id':id},
                    success: function(data){
                        var num = '<input type="number" value="1" min="1" max="'+data+'"></input>';
                        boton.closest('.colegios-container').find('.pedido-container[id='+id+']').last().append(num);
                        var equis = '<a href="#" class="borrar glyphicon glyphicon-remove"></a>'
                        boton.closest('.colegios-container').find('.pedido-container[id='+id+']').last().append(equis);
                        eventoBorrar();
                    },
                });
                $('select').on('change', function(){
                    var id =  $(this).val();
                    var este =  $(this);
                    $.ajax({
                        url: "/index.php/uniformes/cantidad",
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
