// JavaScript Document 自定义function函数



//cookie

/**
 * 配置Cookie的值调用setCookie()函数,以天为单位
 * @param  int id
 */
function setc( id ){	
	setCookie( 'this_'+id,1 ,10);//10天 cookie值为1	
	//getCookie('this_'+id);
	//var sk=setCookie('user',(new Date).getTime());
}

/**
 * 配置另外一个Cookie的值调用setCookie_time()函数,以秒为单位
 * @param  int id
 */
function setcookieTime(id){	
	setCookie_time( 'play_'+id,2,3);//3秒 cookie值为2	
	
	//var sk=setCookie('user',(new Date).getTime());
}
/**
 * 获取名'play_'+id的
 * @param  int id;
 * @return  getCookie('play_'+id);
 */
function getc(id){
	return getCookie('play_'+id);//console.log(sk);
}

/**
 * 获取cookie信息
 * @param  string c_name
 * @return 如果cookie存在返回 string cookie的值
  		   如果不存在返回 空
 */

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
/**
 * 设置cookie信息
 * @param  string c_name	  设置Cookie的值
 * @param  string value 	  设置Cookie的值
 * @param  string expiredays  Cookie的失效时间以天为单位
 
 */

function setCookie(c_name,value,expiredays)
{
var exdate=new Date()
exdate.setDate(exdate.getDate()+expiredays)
document.cookie=c_name+ "=" +escape(value)+
((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
}

/**
 * 设置cookie信息
 * @param  string c_name	  设置Cookie的值
 * @param  string value 	  设置Cookie的值
 * @param  string exptime 	  Cookie的失效时间以秒为单位
 
 */
function setCookie_time(c_name,value,exptime)
{
//毫秒转化为秒
exptime=exptime*1000;
var exdate=new Date();
exdate.setTime(exdate.getTime()+exptime)
document.cookie=c_name+ "=" +escape(value)+
((exptime==null) ? "" : ";expires="+exdate.toGMTString())
}