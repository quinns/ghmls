<?php
// $Id: data.object.inc,v 1.2 2009/06/01 19:38:59 jpetso Exp $
/**
 * @file
 * Transformations -
 * A framework for generic data transformation pipelines.
 *
 * Copyright 2008 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */

class TfObjectData extends TfConcreteData {
  protected function initialize($data, $importing = FALSE) {
    if (!is_object($data)) {
      throw new Exception(
        'Tried to initialize a TfObjectData object with a non-object! Data: '.
        print_r($data, TRUE)
      );
    }
    parent::initialize($data, $importing);
  }

  protected function serializableData() {
    if ($this->data instanceof Serializable) {
      return $this->data;
    }
    return TfDataWrapper::SerializationNotSupported;
  }

  public function isIteratable() {
    return TRUE;
  }

  protected function rawChildElementIterator() {
    if ($this->data instanceof IteratorAggregate) {
      return $this->data->getIterator();
    }
    elseif ($this->data instanceof Iterator) {
      return $this->data;
    }
    else {
      // Just iterate over the object's public properties. ArrayObject is
      // nice enough to provide an iterator that does this for us, so we don't
      // need to extract the public properties for ourselves.
      $arrayObject = new ArrayObject($this->data);
      return $arrayObject->getIterator();
    }
  }
}
