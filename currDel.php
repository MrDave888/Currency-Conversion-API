<?php
  error_reporting(-1);
  ini_set('display_errors', 'On');
  ini_set("allow_url_fopen", 1);

  class delXML{
    #Set protected variables.
    protected $file;

    #Check for errors.
    function __construct(){
      $this->file = new SimpleXMLElement('currencies.xml', NULL, TRUE);
      extract($_POST);

      #Check if the method is recognized.
      if($_SERVER['REQUEST_METHOD'] != "POST" && $_SERVER['REQUEST_METHOD'] != "GET" && $_SERVER['REQUEST_METHOD'] != NULL){
        $this->error(2000);
      }

      #Check if a code has been given and if that code is the right format.
      if(gettype($code)!="string" || !isset($code)){
        $this->error(2200);
      }

      #Check if currency exists.
      if(sizeof($this->file->xpath('currency[@code="'.$code.'"]'))==0){
        $this->error(2400);
      }
    }

    #Delete currency.
    public function delCurr(){
      unset($this->file->xpath('currency[@code="'.$code.'"]'));
    }

    #Display XML information.
    public function responseXML(){
      $xml = new SimpleXMLElement('<method/>');
      $xml->addAttribute('type', 'delete');
      $xml->addChild('at', date('l d m Y h:i:sa'));
      $xml->addChild('code', $_POST['code']);
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
  $delXML = new delXML;
  $delXML->getFile();
  $delXML->delCurr();
  $delXML->responseXML();
?>
