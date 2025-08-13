<?php
global $config;

require_once('common.php');
require_once('config.php');
require_once('config.local.php');
?>

<html>

<head>
    <title>recaptcha V2</title>
    <script type="text/javascript" src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script type="text/javascript" src="qrc:///qtwebchannel/qwebchannel.js"></script>

    <script type="text/javascript">
        var callbackObject;

        function imnotarobotCallback(token) {
            new QWebChannel(qt.webChannelTransport, function(channel) {
                channel.objects.callbackObject.imnotarobotCallback(token);
            });
        }
    </script>

</head>

<body style="background-color: #222222; display: flex; align-items: center; justify-content: center;">
    <div style="text-align: center;">
        <div class="g-recaptcha" data-sitekey="<?php echo $config['recaptcha2_client_secret_key']; ?>" data-callback="imnotarobotCallback"
            style="display: inline-block;" data-theme="dark">
        </div>
    </div>
</body>

</html>