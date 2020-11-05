Info! :                 
<br>
<?
	echo '<pre>';
	print_r( $data_info );
	echo '</pre>';
?> 

<?= anchor( 'M_user_menu/edit/'.$id, 'Edit' ); ?>
<br>
<?= anchor( 'M_user_menu/enroll/', 'Enroll (list)' ); ?>
<br>
