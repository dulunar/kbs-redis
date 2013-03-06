<?php
require_once dirname(__FILE__) . '/../lib/napi.php';

napi_guard_parameters(array('type', 'id'));
napi_guard_login();

global $napi_http;
global $napi_user;

//for api stat.
$akey = intval($napi_http['akey']);
napi_count($akey,'maildelete');

$type = intval($napi_http['type']) % 3;
$id = intval($napi_http['id']);
$typeMap = array('.DIR', '.SENT', '.DELETED');
$path = bbs_setmailfile($napi_user, $typeMap[$type]);

// �ҵ�����id���ʼ�
$mails = bbs_getmails($path, $id, $id + 1);
$mail = $mails[0];
if (!$mail)
   throw new Exception('�����ڴ��ʼ�');

napi_print(array('result' => bbs_delmail($typeMap[$type], $mail['FILENAME'])));
?>
