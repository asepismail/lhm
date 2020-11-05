<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	function get_icon($icon_name) {
		return "<img src='".base_url()."public/images/icon/".$icon_name.".png"."'>";
	}

?>