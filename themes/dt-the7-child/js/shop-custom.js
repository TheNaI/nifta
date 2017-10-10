jQuery(document).ready(function($){

	var fieldGold = ['#uni_cpo_option_charm_gold'];
	var fieldSilver = ['#uni_cpo_option_charm_silver'];
	genData();

	$('#uni_cpo_option_color-field').on('change', function(e){
		if($(this).val() == 'gold'){
			fieldGold.forEach(function(arr){
				var idName = (arr[0] == '#') ? arr.split('#')[1] : arr;
				$('.select-new[data-select-id="'+idName+'"]').show();
			});
			fieldSilver.forEach(function(arr){
				var idName = (arr[0] == '#') ? arr.split('#')[1] : arr;
				$('.select-new[data-select-id="'+idName+'"]').hide();
			});
		}
		else if($(this).val() == 'silver'){
			fieldGold.forEach(function(arr){
				var idName = (arr[0] == '#') ? arr.split('#')[1] : arr;
				$('.select-new[data-select-id="'+idName+'"]').hide();
			});
			fieldSilver.forEach(function(arr){
				var idName = (arr[0] == '#') ? arr.split('#')[1] : arr;
				$('.select-new[data-select-id="'+idName+'"]').show();
			});
		}
	});

	function genData(){
		genSelect('#uni_cpo_option_charm_gold');
		genSelect('#uni_cpo_option_charm_silver');
		fieldGold.forEach(function(arr){
			var idName = (arr[0] == '#') ? arr.split('#')[1] : arr;
			$('.select-new[data-select-id="'+idName+'"]').show();
		});
		fieldSilver.forEach(function(arr){
			var idName = (arr[0] == '#') ? arr.split('#')[1] : arr;
			$('.select-new[data-select-id="'+idName+'"]').hide();
		});
	}

	function genSelect(id){
		var idName = (id[0] == '#') ? id.split('#')[1] : id;
		var divSelect = $('<div>', {class : 'select-new', 'data-select-id' :  idName});
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


		$(id).on('change', function(e){
			$(id).addClass('hidden').delay(0).queue(function(next){
				$(this).removeClass('show');
				$('html').removeClass('lock-scroll');
				next();
			});
			$('[data-select-id="'+idName+'"] .select-image-select').attr('src',$(id + '-field option:selected').attr('data-thumbimageuri'));
			$('[data-select-id="'+idName+'"] .select-title-name').text($(id + '-field option:selected').attr('data-imagetitle'))
		});

	}
}).delegate('.select-modal','click',function(e){
	e.preventDefault();
	$ = jQuery;
	$('html').addClass('lock-scroll');
	$($(this).data('target')).removeClass('hidden').delay(0).queue(function(next){
		$(this).addClass('show');
		next();
	});
	return false;
});


