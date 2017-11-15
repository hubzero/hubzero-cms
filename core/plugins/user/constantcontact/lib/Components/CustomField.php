<?php
class CustomField
{
	public $question;
	public $answers;

	/**
	 * @param string $question - registration question
	 * @param array $answers -  answers for question
	 */
	public function __construct($question, $answers = array())
	{
		$this->question = $question;
		$this->answers = $answers;
	}

	/**
	 * Create CustomField object from XML
	 * @static
	 * @param SimpleXMLElement $parsedXml
	 * @return CustomField
	 */
	public static function createFromXml($parsedXml)
	{
		$quest = (string) $parsedXml->Question;
		$answers = array();
		foreach ($parsedXml->Answers->Answer as $ans) {
			$answers[] = (string) $ans;
		}
		return new CustomField($quest, $answers);
	}
}
