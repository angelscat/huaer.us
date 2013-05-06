jQuery.fn.makeSelector = function(){
	var selectorContainers = [];
	this.each(function(i,v){
		var el = $(v);
		if(!el.is('select')) return; //只针对select元素

		var tpl = '<div class="selector"> \
					    <a href="javascript:;" hidefocus="true" class="trigger"><span class="value"></span><span class="handle"></span></a> \
					    <ul class="values custom-scroll-bar"></ul> \
					</div>',
			selectorContainer = $(tpl),
			trigger = selectorContainer.find('.trigger'),
			value = selectorContainer.find('.value'),
			handle = selectorContainer.find('.handle'),
			values = selectorContainer.find('.values'),
			options = el.find('option'),
			data = '',
			current;
		options.each(function(i,v){
			var option = $(v);
			data += '<li data-value="'+ option.val() +'"' + (option.is(':selected') ? 'class="hover"' : '') +' >'+ option.text() +'</li>';
		})
		values.append(data);
		selectorContainer.width(el.width());
		el.after(selectorContainer).hide();
		value.text(el.find('option:selected').text());
		current = values.find('li.hover');

		selectorContainer.on('click','.trigger',function(){
			if(values.is(':hidden')){
				if(values.height() > 200){
					values.height(200);
					values.css('overflow','auto');
				}
				values.show();
				selectorContainer.css('z-index','99999');
			}else{
				values.hide();
				selectorContainer.css('z-index','');
			}
		});
		values.on('click','li',function(){
			var li = $(this),
				val = li.attr('data-value'),
				txt = li.text();
			value.text(txt);
			el.find('option:[value="'+ val +'"]').attr('selected',true);
			el.trigger('change');
			values.hide();
			selectorContainer.css('z-index','');
			current.removeClass('hover');
			current = li;
		}).on('mouseenter','li',function(){
			$(this).addClass('hover');
		}).on('mouseleave','li',function(){
			if($(this).is(current)) return;
			$(this).removeClass('hover');
		})

		$(document).on('click',function(e){
			var target = $(e.target);
			if(!selectorContainer.find(target)[0]){
				values.hide();
				selectorContainer.css('z-index','');
			}
			// if(!target.is(trigger) && !target.is(values)){
			// 	values.hide();
			// }
		})

		selectorContainers.push(selectorContainer);
	})
	return selectorContainers;
}