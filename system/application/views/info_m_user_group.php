Info! :                 
<br>
<?
	echo '<pre>';
	print_r( $data_info );
	echo '</pre>';
?> 

<?= anchor( 'M_user_group/edit/'.$id, 'Edit' ); ?>
<br>
<?= anchor( 'M_user_group/enroll/', 'Enroll (list)' ); ?>
<br>
