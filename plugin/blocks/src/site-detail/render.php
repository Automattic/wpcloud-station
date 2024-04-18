<?php



if (get_post_type() !== 'wpcloud_site') {
	return;
}

error_log(print_r(the_post(),true));



echo $content;