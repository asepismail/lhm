Info! :                 
<br>
<?
	echo '<pre>';
	print_r( $data_info );
	echo '</pre>';
?> 

<?= anchor( 'M_location/edit/'.$id, 'Edit' ); ?>
<br>
<?= anchor( 'M_location/enroll/', 'Enroll (list)' ); ?>
<br>
