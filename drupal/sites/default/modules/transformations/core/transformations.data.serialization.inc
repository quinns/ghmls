<?php
// $Id: transformations.data.serialization.inc,v 1.3 2009/06/27 14:14:47 jpetso Exp $
/**
 * @file
 * Transformations -
 * A framework for generic data transformation pipelines.
 *
 * Copyright 2009 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */

/**
 * Synchronous iteration over two iterators - a slightly reduced (and bugfixed,
 * and modified) version of DualIterator in the PHP examples directory. Written
 * by Marcus Boerger, but as example file there is no copyright claimed for
 * this class. Also adapted to match Drupal coding standards.
 */
class TfDualIterator implements Iterator {
  const CURRENT_LHS   = 0x01;
  const CURRENT_RHS   = 0x02;
  const CURRENT_ARRAY = 0x03;
  const CURRENT_0     = 0x00;

  const KEY_LHS       = 0x10;
  const KEY_RHS       = 0x20;
  const KEY_LHS_VALUE = 0x30;
  const KEY_RHS_VALUE = 0x40;
  const KEY_0         = 0x00;

  const DEFAULT_FLAGS = 0x13;

  private $lhs;
  private $rhs;
  private $flags;

  function __construct(Iterator $lhs, Iterator $rhs, $flags = 0x13 /* DualIterator::DEFAULT_FLAGS */) {
    $this->lhs   = $lhs;
    $this->rhs   = $rhs;
    $this->flags = $flags;
  }

  /**
   * Return the reft hand side iterator.
   */
  function getLHS() {
    return $this->lhs;
  }

  /**
   * Return the right hand side iterator.
   */
  function getRHS() {
    return $this->rhs;
  }

  function setFlags($flags) {
    $this->flags = $flags;
  }

  function getFlags() {
    return $this->flags;
  }

  function rewind() {
    $this->lhs->rewind();
    $this->rhs->rewind();
  }

  function next() {
    $this->lhs->next();
    $this->rhs->next();
  }

  function valid() {
    return $this->lhs->valid() && $this->rhs->valid();
  }

  /**
   * Return the current value depending on CURRENT_* flags.
   */
  function current() {
    switch ($this->flags & 0x0F) {
      default:
      case self::CURRENT_ARRAY:
        return array($this->lhs->current(), $this->rhs->current());
      case self::CURRENT_LHS:
        return $this->lhs->current();
      case self::CURRENT_RHS:
        return $this->rhs->current();
      case self::CURRENT_0:
        return NULL;
    }
  }

  /**
   * Return the key value depending on KEY_* flags.
   */
  function key() {
    switch ($this->flags & 0xF0) {
      default:
      case self::KEY_LHS:
        return $this->lhs->key();
      case self::KEY_RHS:
        return $this->rhs->key();
      case self::KEY_LHS_VALUE:
        return $this->lhs->current();
      case self::KEY_RHS_VALUE:
        return $this->rhs->current();
      case self::KEY_0:
        return NULL;
    }
  }
}

/**
 * An iterator that takes the keys (usable as is) and values (yet stored in the
 * serializable array format produced by TfDataWrapper::export()), and combines
 * them into a key/value iterator that yields the same values as the originally
 * serialized ones.
 */
class TfChildrenUnserializationIterator extends TfDualIterator {
  public function __construct($keys, $values) {
    parent::__construct(
      new ArrayIterator($keys), new ArrayIterator($values),
      TfDualIterator::KEY_LHS_VALUE | TfDualIterator::CURRENT_RHS
    );
  }

  public function current() {
    $value = parent::current();
    if (is_null($value) || (is_object($value) && $value instanceof TfDataWrapper)) {
      return $value;
    }
    $valueArray = $this->getRHS();
    $valueArray[$valueArray->key()] = TfDataWrapper::import($value);
    return $valueArray->current();
  }
}
