<h1><?php echo __('Are you sure you want to delete %1% and all its descendants?', array('%1%' => render_title($menu))) ?></h1>

<?php echo $form->renderFormTag(url_for(array($menu, 'module' => 'menu', 'action' => 'delete')), array('method' => 'delete')) ?>

  <div class="actions section">
    <h2 class="element-invisible"><?php echo __('Actions') ?></h2>
    <div class="content">
      <ul class="clearfix links">
        <li><?php echo link_to(__('Cancel'), array($menu, 'module' => 'menu', 'action' => 'edit')) ?></li>
        <li><input class="form-submit danger" type="submit" value="<?php echo __('Delete') ?>"/></li>
      </ul>
    </div>
  </div>

</form>
