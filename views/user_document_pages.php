<?php echo $this->get_navigation(); ?>

<h2>Your Contributions</h2>

<?php if ( $user_document_pages ): ?>
<table>
	<thead>
	<tr>
		<th>Document Page Name</th>
		<th>Most Recent Contribution</th>
		<th>Document Title</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ( $user_document_pages as $user_document_page ): ?>
	<tr>
		<td><?php echo $user_document_page['document_page_name']; ?></td>
		<td><?php echo $user_document_page['most_recent_contribution']; ?></td>
		<td><?php echo $user_document_page['document_title']; ?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<p>You have no contributions.</p>
<?php endif; ?>