<?php

/** 
 * Instructions:
 * - Create v2 and v3 recaptcha keys
 * - Set the keys in config.php
 * - Set domain in config.php
 * - Enable Rook Sample on character_samples in config.php
 * - The first character town ID in the list will be used for new characters (usally Rookgaard)
 * ---- Client Configuration ------
 * ---- Edit those values in your client (/conf/config.ini)
 * accountCreationClientServices=https://{your-domain}.com/account.php
 * accountCreationClientServicesRecaptchaV2Content=https://{your-domain}.com/recaptcha_v2_content.php
 * accountCreationClientServicesRecaptchaV3Content=https://{your-domain}.com/recaptcha_v3_content.php
 * --------------------------------
 * Author: <git@rodolfoaugusto | Discord: rrodi>
 * 
 */
global $config, $db, $template_path, $logged, $status, $content, $hooks, $twig_loader, $title;

require_once('common.php');
require_once('config.php');
require_once('config.local.php');
require_once(SYSTEM . 'functions.php');
require_once(SYSTEM . 'init.php');
require_once(SYSTEM . 'status.php');

$request = file_get_contents('php://input');
$requestDecoded = json_decode($request);
$actionType = isset($requestDecoded->type) ? $requestDecoded->type : $requestDecoded->Type;

// file_put_contents('website_log.json', $actionType); # DEBUG

header('Content-Type: application/json');
$response = [];

function errorResponse($message, $code = 2)
{
    $response = [
        'errorCode' => $code,
        'errorMessage' => $message,
        'Success' => false
    ];

    if ($code == 101) {
        $response['IsRecaptcha2Requested'] = true;
    }

    return json_encode($response);
}

function removeSpecialChars($string)
{
    return preg_replace('/[^a-zA-Z0-9]/', '', $string);
}

function verifyRecaptcha(string $token, string $secret, ?string $expectedAction = null, float $minScore = 0.5): bool
{
    $ip = $_SERVER['REMOTE_ADDR'];

    require_once PLUGINS . '/recaptcha/autoload.php';
    $recaptcha = new \ReCaptcha\ReCaptcha($secret);

    $resp = $recaptcha->setExpectedHostname($config['domain'])->verify($token, $ip);

    if ($resp->isSuccess()) {
        return true;
    } else {
        return false;
    }
}

if (!$config['client_account_creation_enabled']) {
    echo errorResponse('Account creation is disabled on this server.', 100);
    return;
}

try {
    switch ($actionType) {
        case 'CreateAccountAndCharacter':
            require_once LIBS . 'CreateCharacter.php';
            $createCharacter = new CreateCharacter();
            $recaptchaToken = '';
            $recaptchaSecretToken = '';
            if (isset($requestDecoded->ReCaptcha2Token) && !empty($requestDecoded->ReCaptcha2Token)) {
                $recaptchaToken = $requestDecoded->ReCaptcha2Token;
                $recaptchaSecretToken = $config['recaptcha2_client_secret_key'];
            } elseif (isset($requestDecoded->ReCaptcha3Token) && !empty($requestDecoded->ReCaptcha3Token)) {
                $recaptchaToken = $requestDecoded->ReCaptcha3Token;
                $recaptchaSecretToken = $config['recaptcha3_client_secret_key'];
            }

            $account_name = isset($requestDecoded->EMail) ? removeSpecialChars($requestDecoded->EMail) : null;
            $email = isset($requestDecoded->EMail) ? $requestDecoded->EMail : null;
            $password =  isset($requestDecoded->Password) ? $requestDecoded->Password : null;

            // account
            if (!Validator::accountName($account_name)) {
                echo errorResponse('Account name is not valid.');
                return;
            }

            // email
            if (!Validator::email($email)) {
                echo errorResponse('E-mail address is not valid.');
                return;
            }

            // country
            $country = 'br';
            $info = json_decode(@file_get_contents('http://ipinfo.io/' . $_SERVER['REMOTE_ADDR'] . '/geo'), true);
            if (isset($info['country'])) {
                $country = strtolower($info['country']);
            }

            if ($config['recaptcha_enabled']) {
                if ($recaptchaToken && !empty($recaptchaToken)) {
                    $checkToken = verifyRecaptcha($recaptchaToken, $recaptchaSecretToken, 'create_account', 0.5);
                    if (!$checkToken) {
                        echo errorResponse('Recaptcha verification failed. Please try again.', 101);
                        return;
                    }
                } else {
                    echo errorResponse('Recaptcha is required. Please complete the recaptcha challenge.', 101);
                    return;
                }
            }

            // password
            if (empty($password)) {
                echo errorResponse('Password is required.');
                return;
            } else if (!Validator::password($password)) {
                echo errorResponse('Password is not valid. It must be between 10 and 29 characters long, contain at least one uppercase letter, one lowercase letter, one number, and no invalid characters.');
                return;
            }

            // check if account name is not equal to password
            if (strtoupper($account_name) == strtoupper($password)) {
                echo errorResponse('Account name cannot be the same as password.');
                return;
            }

            if ($config['account_mail_unique']) {
                $test_email_account = new OTS_Account();
                $test_email_account->findByEMail($email);
                if ($test_email_account->isLoaded()) {
                    echo errorResponse('An account with this e-mail address already exists.');
                    return;
                }
            }

            $account_db = new OTS_Account();
            $account_db->find($account_name);

            if ($account_db->isLoaded()) {
                echo errorResponse('An account with this name already exists.');
                return;
            }

            $params = array(
                'account' => $account_db,
                'email' => $email,
                'country' => $country,
                'password' => $password,
                'account_name' => $account_name,
            );

            $errors = array();
            if (config('account_create_character_create')) {
                $character_name = isset($requestDecoded->CharacterName) ? stripslashes(ucwords(strtolower($requestDecoded->CharacterName))) : null;
                $character_sex = isset($requestDecoded->CharacterSex) ? ($requestDecoded->CharacterSex == 'male' ? 1 : 0) : 1;
                $character_vocation = 0; // non-vocation 'Rook Sample' should be enabled
                $character_town = $config['character_towns'][0]; // first character town in the list (Rookgaard for example)
                $createCharacter->check($character_name, $character_sex, $character_vocation, $character_town, $errors);
            }

            $new_account = new OTS_Account();

            $new_account->create($account_name);

            $config_salt_enabled = $db->hasColumn('accounts', 'salt');
            if ($config_salt_enabled) {
                $salt = generateRandomString(10, false, true, true);
                $password = $salt . $password;
            }

            $new_account->setPassword(sha1($password));
            $new_account->setEMail($email);
            $new_account->save();

            if ($config_salt_enabled)
                $new_account->setCustomField('salt', $salt);

            $new_account->setCustomField('created', time());
            $new_account->logAction('Account created.');

            if ($config['account_country']) {
                $new_account->setCustomField('country', $country);
            }

            if ($config['account_premium_days'] && $config['account_premium_days'] > 0) {
                if ($db->hasColumn('accounts', 'premend')) { // othire
                    $new_account->setCustomField('premend', time() + $config['account_premium_days'] * 86400);
                } else { // rest
                    $premdays = $config['account_premium_days'];
                    $new_account->setCustomField('premdays', $premdays);
                    $lastDay = ($premdays > 0 && $premdays < OTS_Account::GRATIS_PREMIUM_DAYS) ? time() + ($premdays * 86400) : 0;
                    $new_account->setCustomField('lastday', $lastDay);
                }
            }

            if ($config['account_premium_coins']) {
                $new_account->setCustomField('coins', $config['account_premium_coins']);
            }

            $tmp_account = ($account_name);

            // character creation
            $character_created = $createCharacter->doCreate($character_name, $character_sex, $character_vocation, $character_town, $new_account, $errors, true);
            if (!$character_created) {
                // $errors['char'] = Validator::getLastError();
                // var_dump($errors);
                echo errorResponse('Character creation failed: ' . implode(', ', $errors));
                return;
            }

            if ($config['mail_enabled'] && $config['account_welcome_mail']) {
                $mailBody = $twig->render('account.welcome_mail.html.twig', array(
                    'account' => $tmp_account,
                    'email' => $email,
                    'password' => $password ?? null
                ));

                if (_mail($email, 'Your account on ' . $config['lua']['serverName'], $mailBody)) {
                    $response = [
                        "Success" => true,
                        "AccountID" => $new_account->getID(),
                    ];
                } else {
                    echo errorResponse('An internal error has occurred. Please try again later! Error: E30.');
                    return;
                }
            } else {
                $response = [
                    "Success" => true,
                    "AccountID" => $new_account->getID(),
                ];
            }
            break;
        case 'checkpassword':
            $password = isset($requestDecoded->Password1) ? $requestDecoded->Password1 : null;

            function validatePassword($password)
            {
                global $config;
                $requirements = [
                    'PasswordLength' => strlen($password) >= 10 && strlen($password) <= 29,
                    'InvalidCharacters' => preg_match('/[^a-zA-Z0-9!@#$%^&*()_+={}\[\]:;"\'<>,.?\/\\\|`~]/', $password) ? false : true,
                    'HasLowerCase' => !!preg_match('/[a-z]/', $password),
                    'HasUpperCase' => !!preg_match('/[A-Z]/', $password),
                    'HasNumber' => !!preg_match('/[0-9]/', $password)
                ];
                return $requirements;
            }

            function passwordStrength(string $password): int
            {
                $score = 0;

                $len     = strlen($password);
                $lower   = preg_match('/[a-z]/', $password) ? 1 : 0;
                $upper   = preg_match('/[A-Z]/', $password) ? 1 : 0;
                $digit   = preg_match('/\d/', $password) ? 1 : 0;
                $special = preg_match('/[^A-Za-z0-9]/', $password) ? 1 : 0;

                // Length
                if ($len >= 12) {
                    $score += 2;
                } elseif ($len >= 8) {
                    $score += 1;
                }

                // Character variety
                $classes = $lower + $upper + $digit + $special;
                if ($classes >= 3) {
                    $score += 2;
                } elseif ($classes === 2) {
                    $score += 1;
                }

                // Pattern penalty: Name+digits+symbol
                if (preg_match('/^[A-Z][a-z]{3,}\d{2,}[!@#$%^&*()_+\-=\[\]{};:\'",.<>\/?\\|`~]?$/', $password)) {
                    $score -= 1;
                }

                // Clamp to 0–4
                return max(0, min(4, $score));
            }

            function getPasswordStrengthColor($strength)
            {
                switch ($strength) {
                    case 0:
                        return '#ff0000'; // Red
                    case 1:
                        return '#eb8005'; // Orange
                    case 2:
                        return '#eb8005'; // Orange
                    case 3:
                        return '#00ff00'; // Green
                    case 4:
                        return '#00ff00'; // Green
                    default:
                        return '#ff0000'; // Default to red for invalid strength
                }
            }


            $requirements = validatePassword($password);
            $passwordStrength = passwordStrength($password);
            $passwordStrengthColor = getPasswordStrengthColor($passwordStrength);
            $validPassword = $requirements['PasswordLength'] && $requirements['InvalidCharacters'] && $requirements['HasLowerCase'] && $requirements['HasUpperCase'] && $requirements['HasNumber'];

            $response = [
                'PasswordValid' => $validPassword,
                'PasswordStrength' => $passwordStrength,
                'Password1' => $password,
                'PasswordStrengthColor' => $passwordStrengthColor,
                'PasswordRequirements' => $requirements
            ];

            break;
        case 'CheckEMail':
            $email = isset($requestDecoded->Email) ? $requestDecoded->Email : null;

            $validEmail = false;
            if ($email) {
                if (Validator::email($email)) {
                    $validEmail = true;
                }
            }

            $response = [
                'IsValid' => $validEmail,
                'EMail' => $email,
            ];
            break;
        case 'GenerateCharacterName':
            $names = [
                'Juninho da Crossbow',
                'Knight Apocalipse',
                'Druid of Hell',
                'Druida',

                // Portuguese-inspired
                'Guerreiro de Ferro',
                'Mago do Norte',
                'Paladino da Chama',
                'Arqueiro da Aurora',
                'Assassino da Sombra',
                'Cacador do Deserto',
                'Guardiao do Vale',
                'Clerigo da Luz',
                'Bardo do Vento',
                'Bruxo da Tempestade',
                'Monge do Lobo',
                'Soberano de Pedra',
                'Rei do Inverno',
                'Rainha do Sol',
                'Campones de Terra',
                'Capitao do Mar',
                'Cavaleiro de Prata',
                'Cavaleiro do Trono',
                'Druida da Floresta',
                'Feiticeiro do Oeste',
                'Ladino do Porto',
                'Sentinela da Noite',
                'Escudeiro do Rei',
                'Mercador de Ouro',
                'Ferreiro do Reino',
                'Alquimista do Fogo',
                'Heraldo da Coroa',
                'Besta do Pantano',
                'Sabio do Bosque',
                'Caudilho do Sul',
                'Patrulheiro da Fronteira',
                'Vigia da Torre',
                'Vanguarda do Norte',
                'Sombras do Castelo',
                'Senhor de Guerra',
                'Mestre do Arco',
                'Mestre da Lanca',
                'Caudilho de Aco',
                'Artifice da Lua',
                'Arcanista do Cedro',
                'Tempestade do Rei',
                'Vulto do Abismo',
                'Sombra do Corvo',
                'Coroa de Ferro',
                'Coroado do Vale',

                // English-inspired
                'Knight of Dawn',
                'Paladin of Flame',
                'Ranger of Mist',
                'Baron of Ash',
                'Duke of Storms',
                'Warden of Night',
                'Wizard of North',
                'Sorcerer of Stone',
                'Warlock of Ember',
                'Cleric of Light',
                'Monk of Wolves',
                'Bard of Rivers',
                'Hunter of Kings',
                'Assassin of Shadows',
                'Guardian of Vale',
                'Seer of Oak',
                'Herald of Crown',
                'Marshal of West',
                'Captain of Seas',
                'Squire of Honor',
                'Blade of Evening',
                'Shield of Dawn',
                'Aegis of Realm',
                'Hammer of Justice',
                'Arrow of Fate',
                'Watchman of Gate',
                'Keeper of Lore',
                'Witch of Mire',
                'Druid of Grove',
                'Rogue of Harbor',
                'Knight Lionheart',
                'Sir Blackwood',
                'Lady Ravensong',
                'Fen of Iron',
                'Torin the Bold',
                'Galen the Gray',
                'Rowan Stormborn',
                'Alaric the Just',
                'Lyra Nightfall',
                'Cedric Oakshield',
                'Garrick Steel',
                'Isolde Bright',
                'Thane of Frost',
                'Varric Emberfall',
                'Mira Starwind',
                'Ulric Stonehand',
                'Sigurd Wolfbane',
                'Brand of Vale',
                'Elsin Wayfarer',

                // Spanish-inspired
                'Caballero del Alba',
                'Guerrero del Fuego',
                'Hechicero del Norte',
                'Brujo del Bosque',
                'Arquero del Viento',
                'Cazador de Sombras',
                'Clerigo de Luz',
                'Monje del Lobo',
                'Bardo del Rio',
                'Vigilante de la Torre',
                'Guardian del Valle',
                'Senor de Guerra',
                'Maestre de Espada',
                'Maestre de Lanza',
                'Sabio del Roble',
                'Heraldo de la Corona',
                'Capitan de los Mares',
                'Escudero del Rey',
                'Mercader de Oro',
                'Alquimista del Fuego',
                'Druida del Bosque',
                'Paladin del Sol',
                'Condesa del Invierno',
                'Conde del Trueno',
                'Baron de Ceniza',
                'Duque de Tormentas',
                'Sombra del Cuervo',
                'Filo del Destino',
                'Martillo de Justicia',
                'Escudo del Amanecer',

                // Mixed and fun variations
                'Aventureiro de Kairon',
                'Ladrao do Mercado',
                'Corsario de Sal',
                'Sentinela do Oeste',
                'Templario de Ferro',
                'Arqueira da Neve',
                'Mago de Bruma',
                'Dama do Trono',
                'Lobo do Ermo',
                'Rei de Cinzas',
                'Rainha da Aurora',
                'Primo do Duque',
                'Juninho do Reino',
                'Lula do Abismo',
                'Bardo de Vila',
                'Domador de Dragao',
                'Cacador de Bestas',
                'Cavaleiro de Ouro',
                'Feiticeira do Mar',
                'Guardiao de Estrelas',

                // Extra English
                'Knight Apogee',
                'Druid of Ember',
                'Ranger of Frost',
                'Spearman of Vale',
                'Sword of Thunder',
                'Crown of Winter',
                'Queen of Dawn',
                'Prince of Dust',
                'Dame of Oaks',
                'Warden Ashfall',
                'Harbor Rogue',
                'Grove Seer',

                // Extra Spanish
                'Caballero del Trono',
                'Druida del Roble',
                'Cazador del Norte',
                'Guardiana del Alba',
                'Maestra de Magia',
                'Reina del Invierno',
                'Principe del Desierto',
                'Dama de Robles',
                'Vigilante del Puerto',
                'Explorador del Valle',
                'Sabio de Niebla',

                // Extra Portuguese
                'Druida do Cedro',
                'Mestre do Escudo',
                'Escudeiro da Coroa',
                'Princesa do Inverno',
                'Primo do Conde',
                'Explorador do Vale',
                'Sabia da Bruma',
                'Guerreira da Manha',
                'Feiticeiro do Templo',
                'Rei do Norte',
            ];
            $random = $names[array_rand($names)];
            $response = ["GeneratedName" => $random];
            break;

        case 'getaccountcreationstatus';
            $worlds = [
                [
                    'Name' => $config['worlds'][0], // TODO: foreach all worlds
                    'PlayersOnline' => isset($status) && $status['online'] ? $status['players'] : 0,
                    'CreationDate' => 1755360000,
                    'Region' => 'South America',
                    'PvPType' => 'Open PvP',
                    'PremiumOnly' => 0,
                    'TransferType' => 'Blocked',
                    'BattlEyeActivationTimestamp' => 1755360000,
                    'BattlEyeInitiallyActive' => 1
                ]
            ];

            $response = ['Worlds' => $worlds, 'RecommendedWorld' => $config['worlds'][0], 'IsCaptchaDeactivated' => false];
            break;

        case 'CheckCharacterName':
            // TODO: Implement character name validation and availability check
            $response = [
                'IsValid' => true,
            ];
            break;
        default:
            echo errorResponse('Invalid action type: ' . $actionType, 1);
            return;
    }

    echo json_encode($response);
} catch (Exception $e) {
    echo errorResponse('Invalid request format: ' . $e->getMessage(), 2);
    return;
}
