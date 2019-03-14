<?php

//common environment attributes including search paths. not specific to Learnosity
include_once '../env_config.php';

//site scaffolding
include_once 'includes/header.php';

//common Learnosity config elements including API version control vars
include_once '../lrn_config.php';

//alias(es) to eliminate the need for fully qualified classname(s) from sdk
use LearnositySdk\Request\Init;
use LearnositySdk\Utils\Uuid;


//security object. timestamp added by SDK
$security = [
    'consumer_key' => $consumer_key,
    'domain' => $domain
];


//simple api request object for item list view
$request = [
    'mode' => 'item_edit',
	'reference' => Uuid::generate(),
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
        ],
        //set ui > layout > global template to edit_preview for side-by-side edits.
        //add responsive_edit_mode to set viewport breakpoint width--below which, the UI will switch to edit/preview toggle layout
		'dependencies' => [
			'question_editor_api' => [
				'init_options' => [
					'ui' => [
						'layout' => [
							'global_template' => 'edit_preview',
							'responsive_edit_mode' => [
								'breakpoint' => 800
							]
						]
					]
				]
			]
		]
    ],
    'user' => [
        'id' => 'demos-site',
        'firstname' => 'Demos',
        'lastname' => 'User',
        'email' => 'demos@learnosity.com'
    ]
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
            <h2>Edit and Preview Questions Side by Side</h2>
            <p>Change the layout in the question editor to preview your question and feature edits in real time. Automatically switch to edit/preview toggle mode on smaller screens, for best use of screen space.</p>
        </div>
    </div>


    <!-- Container for the author api to load into -->
    <div class="section pad-sml">
        <!--    HTML placeholder that is replaced by API-->
        <div id="learnosity-author"></div>
    </div>


    <!-- version of api maintained in lrn_config.php file -->
    <script src="<?php echo $url_authorapi; ?>"></script>
    <script>
        var initializationObject = <?php echo $signedRequest; ?>;

        //optional callbacks for ready
        var callbacks = {
            readyListener: function () {
                // setTimeout - Temporary work around for readylistener race condition issue. Currently working on a fix
                setTimeout(function(){authorApp.setWidget(
                    {
                        "options": [
                            {
                                "label": "[Option A]",
                                "value": "0"
                            },
                            {
                                "label": "[Option B]",
                                "value": "1"
                            },
                            {
                                "label": "[Option C]",
                                "value": "2"
                            },
                            {
                                "label": "[Option D]",
                                "value": "3"
                            }
                        ],
                        "stimulus": "<p>This is the question the student will answer</p>",
                        "type": "mcq",
                        "validation": {
                            "scoring_type": "exactMatch",
                            "valid_response": {
                                "score": 1,
                                "value": [
                                    "2"
                                ]
                            }
                        },
                        "ui_style": {
                            "type": "horizontal"
                        }
                    },
                    'Multiple choice – standard'
                );
            },1000)},
            errorListener: function (err) {
                console.log(err);
            }
        };

        var authorApp = LearnosityAuthor.init(initializationObject, callbacks);
    </script>


<?php
include_once 'views/modals/initialisation-preview.php';
include_once 'includes/footer.php';