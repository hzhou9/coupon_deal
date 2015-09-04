<?php

if( !$GLOBALS['me']->is_admin ) die;
    
    $csrf = $_SESSION['slider_csrf'] = \site\utils::str_random(10);

    $sendy_url = \query\main::get_option( 'sendy_url' );
    $sendy_api_key = \query\main::get_option( 'sendy_api_key' );
    $sendy_list_id = \query\main::get_option( 'sendy_list_id' );
    $sendy_brand_id = \query\main::get_option( 'sendy_brand_id' );
    $sendy_from_name = \query\main::get_option( 'sendy_from_name' );
    $sendy_from_email = \query\main::get_option( 'sendy_from_email' );
    $sendy_reply_to = \query\main::get_option( 'sendy_reply_to' );
    $sendy_query_string = \query\main::get_option( 'sendy_query_string' );
    $sendy_lastsync =  date("Y-m-d H:i:s", \query\main::get_option( 'sendy_last_sync' ) );
    $sendy_template_root = \query\main::get_option( 'sendy_template_root' );
?>
<style>
.anchor_button {background:rgba(0,0,0,.5); padding:8px 8px; margin:3px; border-radius:6px; color:white;}
</style>

<div id="templatectrl">
<select name="templatectrl_msel"></select>
<input name="templatectrl_load" type="button" value="Load">&nbsp;&nbsp;<input name="templatectrl_save" type="button" value="Preview">&nbsp;&nbsp;<input name="templatectrl_save2" type="button" value="Save">&nbsp;&nbsp;<input name="templatectrl_sync" type="button" value="Sync">[<?php echo $sendy_lastsync;?>]
<div name="campaignctrl_info" class="a-success" style="display:none;"></div>
<div name="campaignctrl" style="display:none;">
<span name="campaignctrl_close" class="anchor_button" style="position:absolute;right:0px;">&nbsp;X&nbsp;</span>
<div class="a-success">确认以下字段填写正确，然后再次点击Save按钮发送</div>
发信抬头：<input type="text" name="from_name" value="<?php echo $sendy_from_name;?>"/><br>
发信邮箱：<input type="text" name="from_email" value="<?php echo $sendy_from_email;?>"/><br>
回复邮箱：<input type="text" name="reply_to" value="<?php echo $sendy_reply_to;?>"/><br>
邮件标题：<input type="text" name="subject" value=""/><br>
附加字段：<input type="text" name="query_string" value="<?php echo $sendy_query_string;?>"/><br>
<input type="checkbox" name="send_campaign"/>直接开始群发
<div class="a-error" name="alert_send_campaign" style="display:none;">注意：（每天）群发前至少需要将用户的个性化信息Sync到Newsletter服务器一次，如果还没有就请先点击Sync按钮</div>
</div>
</div>

<div id="pagecontainer" style="width:100%;">
</div>

<div id="modulectrl" style="z-index: 99; position: fixed; top: 50%; left: 50%; margin-top: -150px; margin-left: -160px;background: rgb(0,0,0);width: 320px;padding: 10px;display:none;">
<select name="modulectrl_msel"></select>
<input type="button" value="Add" name="modulectrl_add">&nbsp;&nbsp;<input type="button" value="Cancel" name="modulectrl_cancel"><hr>
<span style="color:white;">自动获取数据：</span>
<select name="modulectrl_type_msel">
<option value="deal">折扣id</option>
<option value="sale">特卖id</option>
</select>
<input type="text" name="modulectrl_itemid" value="0" style="width:50px;">
</div>

<div id="inputctrl" style="z-index: 99; position: fixed; top: 50%; left: 50%; margin-top: -150px; margin-left: -160px;background: rgb(0,0,0);width: 320px;padding: 10px;display:none;">
<div name="inputctrl_textarea_div">
<span style="color:white;">文字：</span>
<textarea rows="3" name="inputctrl_textarea" style="width: 92%;">
</textarea>
</div>
<div name="inputctrl_text_div">
<span style="color:white;">文字：</span><input type="text" name="inputctrl_text" style="width: 92%;">
</div>
<div name="inputctrl_link_div">
<span style="color:white;">文字：</span>
<textarea rows="3" name="inputctrl_textarea" style="width: 92%;">
</textarea><br>
<span style="color:white;">链接：</span><input type="text" name="inputctrl_link" style="width: 92%;">
</div>
<div name="inputctrl_image_div">
<span style="color:white;">图像：</span><input type="text" name="inputctrl_src" style="width: 92%;"><br>
<span style="color:white;">链接：</span><input type="text" name="inputctrl_link" style="width: 92%;">
</div>

<input type="button" value="Set" name="inputctrl_set">&nbsp;&nbsp;<input type="button" value="Cancel" name="inputctrl_cancel">
</div>

<iframe id="newsletter_sync_ret" style="position:fixed;left:0px;top:0px;width:100%;height:100%;z-index:99;display:none;">
</iframe>

<script>
var root = '<?php echo $sendy_template_root;?>';
var templates = {
template1:{
name:'RetailMeNot Style 1',
folder:'template1',
file:'_retailmenot.html',
edit:{'[name=newsletter_hot_title] td':'text','[name=newsletter_hot_desc] td':'textarea','[name=newsletter_hot_end] span':'text','[name=newsletter_hot_end] a':'link'},
anchor:{'[name=newsletter_hot_desc]':['add']},
custom:{
nofav:{file:'_retailmenot_nofav.html'},
myfav:{file:'_retailmenot_fav.html',anchor:'<!--FAV_ITEMS-->',deal:'_retailmenot_favitem.html',sale:'_retailmenot_favsale.html'},
},
modules:{
m1:{name:'Image Banner',
    file:'_retailmenot1.html',
    edit:{'[name=banner_image]':'image'},
    mapping:[{'image':['[name=banner_image] img'],'link':['[name=banner_image]']}],
    anchor:{'root':['add','del']}},
m2:{name:'GetDeal Banner',
    file:'_retailmenot2.html',
    edit:{'[name=banner_image]':'image','[name=banner_link]':'link','[name=banner_text]':'text','[name=banner_link2]':'link'},
    mapping:[{'image':['[name=banner_image] img'],'link':['[name=banner_image]','[name=banner_link]','[name=banner_link2]'],'expiration':['[name=banner_text]'],'title':['[name=banner_link]']}],
    anchor:{'root':['add','del']}},
m3:{name:'Hot Deal',
    file:'_retailmenot3.html',
    edit:{'[name=banner_image]':'image','[name=banner_link]':'link','[name=banner_text]':'text','[name=banner_image2]':'image','[name=banner_price1]':'text','[name=banner_price2]':'text'},
    mapping:[{'image':['[name=banner_image] img'],'link':['[name=banner_image]','[name=banner_link]','[name=banner_image2]'],'title':['[name=banner_link]'],'price':['[name=banner_price2]'],'old_price':['[name=banner_price1]']}],
    anchor:{'root':['add','del']}},
m4:{name:'横条信息块',
    file:'_retailmenot4.html',
    edit:{'[name=banner_image]':'image','[name=banner_link]':'link','[name=banner_text]':'text','[name=banner_image2]':'image','[name=banner_text2]':'text','[name=banner_link2]':'link'},
    mapping:[{'image':['[name=banner_image] img'],'link':['[name=banner_image]','[name=banner_link2]','[name=banner_image2]'],'title':['[name=banner_link2]'],'expiration':['[name=banner_text2]'],'name':['[name=banner_link]'],'s_link':['[name=banner_link]']}],
    anchor:{'root':['add','del']}},
m5:{name:'两个方形信息块',
    file:'_retailmenot5.html',
    edit:{'[name=banner_image]':'image','[name=banner_link]':'link','[name=banner_link2]':'link','[name=banner_link3]':'link','[name=banner_text]':'text','[name=banner_image_2]':'image','[name=banner_text_2]':'text','[name=banner_link_2]':'link','[name=banner_link2_2]':'link','[name=banner_link3_2]':'link'},
    mapping:[{'image':['[name=banner_image] img'],'link':['[name=banner_image]','[name=banner_link2]','[name=banner_link3]'],'title':['[name=banner_link2]'],'expiration':['[name=banner_text]'],'name':['[name=banner_link]'],'s_link':['[name=banner_link]']},{'image':['[name=banner_image_2] img'],'link':['[name=banner_image_2]','[name=banner_link2_2]','[name=banner_link3_2]'],'title':['[name=banner_link2_2]'],'expiration':['[name=banner_text_2]'],'name':['[name=banner_link_2]'],'s_link':['[name=banner_link_2]']}],
    anchor:{'root':['add','del']}}
}
}
};

function initTemplates(){
    var templatesel = $('[name=templatectrl_msel]');
    templatesel.html('');
    $.each(templates,function(kt,vt){
           templatesel.append("<option value='"+kt+"'>"+vt.name+"</option>");
           });
    $('[name=templatectrl_load]').click(function(){loadTemplate();});
    $('[name=templatectrl_save]').click(function(){
                                        var newWindow = window.open('');
                                        if (newWindow){
                                        //newWindow.onload=function(){
                                        var domdata = $('#pagecontainer').clone();
                                        $.each(domdata.find('._anchordiv'),function(k,v){
                                               v.remove();
                                        });
                                        $(newWindow.document.body).append(domdata);
                                        //};
                                        }
    });
    $('[name=inputctrl_cancel]').click(function(){$('#inputctrl').hide();});
    $('[name=modulectrl_cancel]').click(function(){$('#modulectrl').hide();});
    $('[name=send_campaign]').click(function(){$('[name=alert_send_campaign]').toggle();});
    $('[name=templatectrl_save2]').click(function(){
        if($('[name=campaignctrl]').is(":visible")){
            var domdata = $('#pagecontainer').clone();
            $.each(domdata.find('._anchordiv'),function(k,v){
                v.remove();
            });
            var html_text = domdata.html().replace('<!--[NOFAV,fallback=]-->','[NOFAV,fallback=]').replace('<!--[MYFAV,fallback=]-->','[MYFAV,fallback=]');
            var api_key = "<?php echo $sendy_api_key;?>";
            var from_name = $('[name=from_name]').val();
            var from_email = $('[name=from_email]').val();
            var reply_to = $('[name=reply_to]').val();
            var subject = $('[name=subject]').val();
            var list_ids = "<?php echo $sendy_list_id;?>";
            var brand_id = <?php echo $sendy_brand_id;?>;
            var query_string = $('[name=query_string]').val();
            var send_campaign = $('[name=send_campaign]').is(':checked')?1:0;
            createCampaign(api_key,from_name,from_email,reply_to,subject,html_text,list_ids,brand_id,query_string,send_campaign,function(result){
                        console.log(result);
                           var newWindow = window.open('');
                           if (newWindow){
                           //newWindow.onload=function(){
                           $(newWindow.document.body).html('SUCCESS<br>'+result);
                           //};
                           }
                        $('[name=campaignctrl_info]').hide();
                           clearInterval(window.waitHanlder);
                    },function(msg, request){
                        console.log(msg);
                        alert('ERROR: '+request.status);
                        $('[name=campaignctrl_info]').hide();
                           clearInterval(window.waitHanlder);
            });
            $('[name=campaignctrl_info]').show();
            $('[name=campaignctrl_info]').html('开始发送，请勿关闭...');
            window.waitHanlder = setInterval(function(){$('[name=campaignctrl_info]').append('.');},1000);
        }else{
            $('[name=campaignctrl]').show();
        }
    });
    $('[name=campaignctrl_close]').click(function(){$('[name=campaignctrl]').hide();});
    $('[name=templatectrl_sync]').click(dosyncFav);
    loadTemplate();
}

function loadTemplate(){
    var container = $('#pagecontainer');
    container.html('');
    var selvalue = $('[name=templatectrl_msel]').val();
    var template = templates[selvalue];
    var modulesel = $('[name=modulectrl_msel]');
    modulesel.html('');
    $.each(template.modules,function(km,vm){
           modulesel.append("<option value='"+km+"'>"+vm.name+"</option>");
           });
    if(template.view){
        var view = prepareView(template.view.clone(),template.edit);
        container.append(view);
        prepareAnchor(view, template.anchor);
    }else{
        var htmlurl = root + template.folder +'/'+template.file;
        $.get(htmlurl , function(html){
              template.view = $(html);
              var view = prepareView(template.view.clone(),template.edit);
              container.append(view);
              prepareAnchor(view, template.anchor);
              });
    }
}

function loadModule(anchordiv,data){
    var selvalue = $('[name=templatectrl_msel]').val();
    var template = templates[selvalue];
    var mselval = $('[name=modulectrl_msel]').val();
    var module = template.modules[mselval];
    
    if(module.view){
        var view = prepareView(module.view.clone(),module.edit,module.mapping,data);
        view.insertAfter(anchordiv);
        prepareAnchor(view, module.anchor);
    }else{
        var htmlurl = root + template.folder +'/'+module.file;
        $.get(htmlurl , function(html){
              module.view = $(html);
              var view = prepareView(module.view.clone(),module.edit,module.mapping,data);
              view.insertAfter(anchordiv);
              prepareAnchor(view, module.anchor);
              });
    }
}

function prepareView(view, edit, mapping, data){
    if(mapping && data){
        for(var i=0; i<mapping.length && i<data.length; i++){
            $.each(data[i], function(kd,vd){
                if(kd in mapping[i]){
                   for(var j=0; j<mapping[i][kd].length; j++){
                   
                   if(kd == 'image'){
                   view.find(mapping[i][kd][j]).attr('src',vd);
                   }else if(kd == 'link' || kd == 's_link'){
                   view.find(mapping[i][kd][j]).attr('href',vd);
                   }else{
                   view.find(mapping[i][kd][j]).html(vd);
                   }
                   
                   }
                }
            });
        }
    }
    if(edit){
        $.each(edit, function(ke,ve){
               var obj = view.find(ke);
               obj.click(function(){
                         $('#inputctrl > div').hide();
                         if(ve == 'text'){
                         var text = obj.text();
                         $('#inputctrl [name=inputctrl_text_div]').show();
                         $('#inputctrl [name=inputctrl_text_div] [name=inputctrl_text]').val(text.trim());
                         $('[name=inputctrl_set]').unbind().click(function(){
                                                                  var text = $('#inputctrl [name=inputctrl_text_div] [name=inputctrl_text]').val();
                                                                  obj.text(text);
                                                                  $('#inputctrl').hide();
                                                                  });
                         }else if(ve == 'textarea'){
                         var text = obj.html();
                         $('#inputctrl [name=inputctrl_textarea_div]').show();
                         $('#inputctrl [name=inputctrl_textarea_div] [name=inputctrl_textarea]').val(text.trim());
                         $('[name=inputctrl_set]').unbind().click(function(){
                                                                  var text = $('#inputctrl [name=inputctrl_textarea_div] [name=inputctrl_textarea]').val();
                                                                  obj.html(text);
                                                                  $('#inputctrl').hide();
                                                                  });
                         }else if(ve == 'link'){
                         var text = obj.html();
                         var link = obj.attr('href');
                         $('#inputctrl [name=inputctrl_link_div]').show();
                         $('#inputctrl [name=inputctrl_link_div] [name=inputctrl_textarea]').val(text.trim());
                         $('#inputctrl [name=inputctrl_link_div] [name=inputctrl_link]').val(link.trim());
                         $('[name=inputctrl_set]').unbind().click(function(){
                                                                  var text = $('#inputctrl [name=inputctrl_link_div] [name=inputctrl_textarea]').val();
                                                                  var link = $('#inputctrl [name=inputctrl_link_div] [name=inputctrl_link]').val();
                                                                  obj.html(text);
                                                                  obj.attr('href',link);
                                                                  $('#inputctrl').hide();
                                                                  });
                         }else if(ve == 'image'){
                         var img = obj.find('img').attr('src');
                         var link = obj.attr('href');
                         $('#inputctrl [name=inputctrl_image_div]').show();
                         $('#inputctrl [name=inputctrl_image_div] [name=inputctrl_src]').val(img.trim());
                         $('#inputctrl [name=inputctrl_image_div] [name=inputctrl_link]').val(link.trim());
                         $('[name=inputctrl_set]').unbind().click(function(){
                                                                  var img = $('#inputctrl [name=inputctrl_image_div] [name=inputctrl_src]').val();
                                                                  var link = $('#inputctrl [name=inputctrl_image_div] [name=inputctrl_link]').val();
                                                                  obj.find('img').attr('src',img);
                                                                  obj.attr('href',link);
                                                                  $('#inputctrl').hide();
                                                                  });
                         }
                         $('#inputctrl').show();
                         return false;
                         });
               });
    }
    return view;
}

function prepareAnchor(view, anchor){
    if(anchor){
        $.each(anchor, function(ka,va){
               var anchorpoint = (ka == 'root'?view:view.find(ka));
               var anchordiv = $('<div class="_anchordiv" style="position:absolute;"></div>');
               $.each(va, function(ka2,va2){
                      var viewa = null;
                      if(va2 == 'add'){
                      viewa = $('<span class="anchor_button">&nbsp;+&nbsp;</span>');
                      viewa.click(function(){
                                  $('#modulectrl [name=modulectrl_add]').unbind().click(function(){
                                    var itemid = $('#modulectrl [name=modulectrl_itemid]').val();
                                    var typesel = $('#modulectrl [name=modulectrl_type_msel]').val();
                                    if(itemid != '' && itemid != '0'){
                                        var params = {action:"get_"+typesel,csrf:"<?php echo $csrf;?>",id:itemid};
                                        var url = '../_tools.php';
                                        $.ajax({
                                            url:url,
                                            type:'GET',
                                            data:params,
                                            success:function(result){
                                               if(!result || result == ''){
                                               alert('无法找到数据');
                                               loadModule(anchordiv);
                                               }else{
                                               loadModule(anchordiv,JSON.parse(result));
                                               }
                                            },
                                            error:function(msg){alert('加载数据出现异常');loadModule(anchordiv);}
                                        });
                                    }else{
                                        loadModule(anchordiv);
                                    }
                                    $('#modulectrl').hide();
                                  });
                                  $('#modulectrl').show();
                                  });
                      }else if(va2 == 'del'){
                      viewa = $('<span class="anchor_button">&nbsp;X&nbsp;</span>');
                      viewa.click(function(){
                                  view.unbind().remove();
                                  anchordiv.unbind().remove();
                                  });
                      
                      }
                      if(viewa){
                      anchordiv.append(viewa);
                      }
                      });
               anchordiv.insertAfter(anchorpoint);
               });
    }
}

function syncFav(nofav,myfav,email,listid,on_success,on_error){
    var data = {email:email,
        list:listid,
    NOFAV:nofav,
    MYFAV:myfav,
        _update_custom_fields:1};
    var url = '<?php echo $sendy_url;?>subscribe';
    $.ajax({
           url:url,
           type:'POST',
           data:data,
           success:on_success,
           error:on_error
           });
}

function createCampaign(api_key,from_name,from_email,reply_to,subject,html_text,list_ids,brand_id,query_string,send_campaign,on_success,on_error){
    var data = {api_key:api_key,
    from_name:from_name,
    from_email:from_email,
    reply_to:reply_to,
    subject:subject,
    html_text:html_text,
    list_ids:list_ids,
    brand_id:brand_id,
    query_string:query_string,
        send_campaign:send_campaign};
    var url = '<?php echo $sendy_url;?>api/campaigns/create.php';
    $.ajax({
           url:url,
           type:'POST',
           data:data,
           success:on_success,
           error:on_error
           });
}

function dosyncFav(){
    var selvalue = $('[name=templatectrl_msel]').val();
    var template = templates[selvalue];
    $('[name=campaignctrl_info]').show();
    $('[name=campaignctrl_info]').html('开始发送，请勿关闭...');
    window.waitHanlder = setInterval(function(){$('[name=campaignctrl_info]').append('.');},1000);
    
    $("#newsletter_sync_ret").on("load", (function () {
                                       clearInterval(window.waitHanlder);
                                        $('[name=campaignctrl_info]').hide();
                                       $("#newsletter_sync_ret").show();
                                       }));
    var url = location.href.replace("main.php","sync.php")+"&csrf=<?php echo $csrf;?>&nofav="+template.custom.nofav.file+"&fav="+template.custom.myfav.file+"&fav_anchor="+template.custom.myfav.anchor+"&favdeal="+template.custom.myfav.deal+"&favsale="+template.custom.myfav.sale+"&root="+root+template.folder+'/'+"&template="+selvalue;
    $("#newsletter_sync_ret").attr("src", url);
}

$(document).ready(function () {
                  initTemplates();
                  });


</script>