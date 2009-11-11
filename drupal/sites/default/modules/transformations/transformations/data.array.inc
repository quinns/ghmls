<?php
// $Id: data.array.inc,v 1.2 2009/06/01 19:38:59 jpetso Exp $
/**
 * @file
 * Transformations -
 * A framework for generic data transformation pipelines.
 *
 * Copyright 2008 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */

class TfArrayData extends TfConcreteData {
  protected function initialize($data, $importing = FALSE) {
    if (!is_array($data)) {
      throw new Exception(
        'Tried to initialize a TfArrayData object with a non-array! Data: '.
        print_r($data, TRUE)
      );
    }
    if ($importing) {
      foreach ($data as $key => $exportedData) {
        $data[$key] = TfDataWrapper::import($exportedData);
      }
    }
    parent::initialize($data);
  }

  protected function serializableData() {
    $serialized = array();
    foreach ($this->children(self::ChildrenAlwaysWrapped) as $key => $childWrapper) {
      $serialized[$key] = is_null($childWrapper) ? NULL : $childWrapper->export();
    }
    return $serialized;
  }

  public function isIteratable() {
    return TRUE;
  }

  protected function rawChildElementIterator() {
    $arrayObject = new ArrayObject($this->data);
    return $arrayObject->getIterator();
  }
}
