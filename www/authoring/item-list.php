<?php

//common environment attributes including search paths. not specific to Learnosity
include_once '../env_config.php';

//site scaffolding
include_once 'includes/header.php';

//common Learnosity config elements including API version control vars
include_once '../lrn_config.php';

//alias(es) to eliminate the need for fully qualified classname(s) from sdk
use LearnositySdk\Request\Init;


//security object. timestamp added by SDK
// ---------------------------------------------------------------------------------------------
// Hack Day
  $consumer_key = "PXwoevusXUXSDOOz";
  $consumer_secret = "Zd4KC0cgjzCgadUoWdcwz04JdY2QSVZCOwA3AtmN";
// ---------------------------------------------------------------------------------------------
$security = array(
    "consumer_key"  => $consumer_key,
    "domain"        => $domain
);
// ---------------------------------------------------------------------------------------------


//simple api request object for item list view
$request = [
    "organisation_id" => 1,
    'mode'      => 'item_edit',
    "reference" => "9ff13244-089a-4191-9d52-5f8a9b030490",
    'config' => [
        'item_edit' => [
            'item' => [
                'reference' => [
					'show' => true,
                    'edit' => true
                ],
                'dynamic_content' => true,
                'shared_passage' => true
            ]
        ]
    ],
    'user' => array(
        'id'        => 'urn:pearson:elm:user:1000176', // Pearson Core user, gives access denied error
        "firstname"=>"Victoria","lastname"=>"Stewart","email"=>"victoria.stewart@pearson.com"
        // 'id'        => 'allain.dollete@gmail.com' // Learnosity test user, gives access denied error
        // 'id'        => 'neil.mcgough@learnosity.com' // Saves this item with no issue
        // 'id'        => 'allain.dollete@learnosity.com' // Saves this item with no issue
    )
];

$Init = new Init('author', $security, $consumer_secret, $request);
$signedRequest = $Init->generate();

?>

    <div class="jumbotron section">
        <div class="toolbar">
            <ul class="list-inline">
                <li data-toggle="tooltip" data-original-title="Preview API Initialisation Object"><a href="#"  data-toggle="modal" data-target="#initialisation-preview"><span class="glyphicon glyphicon-search"></span></a></li>
                <li data-toggle="tooltip" data-original-title="Visit the documentation"><a href="https://support.learnosity.com/hc/en-us/categories/360000105358-Learnosity-Author" title="Documentation"><span class="glyphicon glyphicon-book"></span></a></li>
            </ul>
        </div>
        <div class="overview">
            <h2>Browse Items in Your Item Bank</h2>
            <p>The item list mode allows authors to browse and search the Learnosity-hosted item bank for existing items.
                In this demo, we've enabled creation of new items, but this functionality can be disabled as needed.</p>
        </div>
    </div>


    <!-- Container for the author api to load into -->
    <div class="section pad-sml">
        <!--    HTML placeholder that is replaced by API-->
        <div id="learnosity-author"></div>
    </div>


    <!-- version of api maintained in lrn_config.php file -->
    <script src="https://authorapi-va.learnosity.com?v2020.2.LTS"></script>
    <script>
        var initializationObject = <?php echo $signedRequest; ?>;

        //optional callbacks for ready
        var callbacks = {
            readyListener: function () {
               console.log("Author API has successfully initialized.");
            },
            errorListener: function (err) {
                console.log(err);
            }
        };

        var authorApp = LearnosityAuthor.init(initializationObject, callbacks);
    </script>


<?php
include_once 'views/modals/initialisation-preview.php';
include_once 'includes/footer.php';
