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

class clipboardPruneTask extends arBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'cli'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
      new sfCommandOption('older-than', null, sfCommandOption::PARAMETER_OPTIONAL, 'Expiry date expressed as YYYY-MM-DD'),
      new sfCommandOption('force', 'f', sfCommandOption::PARAMETER_NONE, 'Delete without confirmation', null),
    ));

    $this->namespace = 'tools';
    $this->name = 'clipboard-prune';
    $this->briefDescription = 'Prune saved clipboards';
    $this->detailedDescription = <<<EOF
Prune saved clipboards (in entirely or by age)
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    parent::execute($arguments, $options);

    // Abort if not forced or confirmed
    if (!$options['force'] && !$this->getConfirmation($options))
    {
      $this->logSection('clipboard-prune', 'Aborted.');
      return;
    }

    // Assemble criteria
    $criteria = new Criteria;

    if (isset($options['older-than']))
    {
      $criteria->add(QubitClipboardSave::CREATED_AT, $options['older-than'], Criteria::LESS_THAN);
    }

    // Delete clipbooard saves and save items
    $deletedCount = 0;

    foreach(QubitClipboardSave::get($criteria) as $save)
    {
      $itemCriteria = new Criteria;
      $itemCriteria->add(QubitClipboardSaveItem::SAVE_ID, $save->id);

      foreach(QubitClipboardSaveItem::get($itemCriteria) as $item)
      {
        $item->delete();
      }

      $save->delete();

      $deletedCount++;
    }

    $this->logSection('clipboard-prune', sprintf('Finished! %d saved clipboards deleted.', $deletedCount));
  }

  private function getConfirmation($options)
  {
    $message = 'Are you sure you want to delete ';

    if (isset($options['older-than']))
    {
      $message .= 'saved clipboards older than '. $options['older-than'] .'?';
    }
    else
    {
      $message .= 'all saved clipboards?';
    }

    return $this->askConfirmation($message, 'QUESTION_LARGE', false);
  }
}
