<?php
/**
 * @version        $Id: ajax_loginsta.php 1 8:38 2010年7月9日Z tianya $
 * @package        DedeCMS.Member
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");
AjaxHead();
if($myurl == '') exit('');

$uid  = $cfg_ml->M_LoginID;

$pms = $dsql->GetOne("SELECT COUNT(*) AS nums FROM #@__member_pms WHERE toid='{$cfg_ml->M_ID}' AND `hasview`=0 AND folder = 'inbox'");

?>


<li><a href="<?php echo $cfg_memberurl; ?>/pm.php">
	消息
	<?php 
		if($pms['nums'] > 0){
			echo '<span class="unread-message-num hide"><span class="left-border"></span><span class="num">'. $pms['nums'] .'</span><span class="right-border"></span></span>'; 
		} 
	?>
	</a>
</li>
<li class="sep"><span> </span></li>
<li><a href="<?php echo $cfg_memberurl; ?>/edit_baseinfo.php">设置</a></li>
<li class="sep"><span></span></li>
<li><a href="<?php echo $cfg_memberurl; ?>/index_do.php?fmdo=login&dopost=exit">退出</a></li>
