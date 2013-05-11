jQuery(function(){
	if(!document.getElementById('addArticle')) return;

	var articleForm = $('#addArticle form'),
		title = $('#title'),
		writer = $("#writer"),
		channel = $('#channel'),
		vdcode = $('#vdcode'),
		vdcodeImg = $('#vdcodeImg'),
		vdcodeChange = $('#vdcodeChange'),
		smtBtn = $('#smtBtn'),
		cancelBtn = $('#cancelBtn');

	vdcodeImg.vdcodeChange();

	smtBtn.on('click',function(){
		editorPoint.sync();  
		articleForm.submit();
	})

	cancelBtn.on('click',function(){
		return confirm('你确定要取消吗？\r\n取消后编辑或修改的内容都会丢失，请先做好备份。')
	})

	articleForm.on('submit',function(){
		editorPoint.focus();
		if($.trim(title.val()) === ''){
			alert('请填写作文题目');
			title.focus();
			return false;
		}else if(!editorPoint.hasContents()){
			alert('请填写作文正文');
			return false;
		}else if($.trim(writer.val()) === ''){
			alert('请填写作者');
			writer.focus();
			return false;
		}else if($.trim(channel.val()) === '0'){
			alert('请选择一个分类');
			return false;
		}else if($.trim(vdcode.val()) === ''){
			alert('请填写验证码');
			vdcode.focus();
			return false;
		}


	})
})