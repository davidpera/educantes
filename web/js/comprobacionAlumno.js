$(document).ready(function(){
    input = '<p>Introduzca lo datos de su hijo menor en el centro elegido anteriormente</p>'+
        '<div class="form-group field-nombre-hijo required">'+
        '<label class="control-label" for="nombre-hijo">Nombre</label>'+
        '<input type="text" id="nombre-hijo" class="form-control" name="Hijo[nombre]" maxlength="255" aria-required="true">'+
        '<div class="help-block"></div>'+
        '</div>'+
        '<div class="form-group field-prim-ape-hijo required">'+
        '<label class="control-label" for="prim-ape-hijo">Primer Apellido</label>'+
        '<input type="text" id="prim-ape-hijo" class="form-control" name="Hijo[prim-ape]" maxlength="255" aria-required="true">'+
        '<div class="help-block"></div>'+
        '</div>'+
        '<div class="form-group field-sec-ape-hijo required">'+
        '<label class="control-label" for="sec-ape-hijo">Segundo Apellido(si tiene)</label>'+
        '<input type="text" id="sec-ape-hijo" class="form-control" name="Hijo[sec-ape]" maxlength="255" aria-required="true">'+
        '<div class="help-block"></div>'+
        '</div>'+
        '<div class="form-group field-fech-nac-hijo required">'+
        '<label class="control-label" for="fech-nac-hijo">Fecha de nacimiento</label>'+
        '<input type="text" id="fech-nac-hijo" class="form-control" name="Hijo[fech-nac]" maxlength="255" aria-required="true">'+
        '<div class="help-block"></div>'+
        '</div>';
    $('#botones').before(input);

    var inter = setInterval(function()
    {
        if (typeof $('#formulario').data('yiiActiveForm') !== 'undefined'){
            generaValids();
            clearInterval(inter);
        }
    }, 100);
});

function generaValids(){
    $('#formulario').yiiActiveForm('add', {
        id: 'nombre-hijo',
        name: 'Hijo[nombre]',
        container: '.field-nombre-hijo',
        input: '#nombre-hijo',
        error: '.help-block',
        validate: function(attribute, value, messages, deferred) {
            yii.validation.required(value, messages, {message: 'El nombre del hijo no puede estar vacio'});
        }
    });
    $('#formulario').yiiActiveForm('add', {
        id: 'prim-ape-hijo',
        name: 'Hijo[prim-ape]',
        container: '.field-prim-ape-hijo',
        input: '#prim-ape-hijo',
        error: '.help-block',
        validate: function(attribute, value, messages, deferred) {
            yii.validation.required(value, messages, {message: 'El primer apellido del hijo no puede estar vacio'});
        }
    });
    $('#formulario').yiiActiveForm('add', {
        id: 'fech-nac-hijo',
        name: 'Hijo[fech-nac]',
        container: '.field-fech-nac-hijo',
        input: '#fech-nac-hijo',
        error: '.help-block',
        validate: function(attribute, value, messages, deferred) {
            yii.validation.required(value, messages, {message: 'La fecha de nacimiento del hijo no puede estar vacio'});
            yii.validation.number(value, messages, {
                "pattern": /^[1-9]{1}[0-9]{3}-([1][0-2]|[0-9])-([1-2][0-9]|[3][0-1]|[0-9])$/,
                "message": "Fecha no valida",
                "skipOnEmpty": 1
            });
        }
    });
}
