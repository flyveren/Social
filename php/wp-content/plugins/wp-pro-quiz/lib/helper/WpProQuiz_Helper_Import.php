<?php
class WpProQuiz_Helper_Import {
	
	private $_content = null;
	private $_error = false;
	
	public function setImportFileUpload($file) {
		if(!is_uploaded_file($file['tmp_name'])) {
			$this->setError(__('File was not uploaded', 'wp-pro-quiz'));
			return false;
		}
		
		$this->_content = file_get_contents($file['tmp_name']);
		
		return $this->checkCode();
	}
	
	public function setImportString($str) {
		$this->_content = $str;
		
		return $this->checkCode();
	}
	
	private function setError($str) {
		$this->_error = $str;
	}
	
	public function getError() {
		return $this->_error;
	}
	
	private function checkCode() {
		$code = substr($this->_content, 0, 13);
		
		$c = substr($code, 0, 3);
		$v1 = substr($code, 3, 5);
		$v2 = substr($code, 8, 5);
	
		if($c !== 'WPQ') {
			$this->setError(__('File have wrong format', 'wp-pro-quiz'));			
			return false;
		}
		
		if($v2 < 3) {
			$this->setError(__('File is not compatible with the current version'));
			return false;
		}
	
		return true;
	}
	
	public function getContent() {
		return $this->_content;
	}
	
	public function getImportData() {
		
		if($this->_content === null) {
			$this->setError(__('File cannot be processed', 'wp-pro-quiz'));
			return false;
		}
		
		$data = substr($this->_content, 13);
		
		$b = base64_decode($data);
		
		if($b === null) {
			$this->setError(__('File cannot be processed', 'wp-pro-quiz'));
			return false;
		}
		
		$check = $this->saveUnserialize($b, $o);

		if($check === false || !is_array($o)) {
			$this->setError(__('File cannot be processed', 'wp-pro-quiz'));
			return false;
		}
		
		unset($b);
		
		return $o;
	}
	
	public function saveImport($ids = false) {
		$data = $this->getImportData();
		
		if($data === false) {
			return false;
		}
		
		switch($data['exportVersion']) {
			case '3':
			case '4':
				return $this->importData($data, $ids, $data['exportVersion']);
				break;
		}
		
		return false;
	}
	
	private function importData($o, $ids = false, $version = '1') {
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$questionMapper = new WpProQuiz_Model_QuestionMapper();

		foreach($o['master'] as $master) {
			if(get_class($master) !== 'WpProQuiz_Model_Quiz') {
				continue;
			}
			
			$oldId = $master->getId();
			
			if($ids !== false) {
				if(!in_array($oldId, $ids)) {
					continue;
				}
			}
			
			$master->setId(0);
			
			if($version == 3) {
				if($master->isQuestionOnSinglePage()) {
					$master->setQuizModus(WpProQuiz_Model_Quiz::QUIZ_MODUS_SINGLE);
				} else if($master->isCheckAnswer()) {
					$master->setQuizModus(WpProQuiz_Model_Quiz::QUIZ_MODUS_CHECK);
				} else if($master->isBackButton()) {
					$master->setQuizModus(WpProQuiz_Model_Quiz::QUIZ_MODUS_BACK_BUTTON);
				} else {
					$master->setQuizModus(WpProQuiz_Model_Quiz::QUIZ_MODUS_NORMAL);
				}
			}
			
			$quizMapper->save($master);
			
			foreach($o['question'][$oldId] as $question) {
				
				if(get_class($question) !== 'WpProQuiz_Model_Question') {
					continue;
				}
				
				$question->setQuizId($master->getId());
				$question->setId(0);
								
				$questionMapper->save($question);
			}		
		}
		
		return true;
	}
	
	private function saveUnserialize($str, &$into) {
		$into = @unserialize($str);
		
		return $into !== false || rtrim($str) === serialize(false);
	}
}