<?php
declare(strict_types=1);
namespace TempMail\Services;

final class ImapService {
 public function fetch(string $mailbox,string $username,string $password): array {
  if(!function_exists('imap_open')) throw new \RuntimeException('PHP IMAP extension is required.');
  $stream=@imap_open($mailbox,$username,$password,OP_READONLY);
  if(!$stream) throw new \RuntimeException('Unable to connect to IMAP mailbox.');
  $out=[]; foreach(imap_search($stream,'ALL') ?: [] as $num){$h=imap_headerinfo($stream,$num);$out[]=['uid'=>imap_uid($stream,$num),'subject'=>$h->subject??null,'from'=>$h->fromaddress??null,'date'=>$h->date??null,'body'=>imap_fetchbody($stream,$num,1)];}
  imap_close($stream); return $out;
 }
}
