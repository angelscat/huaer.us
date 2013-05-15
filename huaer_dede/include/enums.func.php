<?php   if(!defined('DEDEINC')) exit("Request Error!");
/**
 * 联动菜单类
 *
 * @version        $Id: enums.func.php 2 13:19 2011-3-24 tianya $
 * @package        DedeCMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */

// 弱不存在缓存文件则写入缓存
if(!file_exists(DEDEDATA.'/enums/system.php')) WriteEnumsCache();

/**
 *  更新枚举缓存
 *
 * @access    public
 * @param     string  $egroup  联动组
 * @return    string
 */
function WriteEnumsCache($egroup='')
{
    global $dsql;
    $egroups = array();
    if($egroup=='') {
        $dsql->SetQuery("SELECT egroup FROM `#@__sys_enum` GROUP BY egroup ");
    }
    else {
        $dsql->SetQuery("SELECT egroup FROM `#@__sys_enum` WHERE egroup='$egroup' GROUP BY egroup ");
    }
    $dsql->Execute('enum');
    while($nrow = $dsql->GetArray('enum')) {
        $egroups[] = $nrow['egroup'];
    }
    foreach($egroups as $egroup)
    {
        $cachefile = DEDEDATA.'/enums/'.$egroup.'.php';
        $fp = fopen($cachefile,'w');
        fwrite($fp,'<'."?php\r\nglobal \$em_{$egroup}s;\r\n\$em_{$egroup}s = array();\r\n");
        $dsql->SetQuery("SELECT ename,evalue,issign FROM `#@__sys_enum` WHERE egroup='$egroup' ORDER BY disorder ASC, evalue ASC ");
        $dsql->Execute('enum');
        $issign = -1;
        $tenum = false; //三级联动标识
        while($nrow = $dsql->GetArray('enum'))
        {
            fwrite($fp,"\$em_{$egroup}s['{$nrow['evalue']}'] = '{$nrow['ename']}';\r\n");
            if($issign==-1) $issign = $nrow['issign'];
            if($nrow['issign']==2) $tenum = true;
        }
        if ($tenum) $dsql->ExecuteNoneQuery("UPDATE `#@__stepselect` SET `issign`=2 WHERE egroup='$egroup'; ");
        fwrite($fp,'?'.'>');
        fclose($fp);
        if(empty($issign)) WriteEnumsJs($egroup);
    }

    WriteCatalogDataJs();
    return '成功更新所有枚举缓存！';
}

/**
 *  获取联动表单两级数据的父类与子类
 *
 * @access    public
 * @param     string  $v
 * @return    array
 */
function GetEnumsTypes($v)
{
    $rearr['top'] = $rearr['son'] = 0;
    if($v==0) return $rearr;
    if($v%500==0) {
        $rearr['top'] = $v;
    }
    else {
        $rearr['son'] = $v;
        $rearr['top'] = $v - ($v%500);
    }
    return $rearr;
}

/**
 *  获取枚举的select表单
 *
 * @access    public
 * @param     string  $egroup  联动组
 * @param     string  $evalue  联动值
 * @param     string  $formid  表单ID
 * @param     string  $seltitle  选择标题
 * @return    string  成功后返回一个枚举表单
 */
function GetEnumsForm($egroup, $evalue=0, $formid='', $seltitle='')
{
    $cachefile = DEDEDATA.'/enums/'.$egroup.'.php';
    include($cachefile);
    if($formid=='')
    {
        $formid = $egroup;
    }
    $forms = "<select name='$formid' id='$formid' class='enumselect'>\r\n";
    $forms .= "\t<option value='0' selected='selected'>--请选择--{$seltitle}</option>\r\n";
    foreach(${'em_'.$egroup.'s'} as $v=>$n)
    {
        $prefix = ($v > 500 && $v%500 != 0) ? '└─ ' : '';
        if (preg_match("#\.#", $v)) $prefix = ' &nbsp;&nbsp;└── ';

        if($v==$evalue)
        {
            $forms .= "\t<option value='$v' selected='selected'>$prefix$n</option>\r\n";
        }
        else
        {
            $forms .= "\t<option value='$v'>$prefix$n</option>\r\n";
        }
    }
    $forms .= "</select>";
    return $forms;
}

/**
 *  获取一级数据
 *
 * @access    public
 * @param     string    $egroup   联动组
 * @return    array
 */
function getTopData($egroup)
{
    $data = array();
    $cachefile = DEDEDATA.'/enums/'.$egroup.'.php';
    include($cachefile);
    foreach(${'em_'.$egroup.'s'} as $k=>$v)
    {
        if($k >= 500 && $k%500 == 0) {
            $data[$k] = $v;
        }
    }
    return $data;
}


/**
 *  获取数据的JS代码(二级联动)
 *
 * @access    public
 * @param     string    $egroup   联动组
 * @return    string
 */
function GetEnumsJs($egroup)
{
    global ${'em_'.$egroup.'s'};
    include_once(DEDEDATA.'/enums/'.$egroup.'.php');
    $jsCode = "<!--\r\n";
    $jsCode .= "em_{$egroup}s=new Array();\r\n";
    foreach(${'em_'.$egroup.'s'} as $k => $v)
    {
        // JS中将3级类目存放到第二个key中去
        if (preg_match("#([0-9]{1,})\.([0-9]{1,})#", $k, $matchs))
        {
            $valKey = $matchs[1] + $matchs[2] / 1000;
            $jsCode .= "em_{$egroup}s[{$valKey}]='$v';\r\n";
        } else { 
            $jsCode .= "em_{$egroup}s[$k]='$v';\r\n";
        }
    }
    $jsCode .= "-->";
    return $jsCode;
}

/**
 *  写入联动JS代码
 *
 * @access    public
 * @param     string    $egroup   联动组
 * @return    string
 */
function WriteEnumsJs($egroup)
{
    $jsfile = DEDEDATA.'/enums/'.$egroup.'.js';
    $fp = fopen($jsfile, 'w');
    fwrite($fp, GetEnumsJs($egroup));
    fclose($fp);
}


/**
 *  获取枚举的值
 *
 * @access    public
 * @param     string    $egroup   联动组
 * @param     string    $evalue   联动值
 * @return    string
 */
function GetEnumsValue($egroup, $evalue=0)
{
    include_once(DEDEDATA.'/enums/'.$egroup.'.php');
    if(isset(${'em_'.$egroup.'s'}[$evalue])) {
        return ${'em_'.$egroup.'s'}[$evalue];
    }
    else {
        return "保密";
    }
}


function WriteCatalogDataJs(){
    $jsfile = DEDEDATA.'/js/catalogdata.js';
    $fp = fopen($jsfile, 'w');
    fwrite($fp, GetCatalogDataJs());
    fclose($fp);
}
function GetCatalogDataJs(){

    global $OptionArrayList, $channels, $dsql, $cfg_admin_channel, $admin_catalogs;

    $OptionArrayList = '';
    $channeltype = 1;

    //是否限定用户管理的栏目
    if( $cfg_admin_channel=='array' )
    { 
        if(count($admin_catalogs)==0)
        {
            $query = "SELECT id,typename,ispart,channeltype FROM `#@__arctype` WHERE 1=2 ";
        }
        else
        {
            $admin_catalog = join(',', $admin_catalogs);
            $dsql->SetQuery("SELECT reid FROM `#@__arctype` WHERE id IN($admin_catalog) GROUP BY reid ");
            $dsql->Execute();
            $topidstr = '';
            while($row = $dsql->GetObject())
            {
                if($row->reid==0) continue;
                $topidstr .= ($topidstr=='' ? $row->reid : ','.$row->reid);
            }
            $admin_catalog .= ','.$topidstr;
            $admin_catalogs = explode(',', $admin_catalog);
            $admin_catalogs = array_unique($admin_catalogs);
            $admin_catalog = join(',', $admin_catalogs);
            $admin_catalog = preg_replace("#,$#", '', $admin_catalog);
            $query = "SELECT id,typename,ispart,channeltype FROM `#@__arctype` WHERE id IN($admin_catalog) AND reid=0 AND ispart<>2 ";
        }
    }
    else
    {
        $query = "SELECT id,typename,ispart,channeltype FROM `#@__arctype` WHERE ispart<>2 AND reid=0 ORDER BY sortrank ASC ";
    }

    $dsql->SetQuery($query);
    $dsql->Execute();

    while($row=$dsql->GetObject())
    {
        // $sonCats = '';
        $sonCats = LogicGetOptionArray($row->id, '─', $channeltype, $dsql, $sonCats);
        if($sonCats != '')
        {
            if($row->ispart==1) $OptionArrayList .= "{id:". $row->id .",name:\"". $row->typename ."\",type:1,children:[".$sonCats."]},";//<option value='".$row->id."' class='option1'>".$row->typename."(封面频道)</option>
            else if($row->ispart==2) $OptionArrayList .= '';
            else if( empty($channeltype) && $row->ispart != 0 ) $OptionArrayList .= "{id:". $row->id .",name:\"".$row->typename."(".$channels[$row->channeltype]."\",type:2,children:[".$sonCats."]},";//<option value='".$row->id."' class='option2'>".$row->typename."(".$channels[$row->channeltype].")</option>
            else $OptionArrayList .= "{id:". $row->id .",name:\"". $row->typename ."\",type:1,children:[".$sonCats."]},";//<option value='".$row->id."' class='option3'>".$row->typename."</option>
            //$OptionArrayList .= $sonCats;
        }
        else
        {
            if($row->ispart==0 && (!empty($channeltype) && $row->channeltype == $channeltype) )
            {
                $OptionArrayList .= "{id:". $row->id .",name:\"". $row->typename ."\",type:3},";//<option value='".$row->id."' class='option3'>".$row->typename."</option>
            } else if($row->ispart==0 && empty($channeltype) )
            { 
                // 专题
                $OptionArrayList .= "{id:". $row->id .",name:\"". $row->typename ."\",type:3},";//<option value='".$row->id."' class='option3'>".$row->typename."</option>
            }
        }
    }
    $OptionArrayList = substr_replace($OptionArrayList,'',-1);
    return 'var catalogdata = ['.$OptionArrayList.']';
}

function LogicGetOptionArray($id,$step,$channeltype,&$dsql)
{
    global $OptionArrayList, $channels, $cfg_admin_channel, $admin_catalogs;
    $dsql->SetQuery("Select id,typename,ispart,channeltype From `#@__arctype` where reid='".$id."' And ispart<>2 order by sortrank asc");
    $dsql->Execute($id);
    $son = '';
    while($row=$dsql->GetObject($id))
    {   
        $sonCats = LogicGetOptionArray($row->id,$step.'─',$channeltype,$dsql);
        if($cfg_admin_channel != 'all' && !in_array($row->id, $admin_catalogs))
        {
            continue;
        }
        if($row->channeltype==$channeltype && $row->ispart==1)
        {
            $son .= "{id:". $row->id .",name:\"". $row->typename ."\",type:1,children:[".$sonCats."]},";//<option value='".$row->id."' class='option1'>$step".$row->typename."</option>
        }
        else if( ($row->channeltype==$channeltype && $row->ispart==0) || empty($channeltype) )
        {
            $son .= "{id:". $row->id .",name:\"". $row->typename ."\",type:3,children:[".$sonCats."]},";//<option value='".$row->id."' class='option3'>$step".$row->typename."</option>
        }
        
    }
    return substr_replace($son,'',-1);
}