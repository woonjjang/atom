<?php

/*
 * This file is part of Qubit Toolkit.
 *
 * Qubit Toolkit is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Qubit Toolkit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Qubit Toolkit.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Generate  listSets response of the OAI-PMH protocol for the qubit toolkit
 *
 * @package    qubit
 * @subpackage oai
 * @version    svn: $Id: listSetsComponent.class.php 10288 2011-11-08 21:25:05Z mj $
 * @author     Mathieu Fortin Library and Archives Canada <mathieu.fortin@lac-bac.gc.ca>
 */
class qtOaiPluginlistSetsComponent extends sfComponent
{
  /**
   * Executes action
   *
   * @param sfRequest $request A request object
   */
  public function execute($request)
  {
    $request->setRequestFormat('xml');
    $this->date = QubitOai::getDate();
    $this->path = $this->request->getUriPrefix().$this->request->getPathInfo();
    $this->attributes = $this->request->getGetParameters();

    $this->attributesKeys = array_keys($this->attributes);
    $this->requestAttributes = '';
    foreach ($this->attributesKeys as $key)
    {
      $this->requestAttributes .= ' '.$key.'="'.$this->attributes[$key].'"';
    }
    $this->sets = array();

    foreach (QubitInformationObject::getCollections() as $el)
    {
      $this->sets[] = new sfIsadPlugin($el);
    }
  }
}
