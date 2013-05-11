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
		//selectorContainer.width(el.width());
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

jQuery.fn.makeFileSelector = function(){
	var selectorContainers = [];
	this.each(function(i,v){
		var el = $(v);
		if(!el.is('input:file')) return; //只针对select元素

		var selectorContainer = $('<div class="ipt-file"></div>');
		el.after(selectorContainer);
		selectorContainer.append(el.attr({
			'class':'cover',
			'hidefocus':'true',
			'size':'0'
		}));
		var fileInfo = '<div class="file-info"> \
	                        <input type="text" class="ipt-text" readonly="readonly" /> \
	                        <a class="button button-cancel clearfix" href="###"> \
	                            <span class="button-left hide">&nbsp;</span> \
	                            <span class="button-text">浏览文件</span> \
	                            <span class="button-right hide">&nbsp;</span> \
	                        </a> \
	                    </div>';
	    fileInfo = $(fileInfo);
	    selectorContainer.append(fileInfo);
	    var value = fileInfo.find('input');

	    el.on('change',function(){
	    	value.val(el.val());
	    })

		selectorContainers.push(selectorContainer);
	})
	return selectorContainers;
}

jQuery.fn.vdcodeChange = function(){
	this.each(function(i,v){
		var el = $(v);
		if(!el.is('img')) return; //只针对图片
		var src = el.attr('src').split('?')[0],
			a = el.next(); //跟在后面的链接

		el.on('click',function(){
			el.attr('src',src + '?_r=' + Math.random());
		})
		if(a[0]){
			a.on('click',function(){
				el.attr('src',src + '?_r=' + Math.random());
				return false;
			})
		}
	})
}