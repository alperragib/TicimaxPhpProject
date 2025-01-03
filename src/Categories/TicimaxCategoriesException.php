<?php

	namespace Hasokeyk\Ticimax\Categories;

	use Exception;

	class TicimaxCategoriesException extends \Exception{

		private $categories_response;

		public function __construct($message, $code = 0, Exception $previous = null, $customData = null){
			parent::__construct($message, $code, $previous);
			return $message;
		}

	}