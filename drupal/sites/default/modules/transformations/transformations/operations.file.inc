<?php
// $Id: operations.file.inc,v 1.5 2009/06/23 19:15:19 jpetso Exp $
/**
 * @file
 * Transformations -
 * A framework for generic data transformation pipelines.
 *
 * Copyright 2009 by Jakob Petsovits ("jpetso", http://drupal.org/user/56020)
 */


/**
 * Helper class for providing implementations for standard input/output info.
 */
class TfFileConnectionInfoHelper {
  public static function filepathInfo($propertyKey) {
    switch ($propertyKey) {
      case 'label':
        return t('File path');

      case 'description':
        return t('The path in the local file system where the file is located.');

      case 'expectedType':
        return 'php:type:string';
    }
  }
}


/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_operation_TfDirAndBaseNamesFromFilePath() {
  return array(
    'category' => t('Files and directories'),
    'label' => t('Split file path'),
    'description' => t('Extracts the directory and base name parts of a file path. (In case the file path is relative, "." will be output as directory name.)'),
  );
}

class TfDirAndBaseNamesFromFilePath extends TfOperation {
  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('filepath');
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'filepath') {
      return TfFileConnectionInfoHelper::filepathInfo($propertyKey);
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array('dirname', 'basename');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    if ($outputKey == 'dirname') {
      switch ($propertyKey) {
        case 'label':
          return t('Directory name');

      case 'description':
        return t('The directory part of the path, e.g. "/home/user" if the path is "/home/user/file.txt". If the path is relative, "." will be returned for this output.');

        case 'expectedType':
          return 'php:type:string';
      }
    }
    elseif ($outputKey == 'dirname') {
      switch ($propertyKey) {
        case 'label':
          return t('Base name');

      case 'description':
        return t('The filename part of the path, e.g. "file.txt" if the path is "/home/user/file.txt".');

        case 'expectedType':
          return 'php:type:string';
      }
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    $path = trim($this->input('filepath')->data());
    $output->set('dirname', dirname($path));
    $output->set('basename', basename($path));
  }
}


/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_operation_TfWholeStringAtOnceFromFile() {
  return array(
    'category' => t('Files and directories'),
    'label' => t('Read file contents (at once)'),
    'description' => t('Retrieves the full contents of a file at once.'),
  );
}

class TfWholeStringAtOnceFromFile extends TfOperation {
  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('filepath');
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'filepath') {
      return TfFileConnectionInfoHelper::filepathInfo($propertyKey);
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array('contents');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    if ($outputKey == 'contents') {
      switch ($propertyKey) {
        case 'label':
          return t('Text contents');

        case 'description':
          return t('The whole text contents that has been fetched from the file.');

        case 'expectedType':
          return 'php:type:string';
      }
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    $filename = trim($this->input('filepath')->data());
    $contents = @file_get_contents($filename);
    if ($contents === FALSE) {
      $output->setErrorMessage(t('Unable to open file "!filename".', array(
        '!filename' => $filename,
      )));
      return;
    }
    $output->set('contents', $contents);
  }
}


/**
 * Implementation of [module]_operation_[class]().
 */
function transformations_operation_TfTextLinesFromFile() {
  return array(
    'category' => t('Files and directories'),
    'label' => t('Read file contents (line by line)'),
    'description' => t('Retrieves the contents of a file line by line, which is more memory efficient than reading everything at once.'),
  );
}

class TfTextLinesFromFile extends TfOperation {
  /**
   * Implementation of TfOperation::inputs().
   */
  protected function inputs() {
    return array('filepath');
  }

  /**
   * Implementation of TfOperation::inputInfo().
   */
  protected function inputInfo($inputKey, $propertyKey) {
    if ($inputKey == 'filepath') {
      return TfFileConnectionInfoHelper::filepathInfo($propertyKey);
    }
  }

  /**
   * Implementation of TfOperation::outputs().
   */
  protected function outputs() {
    return array('textLines');
  }

  /**
   * Implementation of TfOperation::outputInfo().
   */
  protected function outputInfo($outputKey, $propertyKey) {
    if ($outputKey == 'textLines') {
      switch ($propertyKey) {
        case 'label':
          return t('Text lines');

        case 'expectedType':
          return 'transformations:list<php:type:string>';
      }
    }
  }

  /**
   * Implementation of TfOperation::execute().
   */
  protected function execute(TfOutput $output) {
    $filename = trim($this->input('filepath')->data());
    $file_handle = @fopen($filename, 'r');
    if ($file_handle == FALSE) {
      $output->setErrorMessage(t('Unable to open file "!filename".', array(
        '!filename' => $filename,
      )));
      return;
    }
    $output->set('textLines', new TfTextLinesFromFileIterator($file_handle));
  }
}

class TfTextLinesFromFileIterator implements Iterator {
  private $handle;
  private $currentLine;

  public function __construct($file_handle) {
    $this->handle = $file_handle;
    $this->currentLine = NULL;
  }

  function __destruct() {
    if ($this->handle) {
      fclose($this->handle);
    }
  }

  public function rewind() {
    if ($this->handle) {
      fseek($this->handle, 0);
      $this->next();
    }
  }

  public function next() {
    if ($this->handle) {
      $this->currentLine = feof($this->handle)
        ? NULL
        : trim(fgets($this->handle), "\r\n");
    }
  }

  public function valid() {
    return isset($this->currentLine);
  }

  public function current() {
    return $this->currentLine;
  }

  public function key() {
    return 'line';
  }
}
