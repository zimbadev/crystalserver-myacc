<?php
global $config;

require_once('common.php');
require_once('config.php');
require_once('config.local.php');
?>

<html>
<header>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo $config['recaptcha3_client_secret_key']; ?>"></script>
</header>

<body style="background-color: #222222;">
    <div class="g-recaptcha"></div>

    <script>
        var execCreateAccount = function() {
            grecaptcha.execute(
                '<?php echo $config['recaptcha3_client_secret_key']; ?>', {
                    action: 'create_account'
                }
            ).then(function(token) {
                window.v3Token = token;
            });
        }

        grecaptcha.ready(function() {
            execCreateAccount();
            setInterval(execCreateAccount, 110 * 1000);
        });
    </script>

</body>

</html>