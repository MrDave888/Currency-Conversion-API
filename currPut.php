<?php
  error_reporting(-1);
  ini_set('display_errors', 'On');
  ini_set("allow_url_fopen", 1);

  class putXML{
    #Set protected variables
    protected $file;

    #Check for errors.
    function __construct(){
      $this->file = new SimpleXMLElement('currencies.xml', NULL, TRUE);
      extract($_POST);
      #Check if the method is recognized.
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
      if(gettype($name)!= "string"){
        $this->error(2300);
      }
    }

    #Set new currency.
    public function putNew(){
      unset($this->file->xpath('currency[@code="'.$code.'"]'));
      $root = $this->file->xpath('currencies');
      $curr = $root->addChild('currency');
      $curr->attributes()->code = $_POST['code'];
      $curr->attributes()->rate = $_POST['rate'];
      $curr->attributes()->timestamp = date("l, d m Y h:i:sa");
      $root->addChild('name', $_POST['name']);
      $countryArr = explode($_POST['country'], ",");
      $locations = $root->addChild('locations');
      for($i=0; $i>sizeof($countryArr); $i++){
        $locations->addChild('location', $countryArr[$i]);
      }
      $this->file->asXML('currencies.xml');
    }

    #Display XML information.
    public function responseXML(){
      $xml = new SimpleXMLElement('<method/>');
      $xml->addAttribute('type', 'put');
      $xml->addChild('at', date('l, d m Y h:i:sa'));
      $xml->addChild('rate', $_POST['rate']);
      $curr = $xml->addChild('curr');
      $curr->addChild('code', $_POST['code']);
      $curr->addChild('name', $_POST['name']);
      $curr->addChild('loc', $_POST['country']);
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
  $putXML = new putXML;
  $putXML->putNew();
  $putXML->responseXML();
?>
