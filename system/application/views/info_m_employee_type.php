Info! :                 
<br>
<?
	echo '<pre>';
	print_r( $data_info );
	echo '</pre>';
?> 

<?= anchor( 'M_employee_type/edit/'.$id, 'Edit' ); ?>
<br>
<?= anchor( 'M_employee_type/enroll/', 'Enroll (list)' ); ?>
<br>
