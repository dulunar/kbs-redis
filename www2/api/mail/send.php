<?php
require_once dirname(__FILE__) . '/../lib/napi.php';
require_once dirname(__FILE__) . '/../lib/napi-call.php';

napi_guard_parameters(array('user', 'title', 'content'));
napi_guard_login();

global $napi_http;

//for api stat.
$akey = intval($napi_http['akey']);
napi_count($akey,'mailsend');

$user = preg_replace('/[^a-zA-Z]/', '', $napi_http['user']);
$reid = intval($napi_http['reid']);
$noquote = !empty($napi_http['noquote']);

// �����ݺ��Զ���������
$content = napi_text_gbk($napi_http['content']);
if (!$noquote && $reid != 0)
   $content .= "\n" . get_quote($reid);

$result = bbs_postmail($user,
                       napi_text_gbk($napi_http['title']),
                       $content,
                       0,
                       1);

napi_print(array('result' => $result));

function get_quote($id) 
{
   global $napi_http;
   $mail = napi_call('mail/get', array(
      'gbk'   => true,
      'type'  => 0,
      'id'    => $id,
      'token' => $napi_http['token']
   ));
   $mail = $mail['mail'];
   if (!mail)
      throw new Exception('�ظ����ʼ�������');

   $result = "�� �� {$mail['author']} ���������ᵽ: ��";
   $lines = explode("\n", $mail['content']);
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
