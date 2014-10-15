<?php // this is the template file report.tpl ?>

<div class="noprint">

<div style="float:right">
  <input class="button" type="button" value="Print" onclick="print();" />
  <input class="button" type="button" value="Back" onclick="location.href='<?php echo \Rum::uri( 'results', array( 'q' => $website['url'] )) ?>'" />
</div>

<p>Details of the search engine optimization analysis report...</p>

</div>

<div>

<h2>Summary</h2>

<fieldset>

  <div>
    <label>URL:</label>
    <a target="_blank" href="<?php echo htmlentities( $website['url'] ) ?>"><?php echo htmlentities( $website['url'] ) ?></a>
  </div>

  <div>
    <label>No. of pages:</label>
    <?php echo count( $pages ) ?> pages
  </div>

  <div>
    <label>Average Score:</label>
    <?php
    for( $stars = 5, $i = (int) $score / 2; (int) $i > 0; $i--, $stars-- ) {
    	echo '<img src="' . \Rum::config()->uri . '/res/assets/icons/star.png" alt="star" />';
    }
    if( (int) $score % 2 ) {
    	echo '<img src="' . \Rum::config()->uri . '/res/assets/icons/half-star.png" alt="star" />';
    	$stars--;
    }
    for( ; $stars > 0; $stars-- ) {
    	echo '<img src="' . \Rum::config()->uri . '/res/assets/icons/grey-star.png" alt="star" />';
    }
    ?>
  </div>

</fieldset>

<h2>Pages analyzed</h2>

<ul>
  <?php foreach( $pages as $page ) : ?>
  <li><a href="#<?php echo htmlentities( basename( $page['url'] )) ?>"><?php echo htmlentities( basename( $page['url'] )) ?></a></li>
  <?php endforeach; ?>
</ul>

</div>


<!-- start page report -->
<?php foreach( $pages as $page ) : ?>

<div class="page">

<h1 id="<?php echo htmlentities( basename( $page['url'] )) ?>"><?php echo htmlentities( basename( $page['url'] )) ?></h1>

<div class="leftbox">
<fieldset>

  <div>
    <label>HTTP Status:</label>
    <?php echo $page['http_status'] ?>
  </div>

  <div>
    <label>Page Title:</label>
    <?php echo htmlentities( extract_segment_from_chunk( $page['content'], '<title>', '</title>' )) ?>
  </div>

  <div>
    <label>Meta-Description:</label>
    <?php echo ( $desc = get_meta_content( $page['content'], 'description' ))?htmlentities( adjust_length( $desc, 70 )):'&lt;not found&gt;' ?>
  </div>

  <div>
    <label>Meta-Keywords:</label>
    <?php echo ( $keywords = get_meta_content( $page['content'], 'keywords' ))?htmlentities( adjust_length( $keywords, 70 )):'&lt;not found&gt;' ?>
  </div>

  <div>
    <label>Targeted Phrases:</label>
    <?php $comma=false; foreach( $page['keywords'] as $keyword ) : ?>
      <?php if( $comma ) : ?>,<?php else : $comma = true ?><?php endif; ?>
      <?php echo $keyword ?>
    <?php endforeach; ?>
  </div>

</fieldset>
</div>

<div class="rightbox">
<fieldset>

  <div>
    <label>URL:</label>
    <a target="_blank" href="<?php echo htmlentities( $page['url'] ) ?>"><?php echo htmlentities( $page['url'] ) ?></a>
  </div>

  <div>
    <label>Page Load:</label>
    <?php echo number_format( (float) $page['response_time'], 4 ) ?> s
  </div>

  <div>
    <label>Page Size:</label>
    <?php echo number_format( (float) strlen( $page['content'] ) / 1024, 2 ) ?> KB
  </div>

  <div>
    <label>Word Count:</label>
    <?php echo str_word_count( parse_content( $page['content'], $page['url'], false )) ?> words
  </div>

  <div>
    <label>Keyword Density:</label>
    <?php echo number_format( $page['keyworddensity'] * 100, 2 ) ?> %
  </div>

  <div>
    <label>Page Rank:</label>
    0
  </div>

  <div>
    <label>Score:</label>
    <?php
    for( $stars = 5, $i = (int) $page['score'] / 2; (int) $i > 0; $i--, $stars-- ) {
    	echo '<img src="' . \Rum::config()->uri . '/res/assets/icons/star.png" alt="star" />';
    }
    if( (int) $page['score'] % 2 ) {
    	echo '<img src="' . \Rum::config()->uri . '/res/assets/icons/half-star.png" alt="star" />';
    	$stars--;
    }
    for( ; $stars > 0; $stars-- ) {
    	echo '<img src="' . \Rum::config()->uri . '/res/assets/icons/grey-star.png" alt="star" />';
    }
    ?>
    <?php // echo $page['score'] ?>
  </div>

</fieldset>
</div>

<div style="clear: both;"></div>

<div class="leftbox">

<h2>Warnings</h2>
<ul class="messages">
  <?php if( $page['warnings'] ) : ?>
  <?php foreach( $page['warnings'] as $warning ) : ?>
  <li class="fail"><?php echo $warning ?></li>
  <?php endforeach; ?>
  <?php else : ?>
  <li class="pass">No Warnings</li>
  <?php endif; ?>
</ul>

</div>
<div class="rightbox">

<h2>Recommendations</h2>
<ul class="messages">
  <?php if( $page['warnings'] ) : ?>
  <?php foreach( $page['recommendations'] as $recommendation ) : ?>
  <li class="notice"><?php echo $recommendation ?></li>
  <?php endforeach; ?>
  <?php else : ?>
  <li class="pass">No Recommendations</li>
  <?php endif; ?>
</ul>

</div>

<div style="clear: both;"></div>

<h2>Spider View</h2>
<?=\parse_content($page["content"], $page["url"], false) ?>
<!--<iframe src="<?php echo \Rum::url( 'preview', array( 'id' => $page['webpage_id'] )) ?>"></iframe>-->

</div>

<?php endforeach; ?>
<!-- end page report -->
