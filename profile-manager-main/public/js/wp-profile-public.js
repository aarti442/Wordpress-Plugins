var ajaxurl = ajax_object.ajax_url;
jQuery( window ).on( "load", function() {
	var pagination = jQuery('.paginationpage').val();
	profile_list(pagination);
});
function profile_list(pagination,formData = '',order_by='',order='',page = ''){
	
	var data = {
		'action': 'wp_profile_filter',		
		'limit': pagination,
		
	};
	
	if(formData != ""){
		data.formData = formData;
	}
	if(order_by != ""){
		data.order_by = order_by;
	}
	if(order != ""){
		data.order = order;
	}
	if(page != ""){
		data.page = page;
	}
	
	jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		data: data,
		async: false,
		cache: false,
		
		//dataType: "json",
		
		beforeSend: function () {
		},
		success: function (response) {
			//console.log(response);
			//var res = JSON.parse(response);
			
			jQuery('#responsedata').html(response);
			jQuery('.page-numbers').attr("href","javascript:void(0)");
			jQuery('.next.page-numbers').parent().attr('page',jQuery('.next.page-numbers').parent().prev().attr("page"));
			jQuery('#skills, #education').select2({
			
				placeholder : "Select"
			});
		}
	});
}
jQuery(document).on('submit', '#filter-list-form', function (e) {
	e.preventDefault();
	var data = jQuery(this).serialize();
	var pagination = jQuery('.paginationpage').val();
	
	profile_list(pagination,data);
});

jQuery(document).on('click','#reset_form',function(e){
	var form = document.getElementById("filter-list-form");
	jQuery('#skills').val(null).trigger('change');
	jQuery('#education').val(null).trigger('change');
  	form.reset();
	jQuery('#age').val(0);
	jQuery('.ageText').text((jQuery('#age').val()));
	jQuery('#filter-list-form').trigger('submit');
})

jQuery(document).on('click','.profile-action',function(){
	jQuery(".page-numbers").removeClass('current');
	jQuery(this).addClass('current new');
	var order_by = jQuery(this).attr('order_by');
	var order = jQuery(this).attr('order');
	var data = jQuery('#filter-list-form').serialize();
	var pagination = jQuery('.paginationpage').val();
	var page = 1;
	profile_list(pagination,data,order_by,order,page);

});

jQuery(document).on('click','.page-numbers',function(){

	var page = jQuery(this).text();
	var data = jQuery('#filter-list-form').serialize();
	var pagination = jQuery('.paginationpage').val();
	var current_page = jQuery('.current').text();
	var order_by =  jQuery('.pagination').attr('order_by');
	var order =  jQuery('.pagination').attr('order');

	if(page == "Next"){
		page = parseInt(current_page) + parseInt(1);
	}

	if(page == "Prev"){
		page = parseInt(current_page) - parseInt(1);
	}

	console.log(page+'test'+current_page);


	jQuery(".page-numbers").removeClass('current');
	profile_list(pagination,data,order_by,order,page);

});


jQuery(document).on('change','#age',function(){
	jQuery('.ageText').text((jQuery(this).val()));
});
jQuery('#skills, #education').select2({		
	placeholder : "Select"
});