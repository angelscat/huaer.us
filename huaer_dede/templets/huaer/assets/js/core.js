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
};

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
};

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
};

(function($){
	window.CatalogSelector = function(options){
	    $.extend(this,options);
	    this.init();
	}
	CatalogSelector.prototype = {
	    template:'<div class="catalog-dialog"> \
	                <dl class="catalogs"> \
	                    <dt>请选择最终分类(如：写景、叙事等)</dt> \
	                    <dd><ul class="school" id="catalogSchool"></ul></dd> \
	                    <dd><ul class="grade" id="catalogGrade"></ul></dd> \
	                    <dd><ul class="type" id="catalogType"></ul></dd> \
	                </dl> \
	            </div>',
	    init:function(){
	        this.catalogData = catalogdata;
	        this.currentSchool = this.catalogData[0];
	        this.currentGrade = this.currentSchool.children[0];
	        // this.currentType = this.currentGrade.children[0];
	        this._initDom();
	        this.updateSchool();
	        this._initEvent();
	    },
	    _initDom:function(){
	        this.el = $(this.template);
	        this.catalog = this.el.find('.catalogs');
	        this.catalogSchool = this.el.find('#catalogSchool');
	        this.catalogGrade = this.el.find('#catalogGrade');
	        this.catalogType = this.el.find('#catalogType');
	        if($.browser.msie){
	            this.el.append('<iframe width="100%" height="100%" frameBorder="0" src="javascript:false" style="position:absolute;top:0;left:0;z-index:1;"></iframe>');
	        }
	        this.el.css({
	            left:-9999,
	            top:-9999
	        }).appendTo($('body'));

	        this.show();
	    },
	    updateSchool:function(){
	        var schools = this.catalogData,
	            lst = ''
	        for(var i = 0, len = schools.length; i < len; i++){
	            lst += '<li><a data-index="'+ i +'" data-id="'+ schools[i].id +'" data-type="'+ schools[i].type +'" href="javascript:;"'+ (i === 0 ? 'class="cur"' : '') +'>'+ schools[i].name +'</a></li>';
	        }
	        this.catalogSchool.html(lst);
	        this.currentSchool = schools[0]
	        this.updateGrade();
	    },
	    updateGrade:function(){
	    	this.currentGrade = null;
	        var grades = this.currentSchool.children,
	            lst = '';
	        for(var i = 0, len = grades.length; i < len; i++){
	            lst += '<li><a data-index="'+ i +'" data-id="'+ grades[i].id +'" data-type="'+ grades[i].type +'" href="javascript:;"'+ (i === 0 ? 'class="cur"' : '') +'>'+ grades[i].name +'</a></li>';
	        }
	        this.catalogGrade.html(lst);
	        this.currentGrade = grades[0]
	        this.updateType();
	    },
	    updateType:function(){
	    	this.currentType = null;
	        var types = this.currentGrade.children,
	            lst = '';
	        for(var i = 0, len = types.length; i < len; i++){
	            lst += '<li><a data-index="'+ i +'" data-id="'+ types[i].id +'" data-type="'+ types[i].type +'" href="javascript:;">'+ types[i].name +'</a></li>';
	        }
	        this.catalogType.html(lst);
	    },
	    _initEvent:function(){
	        var _this = this;
	        this.catalogSchool.on('click','a',function(){
	            var target = $(this),
	                index = target.attr('data-index') * 1,
	                id = target.attr('data-id') * 1,
	                type = target.attr('data-type') * 1;
	            if(target.hasClass('cur')) return;
	            _this.catalogSchool.find('a.cur').removeClass('cur');
	            target.addClass('cur');
	            _this.currentSchool = _this.catalogData[index];
	            _this.updateGrade();
	        });
	        this.catalogGrade.on('click','a',function(){
	            var target = $(this),
	                index = target.attr('data-index') * 1,
	                id = target.attr('data-id') * 1,
	                type = target.attr('data-type') * 1;
	            if(target.hasClass('cur')) return;
	            _this.catalogGrade.find('a.cur').removeClass('cur');
	            target.addClass('cur');
	            _this.currentGrade = _this.currentSchool.children[index];
	            _this.updateType();
	        });
	        this.catalogType.on('click','a',function(){
	            var target = $(this),
	                index = target.attr('data-index') * 1,
	                id = target.attr('data-id') * 1,
	                type = target.attr('data-type') * 1;
	            _this.catalogType.find('a.cur').removeClass('cur');
	            target.addClass('cur');
	            _this.currentType = _this.currentGrade.children[index];
	        });

	        this.el.on('click',function(e){
	            e.stopPropagation();
	        }).on('click','a',function(e){
	            var target = $(this),
	                index = target.attr('data-index') * 1,
	                id = target.attr('data-id') * 1,
	                type = target.attr('data-type') * 1;
				if(type === 3 && _this.callback){
					// console.log(_this.currentSchool, _this.currentGrade, _this.currentType)
					_this.callback(_this.currentSchool, _this.currentGrade, _this.currentType)
					_this.hide();
				}
	        })

	        this._hide = function(){
	            _this.hide();
	        }
	        $(document).on('click',this._hide);
	    },
	    show:function(left,top){
	        var x, y, winWidth, winHeight,
	            isStrictMode = (document.compatMode != "BackCompat");
	        winWidth = isStrictMode ? document.documentElement.clientWidth : document.body.clientWidth;
	        winHeight = isStrictMode ? document.documentElement.clientHeight : document.body.clientHeight;
	        x = left || this.left || Math.max(parseInt((winWidth - this.el.width())/2),0) + $(document).scrollLeft();
	        y = top || this.top || Math.max(parseInt((winHeight - this.el.height())/2),0) + $(document).scrollTop();

	        this.el.css({
	            left:x,
	            top:y,
	            zIndex:9999 
	        })
	    },
	    hide:function(){
	        this.el.css({
	            left:-9999,
	            top:-9999
	        })
	    },
	    remove:function(){
	        $(document).off('click',this._hide);
	        this.el.remove();
	    }
	}
})(jQuery);

