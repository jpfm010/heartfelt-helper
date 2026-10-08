<?php
function mr_env(string $key, ?string $default=null): ?string {
 static $loaded=false;
 if(!$loaded){$file=dirname(__DIR__).'/.env';if(is_file($file)){foreach(file($file,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line){$line=trim($line);if($line===''||$line[0]==='#'||strpos($line,'=')===false)continue;[$k,$v]=explode('=',$line,2);$k=trim($k);$v=trim($v);if(strlen($v)>=2&&(($v[0]==='"'&&substr($v,-1)==='"')||($v[0]==="'"&&substr($v,-1)==="'"))) $v=substr($v,1,-1);if(getenv($k)===false)putenv($k.'='.$v);}}$loaded=true;}
 $v=getenv($key);return ($v===false||$v==='')?$default:$v;
}
$host=mr_env('MYROUTER_DB_HOST','127.0.0.1');$usuario=mr_env('MYROUTER_DB_USER','root');$senha=mr_env('MYROUTER_DB_PASS','');$banco=mr_env('MYROUTER_DB_NAME','myrouter');
$bd=@new mysqli($host,$usuario,$senha,$banco);if($bd->connect_errno){http_response_code(500);die('Banco de dados indisponível. Configure MYROUTER_DB_* no ambiente.');}$bd->set_charset('utf8mb4');$mysqli=$bd;$dbremessa=$bd;
if(!defined('HOST'))define('HOST',$host);if(!defined('BANCO'))define('BANCO',$banco);if(!defined('LOGIN'))define('LOGIN',$usuario);if(!defined('SENHA'))define('SENHA',$senha);if(!defined('KEY'))define('KEY',mr_env('MYROUTER_APP_KEY',''));
require_once __DIR__.'/legacy_mysql_compat.php';if(!isset($GLOBALS['__mr_mysql_link'])){@mysql_connect($host,$usuario,$senha);@mysql_select_db($banco);}
