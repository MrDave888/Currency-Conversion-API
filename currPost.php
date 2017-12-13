<?php
  error_reporting(-1);
  ini_set('display_errors', 'On');
  ini_set("allow_url_fopen", 1);

  class postXML{
    #Set protected variables.
    protected $file;
    protected $oldRate;
    protected $name;
    protected $loc;

    #Check for errors.
    function __construct(){
      $this->file = new SimpleXMLElement('currencies.xml', NULL, TRUE);
      extract($_POST);

      #Check if method recognized.
      if($_SERVER['REQUEST_METHOD'] != "POST" && $_SERVER['REQUEST_METHOD'] != "GET" && $_SERVER['REQUEST_METHOD'] != NULL){
        $this->error(2000);
      }

      #Check if a rate has been given and if that rate is the right format.
      if (!preg_match('/^[+-]?(\d*\.\d+([eE]?[+-]?\d+)?|\d+[eE][+-]?\d+)$/', $rate) || !isset($rate)){
        $this->error(2100);
      }

      #Check if code has been given and if that code is the right format.
      if(gettype($code)!="string" || !isset($code)){
        $this->error(2200);
      }

      #Check if country names have been given and if those names are in the right format.
      if(gettype($name)!= "string" || !isset($name)){
        $this->error(2300);
      }

      #Check if currency exists.
      if(sizeof($this->file->xpath('currency[@code="'.$code.'"]'))==0){
        $this->error(2400);
      }
    }

    #Set new currency rate.
    public function postCurr(){
      $code = $this->file->xpath("currency[@code='".$_POST["code"]."']");
      $oldRate = $code[0]->attributes()->rate;
      $this->oldRate = $oldRate;
      // print_r($this->oldRate);
      $this->name = $code[0]->name;
      foreach($code[0]->locations->location as $child){
        $this->loc = $this->loc.$child.", ";
      }
      $code[0]->attributes()->time = date("l, d m Y h:i:sa");
      $code[0]->attributes()->rate = $_POST['rate'];
      $this->file->asXML('currencies.xml');
    }

    #Display XML information.
    public function responseXML(){
      // print_r($this->oldRate);
      $xml = new SimpleXMLElement('<method/>');
      $xml->addAttribute('type', 'post');
      $xml->addChild('at', date('l, d m Y h:i:sa'));
      $prev = $xml->addChild('previous');
      $prev->addChild('rate', $this->oldRate);
      $pCurr = $prev->addChild('curr');
      $pCurr->addChild('code', $_POST['code']);
      $pCurr->addChild('name', $this->name);
      $pCurr->addChild('loc', $this->loc);
      $new = $xml->addChild('new');
      $new->addChild('rate', $_POST['rate']);
      $nCurr = $new->addChild('curr');
      $nCurr->addChild('code', $_POST['code']);
      $nCurr->addChild('name', $this->name);
      $nCurr->addChild('loc', $this->loc);
      echo $xml->asXML();
      header('Content-Type: text/xml');
    }

    public function error($errCode){

      $codes = [
        2000 => 'Method not recognized or is missing',
  	    2100 => 'Rate in wrong format or is missing',
  	    2200 => 'Currency code in wrong format or is missing',
  	    2300 => 'Country name in wrong format or is missing',
  	    2400 => 'Currency code not found for update',
  	    2500 => 'Error in service'
      ];

      $convError = new SimpleXMLElement("<conv/>");
      $error = $convError->addChild('error');
      $error->addChild('code', $errCode);
      $error->addChild('msg', $codes[$errCode]);

      #Display errors in XML and JSON on based on request.
      echo $convError->asXML();
      header("Content-Type: text/xml");
      die();
    }
  }
  #Run classes and functions.
  $postXML = new postXML;
  $postXML->postCurr();
  $postXML->responseXML();
?>
