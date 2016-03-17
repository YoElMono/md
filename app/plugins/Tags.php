<?php
use Phalcon\Events\Event,
	Phalcon\Mvc\User\Plugin,
	Phalcon\Acl;

class Tags extends Plugin
{



	
	//private $_fontSizeArray=array('12','14','16','18','20','22','24','26','28','30');
	private $_fontSizeArray=array('6','7','8','9','10','11','12','13');
	private $_lightColorArray=array('#0196ef','#ea7603','#ee8207','#F0F8FF','#FFF0F5','#FFE4B5','#EEE8AA','#98FB98','#B0C4DE','#cccccc','	#0000CD','#FDF5E6');
	private $_darkColorArray=array('#0196ef','#ea7603','#ee8207','#FF0000','#00008B','#A52A2A','#cccccc','#008000','#C71585','#8B4513','#008080','#2F4F4F');
	private $_ignoreArray=array('?','of','the','is','off','you','them','then','at','with','i','it','We','we');
	private $_ignoreCharArray=array('?','!','.',',','~','@','#','$','%','^','&','*','(',')','-','_','+','=','<','>','/','[',']','{','}',':',';','"',"'",'~','`');
	var $Total = 260;



	public function SinHtml($texto){
		$texto = @eregi_replace("<head[^>]*>.*</head>"," ",$texto);
		$texto = @eregi_replace("<script[^>]*>.*</script>"," ",$texto);
		$texto = @eregi_replace("<style[^>]*>.*</style>"," ",$texto);
		$texto = @eregi_replace("<[^>]*>"," ",$texto);
		$texto = @eregi_replace("&nbsp;","",$texto);
		return $texto ;
	}



	/**
	 * Function to genrate from a string
	 *
	 * @param string $tagName
	 * @param string $tagLink
	 * @param int $tagRating (optional) Note: range 0-9)
	 * @param string $className (optional) Note: It would override default stysheet
	 * @param boolean $useLightColors
	 *
	 * @return string
	 *
	 * @example generateTagCloudFromString("Rochak Chauhan","www.rochakchauhan.com",3);
	 */
	public function generateTagCloudFromString($tagName,$tagLink,$tagRating=DEFAULT_TAG_RATING,$className="",$useLightColors=USE_LIGHT_COLORS) {
		$return="";

		$tagName=trim(strip_tags($tagName));
		$tagLink=trim(strip_tags($tagLink));
		$tagRating=(int)$tagRating;
		$tagRating=($tagRating<0)?0:$tagRating;
		$tagRating=($tagRating>9)?9:$tagRating;
		$size=$this->_fontSizeArray[$tagRating]."px";
		if ($useLightColors)
		$color=$this->_lightColorArray[$tagRating];
		else
		$color=$this->_darkColorArray[$tagRating];

		if($tagName=="" && $tagLink=="") {
			trigger_error("ERROR: Empty parameters in ".__FUNCTION__, E_USER_ERROR);
		}

		if(trim($className)=="") {
			if(UNDERLINE_LINKS==1) {
				$return = " <span><a href='".$tagLink."".$tagName."' style='color:$color;font-size:$size;'>$tagName</a></span> ";
			}
			else {
				$return = " <span><a href='".$tagLink."".$tagName."' style='text-decoration:none;color:$color;font-size:$size;'>$tagName</a></span> ";
			}
		}
		else {
			$return="<span><a href='$tagLink' class='$className'>$tagName</a></span>";
		}
		return $return;
	}

	/**
	 * Function to generate tag cloud from an array
	 *
	 * @param string $tagArray
	 *
	 * @return string
	 *
	 * @example $tagArray=array("Rochak Chauhan,http://www.rochakchauhan.com,4",
	 *			   				"DMW Validator,http://www.dmwvalidator.com,6",
	 *			   				"PHP Classes,http://www.phpclasses.org,9"
	 *			  				);
	 *
	 */
	public function generateTagCloudFromArray($tagArray) {
		$return='';
		if(!is_array($tagArray)) {
			trigger_error("ERROR: Parameter can only be an array",E_USER_ERROR);
		}
		$count=count($tagArray);
		for ($i=0;$i<$count;$i++){
			$tmpArray=explode(',',$tagArray[$i]);
			if(!is_array($tmpArray) && count($tmpArray)<2) {
				trigger_error("ERROR: Invalid Array type in ".__FUNCTION__,E_USER_ERROR);
			}
			list($tagName, $tagLink, $tagRating, $className, $useLightColors) = $tmpArray;
			// set default values
			if (trim($tagRating)==""){
				$tagRating=DEFAULT_TAG_RATING;
			}
			if (trim($useLightColors)==""){
				$useLightColors=USE_LIGHT_COLORS;
			}
			$return.=$this->generateTagCloudFromString($tagName,$tagLink,$tagRating,$className,$useLightColors);
		}
		return $return;
	}

	/**
	 * Fucntion to generate tag cloud from a csv file
	 *
	 * @param string $csvFileName
	 * @return string
	 */
	public function generateTagCloudFromCsv($csvFileName) {
		if(!file_exists($csvFileName) || !is_readable($csvFileName)){
			trigger_error("ERROR: Failed to open or find the file: $csvFileName", E_USER_ERROR);
		}
		$fileArray=file($csvFileName);
		return $this->generateTagCloudFromArray($fileArray);
	}

	/**
	 * Function to extract and return tags from a paragraph/text
	 *
	 * @param string $string
	 * @access public
	 * 
	 * @return array
	 */
	public function generateTagsFromText($string){
		$returnArray=array();
		$tmpArray=explode(" ",$string);		
		$count=count($tmpArray);
		for ($i=0;$i<$count;$i++){
			$word=trim($tmpArray[$i]);
			$word=strip_tags($word);
			$word=$this->removeSpecialChars($word);
			if(!in_array($word,$this->_ignoreArray) && @strlen($word)>@MINIMUM_TAG_LENGTH) {
				$returnArray[]=$word;
			}
		}
		return $returnArray;
	}
	
	/**
	 * Function to generate tag cloud from a paragraph/text
	 *
	 * @param string $string
	 * @access public
	 * 
	 * @return string;
	 */
	public function generateTagCloudFromText($string , $url){
		$return='';
		$defaultLink="http://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
		$defaultLink = $url . "tag=";

		$tagArray=$this->generateTagsFromText($string);
		$count=count($tagArray);
		if( $count > $this->Total ){
			$count = $this->Total;
		}
		for ($i=0;$i<$count;$i++) {
			$return.= $this->generateTagCloudFromString($tagArray[$i],$defaultLink,rand(0,9));		
		}
		return $return;
	}

	/**
	 * function to remove special or unwanted charactors from a tag
	 *
	 * @param string $word
	 * @access private
	 * 
	 * @return string
	 */
	private function removeSpecialChars($word) {
		$return=$word;
		$count=count($this->_ignoreCharArray);
		for ($i=0;$i<$count;$i++){
			$return=str_replace($this->_ignoreCharArray[$i],'',$return);
		}
		return $return;
	}












}