<script type="text/javascript">
$(document).ready(function() {
	Rum.defaultAjaxStartHandler = function(params) {
		console.log('form submit begin');
		$("#loaded").hide();
		$("#loading").fadeIn();
	};
	Rum.defaultAjaxCompletionHandler = function(params){
		console.log('submit completed!');
		$("#loading").hide();
		$("#loaded").fadeIn();
	};
});
</script> 

<div id="header">
     <div class="header_content">
        
    <a  id="logo" href="#" rel="home"> seo werx</a>
           <div class="nav1">
             <input name="page_form_url" id="page_form_url" title="" value="" type="url">	
             <span id="page_form_url_error" style="display:none; "></span>     
             <div style="visibility:hidden">
                 <button onclick="return Rum.submit(Rum.id('page_form'));" name="page_form_submit" id="page_form_submit" type="submit" onchange="" onblur="" onkeyup="">submit</button>
             </div>
        
            </div>
</div>
    
   
         

<?php $this->form->begin() ?>

<!--<p>Enter a URL in the box below to generate an index of webpages</p>-->

<fieldset id="loaded" style="background-color: #CCCCCC; margin-top: 50px; height: 345px; vertical-align: middle; ">
    <h3>What do search engines see ?</h3>
    <h4>Input your website url below and see your rank against the competition</h4>
  <div style="text-align:center; clear: both; width: auto; margin: 20px 0;">
    <!--<label  style="width:auto;float:none;">Enter the website URL:</label>*/-->
    <?php $this->form->url->render() ?>
	<?php $this->form->url_error->render() ?>
        <div style="visibility:hidden"><?php $this->form->submit->render() ?></div>
  </div>

</fieldset>

<?php $this->form->end() ?>

<div id="loading" style="display:none;text-align:center;">

<object width="242" height="28">
<param name="movie" value="<?php echo \Rum::config()->uri ?>/res/assets/progressbar.swf" />
<embed src="<?php echo \Rum::config()->uri ?>/res/assets/progressbar.swf" width="242" height="28">
</embed>
</object>

<br />Please wait while I scan your website...

</div>

<div id="analyzing" style="display:none;text-align:center;">

<object width="242" height="28">
<param name="movie" value="<?php echo \Rum::config()->uri ?>/resources/flash/progressbar.swf" />
<embed src="<?php echo \Rum::config()->uri ?>/resources/flash/progressbar.swf" width="242" height="28">
</embed>
</object>

<br />Please wait while I analyze your website...

</div>

<div id="footer">
    <ul class="copyright">
        <li><a href="#"> copyright 2014 SEO WERX</a></li>
        
    </ul>
    
   
           
    <ul class="website">
                <li><a href="#" class="current">website by commerx</a></li>
                
                
            </ul>
      
</div>

