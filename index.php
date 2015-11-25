<?php

require "inc/settings.php";
require "inc/tpl.php";

/*


GET index.php?action=new_session&date=2015-11-22
GET index.php?action=edit_session&session_id=123

GET index.php?showmonth=2015-11

POST index.php?action=save_session (session_id)
POST index.php?action=save_session (date)
POST index.php?action=complete_module (module_id=1234)

POST index.php?action=toggle_view_completed (ja/nei)

index.php?action=module_settings
POST index.php?action=save_module_settings (kun endrede felter)

*/




$tpl = new tpl('main');
$tpl->set('hello_world', 'Hello World!');


echo $tpl->render();