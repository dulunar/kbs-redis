<?php
require_once dirname(__FILE__) . '/../lib/napi.php';
require_once dirname(__FILE__) . '/../lib/napi-call.php';

napi_guard_parameters(array('board', 'title', 'content'));
napi_guard_login();

global $napi_http;
$notopten = !empty($napi_http['notopten']);
$noquote = !empty($napi_http['noquote']);
$anony = !empty($napi_http['anony']);
$type = intval($napi_http['type']) % 5;
$reid = intval($napi_http['reid']);
$board = preg_replace('/[^_a-zA-Z0-9\s]/', '', $napi_http['board']);

//for api stat.
$akey = intval($napi_http['akey']);
napi_count($akey,'topicpost');

$arr = array();
if (is_null(bbs_safe_getboard(0, $board, $arr)))
   throw new Exception('��Ч����');

// �����ݺ��Զ���������
$content = napi_text_gbk($napi_http['content']);
if (!$noquote && $reid != 0)
   $content .= "\n" . get_quote($board, $reid) . "\n";

$type_map = array(3, 5, 6, 7, 8);
$result = bbs_postarticle($board,
                          napi_text_gbk($napi_http['title']),
                          $content,
                          0, /* signature */
                          $reid,
                          false, /* outgo */
                          $anony, /* anonymous */
                          false, /* mailback */
                          false, /* tex */
                          $type_map[$type], /* system type */
                          $notopten);

if ($result <= 0) {
   switch ($result) {
   case 0:
      throw new Exception('��Ч�û�');
   case -1:
      throw new Exception('���������������');
   case -2:
      throw new Exception('����Ŀ¼��');
   case -3:
      throw new Exception('����ΪNULL');
   case -4:
      throw new Exception('����������Ψ����, ����������Ȩ���ڴ˷�������');
   case -5:
      throw new Exception('�ܱ�Ǹ, �㱻������Աֹͣ�˱����postȨ��');
   case -6:
      throw new Exception('���η��ļ������, ����Ϣ���������');
   case -7:
      throw new Exception('�����ļ�������');
   case -8:
      throw new Exception('���Ĳ��ܻظ�');
   default:
      throw new Exception('����ʧ�ܣ�' . $result);
   }
}

$topics = napi_call('topic/get', array(
   'board' => $napi_http['board'],
   'id'    => $result,
   'limit' => 1,
   'token' => $napi_http['token']
));

napi_print(array('topic' => $topics['topics'][0]));

function get_quote($board, $reid) 
{
   global $napi_http;
   $topics = napi_call('topic/get', array(
      'gbk'   => true,
      'board' => $board,
      'id'    => $reid,
      'token' => $napi_http['token']
   ));

   $topic = $topics['topics'][0];

   $result = "�� �� {$topic['author']} �Ĵ������ᵽ: ��";
   $lines = explode("\n", $topic['content']);
   $count = 0;
   foreach ($lines as &$line) {
      ++$count;
      if ($count > 4) {
         $return .= "\n: ......";
         break;
      }

      $result .= "\n: " . $line;
   }

   return $result;
}
?>
