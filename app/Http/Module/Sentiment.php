<?php
namespace App\Http\Module;

class Sentiment {

	private $dataFolder = '';

	private $dictionary = array();

	private $minTokenLength = 1;

	private $maxTokenLength = 15;

	private $classes = array('pos', 'neg');

	private $classTokCounts = array(
		'pos' => 0,
		'neg' => 0,
	);

	private $tokCount = 0;


	private $prior = array(
		'pos' => 0.5,
		'neg' => 0.5,
	);

	public function __construct($dataFolder = false) {

		$this->setDataFolder($dataFolder);

		$this->loadDefaults();
	}

	public function score($sentence) {


		$tokens = $this->_getTokens($sentence);

		$total_score = 0;

		$scores = array();

		foreach ($this->classes as $class) {

			$scores[$class] = 1;

			foreach ($tokens as $token) {

				if (strlen($token) > $this->minTokenLength && strlen($token) < $this->maxTokenLength) {
					if (isset($this->dictionary[$token][$class])) {
						$count = $this->dictionary[$token][$class];
					} else {
						$count = 0;
					}

					$scores[$class] *= ($count + 1);
				}
			}

			$scores[$class] = $this->prior[$class] * $scores[$class];
		}

		foreach ($this->classes as $class) {
			$total_score += $scores[$class];
		}

		foreach ($this->classes as $class) {
			$scores[$class] = round($scores[$class] / $total_score, 3);
		}

		arsort($scores);

		return $scores;
	}

	public function setDictionary($class) {
		$fn = "{$this->dataFolder}data.{$class}.php";

		if (file_exists($fn)) {
			$temp = file_get_contents($fn);
			$words = unserialize($temp);
		} else {
			echo 'File does not exist: ' . $fn;
		}

		foreach ($words as $word) {

			$word = trim($word);

			if (!isset($this->dictionary[$word][$class])) {
				$this->dictionary[$word][$class] = 1;
			}

			$this->classTokCounts[$class]++;
			$this->tokCount++;
		}

		return true;
	}

	public function setDataFolder($dataFolder = false, $loadDefaults = false){
		if($dataFolder == false){
			$this->dataFolder = __DIR__ . '/data/';
		}
		else{
			if(file_exists($dataFolder)){
				$this->dataFolder = $dataFolder;
			}
			else{
				echo 'Error: could not find the directory - '.$dataFolder;
			}
		}

		if($loadDefaults !== false){
			$this->loadDefaults();
		}
	}

	private function loadDefaults(){
		foreach ($this->classes as $class) {
			if (!$this->setDictionary($class)) {
				echo "Error: Dictionary for class '$class' could not be loaded";
			}
		}

		if (!isset($this->dictionary) || empty($this->dictionary))
			echo 'Error: Dictionaries not set';

	}

	private function _getTokens($string) {

		$string = str_replace("\r\n", " ", $string);

		$string = strtolower($string);

		$matches = explode(' ', $string);

		return $matches;
	}
}

?>
