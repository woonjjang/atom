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

class UserStatusAction extends sfAction
{
  public function execute($request)
  {
    // Determine if slugs queried for are in clipboard
    $querySlugs = array();

    if ($request->getParameter('slugs'))
    {
      $querySlugs = explode(',', $request->getParameter('slugs'));
    }

    $slugsInClipboard = array();

    if (count($querySlugs))
    {
      foreach ($this->context->user->getClipboard()->getAll() as $slug)
      {
          if (in_array($slug, $querySlugs))
          {
            array_push($slugsInClipboard, $slug);
          }
      }
    }

    // Get clipboard counts and object types
    $countByType = $this->context->user->getClipboard()->countByType();
    $count = array_sum($countByType);

    $objectTypes = array(
      'QubitInformationObject' => sfConfig::get('app_ui_label_informationobject'),
      'QubitActor' => sfConfig::get('app_ui_label_actor'),
      'QubitRepository' => sfConfig::get('app_ui_label_repository'));

    // Summarize object type counts
    $objectCountDescriptions = array();
    foreach ($countByType as $objectType => $countType)
    {
      array_push($objectCountDescriptions, $this->context->i18n->__('%1% count: %2%', array('%1%' => $objectTypes[$objectType], '%2%' => $countType)));
    }

    // Amalgamate response data
    $response = array(
      'count' => $count,
      'countByType' => $countByType,
      'objectCountDescriptions' => $objectCountDescriptions,
      'slugs' => $slugsInClipboard
    );

    // Augment response data
    if ($this->context->user->isAuthenticated())
    {
      $response['username'] = $this->context->user->user->username;

      $response['gravatar'] = sprintf('https://www.gravatar.com/avatar/%s?s=%s',
        md5(strtolower(trim($this->context->user->user->email))),
        25
      );
    }

    return $this->renderText(json_encode($response));
  }
}
