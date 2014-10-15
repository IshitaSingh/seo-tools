<?php // this is the template file index.tpl ?>

<h1 style="margin-top: 40px;">Site Index</h1>
<div style="float:right">
	<a href="<?=\Rum::uri('report', array('id'=>$website["website_id"]))?>" class="button">Analyze</a>
</div>

<p>The following pages will be analyzed</p>

<table>

  <thead>
    <tr>
      <th></th>
      <th>URL</th>
      <th>Last Crawled</th>
      <th>Status</th>
    </tr>
  </thead>

  <tfoot>
    <tr>
      <td colspan="3"></td>
      <td colspan="2" style="text-align: right;"><?php echo count( $pages ) ?> Pages</td>
    </tr>
  </tfoot>

  <tbody>

<?php $i=0; ?>
<?php foreach( $pages as $page ) : ?>

    <tr class="<?php echo ($i++%2)?'row_alt':'row'; ?>" onmouseover="document.getElementById('del_<?php echo $page['webpage_id'] ?>').style.visibility='visible'" onmouseout="document.getElementById('del_<?php echo $page['webpage_id'] ?>').style.visibility='hidden'">
      <td style="width:16px;"></td>
      <td style="width:100%;white-space:wrap;"><?php echo htmlentities( $page['url'] ) ?></td>
      <td><?php echo date( 'F j, Y g:ia', strtotime( $page['last_crawled'] )) ?></td>
      <td><?php echo $page['http_status'] ?></td>
    </tr>

<?php endforeach; ?>

  </tbody>

</table>
