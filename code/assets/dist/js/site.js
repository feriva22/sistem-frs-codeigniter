function ajaxExtend(options){   //untuk memasukkan csrf tiap call ajax
    options.data[csrf_name] = Cookies.get(csrf_name);
	if(typeof(options.data[csrf_name]) == 'undefined'){
		//refresh if csrf token is not found
		document.location = document.location;
		return;
	}
	$.ajax({
        url: options.url,
        type: 'post',
        data: options.data,
        success: options.success,
        error: options.error,
        dataType : 'json'
    });
}
