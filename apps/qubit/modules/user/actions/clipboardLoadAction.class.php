<?php

/*
 * This file is part of the Access to Memory (AtoM) software.
 *
 * Access to Memory (AtoM) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Access to Memory (AtoM) is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Access to Memory (AtoM).  If not, see <http://www.gnu.org/licenses/>.
 */

class UserClipboardLoadAction extends DefaultEditAction
{
  // Arrays not allowed in class constants
  public static
    $NAMES = array(
      'password',
      'mode');

  protected function earlyExecute()
  {

  }

  protected function addField($name)
  {
    switch ($name)
    {
      case 'password':
        $this->form->setValidator('password', new sfValidatorString(array('required' => true)));
        $this->form->setWidget('password', new sfWidgetFormInput);

        break;

      case 'mode':
        $this->form->setDefault('mode', 'merge');
        $this->form->setValidator('mode', new sfValidatorString);
        $choices = array('merge' => 'Merge', 'replace' => 'Replace');
        $this->form->setWidget('mode', new sfWidgetFormSelect(array('choices' => $choices)));

        break;
    }
  }

  protected function processField($field)
  {
    switch ($field->getName())
    {
      case 'password':
        $this->password = $this->form->getValue($field->getName());

        break;

      case 'mode':
        $this->mode = $this->form->getValue($field->getName());

        break;
    }
  }

  public function execute($request)
  {
    parent::execute($request);

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getPostParameters());

      if ($this->form->isValid())
      {
        $this->processForm();

        // Attempt to add saved clipboard to user's clipboard and notify user of results
        if (null === $addedCount = $this->addSavedClipboardItems($this->password, $this->mode))
        {
          $message = $this->context->i18n->__('Incorrect password for saved clipboard.');
          $this->context->user->setFlash('error', $message);
        }
        else
        {
          $message = $this->context->i18n->__('Clipboard loaded (%1% slugs added).', array('%1%' => $addedCount));
          $this->context->user->setFlash('info', $message);
        }

        $this->redirect(array('module' => 'user', 'action' => 'clipboard'));
      }
    }
  }

  private function addSavedClipboardItems($password, $mode)
  {
    // Get saved clipboard corresponding to password
    $criteria = new Criteria;
    $criteria->add(QubitClipboardSave::PASSWORD, $password);
    $save = QubitClipboardSave::getOne($criteria);

    if ($save === null)
    {
      return null;
    }

    // Clear clipboard if in replace mode
    if ($mode == 'replace')
    {
      $this->context->user->getClipboard()->clear();
    }

    // Get item details for saved clipboard
    $criteria = new Criteria;
    $criteria->add(QubitClipboardSaveItem::SAVE_ID, $save->id);
    $items = QubitClipboardSaveItem::get($criteria);

    // Create array representing item details for saved clipboard
    $addItems = array();
    foreach($items as $item)
    {
      if (!isset($addItems[$item->itemClassName]))
      {
        $addItems[$item->itemClassName] = array();
      }
      array_push($addItems[$item->itemClassName], $item->slug);
    }

    // Attempt to add items to user's clipboard
    return $this->context->user->getClipboard()->addItems($addItems);
  }
}
