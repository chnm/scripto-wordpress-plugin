<h2>Page History for <cite><?php echo $doc->getPageName(); ?></cite></h2>
<h3>in <cite><?php echo $doc->getTitle(); ?></cite></h3>

<?php if ( $history ): ?>
<table>
	<thead>
	<tr>
		<th>Compare Changes</th>
		<th>Changed on</th>
		<th>Changed by</th>
		<th>Size (bytes)</th>
		<th>Action</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ( $history as $revision ): ?>
	<tr>
		<td><?php echo $revision['compare_changes']; ?></td>
		<td><?php echo $revision['changed_on']; ?></td>
		<td><?php echo $revision['changed_by']; ?></td>
		<td><?php echo $revision['size']; ?></td>
		<td><?php echo $revision['action']; ?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<p>There is no history for this page.</p>
<?php endif; ?>