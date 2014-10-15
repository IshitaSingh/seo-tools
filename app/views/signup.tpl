<?php // this is the template file index.tpl ?>

<?php $this->form->begin() ?>

Signup to receive report via email
<fieldset id="loaded" style="background-color: #CCCCCC; margin-top: 50px; height: 345px; vertical-align: middle;">

  <div style="text-align:center;">
    <label style="width:auto;float:none;">Signup:</label>
    <?php $this->form->name->render() ?><?php $this->form->name_error->render() ?>
	<?php $this->form->email->render() ?><?php $this->form->email_error->render() ?>
	<?php $this->form->accept_terms->render() ?><?php $this->form->accept_terms_error->render() ?>
    <?php $this->form->submit->render() ?>
  </div>

</fieldset>

<?php $this->form->end() ?>
