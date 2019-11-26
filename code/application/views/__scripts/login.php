<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">

    //auto select handler
    $("#loginas_select").change(function(){
        if(this.value == <?php echo ADMIN;?> || this.value == "") {
            $('#user_select').empty().hide("slow").next('i').hide();return;
        }else{
            ((this.value == <?php echo DOSEN;?>) ? prefix='dos_' : prefix='mhs_' );
        } //return if choose ADMIN

        ajaxExtend({
            url: base_url+'login/fetch_user',
            data: {group_id: this.value},
            success: (result) => { 
                if(result.status == "success"){
                    $("#user_select").empty();
                    $.each(result.data, function(idx,value) {  
                        $('#user_select')
                            .append($("<option></option>")
                                    .attr("value",value[prefix+'id'])
                                    .text(value[prefix+'departemen']+' - '+value[prefix+'nama']));     
                        });
                    $('#user_select').show("slow").next('i').show();
                }else{
                    console.log(result.message);
                }
            },
            error: (err) => {  }
        })
    });

    $("#formLogin").submit(function(e){
        e.preventDefault();
        ajaxExtend({
            url: base_url+'login/authenticate',
            data: {group_id: $("#loginas_select").val(),user_id: $("#user_select").val()  },
            success: (data) => { 
                if(data.status == "success"){
                    //console.log(data);
                    show_alert('success',data.message);
                    //success login redir to redi_page
                    document.location = base_url+data.redir_page;
                }else{
                    show_alert('warning',data.message);
                    //console.log(data);
                }
            },
            error: (err) => {  }
        })
    })


    function show_alert(status, message){
        final_string = message;
        if(typeof(message) === 'object'){
            final_string = '';
            Object.keys(message).forEach(function(key){
                final_string += `${message[key]}\n`;
            })
        }
        $('#alert_notice').append(`
                                <div class="alert alert-${status} alert-dismissible fade show" id="" role="alert">
                                <strong>${status} !</strong> ${final_string}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <i class="ik ik-x"></i></button></div>`);
    }
</script>