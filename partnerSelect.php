<?php
//This script displays the selector for choosing a partner on the account
	$response = $_REQUEST['response'];
?>
<label for="partnerid">Select a Partner ID: <span class="required">*</span></label>
<div id="partnerSelect">
	<select data-placeholder="Choose a Partner ID" id="partnerChoice" class="form-control select" style="width:250px;" tabindex="2">
		<?php
		for($i = 1; $i < $response[0] + 1; ++$i) {
			echo '<option value="'.$response[$i][0].'">'.$response[$i][0].': '.$response[$i][1].'</option>';
		}
		?>
	</select>
</div>
<br/>
<img src="lib/loginLoader.gif" id="partnerLoader" style="display: none;">
