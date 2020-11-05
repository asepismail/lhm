Info! :                 
<br>
<?
	echo '<pre>';
	print_r( $data_info );
	echo '</pre>';
?> 

<?= anchor( 'M_gang_activity/edit/'.$id, 'Edit' ); ?>
<br>
<?= anchor( 'M_gang_activity/enroll/', 'Enroll (list)' ); ?>
<br>
