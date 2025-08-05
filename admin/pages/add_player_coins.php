<?php 
global $db, $account_logged;

/**
 * Add Tibia Coins to a Specific Player by Character Name
 *
 * @package   MyAAC
 * @author    João Paulo (Tryller/jprzimba)
 * @copyright 2023 MyAAC
 */
defined('MYAAC') or die('Direct access not allowed!');

$title = 'Add Player Coins';
$base = BASE_URL . 'admin/?p=add_player_coins';

function echo_success($message)
{
    echo '<p class="success">' . $message . '</p>';
}

function echo_error($message)
{
    global $error;
    echo '<p class="error">' . $message . '</p>';
    $error = true;
}


$query = $db->query("SELECT `id`, `name`, `email`, `coins`, `coins_transferable` FROM `accounts` WHERE ID > 1;");
$accounts = $query->fetchAll();

if (isset($_POST['add_coins_to_player']) && $account_logged->isSuperAdmin()) {
    $characterName = trim($_POST['character_name'] ?? '');
    $coinsToAdd = (int)$_POST['coins_'] ?? 0;
    $coinType = $_POST['coin_type'] ?? 'coins';

    if ($coinsToAdd < 1) {
        echo_error("You need to add 1 or more Tibia Coins!");
    } elseif (empty($characterName)) {
        echo_error("Character name cannot be empty!");
    } else {
        $coinColumn = $coinType === 'transferable' ? 'coins_transferable' : 'coins';
        try {
            $characterQuery = $db->query("SELECT `account_id` FROM `players` WHERE `name` = " . $db->quote($characterName) . ";");
            $character = $characterQuery->fetch();
            
            if (!$character) {
                echo_error("Character '{$characterName}' does not exist!");
            } else {
                $accountID = $character['account_id'];
                $accountQuery = $db->query("SELECT `id`, `name`, `coins`, `coins_transferable` FROM `accounts` WHERE `id` = {$accountID};");
                $account = $accountQuery->fetch();
                
                if (!$account) {
                    echo_error("Account associated with character '{$characterName}' does not exist!");
                } else {
                    $newCoins = (int)$account[$coinColumn] + $coinsToAdd;
                    $db->exec("UPDATE `accounts` SET `{$coinColumn}` = {$newCoins} WHERE `id` = {$accountID}");
                    echo_success("You have added {$coinsToAdd} Tibia Coins ({$coinType}) to the account '{$account['name']}' (associated with character '{$characterName}').");
                    $accounts = $db->query("SELECT `id`, `name`, `email`, `coins`, `coins_transferable` FROM `accounts` WHERE ID > 1;")->fetchAll();
                }
            }
        } catch (PDOException $error) {
            echo_error($error->getMessage());
        }
    }
}
?>

<div class="row">
    <div class="col-12 col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Add Player Coins</h3><br>
                <?php if ($account_logged->isSuperAdmin()) { ?>
                    <div class="box mt-4">
                        <div class="box-body">
                            <form action="<?= $base ?>" method="post">
                                <div class="form-group">
                                    <small>Insert the Character Name:</small><br>
                                    <input type="text" name="character_name" placeholder="Character Name" style="width: 100%;" required>
                                </div>
                                <br/>
                                <div class="form-group d-flex align-items-end">
                                    <div style="flex: 1; margin-right: 10px;">
                                        <small>Insert the number of coins you want to add:</small><br>
                                        <input type="number" name="coins_" min="1" max="9999" placeholder="(min 1, max 9999)" style="width: 100%;" required>
                                    </div>
                                    <div style="flex: 1;">
                                        <small>Select the type of coin:</small><br>
                                        <select name="coin_type" class="btn btn-primary dropdown-toggle" style="width: 100%;">
                                            <option value="coins">Non-Transferable</option>
                                            <option value="transferable">Transferable</option>
                                        </select>
                                    </div>
                                </div>

                                <br/>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success" name="add_coins_to_player"><i class="fa fa-add"></i> ADD</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Accounts:</h3>
            </div>
            <div class="box-body no-padding">
                <table class="table table-striped">
                    <tbody>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Acc Name</th>
                        <th>E-mail</th>
                        <th style="text-align: right">Non-Transferable Coins</th>
                        <th style="text-align: right">Transferable Coins</th>
                    </tr>
                    <?php foreach ($accounts as $k => $acc) { ?>
                        <tr>
                            <td><?= $k + 1 ?></td>
                            <td><?= $acc['id'] ?></td>
                            <td><?= $acc['name'] ?></td>
                            <td><?= $acc['email'] ?></td>
                            <td style="text-align: right;"><?= $acc['coins'] ?></td>
                            <td style="text-align: right;"><?= $acc['coins_transferable'] ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>