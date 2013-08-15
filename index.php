<?php
require 'vendor/autoload.php';

$app = new \Slim\Slim();
$app->get('/shop_path/', function () use ($app ){
    
    $reader = simplexml_load_file('http://www.westelm.com/services/catalog/v2/categorytree/shop/index.xml');

    echo json($reader);
});



$app->get('/documents/', function () use ($app ){
    
    // if(!array_key_exists('CLOUDANT_URL', $_ENV)){
    //     echo 'no cloudant';
    // }
    // if($id !== 'all'){
    //     $doc = $client->getDoc($id);
    // }
    // else{
    //     $doc = $client->getAllDocs();
    // }
    //$doc = couchDocument::getInstance($client, 'a9d33d44e1ed983f1c9bc79f22000799');
    //$doc->storeAsAttachment('sup', 'file.txt', 'text/plain');
    // $google_hom=file_get_contents('http://www.skillshare.com/westelm');
    // $ok = $doc->storeAsAttachment($doc,$google_hom,'text/html', 'SkillshareHomepage3.html');
    //print_r($doc);

    // $request = $app->request();
    // die($request->headers('cookie'));

    $couch_dsn = 'https://app16358878.heroku:8SIBnDWEabLuvNp88A6EMqwp@app16358878.heroku.cloudant.com';
    $couch_db = "stores";
    $client = new couchClient($couch_dsn,$couch_db);
    $dbs = $client->listDatabases();
    print_r($dbs);
});

$app->get('/showFacebookData', function () use ($app ){
    $couch_dsn = 'https://app16358878.heroku:8SIBnDWEabLuvNp88A6EMqwp@app16358878.heroku.cloudant.com';
    $couch_db = "stores";
    $client = new couchClient($couch_dsn,$couch_db);

    echo json($client->getView('all', 'with_events'));
});

$app->get('/stores/', function ($store_id = '') use ($app ){
    $couch_dsn = 'https://app16358878.heroku:8SIBnDWEabLuvNp88A6EMqwp@app16358878.heroku.cloudant.com';
    $couch_db = "stores";
    $client = new couchClient($couch_dsn,$couch_db);
    $res = $app->response();
    $res->header('Content-Type','application/json');
    $res->header('Access-Control-Allow-Origin','http://www.westelm.com');
    $res->header('Access-Control-Allow-Methods','PUT, GET, POST, DELETE, OPTIONS');
    
    $res->body(json($client->getView('store', 'all')));
    
    //echo json($client->getView('store', 'all'));
});

$app->get('/stores/:id/map/', function ($id) use ($app ){
    $couch_dsn = 'https://app16358878.heroku:8SIBnDWEabLuvNp88A6EMqwp@app16358878.heroku.cloudant.com';
    $couch_db = "stores";
    $client = new couchClient($couch_dsn,$couch_db);

    $opts = array( "startkey" => $id, "endkey" => $id);
    $response = $client->setQueryParameters($opts)->getView("store","location");
    $latlon = $response->rows[0]->value->latlon;
   $app->render('map.php', array('lat' => $latlon[0], 'lon' => $latlon[1], 'store_id' => (String)$id));
});

$app->get('/stores(/:store_id)/', function ($store_id = '') use ($app ){
    $couch_dsn = 'https://app16358878.heroku:8SIBnDWEabLuvNp88A6EMqwp@app16358878.heroku.cloudant.com';
    $couch_db = "stores";
    $client = new couchClient($couch_dsn,$couch_db);

    if($store_id == ''){
        echo json($client->getView('store', 'all'));
    }
    elseif($store_id == 'events'){
        echo json($client->getView('store', 'with_events'));
    }
    elseif($store_id == 'list_ids'){
        echo json($client->getView('store', 'list_ids'));

    }
    elseif($store_id == 'list_ids_with_events'){
        echo json($client->getView('store', 'list_ids_with_events'));
    }
    else{
        $opts = array( "startkey" => $store_id, "endkey" => $store_id);
        echo json($client->setQueryParameters($opts)->getView("store","store_info"));    
    }
    
});

$app->get('/insta(/:store_id)/', function ($store_id = '') use ($app) {
    
   

    if($store_id !== ''){
        $file = 'json/'.$store_id.'.json';
        if(file_exists($file)){
            $json = json_decode(file_get_contents($file), true);
        }
        else{
            $file = 'json/insta.json';
            $json = json_decode(file_get_contents($file), true);
        }
        
    }

    echo json($json);
});

$app->get('/test/:command/', function($cmd) use ($app) {
     $couch_dsn = 'https://app16358878.heroku:8SIBnDWEabLuvNp88A6EMqwp@app16358878.heroku.cloudant.com';
    $couch_db = "stores";
    $client = new couchClient($couch_dsn,$couch_db);

    
        echo json($client->getView('test', $cmd));
});

$app->get('/stores/:store_id/location/', function ($store_id = '') use ($app ){
    $couch_dsn = 'https://app16358878.heroku:8SIBnDWEabLuvNp88A6EMqwp@app16358878.heroku.cloudant.com';
    $couch_db = "stores";
    $client = new couchClient($couch_dsn,$couch_db);

    $opts = array( "startkey" => $store_id, "endkey" => $store_id);
    $response = $client->setQueryParameters($opts)->getView("store","location");
    //echo json($response);
    $new_etag = md5('secret');
    $app->etag($new_etag);

    $app->setCookie('foo', 'bar', '2 days');
    var_dump('Affoo sdffsdfs fsdf oo');
});

$app->get('/stores(/:store_id/events(/:event_id))/', function ($store_id = '', $event_id= '') use ($app ){
    $couch_dsn = 'https://app16358878.heroku:8SIBnDWEabLuvNp88A6EMqwp@app16358878.heroku.cloudant.com';
    $couch_db = "stores";
    $client = new couchClient($couch_dsn,$couch_db);

    $opts = array( "startkey" => $store_id, "endkey" => $store_id);
    $response = $client->setQueryParameters($opts)->getView("store","with_events");
    echo json($response);
    
});







$app->get('/updateFacebookData', function () use ($app ){
    $couch_dsn = 'https://app16358878.heroku:8SIBnDWEabLuvNp88A6EMqwp@app16358878.heroku.cloudant.com';
    $couch_db = "stores";
    $client = new couchClient($couch_dsn,$couch_db);

    $data = _facebook()->api('/westelm/locations?fields=events.fields(name,location,start_time,end_time,timezone),about,name,location,username,likes,hours,website,picture,cover','GET');

    $stores_data = $data['data'];
    $counter = 1;
    foreach($stores_data as $store_data){
        
       
        $new_id = str_replace(' ','',$store_data['name']);
        $new_id = strtolower(str_replace('WestElm','',$new_id));
        try {
            $doc= $client->getDoc( $new_id);

        } catch (Exception $e) {
             $doc = new stdClass();
            $doc->_id = $new_id;
        }
        if(array_key_exists('username', $store_data)){
            $doc->username = $store_data['username'];
        }        
        if(array_key_exists('hours', $store_data)){
            $doc->hours = $store_data['hours'];
        }
        $doc->name = $store_data['name'];
        $doc->cover = $store_data['cover'];
        if(array_key_exists('picture', $store_data)){
            $doc->hours = $store_data['picture']['data'];
        }
        $doc->location = $store_data['location'];
        $doc->likes = $store_data['likes'];
        if(array_key_exists('events', $store_data)){
            $doc->events = $store_data['events']['data'];   
        }
        
        $client->storeDoc($doc);
        echo json($doc);
    }
       
});



$app->get('/database', function () {
    
    // if(!array_key_exists('CLOUDANT_URL', $_ENV)){
    //     echo 'no cloudant';
    // }
    $couch_dsn = 'https://app16358878.heroku:8SIBnDWEabLuvNp88A6EMqwp@app16358878.heroku.cloudant.com';
    $couch_db = "stores";
    $client = new couchClient($couch_dsn,$couch_db);

    $doc = $client->getChanges();

    echo json($doc);
});

$app->get('/create/:id', function ($id) {
    $couch_dsn = 'https://app16358878.heroku:8SIBnDWEabLuvNp88A6EMqwp@app16358878.heroku.cloudant.com';
    $couch_db = "stores";
    $client = new couchClient($couch_dsn,$couch_db);
    
    $doc = new stdClass();
    $doc->_id = $id; // the unique id of the document

    // let's add some other properties
    $doc->type = "article";
    $doc->published = true;
    $doc->tags = array ( "story", "couch", "php" ) ;

    try {
        $client->storeDoc($doc);
        echo json($doc);
    } catch ( Exception $e ) {
        die("Unable to store the document : ".$e->getMessage());
    }
});





$app->get('/', function () {
    $params = array(
      'scope' => 'read_stream, friends_likes',
      'redirect_uri' => 'http://wecomm.herokuapp.com/updateFacebookData'
    );
    
    $login = _facebook()->getLoginUrl($params);
    echo '<a href="'.$login.'">facebook</a>';
});

$app->get('/batch', function () {

    $batch = array();

    $req = array(
        'method'       => 'GET',
        'relative_url' => '/westelm?fields=feed'
    );

    $batch[] = json_encode($req);

    $req = array(
        'method'       => 'GET',
        'relative_url' => '/westelm/locations?fields=events,name,location,likes'
    );

    $batch[] = json_encode($req);

    // $req = array(
    //     'method'       => 'GET',
    //     'relative_url' => '/westelm/locations?fields=events,name,location'
    // );

    
    // $batch[] = json_encode($req);

    $params = array(
        'batch' => '[' . implode(',',$batch) . ']'
    );
    $info = _facebook()->api('/','POST',$params);

    $we_info_json = json_decode($info[0]['body']);

    $store_info_json = json_decode($info[1]['body']);

    //$store_info_json['type'] = 'facebook';

    // $event_info_json = json_decode($info[2]['body']);

    $couch_dsn = "https://app16358878.heroku:8SIBnDWEabLuvNp88A6EMqwp@app16358878.heroku.cloudant.com";
    $couch_db = "stores";
    $client = new couchClient($couch_dsn,$couch_db);
    //$doc = new couchDocument($client);
    //$doc->set( $store_info_json );

    $out = $store_info_json;

    echo json($out);
});

$app->get('/welcome', function () {
    $me = _facebook()->api('/westelm?fields=locations.fields(name,events.fields(name,attending),location),name,cover,company_overview,link','GET');

    $_locations = $me['locations']['data'];
    

    $name =     '<a href="'.$me['link'].'"><h1>'.$me['name'].'</h1></a>';
    $cover =    '<img src="'. $me['cover']['source'] . '" />';
    $overview=  '<p>'. $me['company_overview'].'</p>'; 

    foreach ($_locations as $key=>$location) 
    {
        if(array_key_exists('events', $location)){
            echo '<h1>'.$location['name'].'</h1>';
            echo '<ul>';
            $_events =  $location['events']['data'];
            foreach ($_events as $key=>$event) 
            {

                echo '<br/>';
                echo '<li>'.$event['name'].'</li>';
                echo '<br/>';
                if(array_key_exists('attending',$event)){
                    echo '<h3>Attending</h3><br/>';
                    echo '<ul>';
                    $_attendees = $event['attending']['data'];
                    foreach ($_attendees as $key=>$attendee) {
                        echo '<li>'.$attendee['name'].'</li>';
                    }
                    echo '</ul>';
                }
                
            }
            echo '</ul>';

            //echo json($_events);
        }

        

        
        
    
        //echo $location['name'];
        //echo '<br/>';
    }
    
    //echo json($me);//.$name.$cover.$overview;
    //echo ;


    // echo '<img src="'.$me['picture']['data']['url'].'" /><br/>'
    //      .'<h1>'.$me['name'].'</h1>';


});


$app->get('/hello/:name', function ($name) {
    echo "Hello, $name";
});

$app->get('/stores', function () {  
    $couch_dsn = "http://localhost:5984/";
    $couch_db = "westelm";
    $client = new couchClient($couch_dsn,$couch_db);

    try {
        $doc = $client->getDoc('2348bf358a840310f078c0ceb31bec1e');
    } catch (Exception $e) {
        if ( $e->code() == 404 ) {
            echo "Document not found\n";
        } else {
            echo "Error: ".$e->getMessage()." (errcode=".$e->getCode().")\n";
        }
        exit(1);
    }
    echo json($doc);
});

function json( $data ){
  header('Content-Type: application/json');
  return json_encode( $data );
}

function _facebook(){
    $config = array();
    $config['appId'] = '436227196476402';
    $config['secret'] = 'edcb031850e0b26665ff1967e931e214';
    $facebook = new Facebook($config);
    $access_token = $facebook->getAccessToken();
    return $facebook;
};

$app->run();

