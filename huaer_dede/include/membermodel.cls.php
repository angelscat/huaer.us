<?php   if(!defined('DEDEINC')) exit('forbidden');
/**
 * 会员自定义模块
 *
 * @version        $Id: oxwindow.class.php 1 15:21 2010年7月5日Z tianya $
 * @package        DedeCMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once DEDEINC.'/dedetag.class.php';
require_once DEDEINC.'/customfields.func.php';
require_once DEDEINC.'/enums.func.php';

/**
 * 会员自定义模块
 *
 * @package          membermodel
 * @subpackage       DedeCMS.Libraries
 * @link             http://www.dedecms.com
 */
class membermodel
{
    var $modid;
    var $db;
    var $info;
    var $name;
    var $table;
    var $public;
    var $egroups;
    var $listTemplate;
    var $viewTemplate;
    var $postTemplate;

    //兼容PHP4版本
    function membermodel($modtype){
        $this->__construct($modtype);
    }
    
    //析构函数
    function __construct($modtype){
        $this->name = $modtype;
        $this->db = $GLOBALS['dsql'];
        $query = "SELECT * FROM #@__member_model WHERE name='{$modtype}'";
        $diyinfo = $this->db->getone($query);
        if(!is_array($diyinfo))
        {
            showMsg('参数不正确，该会员模型不存在','javascript:;');
            exit();
        }
    $etypes = array();
    $egroups = array();
    $this->db->Execute('me','SELECT * FROM `#@__stepselect` ORDER BY id desc');
    while($arr = $this->db->GetArray())
    {
       $etypes[] = $arr;
       $egroups[$arr['egroup']] = $arr['itemname'];
    }
    $this->egroups = $egroups;
        $this->modid = $diyinfo['id'];
        $this->table = $diyinfo['table'];
        $this->description = $diyinfo['description'];
        $this->state = $diyinfo['state'];
        $this->issystem = $diyinfo['issystem'];
        $this->info = $diyinfo['info'];
    }//end func __construct()

   /**
    *  获取用户数据表单
    *
    * @access    public
    * @param     string  $type  表单类型
    * @param     string  $value  值
    * @param     string  $admintype  模型类型
    * @return    string
    */
    function getForm($type = 'post', $value = '', $admintype='membermodel2')
    {
        global $cfg_cookie_encode;
        $dtp = new DedeTagParse();
        $dtp->SetNameSpace("field","<",">");
        $dtp->LoadSource($this->info);
        $formstring = '';
        $formfields = '';
        $func = $type == 'post' ? 'GetFormItem' : 'GetFormItemValue';

        if(is_array($dtp->CTags))
        {
            foreach($dtp->CTags as $tagid=>$tag)
            {
                if($tag->GetAtt('autofield'))
                {
                    if($tag->GetAtt('state') == 1)
                    {
                        //如果启用该字段
                        if($type == 'post')
                        {
                            //对一些字段进行特殊处理
                            if($tag->GetName() == 'onlynet')
                            {
                                $formstring .= '<li><span>联系方式限制：</span><div class="lform">
                    <input name="onlynet" type="radio" id="onlynet" value="2" checked="checked" />
                    不公开所有联系方式
                    <input name="onlynet" type="radio" id="onlynet" value="1" />
                    不公开电话、详细地址
                    <input name="onlynet" type="radio" id="onlynet" value="0"  />
                    公开所有联系方式</div></li>';
                            } else if ($tag->GetName() == 'place' || $tag->GetName() == 'oldplace')
                            {
                                $formtitle = ($tag->GetName() == 'place')? '所在地' : '家乡所在地';
                                $formstring .='<li><div class="lform">' . GetEnumsForm('nativeplace', 
                                       0,$tag->GetName()).'</div><span>'.$formtitle.'：</span></li>';
                            } else if (array_key_exists($tag->GetName(),$this->egroups))
                            {
                                //对联动模型进行特殊处理
                                $formstring .='<li><div class="lform">'. GetEnumsForm($tag->GetName(),
                                0,$tag->GetName()).'</div><span>'.$this->egroups[$tag->GetName()].'：</span></li>';
                            } else if ($tag->GetAtt('type') == 'checkbox')
                            {
                                //对checkbox模型进行特殊处理
                                $formstring .=$func($tag,$admintype);
                            } else {
                                  $formstring .= $func($tag,$admintype);
                            }
                        } else {
                            if($tag->GetName() == 'onlynet'){
                                $formstring .= '
                                    <div class="field sex hide">
                                        <div class="label">
                                            <label>隐私设置：</label>
                                        </div>
                                        <div class="ipt">
                                            <input type="radio" name="onlynet" value="2" id="onlynet2" '.($value['onlynet']=='2' ? 'checked="checked"' : '').' /><label for="onlynet2"> 不公开所有联系方式</label>&nbsp;&nbsp;&nbsp;
                                            <input type="radio" name="onlynet" value="1" id="onlynet1" '.($value['onlynet']=='1' ? 'checked="checked"' : '').' /><label for="onlynet1"> 不公开电话、详细地址</label>&nbsp;&nbsp;&nbsp;
                                            <input type="radio" name="onlynet" value="0" id="onlynet0" '.($value['onlynet']=='0' ? 'checked="checked"' : '').' /><label for="onlynet0"> 公开所有联系方式</label>
                                        </div>
                                        <div class="tips"></div>
                                    </div>
                                ';
                            }else if($tag->GetName() == 'place' || $tag->GetName() == 'oldplace'){
                                $formtitle = ($tag->GetName() == 'place')? '所在地' : '家乡';
                                $topDatas = getTopData('nativeplace');
                                $topDataStr = '<select class="province">';
                                foreach ($topDatas as $k => $v) {
                                  $topDataStr .= '<option value="'.$k.'">'.$v.'</option>';
                                }
                                $topDataStr .='</select><select class="city" name="'. $tag->GetName() .'"></select>';
                                $formstring .='
                                    <div class="field place place-choose" data-id="'. $value[$tag->GetName()] .'">
                                        <div class="label">
                                            <label>'. $formtitle .'：</label>
                                        </div>
                                        <div class="ipt">
                                            '. $topDataStr .'
                                        </div>
                                        <div class="tips"></div>
                                    </div>
                                ';
                            }else if($tag->GetName() == 'birthday'){
                                $formstring .= '
                                    <div class="field bir">
                                        <div class="label">
                                            <label for="birthday">生日：</label>
                                        </div>
                                        <div class="ipt">
                                            <input type="text" class="ipt-text" id="birthday" name="birthday" value="'. $value[$tag->GetName()] .'" onClick="WdatePicker()" />
                                        </div>
                                        <div class="tips"></div>
                                    </div>
                                ';
                            }else if($tag->GetName() == 'sex'){
                                $formstring .= '
                                    <div class="field sex">
                                        <div class="label">
                                            <label>性别：</label>
                                        </div>
                                        <div class="ipt">
                                            <input type="radio" name="sex" value="男" id="sex1" '. ($value['sex']=='男' ? 'checked="checked"' : '') .' /><label for="sex1"> 男</label>&nbsp;&nbsp;&nbsp;
                                            <input type="radio" name="sex" value="女" id="sex2" '. ($value['sex']=='女' ? 'checked="checked"' : '') .' /><label for="sex2"> 女</label>&nbsp;&nbsp;&nbsp;
                                            <input type="radio" name="sex" value="保密" id="sex3" '. ($value['sex']=='保密' ? 'checked="checked"' : '') .' /><label for="sex3"> 保密</label>
                                        </div>
                                        <div class="tips"></div>
                                    </div>
                                ';
                            }else{
                                $formstring .= $func($tag,htmlspecialchars($value[$tag->GetName()],ENT_QUOTES),$admintype);
                            }

                             // if($tag->GetName() == 'onlynet')
                             //    {
                             //        $formstring .= '<p><label>联系方式限制：</label>
                             //                        <input name="onlynet" type="radio" id="onlynet" value="2" checked="checked" />
                             //                        不公开所有联系方式
                             //                        <input name="onlynet" type="radio" id="onlynet" value="1" />
                             //                        不公开电话、详细地址
                             //                        <input name="onlynet" type="radio" id="onlynet" value="0"  />
                             //                        公开所有联系方式</p>';
                             //    } else if ($tag->GetName() == 'place' || $tag->GetName() == 'oldplace'){
                             //        $formtitle = ($tag->GetName() == 'place')? '目前所在地' : '家乡所在地';
                             //        $formstring .='<p><label>'.$formtitle.'：</label>' . GetEnumsForm('nativeplace',$value[$tag->GetName()],$tag->GetName()).'</p>';
                             //    } else if ($tag->GetName() == 'birthday'){
                             //        $formstring .='<p><label>'.$tag->GetAtt('itemname').'：</label><input type="text" class="intxt" style="width: 100px;" id="birthday" value="'.$value[$tag->GetName()].'" name="birthday"></p>';
                             //    } else if (array_key_exists($tag->GetName(),$this->egroups)){
                             //        //对联动模型进行特殊处理
                             //        $formstring .='<p><label>'.$this->egroups[$tag->GetName()].'：</label> '. GetEnumsForm($tag->GetName(),$value[$tag->GetName()],$tag->GetName()).'</p>';
                             //    } else if ($tag->GetAtt('type') == 'checkbox'){
                             //        //对checkbox模型进行特殊处理
                             //        $formstring .=$func($tag,htmlspecialchars($value[$tag->GetName()],ENT_QUOTES),$admintype);
                             //    }
                             //      else if ($tag->GetAtt('type') == 'img')
                             //    {
                             //        $fieldname = $tag->GetName();
                             //        $labelname = $tag->GetAtt('itemname');
                             //        $fvalue = htmlspecialchars($value[$tag->GetName()],ENT_QUOTES);
                             //        $imgstrng = "<p><label>{$labelname}：</label><input type='text' name='$fieldname' value='$fvalue' id='$fieldname' style='width:300px'  class='text' /> <input name='".$fieldname."_bt' class='inputbut' type='button' value='浏览...' onClick=\"SelectImage('addcontent.$fieldname','big')\" />\r\n</p>";
                             //        $formstring .=$imgstrng;
                                    
                             //    }
                             //    else {
                             //      $formstring .= $func($tag,htmlspecialchars($value[$tag->GetName()],ENT_QUOTES),$admintype);
                             //      //echo $formstring;
                             //  }                        
                        }
                        $formfields .= $formfields == '' ? $tag->GetName().','.$tag->GetAtt('type') : ';'.$tag->GetName().','.$tag->GetAtt('type');
                    }
                }
            }
        }
        $formstring .= "<input type=\"hidden\" name=\"dede_fields\" value=\"".$formfields."\" />\n";
        $formstring .= "<input type=\"hidden\" name=\"dede_fieldshash\" value=\"".md5($formfields.$cfg_cookie_encode)."\" />";
        return $formstring;
    }//end func getForm

    /**
     *  获取字段列表
     *
     * @access    public
     * @param     string
     * @return    array
     */
    function getFieldList()
    {
        $dtp = new DedeTagParse();
        $dtp->SetNameSpace("field","<",">");
        $dtp->LoadSource($this->info);
        $fields = array();
        if(is_array($dtp->CTags))
        {
            foreach($dtp->CTags as $tagid=>$tag)
            {
                $fields[$tag->GetName()] = array($tag->GetAtt('itemname'), $tag->GetAtt('type'));
            }
        }
        return $fields;
    }
}