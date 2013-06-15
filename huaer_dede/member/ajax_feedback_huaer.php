<?php
/**
 * @version        $Id: ajax_feedback.php 1 8:38 2010年7月9日Z tianya $
 * @package        DedeCMS.Member
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__).'/config.php');
AjaxHead();
if($myurl == '') exit('');

else
{
    $uid  = $cfg_ml->M_LoginID;
    $face = $cfg_ml->fields['face'] == '' ? $GLOBALS['cfg_memberurl'].'/images/nopic.gif' : $cfg_ml->fields['face'];
	
	echo '<div class="comment-body">
			<textarea class="ipt-text" name="msg" id="commentMsg"></textarea>
		</div>';
		
	if($cfg_feedback_ck == 'Y'){
		echo '<div class="comment-code pull-left">
			<label for="validate">验证码：</label>
			<input type="text" class="ipt-text" name="validate" id="validate" />
			<img id="validateImg" src="'.$cfg_cmspath.'/include/vdimgck.php?" onclick="this.src=\''.$cfg_cmspath.'/include/vdimgck.php\'+\'?_r=\'+Math.random()" />
		</div>';
	}
	
	echo '<div class="comment-smt pull-right">
			<a class="button button-save button-cmt clearfix" id="smtBtn" href="###">
				<span class="button-left">&nbsp;</span>
				<span class="button-text">提交</span>
				<span class="button-right">&nbsp;</span>
			</a>
		</div>';
}

