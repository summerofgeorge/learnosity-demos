<?php

//common environment attributes including search paths. not specific to Learnosity
include_once '../env_config.php';

//site scaffolding
include_once 'includes/header.php';

//common Learnosity config elements including API version control vars
include_once '../lrn_config.php';

use LearnositySdk\Request\Init;
use LearnositySdk\Utils\Uuid;

$language = filter_input(INPUT_GET, 'language', FILTER_SANITIZE_FULL_SPECIAL_CHARS, ['options'=>['default'=>'pt-PT']]);

/*
    We pull in all i18n files from an open source Github repo:
     - label bundles per API
     - question template groups
     - question templates
     - question template thumbnails
*/
$baseRepoUrl = 'https://raw.githubusercontent.com/Learnosity/learnosity-i18n/master/languages/';

/*
    Retrieve the label bundles, per API, that contain translations.
    We need one for Author API and the embedded Question Editor API
    (loaded by Author API internally). We store them in separate
    files for easier maintenance and a cleaner initialization
    object for this demo file.
*/
if (preg_match('/^[A-Za-z\-]+$/', $language)) {
    $labels = file_get_contents($baseRepoUrl . $language . '/label_bundles/reports-api.json');
}

$security = [
    'consumer_key' => $consumer_key,
    'domain'       => $domain
];

//simple api request object for Reports API
$request = [
    'reports' => [
        [
            'id' => 'sessions-summary',
            'type' => 'sessions-summary',
            'user_id' => '$ANONYMIZED_USER_ID',
            'session_ids' => [
                '02be514f-bb82-4b5e-af71-7538f07e90fa'
            ]
        ],
        [
            'id' => 'sessions-list',
            'type' => 'sessions-list',
            'limit' => 5,
            'ui' => 'table',
            'activities' => [
                ['id' => 'Weekly_Math_Quiz', 'name' => 'Weekly Math Quiz']
            ]
        ],
        [
            'id' => 'sessions-list-item',
            'type' => 'sessions-list-by-item',
            'limit' => 5,
            'activity_id'  => 'Weekly_Math_Quiz',
            'display_user' => false,
            'users' => [
                ['id' => '$ANONYMIZED_USER_ID', 'name' => 'Walter White']
            ]
        ],
        [
            'id' => 'lastscore-activity',
            'type' => 'lastscore-by-activity',
            'scoring_type' => 'partial',
            'user_id' => '$ANONYMIZED_USER_ID',
            'display_time_spent' => true,
            'activities' => [
                ['id' => 'Weekly_Math_Quiz', 'name' => 'Weekly Math Quiz'],
                ['id' => 'Summer_Test_1', 'name' => 'Summer Test']
            ]
        ]
    ],
    'label_bundle' => json_decode($labels, true)
];

$Init = new Init('reports', $security, $consumer_secret, $request);
$signedRequest = $Init->generate();

?>

    <div class="jumbotron section">
        <div class="toolbar">
            <ul class="list-inline">
                <li data-toggle="tooltip" data-original-title="Preview API Initialisation Object"><a href="#"  data-toggle="modal" data-target="#initialisation-preview"><span class="glyphicon glyphicon-search"></span></a></li>
                <li data-toggle="tooltip" data-original-title="Visit the documentation"><a href="https://support.learnosity.com/hc/en-us/categories/360000105378-Learnosity-Analytics" title="Documentation"><span class="glyphicon glyphicon-book"></span></a></li>
            </ul>
        </div>
        <div class="overview">
            <h2>Display Student-Centric reports (i18n support)</h2>
            <p>Learn more about individual students in an easy, in-depth fashion! Our Reports API provides embeddable, student-focused reports to provide a student with additional information and feedback or provide a teacher with a drilled down view of their student progress.
            <p>This demo uses learnosity-i18n which is a public repository, containing Learnosity internationalization language bundles.</p>
            <p style="margin-bottom:25px;">Click a language icon to see a translation of the assessment below:</p>
            <div>
                <div class="language-button-container">
                    <a class="language-button <?php if ($language === 'pt-PT') { echo 'selected'; } ?>" href="/analytics/student-centric-reporting-i18n.php?language=pt-PT">
                        <img class="language-flag" src="/static/images/i18n/flag-PT.png" />
                        Português / Portuguese
                    </a>
                </div>
                <div class="language-button-container">
                    <a class="language-button <?php if ($language === 'es-ES') { echo 'selected'; } ?>" href="/analytics/student-centric-reporting-i18n.php?language=es-ES">
                        <img class="language-flag" src="/static/images/i18n/flag-ES.png" />
                        Español / Spanish
                    </a>
                </div>
            </div>
            <ul>
                <li><h4><a href="#sessions-summary-report">Sessions Summary</a></h4></li>
                <li><h4><a href="#sessions-list-report">List of Student Sessions and Scores</a></h4></li>
                <li><h4><a href="#sessions-list-item-report">List of Student Sessions - broken down by item scores</a></h4></li>
                <li><h4><a href="#lastscore-activity-report">Most recent score per Activity</a></h4></li>

            </ul>
            </p>
        </div>
    </div>

    <div class="section pad-sml">
        <!-- Container for the reports api to load into -->
        <h3 id="sessions-summary-report"><a href="#sessions-summary-report">Sessions Summary</a></h3>
        <p>See a running total of correct, incorrect and skipped items for an individual session or a combination of sessions.</p>
        <div id="sessions-summary"></div>
    </div>

    <div class="section pad-sml">
        <!-- Container for the reports api to load into -->
        <h3 id="sessions-list-report"><a href="#sessions-list-report">List of Student Sessions and Scores</a></h3>
        <p>View multiple attempts at the same activity, or multiple different activities, for a single student.</p>
        <div id="sessions-list"></div>
    </div>

    <div class="section pad-sml">
        <!-- Container for the reports api to load into -->
        <h3 id="sessions-list-item-report"><a href="#sessions-list-item-report">List of Student Sessions - broken down by item scores</a></h3>
        <p>Dive deeper and analyze exactly how a student did at a per-item level for a number of sessions.</p>
        <div id="sessions-list-item"></div>
    </div>

    <div class="section pad-sml">
        <!-- Container for the reports api to load into -->
        <h3 id="lastscore-activity-report"><a href="#lastscore-activity-report">Most recent score per Activity</a></h3>
        <p>See a student score for their most recent attempt at one or multiple activities.</p>
        <div id="lastscore-activity"></div>
    </div>

    <script src="<?php echo $url_reports; ?>"></script>
    <script>

        var reportsApp = LearnosityReports.init(<?php echo $signedRequest; ?>);

    </script>

<?php
include_once 'views/modals/initialisation-preview.php';
include_once 'includes/footer.php';
