<?php
   //1传入页码	         	   
   $page=(isset($_GET['p']))?$_GET['p']:1;//$page默认值为1
   //$page=1;
   $page_Size=10;//配置每页条数
   $showpage=7;
   //2根据页码取出数据php,Sqlite 链接数据库，连接数据表的处理
   include_once('sqlite.php');
   $DB=new SQLite( 'data_source_listnew.db' );
   //编写sql获取分页数据 select * from 表名 Limit条数offset起始位置
   $sql='select * from list_source where status = 1 order by id desc limit '.$page_Size.' offset '.($page-1)*$page_Size;
   $data= $DB->getlist($sql);
   //print_r($data);
   $total= $DB->RecordCount('select id from list_source where status = 1');//获取数据总数
   $total_page=ceil($total/$page_Size);//获取总页数              
 ?>
    
<!doctype html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html">
    <meta charset="utf-8">
    <title>分页固定推荐点赞播放次数</title>   
    <script src="jquery-3.1.1.min.js"></script>
    <script src="cookie.func.js"></script>   	
    <style>
	body{
		font-size:15px;
		font-family:Verdana, Geneva, sans-serif;
		width:90%;
		}		
	div.page{
		text-align:center;
		margin:1em;
		}
    div.page a{
        border:#aaaadd 1px solid;
        text-decoration:none;
        padding:2px 5px 2px 5px;
        margin:2px;
        }
    div.page span.current{		
        border:#000099 1px solid;
        background-color:#000099;
        padding:5px 5px 5px 5px;
        margin:2px;
        color:#fff;
        font-weight:bold;
        }
	div.page span.disable{
		color:#666;
		border:#eee 1px solid;
		padding:2px 5px 2px 5px;
		margin:2px;
		
		}
	div.page form{
		display:inline;
		}
	.small{
		display:block;
		width:120px;
		height:72px;
		border:0;
		margin:0 auto;
		}
	.like{
		 color:#369; height: 20px; padding-left:18px; background: url("img/like_bg2.jpg") no-repeat left center;
		}
	.like:hover{
		text-decoration:underline; background: url("img/like_bg3.jpg") no-repeat left center;
		}	
	.liked{
		color:#F00; height: 20px; padding-left:18px; background: url("img/like_bg3.jpg") no-repeat left center;
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
		attr_hot( $(this).attr('data-id') );
	});
	//视频点击添加播放次数
	$('video').bind('play', function() {
		attr_hot( $(this).attr('data-id') );
	});
});

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
				//调用setc(),函数来源cookie.func.js	
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
//调用getc(),函数来源cookie.func.js	
if(getc(this_id)!=2){
	$.ajax({		
		url: 'doaction.php?ac=add_hot&id='+this_id,
		dataType:"text",
		//context: document.body,
		success:function(message){
			if( message=="成功"){								
				alter_hot( this_id );
				//调用setcookieTime(),函数来源cookie.func.js	
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
	}
//改变like返回值的标签的样式
function alter_a_like( this_id ){
	
	var _str='tr'+this_id;
	var _node=$('.'+_str).find( 'td' ).eq(5);
	
	var _this=$(_node).find('a').eq(2);
	
	 _this[0].outerHTML='<span class="liked" >+1</span>';	
}

</script>
<div class="content">
  <h2 align="center">视频后台编辑|<a href="position.php?p=<?php echo $page;?>">配置固定位与推荐位</a></h2>
  <table border=1 cellspacing="0" width=100% align="center">
    <tr bgcolor="#ABCDEF">  
    <td>视频编号</td>
    <td>视频名称</td>
    <td>图片</td>    
    <td>固定</td>
    <td>推荐</td>
    <td>是否推荐固定</td>
    <td>播放次数</td>
    <td>点赞次数</td>
    <td>播放</td>   
    </tr>
    <?php foreach($data as $row):?>
    <tr class="tr<?php echo $row['id'];?>">    
    <td><?php echo $row['id'];?></td>
    <td><?php echo $row['string_name'];?></td>
    <td><a href="#"  class="hot" data-id="<?php echo $row['id'];?>"><img class="small" src="<?php echo $row['string_img']?>"  alt="" /></a></td>
    <td><?php echo $row['spare01']==1 ? '是' : '';	//$id = isset($_GET['id']) ? $_GET['id'] : false;
	//if ($row['spare01']==1){echo '是';}else{echo '';}	    
	?></td>
    <td><?php echo $row['spare03']==1 ? '是' : '';  ?></td>    
    <td><a href="doaction.php?id=<?php echo $row['id'];?>&ac=add">固定</a>|<a href="doaction.php?id=<?php echo $row['id'];?>&ac=add_1">推荐</a>|<?php

if ( isset($_COOKIE[ 'this_'.$row['id']]) ){
	echo '<span class="liked" style="color:#F00">+1</span>';
}else{
	echo '<a href="#" class="like" data-id="'. $row['id'].'">赞</a>';
}
    ?></td>
    <td><?php echo $row['hot'];?></td>
    <td><?php echo $row['like'];?></td>
    <td><video class="small" data-id="<?php echo $row['id'];?>" controls src="<?php echo $row['string_link'];?>"></video></td>
    </tr>
    <?php endforeach;?>
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
       $page_banner="<div class='page'>";//分页条
       $pageoffset=($showpage-1)/2;//计算偏移量
       if($page>1){
           $page_banner .="<a href='".$_SERVER['PHP_SELF']."?p=1'>首页</a>";	
           $page_banner .="<a href='".$_SERVER['PHP_SELF']."?p=".($page-1)."'><上一页</a>";
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
                 $page_banner .="<a href='".$_SERVER['PHP_SELF']."?p=".$i."'>{$i}</a>";
                 }
       }
       if($total_page>$showpage && $total_page>$page + $pageoffset){
           $page_banner .="...";
       }	   
       if($page<$total_page){
           $page_banner .="<a href='".$_SERVER['PHP_SELF']."?p=".($page+1)."'>下一页></a>";
           $page_banner .="<a href='".$_SERVER['PHP_SELF']."?p=".($total_page)."'>尾页</a>";
       }else{
           $page_banner .="<span class='disable'>下一页></span>";
           $page_banner .="<span class='disable'>尾页</span>"; 
       }
       $page_banner .="共{$total_page}页,"; 
       $page_banner .="<form action='page.php' method='get'>";
       $page_banner .="到第<input type='text' size='2' name='p'>页";
       $page_banner .="<input type='submit' value='确定'>";
       $page_banner .="</form></div>";
       echo $page_banner;
    
?></body>
</html>