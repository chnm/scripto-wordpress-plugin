<table>
	<thead>
	<tr>
		<th>Changed on</th>
		<th>Changed by</th>
		<th>Size (bytes)</th>
		<th>Action</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ( $doc->getTranscriptionPageHistory() as $revision ): ?>
	<tr>
		<td><?php echo date( 'H:i:s M d, Y', strtotime( $revision['timestamp'] ) ); ?></td>
		<td><?php echo $revision['user']; ?></td>
		<td><?php echo $revision['size']; ?></td>
		<td><?php echo ucfirst($revision['action']); ?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
</table>