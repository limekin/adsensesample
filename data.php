<?php

session_start();
require('vendor/autoload.php');

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
            $optParams = array(
                'metric' => array(
                'CLICKS',
                'AD_REQUESTS_CTR', 'COST_PER_CLICK', 'AD_REQUESTS_RPM', 'EARNINGS'),
                'dimension' => 'DATE',
                'sort' => '+DATE',
                'filter' => array(
                'AD_CLIENT_ID==' . $adClientId,
                'URL_CHANNEL_ID==' . $urlChannelId
                )
            );
            $startDate = 'today-7d';
            $endDate = 'today-1d';
            $report = $adsense->accounts_reports->generate($accountId, $startDate,
            $endDate, $optParams);

            if (isset($report) && isset($report['rows'])) {
                ?>

                <table>
                    <tr>
                        <?php foreach($report['headers'] as $header): ?>
                                <th><?php echo $header['name']; ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <?php foreach($report['rows'] as $row): ?>
                        <tr>
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


