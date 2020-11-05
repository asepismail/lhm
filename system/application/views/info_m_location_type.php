Info! :                 
<br>
<?
	echo '<pre>';
	print_r( $data_info );
	echo '</pre>';
?> 

<?= anchor( 'M_location_type/edit/'.$id, 'Edit' ); ?>
<br>
<?= anchor( 'M_location_type/enroll/', 'Enroll (list)' ); ?>
<br>
