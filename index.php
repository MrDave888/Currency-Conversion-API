<?php
  error_reporting(-1);
  ini_set('display_errors', 'On');
  ini_set("allow_url_fopen", 1);

  class createXML{
    #Set protected variables.
    protected $currency;
    protected $loc;

    #Function to get rate XML file.
    public function getCurr(){
      $this->currency = new SimpleXMLElement('http://www.floatrates.com/daily/gbp.xml', NULL, TRUE);
    }

    #Function to get country XML file.
    public function getLoc(){
      $this->loc = new SimpleXMLElement('countries.xml', NULL, TRUE);
    }

    #Function to construct and save XML file.
    public function saveXML(){
      $xml = new SimpleXMLElement('<currencies/>');
        $base = $xml->addChild('currency');
        $base->addAttribute('code', $this->currency->baseCurrency);
        $base->addAttribute('rate', '1');
        $base->addAttribute('timestamp', $this->currency->pubDate);
        $base->addChild('name', $this->currency->item->baseName);
        $baseLocations = $base->addChild("locations");
        foreach($this->loc->xpath("country[@currency='".$this->currency->baseCurrency."']") as $x){
          $baseLocations->addChild("location", explode(",",$x->attributes()->name)[0]);
        }
      foreach($this->currency->item as $item){
        $curr = $xml->addChild('currency');
        $curr->addAttribute('code', $item->targetCurrency);
        $curr->addAttribute('rate', $item->exchangeRate);
        $curr->addAttribute('timestamp', $item->pubDate);
        $curr->addChild('name', $item->targetName);
        $locations = $curr->addChild("locations");
        foreach($this->loc->xpath("country[@currency='".$item->targetCurrency."']") as $x){
          $locations->addChild("location", explode(",",$x->attributes()->name)[0]);
        }
      }
      $xml->asXML('currencies.xml');
    }
  }

  class convert{
    #Set protected variables.
    protected $currencies;

    #Check for errors.
    function __construct(){
      $this->currencies = new SimpleXMLElement('currencies.xml', NULL, TRUE);
      $params = array('from', 'to', 'amnt', 'format');
      $formats = array('xml', 'json');

      extract($_GET);
      $get = array_intersect($params, array_keys($_GET));
      if (count($get) < 4) {
  	     $this->error(1100);
      }

      if (count($_GET) > 4) {
  	    $this->error(1200);
      }

      #Check if currency is in the XML file.
      if(sizeof($this->currencies->xpath("currency[@code='".$_GET["to"]."']")) == 0 || $this->currencies->xpath(sizeof("currency[@code='".$_GET["from"]."']")) == 0){
        $this->error(1000);
      }

      if(!in_array($format, $formats)){
        $this->error(1200);
      }

      if (!preg_match('/^[+-]?(\d*\.\d+([eE]?[+-]?\d+)?|\d+[eE][+-]?\d+)$/', $_GET['amnt'])){
        $this->error(1300);
      }
    #If there are no errors, run next function.
     $this->getData();
    }

    #Get XML data from XML file constructing a response that will be displayed.
    public function getData(){
      $toCurr = $this->currencies->xpath("currency[@code='".$_GET["to"]."']");
      $fromCurr = $this->currencies->xpath("currency[@code='".$_GET["from"]."']");
      $rate = floatval(floatval($toCurr[0]->attributes()->rate)/floatval($fromCurr[0]->attributes()->rate));
      $conv = new SimpleXMLElement('<conv/>');
      $conv->addChild('at', $toCurr[0]->attributes()->timestamp);
      $conv->addChild('rate', $rate);
      $from = $conv->addChild('from');
      $from->addChild('code', $_GET["from"]);
      $from->addChild('curr', $fromCurr[0]->name);
      $fromLocArray = [];
      foreach($fromCurr[0]->locations->location as $fromLocation){
        array_push($fromLocArray, $fromLocation);
      }
      $fromLocString = implode(", ", $fromLocArray);
      $from->addChild('loc', $fromLocString);
      $from->addChild('amnt', $_GET["amnt"]);
      $to = $conv->addChild('to');
      $to->addChild('code', $_GET["to"]);
      $to->addChild('curr', $toCurr[0]->name);
      $toLocArray = [];
      foreach($toCurr[0]->locations->location as $toLocation){
        array_push($toLocArray, $toLocation);
      }
      $toLocString = implode(",", $toLocArray);
      $to->addChild('loc', $toLocString);
      $to->addChild('amnt', ($rate*floatval($_GET['amnt'])));

      #Display XML information. Convert the information to JSON if requested.
      if($_GET['format'] == "JSON" || $_GET['format'] == "json"){
        echo json_encode($conv);
        header("Contet-Type: application/json");
      }else{
        echo $conv->asXML();
        header("Content-Type: text/xml");
      }
    }

    #Construct and display errors based on code.
    public function error($errCode){

      $codes = [
        1000 => 'Currency type not recognized',
	      1100 => 'Required parameter is missing',
	      1200 => 'Parameter not recognized',
	      1300 => 'Currency amount must be a decimal number',
	      1400 => 'Error in service'
      ];

      $convError = new SimpleXMLElement("<conv/>");
      $error = $convError->addChild('error');
      $error->addChild('code', $errCode);
      $error->addChild('msg', $codes[$errCode]);

      #Display errors in XML and JSON on based on request.
      if(isset($_GET['format']) && $_GET['format'] == "json"){
        echo json_encode($convError);
        header("Contet-Type: application/json");
      }else{
        echo $convError->asXML();
        header("Content-Type: text/xml");
      }
      die();
    }
  }

  #Run classes and functions.
  $createXML = new createXML;
  $createXML->getCurr();
  $createXML->getLoc();
  $createXML->saveXML();
  $convert = new convert;
?>
