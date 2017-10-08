jQuery(document).ready(function($){
	genSelect('#uni_cpo_option_charm_gold');
	$('#uni_cpo_option_charm_gold-field').on('change', function(e){
		console.log($(this).val());
		$('#uni_cpo_option_charm_gold').addClass('hidden').delay(500).queue(function(next){
			$(this).removeClass('show');
			next();
		});
		$('.select-image-select').attr('src',$('#uni_cpo_option_charm_gold-field option:selected').attr('data-thumbimageuri'))
		$('.select-title-name').text($('#uni_cpo_option_charm_gold-field option:selected').attr('data-imagetitle'))
	});

	function genData(){
	}

	function genSelect(id){
		var divSelect = $('<div>', {class : 'select-new'});
		$(id).addClass('select-old hidden');
		var selectField = id+'-field';
		$(selectField + ' option:selected').attr('data-thumbimageuri');


		var selectText = $(id + " .uni_cpo_fields_header")
			.clone()    //clone the element
			.children() //select all the children
			.remove()   //remove all the children
			.end()  //again go back to selected element
			.text();
		divSelect.append($('<span>', { text : selectText, class : 'select-title' }));
		divSelect.append($('<span>', { text : $(selectField + ' option:selected').attr('data-imagetitle'), class : 'select-item-name' }));
		divSelect.append($('<img>', {
			src : $(selectField + ' option:selected').attr('data-thumbimageuri'),
			class : 'select-image-select'
		}));
		divSelect.append($('<a>', {href : '#', class : 'select-modal',text : 'SELECT','data-target' : id}));
		$(id).after(divSelect);
	}
}).delegate('.select-modal','click',function(e){
	e.preventDefault();
	$ = jQuery;
	$($(this).data('target')).removeClass('hidden').delay(500).queue(function(next){
		$(this).addClass('show');
		next();
	});
	return false;
});


