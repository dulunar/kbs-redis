<?php
require_once dirname(__FILE__) . '/../lib/napi.php';
require_once dirname(__FILE__) . '/../lib/Predis.php';

napi_guard_login();

global $napi_http;
global $napi_user;
$user = $napi_user;

//for api stat.
$akey = intval($napi_http['akey']);
napi_count($akey, 'usermypst');

$total = 0;
@$link = mysql_connect(bbs_sysconf_str("MYSQLBLOGHOST"), bbs_sysconf_str("MYSQLBLOGUSER"), bbs_sysconf_str("MYSQLBLOGPASSWORD")) or die("�޷����ӵ�������!");
@mysql_select_db(bbs_sysconf_str("MYSQLBLOGDATABASE"), $link);
$sql="SELECT count(*) FROM postlog WHERE userid='".$user."' AND UNIX_TIMESTAMP(time)>=".$currentuser["firstlogin"];
if($result = mysql_query($sql,$link)) {
    $row = mysql_fetch_row($result);
    $total = $row[0];
    if($total == 0) {
        mysql_free_result($result);
        mysql_close($link);
        throw new Exception('����δ��������(ϵͳ����¼��2008��9�·�֮�������)');
    }
    else
        mysql_free_result($result);
}
else {
    mysql_free_result($result);
    mysql_close($link);
    throw new Exception('���ݿ����'); 
}
if (empty($napi_http['start'])) {
    $start = 1;
} else {
    $start = $napi_http['start'];
}

if ($start < 1 || $start >= $total)
    $start = 1;
$num = 20;
$sql = "SELECT `bname`,`title`,UNIX_TIMESTAMP(`time`),`postid` FROM postlog WHERE userid='".$user."' AND UNIX_TIMESTAMP(time)>=".$currentuser["firstlogin"]." ORDER BY time DESC LIMIT ".($start-1).",".$num;
$articles = mysql_query($sql,$link);

$i = 0;
while($article=mysql_fetch_array($articles,MYSQL_ASSOC)) {
    $article_content = array();
    $index = bbs_get_records_from_id($article['bname'],$article['postid'],0,$article_content);
    $board = array();
    $brdnum = bbs_getboard($article["bname"],$board);
    if(!bbs_checkreadperm($currentuser["index"],$brdnum)) {
        $index=0; 
    }
    if($index <= 0) {
        $resultt[] = array(
            'num'   => $total-$start-$i+1,
            'bid'   => $board['BID'],
            'bname' => $article['bname'],
            'title' => napi_text_utf8($article['title']),
            'time'  => $article['UNIX_TIMESTAMP(`time`)']
            );
    } else {
        $resultt[] = array(
            'num'   => $total-$start-$i+1,
            'bid'   => $board['BID'],
            'bname' => $article['bname'],
            'id'    => $article['postid'],
            'title' => napi_text_utf8($article['title']),
            'time'  => $article['UNIX_TIMESTAMP(`time`)']
            );
    }
	$i++;
}
napi_print(array('topics' => $resultt));
?>
