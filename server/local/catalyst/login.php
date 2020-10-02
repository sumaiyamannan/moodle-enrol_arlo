<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

// This page can be used as the custom login url on testing sites - if site available, pass to login page with noredirect param, otherwise display error.
$catconfig = get_config('local_catalyst');
if (empty($catconfig->testingsite) || $catconfig->testingavailableto > time()) {
    redirect($CFG->wwwroot.'/login/index.php', array('noredirect' => 'true'));
}
?>

<head>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>

<div id="access-message">

  <div class="content">

    <h1>Catalyst Testing Site</h1>

    <p>You are trying to access a Catalyst testing site. <br>Your access to this site has expired.</p>
    <p>Please create a ticket at <a href="https://wrms.catalyst.net.nz"> https://wrms.catalyst.net.nz </a> to request access.</p>

  </div>

  <div class="logo">

  </div>

</div>
