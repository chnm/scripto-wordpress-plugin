<div id="scripto">

<p><?php echo $this->get_navigation(); ?></p>

<style type="text/css">
	#scripto-diff tr {border: none !important;}
	#scripto-diff td {padding: 2px !important;}
	td.diff-marker {width: 10px;}
	td.diff-deletedline {background-color: #FFEDED;}
	td.diff-addedline {background-color: #EDFFEF;}
	ins.diffchange {background-color: #BDFFC8;}
	del.diffchange {background-color: #FFBDBD;}
</style>

<h2>Revision Difference for <?php if ( '1' == $_GET['scripto_ns_index'] ): ?>Talk: <?php endif; ?><cite><?php echo $doc->getPageName(); ?></cite></h2>
<h3>in <cite><?php echo $doc->getTitle(); ?></cite></h3>

<p><a href="<?php echo $url_transcribe; ?>">transcribe page</a> | <a href="<?php echo $url_talk; ?>">discuss page</a> | <a href="<?php echo $url_document; ?>">view document</a></p>

<table id="scripto-diff">
	<thead>
	<tr>
		<th colspan="2"><?php echo $col_1_header; ?></th>
		<th colspan="2"><?php echo $col_2_header; ?></th>
	</tr>
	</thead>
	<tbody>
		<?php echo $this->_scripto->getRevisionDiff( $_GET['scripto_old_rev_id'], $_GET['scripto_rev_id'] ) ?>
	</tbody>
</table>

<h2>Revision as of <?php echo $revision_as_of; ?></h2>
<div><?php echo $revision_html; ?></div>

</div>