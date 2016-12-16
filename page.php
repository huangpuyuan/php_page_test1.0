<?php
   //1传入页码	         	   
$page=(isset($_GET['p']))?$_GET['p']:1;//$page默认值为1
$sel=(isset($_GET['sel']))?$_GET['sel']:'all';
$keywords=(isset($_GET['s']))?$_GET['s']:'';
//$page=1;
$page_Size=8;//配置每页条数
$showpage=7;
//2根据页码取出数据php,Sqlite 链接数据库，连接数据表的处理
include_once('sqlite.php');
$DB=new SQLite( 'data_source_listnew.db' );
$sql_x="select * from coumns";
$data_x=$DB->getlist($sql_x);
//获取P_ID		
	function getPid($data_x,$pid){
		  for($J=0;$J<count($data_x);$J++){
			  if($data_x[$J]["string_short"]==$pid){
				  return $data_x[$J]["string_name"];
			  }
		  }		  
	}

if($sel=='all'&&$keywords==''){
	//编写sql获取分页数据 select * from 表名 Limit条数offset起始位置
	$sql='select * from list_source where status = 1 order by id desc limit '.$page_Size.' offset '.($page-1)*$page_Size;
	$data= $DB->getlist($sql);	   
	//print_r($data);
	$total= $DB->RecordCount('select id from list_source where status = 1');	
	//获取数据总数
	$total_page=ceil($total/$page_Size);//获取总页数
    	 
}
else if($sel!="all"){
    $sql_b='select * from list_source where status = 1 and p_id="'.$sel.'" order by id desc limit '.$page_Size." offset ".($page-1)*$page_Size;
	$data= $DB->getlist($sql_b);	
	$total= $DB->RecordCount('select id from list_source where status = 1 and p_id="'.$sel.'"');	
	//获取数据总数
	$total_page=ceil($total/$page_Size);//获取总页数	
}
else if($keywords!=''){//搜索	
	$sql_a='select string_short from coumns where string_name like "%'.$keywords.'%"';
	$data_a=$DB->getlist($sql_a);
	$str_sql='';
	for($i=0;$i<count($data_a);$i++){
		$str_sql .= 'p_id="'. $data_a[$i]['string_short'].'"';
		if( $i!=(count($data_a)-1) ){
			$str_sql .= ' or ';	
		}
	}
	if( $str_sql!='' ){
		$str_sql='or '.$str_sql;
	}
	
	$sql_s2='select * from list_source where status = 1 and (string_name like "%'.$keywords.'%" '.$str_sql.') order by id desc limit '.$page_Size.' offset '.($page-1)*$page_Size;	
	$data=$DB->getlist($sql_s2);
	$sql_s3='select id from list_source where status = 1 and (string_name like "%'.$keywords.'%" '.$str_sql.')';
	$total=$DB->RecordCount($sql_s3);
	$total_page=ceil($total/$page_Size);
}
?><!doctype html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html">
    <meta charset="utf-8">
    <title>分页固定推荐点赞播放次数</title>   
    <script src="jquery-3.1.1.min.js"></script>    	
    <style>
	body{
		font-size:15px;
		font-family:Verdana, Geneva, sans-serif;
		width:99%;
		height:100%;	
		}
	caption{
	font-size: 22px;
	font-weight: bold;
		}		
	div.page{
		text-align:center;
		margin:1em;
		}
    div.page a{border:#aaaadd 1px solid;text-decoration:none;padding:2px 5px 2px 5px;margin:2px;}
	div.page a:hover{border:#000099 1px solid;color:#fff;text-decoration:none;padding:2px 5px 2px 5px;margin:2px;background-color:#000099;}
    div.page span.current{border:#000099 1px solid;background-color:#000099;padding:5px 5px 5px 5px;margin:2px;
	color:#fff;	font-weight:bold;}
	div.page span.disable{
		color:#666;
		border:#eee 1px solid;
		padding:2px 5px 2px 5px;
		margin:2px;
		}
	form{
		display:inline;	
		}
	.form0{float:left;font-size: small;}
	.form1{float:right;}
	.small{
		display:block;
		width:132px;
		height:79px;
		border:0;
		margin:0 auto;
		}
	.like{
		 color:#369; height: 20px; padding-left:18px; background: url("img/bg2.jpg") no-repeat left center;
		}
	.like:hover{
		text-decoration:underline; background: url("img/bg3.jpg") no-repeat left center;
		}	
	.liked{
		color:#F00; height: 20px; padding-left:18px; background: url("img/bg3.jpg") no-repeat left center;
	}
    </style>
</head>
<body>
<script>
  
$(document).ready(function() {
	 
		//点赞
	$('.like').click(function(){	
		attr_like( $(this).attr('data-id') );	
		//return false;	
	});
	//图片点击添加播放次数
	$('.hot').click(function(){
		var data_id=$(this).attr('data-id');   			
		attr_hot(data_id);		
		var _hot2=$('.tr'+data_id).find('img');
		window.open(_hot2.attr('src'),'_blank');
		//return false;
		//$(this).attr('href',$('img').attr('src'));		
	});
	//视频点击添加播放次数
	$('video').on('play', function() {
		attr_hot( $(this).attr('data-id') );
	});
	//下拉列表做出选择立即触发
	$('.target').change(function(){
		$('.form0').submit();
	});
	
	$('.recommod').click(function(){
		//$(this).parent().parent()
		var data_id=$(this).attr('data-id');
		$.get('doaction.php?ac=add_1&id='+data_id,			
			  function(data){
				if(data="成功"){	
					var _str='tr'+data_id;
					var _node=$('.'+_str).find( 'td' ).eq(4);				
					_node.html('是');
				}	  
			})
	});
	
	/*-----------------------------------------------
	var sel_php='<-?php echo $sel?>';
	
	var select_option=$('.target').find('option')
	
	for(var i=0;i<select_option.length;i++){
		if( sel_php==select_option[i].value ){
			select_option[i].selected='selected';
		}else{
			//select_option
			select_option[i].removeAttribute('selected');
			console.log(i)
		}
	}*/
	
});

function asdf(node){
	 return $(node).attr('data-id');
}

function qwer(node,fn){
	
	fn();
}

//异步传参
function attr_like( this_id ){

	$.ajax({		
		url: 'doaction.php?ac=add_like&id='+this_id,
		dataType:"text",
		//context: document.body,
		success:function(message){
			if( message=="成功" ){
				alter_like( this_id );
				alter_a_like( this_id );
				setc( this_id );				
			}
			//alert(message)
		},
		error:function(message){
		   //alert('err')
		}
	});
}
function attr_hot( this_id ){
	if(getc(this_id)!=2){
		$.ajax({		
			url: 'doaction.php?ac=add_hot&id='+this_id,
			dataType:"text",
			//context: document.body,
			//async:false,
			success:function(message){
				if( message=="成功"){								
					alter_hot( this_id );
					setcookieTime( this_id );					
				}
				//alert(message)
			},
			error:function(message){
			   //alert('err')
			}
		});
    }
}

function alter_like( this_id ){
	var _str='tr'+this_id;
	var _node=$('.'+_str).find( 'td' ).eq(7);
	_node.html( parseInt(_node.html())+1 );
	//_node[0].innerHTML= parseInt(_node[0].innerHTML)+1;
}
function alter_hot(this_id){
	var _str='tr'+this_id;
	var _hot=$('.'+_str).find( 'td' ).eq(6);		
	_hot.html(parseInt(_hot.html())+1);
	//console.log( _hot2.attr('src') );
	//$('.'+_str).find('.hot').attr('href',_hot2.attr('src'));
	//console.log( $('.'+_str).find('.hot')[0].outerHTML );	
	//alert(_hot2.attr('src'))	
}

/*
//改变like,hot返回值
function alter_like( this_id ){
	var _str='tr'+this_id;
	var _node=$('.'+_str).find( 'td' ).eq(7);
	_node.html( parseInt(_node.html())+1 );
	//_node[0].innerHTML= parseInt(_node[0].innerHTML)+1;
}
function alter_hot(this_id){
	var _str='tr'+this_id;
	var _hot=$('.'+_str).find( 'td' ).eq(6);		
	_hot.html(parseInt(_hot.html())+1);
	//console.log( _hot2.attr('src') );
	//$('.'+_str).find('.hot').attr('href',_hot2.attr('src'));
	//console.log( $('.'+_str).find('.hot')[0].outerHTML );	
	//alert(_hot2.attr('src'))	
}
*/
//改变like返回值的标签的样式
function alter_a_like( this_id ){
	
	var _str='tr'+this_id;
	var _node=$('.'+_str).find( 'td' ).eq(5);
	
	var _this=$(_node).find('a').eq(2);
	_this.replaceWith(function(){     
		return '<span class="liked" >+1</span>';	
		 });
	 //_this[0].outerHTML='<span class="liked" >+1</span>';	
}


</script>
<div class="content">
  <table border=1 cellspacing="0" width=100% align="center">
  <caption> 
  <form  class="form0" name="form0" method="get" action="page.php">
  热门栏目分类：      
   <select class="target" name="sel"><?php 
        $select=array(
        "all"=>"全部",
        "ylgs"=>"娱乐节选",
        "dsp"=>"拍客",
        "21"=>"香港故事",
        "26"=>"西望成都",
        "77"=>"看戏",
        "gy"=>"女性健康"
        );
        foreach($select as $key=>$value){$strs="<option value='{$key}'"; if($sel==$key){$strs .="selected='selected'";} $strs .=">{$value}</option>";echo $strs;}?></select>
  </form>
   视频后台编辑|<a href="position.php">配置固定位与推荐位</a>
    <form class="form1" name="form1" method="get" action="page.php"> 
      <input type="text" name="s" id="search" value="<?php if(isset($_GET['s'])){echo $keywords;}?>" placeholder="请输入搜索关键词">
    <input type="submit" value='搜索'>
  </form>   
    </caption>
    <tr bgcolor="#ABCDEF">  
      <th>编号</th>
      <th>视频名称</th>
      <th>图片</th>    
      <th>固定</th>
      <th>推荐</th>
      <th>操作</th>
      <th>播放次数</th>
      <th>点赞次数</th>
      <th>播放</th>
      <th>栏目名称</th>   
    </tr>
    <?php $num=1;foreach($data as $row):?>
    <tr class="tr<?php echo $row['id'];?>">    
      <td><?php echo $num+($page-1)*$page_Size;?></td>
      <td><?php echo $row['string_name'];?></td>
      <td><a href="#" class="hot" data-id="<?php echo $row['id'];?>"><img class="small" src="<?php echo $row['string_img']?>"  alt="" /></a></td>
      <td><?php echo $row['spare01']==1 ? '是' : '';	//$id = isset($_GET['id']) ? $_GET['id'] : false;
	//if ($row['spare01']==1){echo '是';}else{echo '';}	    
	?></td>
      <td><?php echo $row['spare03']==1 ? '是' : '';  ?></td>    
      <td><a href="doaction.php?id=<?php echo $row['id'];?>&ac=add">固定</a>|<a data-id="<?php echo $row['id'];?>" class="recommod" href="#">推荐</a>|<?php

if ( isset($_COOKIE[ 'this_'.$row['id']]) ){
	echo '<span class="liked" style="color:#F00">+1</span>';
}else{
	echo '<a href="#" class="like" data-id="'. $row['id'].'">赞</a>';
}
    ?></td>
      <td><?php echo $row['hot'];?></td>
      <td><?php echo $row['like'];?></td>
      <td><video class="small" data-id="<?php echo $row['id'];?>" controls src="<?php 
	$link=$row['string_link'];
	$url_str1="http://vod1.hkstv.tv:5080/api/player/";
	$url_str2="var url_str2='?mode=vod&protocal=hls";
	if (substr(strtolower($link),0,4)=="http"){
		echo $link;
	}else{
		//$link=substr($link,0,32);
		$json_string=@file_get_contents($url_str1.$link.$url_str2);
		$url=json_decode($json_string,true);
		echo $url['url'];	
	}
	?>"></video></td>
      <td><?php echo getPid($data_x,$row['p_id']).'<br/>';
	  			 if($row['create_time']!=''){
				echo date('Y-m-d',$row['create_time']);
					};?></td>
    </tr>
    <?php $num++;endforeach;?>
  </table>
</div>

<?php
      //3显示数据 数据+分页条
      /*$pp=1;
        if( $page-1<1 ){
            $pp=1;
        }else{
            $pp=$page-1;
        }*/
	   $get_str='&sel='.$sel.'&s='.$keywords;
       $page_banner="<div class='page'>";//分页条
       $pageoffset=($showpage-1)/2;//计算偏移量
       if($page>1){
           $page_banner .="<a href='".$_SERVER['PHP_SELF']."?p=1".$get_str."'>首页</a>";	
           $page_banner .="<a href='".$_SERVER['PHP_SELF']."?p=".($page-1).$get_str."'><上一页</a>";
       }else{
           $page_banner .="<span class='disable'>首页</span>";
           $page_banner .="<span class='disable'><上一页</span>"; 
       }
       $start=1;$end=$total_page;//初始化数据
       if($total_page>$showpage){
           if($page>$pageoffset+1){
              $page_banner .="...";
           }
           
           if($page>$pageoffset){
              $start=$page-$pageoffset;
              $end= $total_page>$page+$pageoffset?$page+$pageoffset:$total_page;            
           }else{
              $start=1;
              $end =$total_page>$showpage?$showpage:$total_page; 
           }
           if($page+$pageoffset>$total_page){
              $start=$start-($page+$pageoffset-$end);
           }
        }
         for($i=$start;$i<=$end;$i++){
             if($page==$i){
                 $page_banner .="<span class='current'>{$i}</span>";
             }else{
                 $page_banner .="<a href='".$_SERVER['PHP_SELF']."?p=".$i.$get_str."'>{$i}</a>";
                 }
       }
       if($total_page>$showpage && $total_page>$page + $pageoffset){
           $page_banner .="...";
       }	   
       if($page<$total_page){
           $page_banner .="<a href='".$_SERVER['PHP_SELF']."?p=".($page+1).$get_str."'>下一页></a>";
           $page_banner .="<a href='".$_SERVER['PHP_SELF']."?p=".($total_page).$get_str."'>尾页</a>";
       }else{
           $page_banner .="<span class='disable'>下一页></span>";
           $page_banner .="<span class='disable'>尾页</span>"; 
       }
       $page_banner .="共{$total_page}页,"; 
       $page_banner .="<form name='form3' action='page.php' method='get'>";
       $page_banner .="到第<input type='text' size='2' name='p'>页";
       $page_banner .="<input type='submit' value='确定'>";
       $page_banner .="</form></div>";
       echo $page_banner;
    
?>
<script>

//cookie
function setc( id ){	
	setCookie( 'this_'+id,1 ,10);	
	getCookie('this_'+id);
	//var sk=setCookie('user',(new Date).getTime());
}
//cookie
function setcookieTime(id){	
	setCookie_time( 'play_'+id,2,3);	
	
	//var sk=setCookie('user',(new Date).getTime());
}

function getc(id){
	return getCookie('play_'+id);//console.log(sk);
}
function getCookie(c_name)
{
if (document.cookie.length>0)
  {
  c_start=document.cookie.indexOf(c_name + "=")
  if (c_start!=-1)
    { 
    c_start=c_start + c_name.length+1 
    c_end=document.cookie.indexOf(";",c_start)
    if (c_end==-1) c_end=document.cookie.length
    return unescape(document.cookie.substring(c_start,c_end))
    } 
  }
return "";
}

function setCookie(c_name,value,expiredays)
{
var exdate=new Date()
exdate.setDate(exdate.getDate()+expiredays)
document.cookie=c_name+ "=" +escape(value)+
((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
}

function setCookie_time(c_name,value,exptime)
{
//毫秒转化为秒
var exdate=new Date();
exdate.setTime(exdate.getTime()+exptime*1000)
document.cookie=c_name+ "=" +escape(value)+
((exptime==null) ? "" : ";expires="+exdate.toGMTString())
}
</script>
</body>
</html>