<?php

namespace App\Exceptions;

use Exception;


// Create a base exception
abstract class EmloNotFoundException extends Exception {}

// Make your specific exceptions extend it
class EmloParamNotFoundException extends EmloNotFoundException {}
class EmloResponseNotFoundException extends EmloNotFoundException {}
class EmloPathIdNotFoundException extends EmloNotFoundException {}
class EmloParamValueNotFoundException extends EmloNotFoundException {}
class EmloSegmentParamNotFoundException extends EmloNotFoundException {}
class EmloParamSpecNotFoundException extends EmloNotFoundException {}
