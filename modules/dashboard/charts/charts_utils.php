<?php


function getComboxBoxOnChangeEvent($_arrObj, $_name, $_size, $_css = 'combo', $_event, $_default) {
	echo "<select size=\"$_size\" id=\"$_name\" name=\"$_name\" class=\"$_css\" onchange=\"return $_event\" >";

	foreach ($_arrObj as $key => $value) {
		$key = trim($key);
		if (trim($_default) == trim($key)) {
			echo "<option value=\"$key\" selected=selected>$value</option>";
		} else {
			echo "<option value=\"$key\">$value</option>";
		}
	}
	echo "</select>";
}