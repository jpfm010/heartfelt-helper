<?php
if(!function_exists('mysql_connect')){
function mysql_connect($host=null,$user=null,$pass=null,$new_link=false,$client_flags=0){global $__mr_mysql_link;$link=@new mysqli($host??'127.0.0.1',$user??'',$pass??'');if($link->connect_errno){$__mr_mysql_link=false;return false;}$link->set_charset('utf8mb4');$__mr_mysql_link=$link;return $link;}
function mysql_select_db($db,$link_identifier=null){global $__mr_mysql_link;$link=$link_identifier?:$__mr_mysql_link;return $link instanceof mysqli?$link->select_db($db):false;}
function mysql_query($query,$link_identifier=null){global $__mr_mysql_link;$link=$link_identifier?:$__mr_mysql_link;return $link instanceof mysqli?$link->query($query):false;}
function mysql_fetch_array($result,$result_type=MYSQLI_BOTH){return $result instanceof mysqli_result?$result->fetch_array($result_type):false;}
function mysql_fetch_assoc($result){return $result instanceof mysqli_result?$result->fetch_assoc():false;}
function mysql_fetch_row($result){return $result instanceof mysqli_result?$result->fetch_row():false;}
function mysql_num_rows($result){return $result instanceof mysqli_result?$result->num_rows:0;}
function mysql_insert_id($link_identifier=null){global $__mr_mysql_link;$link=$link_identifier?:$__mr_mysql_link;return $link instanceof mysqli?$link->insert_id:0;}
function mysql_affected_rows($link_identifier=null){global $__mr_mysql_link;$link=$link_identifier?:$__mr_mysql_link;return $link instanceof mysqli?$link->affected_rows:0;}
function mysql_error($link_identifier=null){global $__mr_mysql_link;$link=$link_identifier?:$__mr_mysql_link;return $link instanceof mysqli?$link->error:'Banco indisponível';}
function mysql_close($link_identifier=null){global $__mr_mysql_link;$link=$link_identifier?:$__mr_mysql_link;if($link instanceof mysqli){$r=$link->close();$__mr_mysql_link=null;return $r;}return false;}
function mysql_real_escape_string($string,$link_identifier=null){global $__mr_mysql_link;$link=$link_identifier?:$__mr_mysql_link;return $link instanceof mysqli?$link->real_escape_string((string)$string):addslashes((string)$string);}
}
