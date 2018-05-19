<?php

session_start();
require('vendor/autoload.php');
//$mysqli = new mysqli('127.0.0.1', 'your_user', 'your_pass', 'sakila');
$_SESSION['access_token']='ya29.GlvABZICOmRnkIFGGtJj5ytCL9pSxSyIE6CA0IjEMdyyiPPZqYfAsphYcbnsW9wOzZlG0-NwytjMYQ_7HGvvOjzv3aMSlPz7jvwjN7E5aJAxc2TJCR1r6N1UYwWS';
if(! isset($_SESSION['access_token'])) {
    header('Location: index.php');
    exit;
} else
    $access_token = $_SESSION['access_token'];



$client = new Google_Client();
$client->setAuthConfig('client_secret.json');
$client->setAccessType("offline");
$client->setIncludeGrantedScopes(true);
$client->setAccessToken($access_token);

$adsense = new Google_Service_AdSense($client);

$accounts = $adsense->accounts->listAccounts(array('maxResults' => 10));

// Let's get the list for each of the adsense accounts.
foreach($accounts as $account) {
    $accountId = $account->id;

    // Get all the ad clients under this account.
    $adClients = $adsense->accounts_adclients->listAccountsAdclients($accountId, array('maxResults' => 50));

    // Now foreach of the ad clients lets get the url channels.
    foreach($adClients as $adClient) {

        $adClientId = $adClient->id;
        $urlChannels = $adsense->accounts_urlchannels->listAccountsUrlchannels($accountId, $adClientId, array('maxResults' => 50));

        // Now foreach of the url channels get the reports.
        foreach($urlChannels as $urlChannel) {

            $urlChannelId = $urlChannel->id;
            ?>

            <h3>Url channel id: <?php echo $urlChannelId; ?> </h3>
            <h4>Url pattern: <?php echo $urlChannel->urlPattern; ?></h4>
        <?php 
            $optParams = array(
                'useTimezoneReporting' => true,
                'metric' => array(
                'PAGE',
                'IMPRESSIONS', 'CLICKS', 'PAGE_VIEWS', 'IMPRESSIONS_RPM', 'EARNINGS'),
                'dimension' => array('DATE'),
                'sort' => '+DATE',
                'filter' => array(
                'AD_CLIENT_ID==' . $adClientId,
                'URL_CHANNEL_ID==' . $urlChannelId
                )
            );
            /*
            $startDate = 'today-1d';
            $endDate = 'today-1d';
            */
            $startDate = '2018-05-16';
            $endDate = '2018-05-18';
            $report = $adsense->accounts_reports->generate($accountId, $startDate,
            $endDate, $optParams);

            if (isset($report) && isset($report['rows'])) {
                ?>
                <table>
                    <tr>
                        <th>URL Channel</th>
                        <?php foreach($report['headers'] as $header): ?>
                                <th><?php echo $header['name']; ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <?php foreach($report['rows'] as $row): ?>
                    <?php
                        // Also make an insertion.
                        /*
                        $query = "INSERT INTO stat (DATE, CLICKS, AD_REQUESTS_CTR, COST_PER_CLICK, AD_REQUEST_RPM, EARNINGS) ";
                        $query .= " VALUES ('$row[0]', '$row[1]', '$row[2]', '$row[3]', '$row[4]', '$row[5]')";
                        mysqli_query($mysqli, $query);
                        */
                    ?>
                        <tr>
                            <td><?php echo $urlChannel->urlPattern; ?>
                            <?php foreach($row as $column): ?>
                                <td><?php echo $column; ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
            <?php
            } else {
                print "No rows returned.\n";
            }

        }
    }

}
?>


