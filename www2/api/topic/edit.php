<?php
require_once dirname(__FILE__) . '/../lib/napi.php';
require_once dirname(__FILE__) . '/../lib/napi-call.php';

napi_guard_parameters(array('board', 'id'));
napi_guard_login();

global $napi_http;
$board = preg_replace('/[^_a-zA-Z0-9\s]/', '', $napi_http['board']);
$id    = intval($napi_http['id']);
$ftype = empty($napi_http['top']) ? 0 : 11;

//for api stat.
$akey = intval($napi_http['akey']);
napi_count($akey,'topicedit');

$arr = array();
if (is_null(bbs_safe_getboard(0, $board, $arr)))
   throw new Exception('��Ч����');

$articles = array();
if (!bbs_get_records_from_id($board, $id, $ftype, $articles))
   throw new Exception('��������º�');

if (!empty($napi_http['title'])) {
   $ret = bbs_edittitle($board, $id, napi_text_gbk(rtrim($napi_http['title'])), $ftype, 0);
   if ($ret != 0)
       switch ($ret) {
       case -1:
           throw new Exception('�����������');
       case -2:
           throw new Exception('�Բ��𣬸������������޸ı���');
       case -3:
           throw new Exception('�Բ��𣬸�������Ϊֻ��������');
       case -4:
           throw new Exception('��������º�');
       case -5:
           throw new Exception('�Բ������ѱ�ֹͣ�ڸð�ķ���Ȩ��');
       case -6:
           throw new Exception('�Բ�������Ȩ�޸ı���');
       case -7:
           throw new Exception('���⺬�в�������');
       case -8:
           throw new Exception('�Բ��𣬵�ǰģʽ�޷��޸ı���');
       case -9:
           throw new Exception('�������');
       default:
           throw new Exception('ϵͳ��������ϵ����Ա');
       }
}

if (!empty($napi_http['content'])) {
   $ret = bbs_updatearticle($board, $articles[1]['ID'], $articles[1]['FILENAME'], napi_text_gbk($napi_http['content']));
   if ($ret != 0)
      switch ($ret) {
         case -1:
             throw new Exception('�޸�����ʧ�ܣ����¿��ܺ��в�ǡ������');
         case -10:
             throw new Exception('�Ҳ����ļ�!');
         default:
             throw new Exception('ϵͳ��������ϵ����Ա');
      }
}

$topics = napi_call('topic/get', array(
   'board' => $board,
   'id'    => $id,
   'limit' => 1,
   'token' => $napi_http['token']
));

napi_print(array('topic' => $topics['topics'][0]));
?>
