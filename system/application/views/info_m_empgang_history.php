Info! :                 
<br>
<?
	echo '<pre>';
	print_r( $data_info );
	echo '</pre>';
?> 

<?= anchor( 'M_empgang_history/edit/'.$id, 'Edit' ); ?>
<br>
<?= anchor( 'M_empgang_history/enroll/', 'Enroll (list)' ); ?>
<br>
