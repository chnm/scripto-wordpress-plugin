<?php echo $this->get_navigation(); ?>

<h2>Recent Changes to Scripto</h2>

<?php if ( $recent_changes ): ?>
<table>
	<thead>
	<tr>
		<th>Changes</th>
		<th>Document Page Name</th>
		<th>Changed on</th>
		<th>Changed</th>
		<th>Changed By</th>
		<th>Document Title</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ( $recent_changes as $recent_change ): ?>
	<tr>
		<td><?php echo $recent_change['changes']; ?></td>
		<td><?php echo $recent_change['document_page_name']; ?></td>
		<td><?php echo $recent_change['changed_on']; ?></td>
		<td><?php echo $recent_change['length_changed']; ?></td>
		<td><?php echo $recent_change['user']; ?></td>
		<td><?php echo $recent_change['document_title']; ?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<p>There are no recent changes.</p>
<?php endif; ?>