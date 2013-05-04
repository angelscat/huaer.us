<?php
/**
 * 浏览记录
 * 
 * @version        $Id: visit-history.php 1 8:38 2010年7月9日Z tianya $
 * @package        DedeCMS.Member
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");
CheckRank(0,0);
$menutype = 'mydede';
if(!isset($dopost)) $dopost = '';

require_once(DEDEINC."/datalistcp.class.php");
require_once(DEDEINC.'/enums.func.php');
require_once(DEDEDATA.'/enums/nativeplace.php');
$wsql = '';
if($dopost=='meview')
{
    $wsql = " v.vid='{$cfg_ml->M_ID}' ";
    $tname = "我最近访问";
    $osql="ON v.mid=m.mid ";
}
else
{
    $wsql = " v.mid='{$cfg_ml->M_ID}' ";
    $tname = "关注我的人";
    $osql="ON v.vid=m.mid ";
}

$count = $dsql->GetOne("SELECT COUNT(v.id) AS num FROM #@__member_vhistory AS v WHERE $wsql");

$query = "SELECT v.*,m.sex,m.face,m.uname,YEAR(CURDATE())-YEAR(mp.birthday) as age,DATE_FORMAT(mp.birthday,'%c月%d日出生') as birthday
FROM `#@__member_vhistory` AS v  
LEFT JOIN `#@__member` AS m $osql 
LEFT JOIN `#@__member_person` AS mp ON mp.mid=m.mid
WHERE $wsql ORDER BY vtime DESC";
$dlist = new DataListCP();
$dlist->pageSize = 20;
$dlist->SetParameter("dopost",$dopost);
$dlist->SetTemplate(DEDEMEMBER.'/templets/visit-history.htm');
$dlist->SetSource($query);
$dlist->Display();