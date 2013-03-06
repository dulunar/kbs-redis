<?php
require_once dirname(__FILE__) . '/../lib/napi.php';

napi_try_login();

global $napi_http;
if (empty($napi_http['name'])) {
   global $napi_user;
   $user = $napi_user;
} else {
   $user = preg_replace('/[^a-zA-Z]/', '', $napi_http['name']);
}

//for api stat.
$akey = intval($napi_http['akey']);
napi_count($akey,'userget');

$arr = array();
$qur = array();
if (0 == bbs_getuser($user, $arr))
   throw new Exception('���û�������');

bbs_get_query_info($user, $qur);

$result = array(
   'id'         => $arr['userid'],
   'name'       => napi_text_utf8($arr['username']),
   'avatar'     => 'http://bbs.seu.edu.cn/wForum/' . $arr['userface_url'],
   'lastlogin'  => $arr['lastlogin'],
   'level'      => napi_text_utf8(bbs_getuserlevel($arr['userid'])),
   'posts'      => $arr['numposts'],
   'perform'    => $qur['perf'],
   'experience' => $qur['exp'],
   'medals'     => $arr['nummedals'],
   'logins'     => $arr['numlogins'],
   'life'       => bbs_compute_user_value($arr['userid'])
);

if ($arr['userdefine0'] & BBS_DEF_SHOWDETAILUSERDATA) {
   $result['gender'] = chr($arr['gender']);
   $result['astro']  = napi_text_utf8(get_astro($arr['birthmonth'], $arr['birthday']));
}

napi_print(array('user' => $result));

function get_astro($birthmonth, $birthday)
{
    if ($birthmonth == 0 || $birthday == 0)
        return 'δ֪';

    switch ($birthmonth) {
    case 1:
        if ($birthday >= 21)
           return 'ˮƿ��';
        else
           return 'ħ����';
    case 2:
        if ($birthday >= 20)
           return '˫����';
        else
           return 'ˮƿ��';
    case 3:
        if ($birthday >= 21)
           return '������';
        else
           return '˫����';
    case 4:
        if ($birthday >= 21)
           return '��ţ��';
        else
           return '������';
    case 5:
        if ($birthday >= 22)
           return '˫����';
        else
           return '��ţ��';
    case 6:
        if ($birthday >= 22)
           return '��з��';
        else
           return '˫����';
    case 7:
        if ($birthday >= 23)
           return 'ʨ����';
        else
           return '��з��';
    case 8:
        if ($birthday >= 24)
           return '��Ů��';
        else
           return 'ʨ����';
    case 9:
        if ($birthday >= 24)
           return '������';
        else
           return '��Ů��';
    case 10:
        if ($birthday >= 24)
           return '��Ы��';
        else
           return '������';
    case 11:
        if ($birthday >= 23)
           return '������';
        else
           return '��Ы��';
    case 12:
        if ($birthday >= 22)
           return 'ħ����';
        else
           return '������';
    default:
        return '������';
    }
}
?>
