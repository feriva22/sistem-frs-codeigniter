<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
$(document).ready(function() {

    var url = {
        data : base_url+'tahunakademik/get_datatable',
        detail : base_url+'tahunakademik/detail',
        add: base_url + 'tahunakademik/add',
		edit: base_url + 'tahunakademik/edit',
		delete: base_url + 'tahunakademik/delete',
    };

    var dataTableObj = $("#tahunakademik_table").DataTable({
        processing:true,
        serverSide:false,
        bProcessing: true,
        stateSave: false,
        pagingType: 'full_numbers',
        ajax:{
            url: url.data,
            type: 'POST',
            dataType: "json",
            data: { [csrf_name] : Cookies.get(csrf_name)}, 
        },
        'columns':[ 
            {   data:"tak_id",
                render: function(data, type, meta){
                    return '<input type="checkbox" name="tak_id[]" value="'+ data +'"/>';
                }
            },
            { data:"tak_tahun" },
            { data:"tak_isganjil",
                render:function(data,type,meta){
                    if(data == 1){
                        return 'Ganjil';
                    }
                    else{
                        return 'Genap';
                    }
                }
            },
            { 
                data:"tak_id",
                render: function(data, type, meta){
                    /*return `<button class="btn btn-sm btn-success btn-edit" data-id=${data}><i class="far fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id=${data}><i class="fa fa-trash"></i></button>`;*/

                    return `<div class="table-actions">
                                    <a href="#"><i class="ik ik-eye"></i></a>
                                    <a href="#"><i class="ik ik-edit-2"></i></a>
                                    <a href="#"><i class="ik ik-trash-2"></i></a>
                                </div>`
                }
            }
        ]
    });

});
</script>