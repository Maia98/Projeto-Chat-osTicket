<?php
if(!defined('OSTSTAFFINC') || !$thisstaff) die('Access Denied');

?>
<h2>Central de Relatórios</h2>
<hr>
<div id="msg_error" style="display: none;"></div>
    <div class="row">
    <form id="filtro" action="relatorio.php" method="get" target="_blank">
        <div class="col-md-4">
            <h3>Tipo Relatório: </h3>
            <select name="relatorio_selected" id="relatorio_selected" class="form-control">
                <option name="selecione" value="0" >— Selecione o Relatório —</option>
                <option name="todos_tickets" value="todos_tickets">Tickets</option>
                <option name="tickets_qtd" value="tickets_qtd">Tickets P/Qtd</option>
                <option name="tickets_atrasado" value="tickets_atrasado">Tickets Atrasados</option>
                <option name="tickets_atrasado_qtd" value="tickets_atrasado_qtd">Tickets Atrasados P/Qtd</option>
            </select>
        </div>
        <div class="col-md-2">
            <h3>De:</h3>
            <input class="form-control" id="data_ini" name="data_ini" type="date">
        </div>
        <div class="col-md-2">
            <h3>Até:</h3>
            <input class="form-control" id="data_fim" name="data_fim" type="date">
        </div>
        <div class="col-md-4">
            <h3>Status</h3>
            <input type="radio" name="status" value="todos" checked="checked"> Todos&nbsp;
            <input type="radio" name="status" value="aberto" checked="checked"> Aberto&nbsp;
            <input type="radio" name="status" value="fechado"> Fechado
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <h3>Departamento: </h3>
            <select name="departamento_selected" id="departamento_selected" class="form-control">
                <option value=""  >&mdash; <?php echo __('Select Department'); ?>&mdash;</option>
                <?php
                if($depts=Dept::getDepartments()) {
                    foreach($depts as $id =>$name) {
                        echo sprintf('<option value="%d" %s>%s</option>',
                                $id, ($info['deptId']==$id)?'selected="selected"':'',$name);
                    }
                }
                ?>
            </select>
        </div>
        <div class="col-md-4">
            <h3>Assunto: </h3>
            <select name="assunto_selected" class="form-control select-subjects">
                <option value=""> — Selecione o Assunto — </option>
            </select>
        </div>
        <div class="col-md-12">
            <input id="enviar" type="submit" class="btn btn-primary" value="Filtrar">
        </div>
    </div>
    </form>
<style>

    input, select{
        margin-right: 10px !important;
    }

    @media screen and (max-width: 450px) {

        #pjax-container {
            width: 100% !important;
        }

        input[type=submit], input[type=reset], input[type=button] {
            margin-bottom: 10px;
        }

        .navbar {
            z-index: 2 !important;
        }

        .redactor_box {
            z-index: 1 !important;
        }

    }

</style>
<script type="text/javascript">
    
        
        
    $( document ).ready(function() {
        
        getSubjects();

        $("select[name=departamento_selected]").change(function (event) {
        
          getSubjects($(this).val());
              
        });
        

        if($("select[name=departamento_selected]").val() != 0){
           getSubjects($("select[name=departamento_selected]").val());
           
        }

    });

   

    function getSubjects(id) {
        
        $.ajax({

            type: "POST",
            data: "id="+id,
            //url: "ajax.php/tickets/subjects",
            url:  "../ajax.php/form/subjects",
            dataType: 'json',

            success: function(data) {
                 //console.log(data);   
                if(data.success === true){
                    var subjects = data.subjects;
                    $(".select-subjects option").remove();
                    $(".select-subjects").append('<option value=""> — Selecione o Assunto — </option>');
                    subjects.forEach(function (index, value) {
                        $(".select-subjects").append("<option value='"+index.id+"'>"+ index.value +"</option>");
                    });
                }else{
                        
                }
            }
        });
    }

    $('#enviar').click(function (e) {
        e.preventDefault();
        var data_ini = $('#data_ini').val();
        var data_fim = $('#data_fim').val();
        var relatorio_selected = $('#relatorio_selected').val();

        if(relatorio_selected == '0'){
            alerta('Relatório não selecionado')
        }else if(data_ini == ''){
            alerta('Data início não preenchido')
        }else if(data_fim == ''){
            alerta('Data fim não preenchido')
        }else{
            $('#filtro').submit();
        }
    });

    function alerta(msg){
        $('#msg_error').text(msg);
        $('#msg_error').fadeIn();
        setTimeout(function(){
            $('#msg_error').fadeOut();
        }, 2000);
    }
</script>
