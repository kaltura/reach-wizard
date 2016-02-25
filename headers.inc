<?php
$year= date("Y");
define ('HTML_HEADER','
<!-- header of the page -->
<header id="header">
<div class="container">
<!-- page logo -->
<div class="logo"><a href="http://www.kaltura.com"><img src="images/logo.png" width="146" height="65" alt="kaltura"></a></div>
</div>
</header>
');

define ('HTML_FOOTER','
<!-- footer of the page -->
<footer id="footer">
<div class="f1">
<div class="content">
<p> Copyright &copy; '.$year.' <a href="http://www.kaltura.com">Kaltura</a>, inc. All rights reserved. </p>
</div>
</div>
</footer>
');
?>

