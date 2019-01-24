<?


$dateRus = "2/1/2017";
$dateEn = str_replace("/","-",$dateRus);

//echo date("Y-m-d", strtotime($dateEn));
//echo date( "Y-m-d", strtotime( "2009-01-31 +2 month" ) )

$xml=simplexml_load_file("old_var.xml") or die("Error: Cannot create object");
//foreach ($xml->news->ann as $key => $value) {
$arrData = array(); 
foreach($xml->children() as $key=>$ann) {
	$dateEn = str_replace("/","-", $ann['date']);
    $dateMySql = date("Y-m-d", strtotime($dateEn));

    $arrItem = array("date" => $dateMySql, "text" => $ann->__toString ());
    $arrData[] = $arrItem;
	//echo "{$ann->__toString ()} \n" ;
}
echo "count = " . count($arrData) . "\n";
for($i = count($arrData); $i > 0; $i--){
$item = $arrData[$i-1];
   echo "{$item['date']} : {$item['text']} \n";
   $request['ann_date'] = $item['date'];
   $request['ann_text'] = trim($item['text']);
   $request['deleted'] = 0;
   $res = createAnnouncement($request);
   if(!$res) {
   	echo "problem with {$request['ann_date']}: {$res}";
   	break;

   }

}


function createAnnouncement($request){
	define('HOST', 'localhost');
	define('DB', 'rest_news');
	define('USER', 'root');
	define('PASS', '');
		try {
		$conn = new PDO(sprintf("mysql:host=%s;dbname=%s", HOST, DB), USER, PASS);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "INSERT INTO data (ann_date, ann_text, deleted) VALUES ('%s', '%s', '%s')";
		$sql = sprintf($sql, $request['ann_date'], $request['ann_text'], $request['deleted']);
       	$conn->exec($sql);
		return true;
	} catch(PDOException $e) {
		return "Creation failed: " . $e->getMessage();
	}
}

?>