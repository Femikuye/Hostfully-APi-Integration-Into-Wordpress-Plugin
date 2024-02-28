<div class="wrap">
    <?php settings_errors();

    $banner_images = get_option('phemrise_plugin_biem');
    ?>

    <ul class="nav espl-nav-tabs">
        <li class="active">
            <a href="#espl-tab-1">Settings</a>
        </li>
        <li class="">
            <a href="#espl-tab-2">Import</a>
        </li>
    </ul>
    <div class="espl-tab-content">
        <div id="espl-tab-1" class="espl-tab-pane active">
            <h3>Hostfully API Settings</h3>
            <?php
            // print_r(get_option('espl_settings'));
            ?>

            <form method="post" action="options.php">
                <?php
                settings_fields('espl_settings_option');
                do_settings_sections('es_property_listings');
                submit_button();
                ?>
            </form>
        </div>
        <div id="espl-tab-2" class="espl-tab-pane">
            <h3>Update Properties From Hostfully</h3>
            <p>Click the update button below to connect to hostfully API to fetch and update
                all the properties in to the site database
            </p>
            <?php
            $nonce = wp_create_nonce('espl_update_property_nonce');

            // Include nonce in your HTML form
            echo '<input type="hidden" id="espl_ajax_nonce" value="' . esc_attr($nonce) . '">';
            ?>
            <div class="espl-server-response"></div>
            <button type="button" class="btn btn-success" id="espl_update_properties">Update Properties</button>
        </div>
    </div>
</div>
<!-- <script type="text/javascript" src="https://platform.hostfully.com/assets/js/pikaday.js"></script>

<script type="text/javascript" src="https://platform.hostfully.com/assets/js/leadCaptureWidget_2.0.js"></script>

<div id="leadWidget"></div>

<script>
    var widget = new Widget('leadWidget', '9318d479-cf05-4f30-8e58-3314318eda38', {
        "maximun_availability": "2027-01-23T11:41:48.933Z",
        "type": "agency",
        "fields": ["phone"],
        "showAvailability": true,
        "lang": "US",
        "minStay": true,
        "price": true,
        "hidePriceWithoutDates": false,
        "cc": false,
        "emailClient": true,
        "saveCookie": true,
        "showDynamicMinStay": true,
        "backgroundColor": "#FFFFFF",
        "buttonSubmit": {
            "backgroundColor": "#c69b71"
        },
        "showPriceDetailsLink": true,
        "showGetQuoteLink": false,
        "labelColor": "#000000",
        "showTotalWithoutSD": true,
        "redirectURL": false,
        "showDiscount": true,
        "includeReferrerToRequest": true,
        "customDomainName": null,
        "source": null,
        "aid": "ORB-49587220416635719",
        "clickID": null,
        "valuesByDefaults": {
            "checkIn": {
                "value": ""
            },
            "checkOut": {
                "value": ""
            },
            "guests": {
                "value": ""
            },
            "discountCode": {
                "value": ""
            }
        },
        "pathRoot": "https://platform.hostfully.com/"
    });
</script> -->