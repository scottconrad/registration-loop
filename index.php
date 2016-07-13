<?php
require 'vendor/autoload.php';
//error_reporting(E_ALL ^ E_NOTICE);
if($_GET && $_GET['JSON']) $dump_json = true;
$page = 0;
if(isset($_GET) && array_key_exists('page',$_GET)){
    $page = $_GET['page'];
}
$review_from = 0;
if (isset($_GET) && array_key_exists('review_from', $_GET)) {
    $review_from = $_GET['review_from'];
}



$page = (int)$page;
$http = new GuzzleHttp\Client();
$offset = $page * 10;
try{
    $data =
      ['apiKey' => '61067f81f8cf7e4a1f673cd230216112',
        'noOfReviews' => 10,
        'offset'=>$offset,
        'threshold'=>1,
        'page'=>$page,
        'showLoader'=>false,
        'review_from'=>$review_from
      ];

    if($review_from ==  0){
        $data['internal'] = 1;
    }elseif($review_from == 1){
        $data['yelp'] = 1;
    }elseif($review_from == 2){
        $data['google'] = 1;
    }


    $data = array_merge($data,$_GET);
    $request = $http->request('GET','https://test.localfeedbackloop.com/api',
      ['query' => $data]);
    $json = $request->getBody();
    $request_data = json_decode($json,true);
    $request_data['pages'] = $request_data['business_info']['total_rating']['total_no_of_reviews'] /  $data['noOfReviews'];
    $request_data['page'] = $page;
    $request_data['perPageAmount'] = $data['noOfReviews'];
    $request_data['review_from'] = $review_from;
    $json = json_encode($request_data,true);

}catch(\Exception $e){
    print $e->__toString();
}
if(isset($dump_json) && $dump_json){
    header('Content-Type: application/json');
    print $json;
    exit();
}
$reviews_sources =  [ 0 => 'Molomedia',1=>'Yelp',2=>'Google' ];
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="css/app.css" />
    <title>Online Reputation Manager - Scott Conrad</title>
</head>
<body>
<div class="container" style="display:none;" :v-show="reviews" id="app">
    <div id="loader" v-show="showLoader" style="display:none;z-index:1000; color:white; position:fixed;top:0px;left:0px;width:100%;height:100%; background-color:rgba(50,50,50,0.5); text-align:center;">
        <h2 style="margin-top:100px;">Loading...</h2>

    </div>
    <div class="row">
        <div class="col-lg-12"><img src="//www.reputationloop.com/wp-content/uploads/2016/02/RepLoop_logo_260x40.png" alt="reputation loop" /></div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <label for="">Source:</label>
            <select class="form-control" v-on:change="sourceChanged()" id="review_from" v-model="review_source">
                <?php foreach($reviews_sources as $key => $value){ ?>
                    <option value="<?=$key; ?>"><?=$value; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            {{getOffset()}} to {{getStartingItem()}} of {{getTotalReviews()}}
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-offset-3">
            <h1>{{getBusinessName()}} - </h1>
            <p>Total Reviews: {{getTotalReviews()}} - Average Review: {{getRating()}}</p>
            <div class="review" v-for="review in getReviews()">
                <strong>Customer Rating: {{review.rating}}</strong><br />
                <hr />
                {{review.customer_name}} said:<br />
                <a :src="review.customer_url"><h4>from: {{review.review_source}}</a></h4> - {{getReviewDate(review)}} - {{getReviewSourceById(review.review_from)}}
                <hr />
                <p>{{review.description}}</p>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6"><a href="" style="display:none;" @click="populatePreviousPage" v-show="doesPreviousPageExist()" class="btn pull-left btn-primary">Load Previous</a></div>
        <div class="col-lg-6"><a href="" style="display:none;" :v-show="isNextPage" @click="populateNextPage" v-show="doesNextPageExist()" class="btn pull-right btn-success">Load Next</a>
        </div>


    </div>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.26/vue.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.14.1/moment.min.js"></script>
    <script>
        var RL = {
            '__state':<?=$json ?: '{}'?>
        }
    </script>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/zepto/1.1.6/zepto.min.js"></script>
    <script type="text/javascript" src="js/app.js"></script>
</body>
</html>