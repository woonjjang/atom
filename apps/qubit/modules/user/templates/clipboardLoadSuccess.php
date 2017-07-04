<?php decorate_with('layout_1col.php') ?>

<?php slot('title') ?>

  <h1><?php echo __('Load clipboard') ?></h1>

<?php end_slot() ?>

<?php slot('content') ?>

  <?php echo $form->renderFormTag(url_for(array('module' => 'user', 'action' => 'clipboardLoad'))) ?>

    <?php echo $form->renderHiddenFields() ?>

    <div id="content">

      <fieldset class="collapsible">

        <div class="fieldset-wrapper">
          <?php echo $form->password->label(__('Password'))->renderRow() ?>
        </div>

        <div class="fieldset-wrapper">
          <?php echo $form->mode->label(__('Mode'))->renderRow() ?>
        </div>

      </fieldset>

    </div>

    <section class="actions">
      <ul>
        <li><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Load') ?>"/></li>
      </ul>
    </section>

  </form>

<?php end_slot() ?>
